<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\RoleResource;
use App\Http\Resources\RolesCollection;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param RoleRepositoryInterface $repository
     */
    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(RoleResource::class)->setCollectionResource(RolesCollection::class);
    }

    /**
     * @return array
     */
    protected function with(): array
    {
        return ['permissions'];
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
