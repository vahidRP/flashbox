<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\UserResource;
use App\Http\Resources\UsersCollection;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(UserResource::class)
            ->setCollectionResource(UsersCollection::class);
    }

    /**
     * @return array
     */
    protected function with(): array
    {
        return [
            'address',
            'roles',
            'stores'
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
            'name'     => ['required'],
            'email'    => ['required', Rule::unique($this->repository->getModelClassName())->ignore($id)],
            'password' => ['required']
        ], $id);
    }
}
