<?php

use Src\Application\UseCases\CreateProduct\CreateProduct;
use Src\Application\UseCases\CreateProduct\CreateProductInput;
use Src\Application\UseCases\CreateProduct\CreateProductOutput;
use Src\Domain\Repositories\ProductRepository;

test('Deve criar uma produto', function () {
    $input = new CreateProductInput(
        name: 'Product Example',
        // description: 'teste',
    );

    $productRepository = Mockery::mock(ProductRepository::class);
    $productRepository->shouldReceive('save')->once();
    $createProduct = new CreateProduct(
        productRepository: $productRepository,
    );

    $output = $createProduct->execute(
        input: $input
    );

    expect($output)->toBeInstanceOf(CreateProductOutput::class);
    expect($output->productId)->toBeString();
});
