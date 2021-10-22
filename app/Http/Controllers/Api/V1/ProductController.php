<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use App\Repositories\Criteria\Eloquent\FilterByColumns;
use App\Repositories\Criteria\Eloquent\FilterByRelations;
use App\Repositories\Criteria\Eloquent\NearbyProducts;
use App\Repositories\Criteria\Eloquent\SearchByColumns;
use App\Repositories\Criteria\Eloquent\SortByColumns;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProductRepositoryInterface $repository
     */
    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(ProductResource::class)->setCollectionResource(ProductsCollection::class);
    }

    /**
     * @return array
     */
    protected function with(): array
    {
        return [];
    }

    /**
     * @param Request $request
     * @param null    $id
     * @return array
     */
    protected function validationRules(Request $request, $id = null): array
    {
        return $this->rules([

        ], $id);
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
            ->addCriteria(new NearbyProducts())
            ->with($this->with());

        $perPage = $request->input('per_page', $this->perPage);
        return $perPage === '*' ? $query->findAll() : $query->paginate((int)$perPage);
    }
}
