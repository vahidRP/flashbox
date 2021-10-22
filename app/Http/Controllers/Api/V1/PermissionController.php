<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\PermissionsCollection;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PermissionRepositoryInterface $repository
     */
    public function __construct(PermissionRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(PermissionResource::class)
            ->setCollectionResource(PermissionsCollection::class);
    }

    /**
     * @return array
     */
    protected function with(): array
    {
        return ['roles'];
    }

    /**
     * @param Request $request
     * @param null    $id
     * @return array
     */
    protected function validationRules(Request $request, $id = null): array
    {
        return $this->rules([
            'title'    => ['nullable', 'string'],
            'identity' => ['required', 'string', Rule::unique($this->repository->getModelClassName())->ignore($id)]
        ], $id);
    }
}
