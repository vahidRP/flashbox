<?php

namespace App\Policies;

use App\Models\Receipt;

class ReceiptPolicy extends Policy
{
    protected string $model = Receipt::class;
}
