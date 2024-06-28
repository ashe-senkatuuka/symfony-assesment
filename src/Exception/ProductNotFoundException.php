<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, 'Product not found');
    }
}
