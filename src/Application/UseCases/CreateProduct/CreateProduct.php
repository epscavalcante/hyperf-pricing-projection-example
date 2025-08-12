<?php

declare(strict_types=1);

namespace Src\Application\UseCases\CreateProduct;

use Src\Domain\Entities\Product;
use Src\Domain\Repositories\ProductRepository;

class CreateProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {}

    public function execute(CreateProductInput $input): CreateProductOutput
    {
        $product = Product::create(
            name: $input->name,
        );

        $this->productRepository->save($product);

        // disparar um evento

        return new CreateProductOutput(
            productId: $product->getId(),
        );
    }
}
