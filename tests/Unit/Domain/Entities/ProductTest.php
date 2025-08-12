<?php

use Src\Domain\Entities\Product;

test('Deve criar um um produto', function () {
    $product = Product::create(
        name: 'Product 1',
    );

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->name)->toBe('Product 1');
});