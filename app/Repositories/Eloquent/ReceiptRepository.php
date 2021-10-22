<?php

namespace App\Repositories\Eloquent;

use App\Models\Receipt;
use App\Repositories\Interfaces\ReceiptRepositoryInterface;
use App\Services\Payment;
use App\Support\Repository\EloquentRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReceiptRepository.
 */
class ReceiptRepository extends EloquentRepository implements ReceiptRepositoryInterface
{
    /**
     * ReceiptRepository constructor.
     *
     * @param Receipt $model
     * @throws \ReflectionException
     */
    public function __construct(Receipt $model)
    {
        parent::__construct($model);
    }

    public function pay($request, $id)
    {
        $result = Payment::receipt(Receipt::find($id))->pay();

        if($result['status']){
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
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
