<?php

namespace App\Policies;

use App\Models\Product;

class ProductPolicy extends Policy
{
    protected string $model = Product::class;
}
