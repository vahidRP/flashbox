<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\StoreResource;
use App\Http\Resources\StoresCollection;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param StoreRepositoryInterface $repository
     */
    public function __construct(StoreRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(StoreResource::class)
            ->setCollectionResource(StoresCollection::class);
    }

    /**
     * @return array
     */
    protected function with(): array
    {
        return ['user', 'address'];
    }

    /**
     * @param Request $request
     * @param null    $id
     * @return array
     */
    protected function validationRules(Request $request, $id = null): array
    {
        return $this->rules([
            'user_id' => ['required', 'integer'],
            'title'   => ['required', 'string'],
        ], $id);
    }
}
