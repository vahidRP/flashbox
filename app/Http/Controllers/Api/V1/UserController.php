<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\UserResource;
use App\Http\Resources\UsersCollection;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(UserResource::class)->setCollectionResource(UsersCollection::class);
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
