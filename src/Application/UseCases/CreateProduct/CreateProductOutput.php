<?php

declare(strict_types=1);

namespace Src\Application\UseCases\CreateProduct;

readonly class CreateProductOutput
{
    public function __construct(
        public string $productId,
    ) {}
}
