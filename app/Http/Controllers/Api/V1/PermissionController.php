<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\PermissionsCollection;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PermissionRepositoryInterface $repository
     */
    public function __construct(PermissionRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(PermissionResource::class)->setCollectionResource(PermissionsCollection::class);
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
}
