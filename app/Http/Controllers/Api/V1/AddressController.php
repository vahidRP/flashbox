<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AddressResource;
use App\Http\Resources\AddressesCollection;
use App\Repositories\Interfaces\AddressRepositoryInterface;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AddressRepositoryInterface $repository
     */
    public function __construct(AddressRepositoryInterface $repository)
    {
        $this->repository = $repository->setResource(AddressResource::class)->setCollectionResource(AddressesCollection::class);
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
