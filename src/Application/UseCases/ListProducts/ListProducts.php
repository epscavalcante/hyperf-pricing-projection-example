<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ListProducts;

use Src\Domain\Repositories\ProductRepository;

class ListProducts
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {}

    public function execute(): ListProductsOutput
    {
        $products = $this->productRepository->list();
        return new ListProductsOutput(
            total: count($products),
            items: $products
        );
    }
}
