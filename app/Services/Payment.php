<?php

namespace App\Services;

use App\Models\Receipt;

class Payment
{
    private function __clone()
    {
    }

    private function __construct(public Receipt $receipt)
    {
    }

    public static function receipt(Receipt $receipt): static
    {
        return (new static($receipt));
    }

    public function pay()
    {
        // This method will pay the price in the bank portal
        $totalPrice = $this->receipt->total_price;

        $this->receipt->load([
            'products'
        ]);

        $this->beforePay();
        $paid = true;

        if($paid){
            return $this->successfulPay();
        }

        return $this->failedPay();
    }

    protected function beforePay(): void
    {
        $this->receipt->status = Receipt::STATUES[1];
        $this->receipt->save();

        foreach($this->receipt->products as $product){
            $product->capacity = $product->capacity - 1;
            $product->save();
        }
    }

    protected function successfulPay(): array
    {
        $this->receipt->status = Receipt::STATUES[2];
        $this->receipt->save();

        return ['status' => true];
    }

    protected function failedPay(): array
    {
        $this->receipt->status = Receipt::STATUES[0];
        $this->receipt->save();

        foreach($this->receipt->products as $product){
            $product->capacity = $product->capacity + 1;
            $product->save();
        }

        return ['status' => false];
    }
}
