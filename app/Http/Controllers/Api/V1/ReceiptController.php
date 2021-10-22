<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ReceiptResource;
use App\Http\Resources\ReceiptsCollection;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\ReceiptRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ReceiptRepositoryInterface $repository
     */
    public function __construct(ReceiptRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(ReceiptResource::class)
            ->setCollectionResource(ReceiptsCollection::class);
    }

    /**
     * @return array
     */
    protected function with(): array
    {
        return [
            'products.store.address'
        ];
    }

    /**
     * @param Request $request
     * @param null    $id
     * @return array
     */
    protected function validationRules(Request $request, $id = null): array
    {
        return $this->rules([
            'user_id'     => ['required', Rule::exists(User::class, 'id')],
            'total_price' => ['prohibited'],
            'status'      => ['prohibited']
        ], $id);
    }

    /**
     * attach an item to relation
     *
     * @param Request $request
     * @param int     $id
     * @return mixed
     */
    public function attach(Request $request, int $id)
    {
        Gate::authorize('view', $this->repository->getModelClassName());

        $inputs = $this->repository->cast($request->all());
        $this->repository->setValidator([
            'relation'   => ['required'],
            'related_id' => ['required'],
        ])->validate($inputs);

        $relation = $inputs['relation'];
        $relatedId = $inputs['related_id'];

        $model = $this->repository->skipResource()->skipCriteria()->with($this->with())->findOneBy($id);

        if($relation === 'products'){
            $product = Product::find($relatedId);
            if($product->capacity < 1){
                return response()->json([
                    'data' => [
                        'status'  => false,
                        'message' => 'There is no more product capacity left'
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if($model->products->where('id', $relatedId)->first()){
                return response()->json([
                    'data' => [
                        'status'  => false,
                        'message' => 'product attached previously'
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $model->total_price = $model->total_price + $product->price;
            $model->save();
        }

        if(method_exists($model, $relation)){
            $model->{$relation}()->attach($relatedId);
            return response()->json([
                'data' => [
                    'status' => true
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'status' => false
            ]
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * detach an item from relation
     *
     * @param Request $request
     * @param int     $id
     * @return mixed
     */
    public function detach(Request $request, int $id)
    {
        Gate::authorize('view', $this->repository->getModelClassName());

        $inputs = $this->repository->cast($request->all());
        $this->repository->setValidator([
            'relation'   => ['required'],
            'related_id' => ['required'],
        ])->validate($inputs);

        $relation = $inputs['relation'];
        $relatedId = $inputs['related_id'];

        $model = $this->repository->skipResource()->skipCriteria()->findOneBy($id);

        if($relation === 'products'){
            $product = Product::find($relatedId);

            if($model->products->where('id', $relatedId)->first()){
                $model->total_price = $model->total_price - $product->price;
                $model->save();
            }
        }

        if(method_exists($model, $relation)){
            $model->{$relation}()->detach($relatedId);
            return response()->json([
                'data' => [
                    'status' => true
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'status' => false
            ]
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function pay(Request $request, $id)
    {
        return $this->repository->pay($request, $id);
    }
}
