<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AddressResource;
use App\Http\Resources\AddressesCollection;
use App\Models\Address;
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
        return ['userable'];
    }

    /**
     * @param Request $request
     * @param null    $id
     * @return array
     */
    protected function validationRules(Request $request, $id = null): array
    {
        return $this->rules([
            (Address::USERABLE_KEY . '_type') => ['prohibited'],
            (Address::USERABLE_KEY . '_id') => ['prohibited'],
            'address' => ['required_without:lat'],
            'lat' => ['required_without:address'],
            'lng' => ['required_without:address'],
        ], $id);
    }
}
