<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller as BaseController;

use App\Repositories\Criteria\Eloquent\FilterByColumns;
use App\Repositories\Criteria\Eloquent\FilterByRelations;
use App\Repositories\Criteria\Eloquent\SearchByColumns;
use App\Repositories\Criteria\Eloquent\SortByColumns;
use App\Support\Repository\Contracts\RepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Controller extends BaseController
{
    protected int $perPage = 15;
    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    protected function with(): array
    {
        return [];
    }

    protected function validationRules(Request $request, $id = null): array
    {
        return $this->rules([

        ], $id);
    }

    /**
     * @param array $rules
     * @param ?int  $id
     * @return array
     */
    protected function rules(array $rules = [], ?int $id = null)
    {
        $tableName = $this->repository->getModelClassName();

        if(!empty($id)){
            $rules = array_merge($rules, [
                'id' => ["exists:{$tableName}"]
            ]);
        }

        return $rules;
    }

    /**
     * Authorize a given action against a set of permissions.
     *
     * @param array|string $permission
     * @param bool         $requireAll
     * @return Controller
     * @throws AuthorizationException
     */
    public function authorizeByPermission(array|string $permission, bool $requireAll = false)
    {
        if(!Auth::user()->hasPermission($permission, $requireAll)){
            throw (new AuthorizationException('This action is unauthorized.', Response::HTTP_FORBIDDEN));
        }

        return $this;
    }

    /**
     * Returns collection of items based on custom filters
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        Gate::authorize('view', $this->repository->getModelClassName());

        $query = $this->repository->addCriteria(new FilterByRelations($request->input('relations', [])))
            ->addCriteria(new FilterByColumns($request->input('filters', [])))
            ->addCriteria(new SearchByColumns($request->input('search', [])))
            ->addCriteria(new SortByColumns($request->input('sort', [])))
            ->with($this->with());

        $perPage = $request->input('per_page', $this->perPage);
        return $perPage === '*' ? $query->findAll() : $query->paginate((int)$perPage);
    }

    /**
     * Create new item
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        Gate::authorize('create', $this->repository->getModelClassName());

        $model = $this->repository->setValidator($this->validationRules($request))->create($request->all());
        return $this->repository->respondWithResource($model, Response::HTTP_CREATED);
    }

    /**
     * returns one item
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function show($id)
    {
        Gate::authorize('read', $this->repository->getModelClassName());

        return $this->repository->with($this->with())->findOneBy($id);
    }

    /**
     * Updates one item
     *
     * @param Request $request
     * @param         $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('update', $this->repository->getModelClassName());

        return $this->repository->setValidator($this->validationRules($request, $id))->updateBy($request->all(), $id);
    }

    /**
     * delete an item
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        Gate::authorize('delete', $this->repository->getModelClassName());

        try{
            return response()->json([
                'data' => [
                    'status' => $this->repository->delete($id)
                ]
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => [
                    'text' => __($e->getMessage())
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            'relation' => ['required'],
            'related_id' => ['required'],
        ])->validate($inputs);

        $relation = $inputs['relation'];
        $relatedId = $inputs['related_id'];

        $model = $this->repository->skipResource()->skipCriteria()->findOneBy($id);

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
            'relation' => ['required'],
            'related_id' => ['required'],
        ])->validate($inputs);

        $relation = $inputs['relation'];
        $relatedId = $inputs['related_id'];

        $model = $this->repository->skipResource()->skipCriteria()->findOneBy($id);

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
}
