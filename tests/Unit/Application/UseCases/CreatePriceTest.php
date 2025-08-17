<?php

use Src\Application\UseCases\CreatePrice\CreatePrice;
use Src\Application\UseCases\CreatePrice\CreatePriceInput;
use Src\Application\UseCases\CreatePrice\CreatePriceOutput;
use Src\Domain\Entities\Layer;
use Src\Domain\Entities\Product;
use Src\Domain\Enums\LayerType;
use Src\Domain\Exceptions\LayerNotFoundException;
use Src\Domain\Exceptions\PriceAlreadExistsException;
use Src\Domain\Exceptions\ProductNotFoundException;
use Src\Domain\Repositories\LayerRepository;
use Src\Domain\Repositories\PriceRepository;
use Src\Domain\Repositories\ProductRepository;

test('Deve falhar criar um Price para uma layer que não existe', function () {
    $layerRepository = Mockery::mock(LayerRepository::class);
    $layerRepository->shouldReceive('findById')
        ->with('layer')
        ->once()
        ->andReturnNull();
    $productRepository = Mockery::mock(ProductRepository::class);
    $productRepository->shouldNotReceive('findById');
    $priceRepository = Mockery::mock(PriceRepository::class);
    $priceRepository->shouldNotReceive('existsByLayerIdAndProductId');

    $useCase = new CreatePrice(
        layerRepository: $layerRepository,
        priceRepository: $priceRepository,
        productRepository: $productRepository,
    );

    $input = new CreatePriceInput(
        layerId: 'layer',
        productId: 'product',
        value: 0
    );

    $useCase->execute(
        input: $input
    );
})->throws(LayerNotFoundException::class);

test('Deve falhar criar um Price para um produto que não existe', function () {
    $layerRepository = Mockery::mock(LayerRepository::class);
    $layer = Layer::createSimpleLayer(
        code: 'layer',
    );
    $layerRepository->shouldReceive('findById')
        ->with($layer->getId())
        ->once()
        ->andReturn($layer);
    $productRepository = Mockery::mock(ProductRepository::class);
    $productRepository->shouldReceive('findById')
        ->with('product')
        ->once()
        ->andReturnNull();
    $priceRepository = Mockery::mock(PriceRepository::class);
    $priceRepository->shouldNotReceive('existsByLayerIdAndProductId');

    $useCase = new CreatePrice(
        layerRepository: $layerRepository,
        priceRepository: $priceRepository,
        productRepository: $productRepository,
    );

    $input = new CreatePriceInput(
        layerId: $layer->getId(),
        productId: 'product',
        value: 0
    );

    $useCase->execute(
        input: $input
    );
})->throws(ProductNotFoundException::class);

test('Deve falhar criar um Price que já existe', function () {
    $layerRepository = Mockery::mock(LayerRepository::class);
    $layer = Layer::createSimpleLayer(
        code: 'layer',
    );
    $layerRepository->shouldReceive('findById')
        ->with($layer->getId())
        ->once()
        ->andReturn($layer);
    $productRepository = Mockery::mock(ProductRepository::class);
    $product = Product::create(
        name: 'Produto'
    );
    $productRepository->shouldReceive('findById')
        ->with($product->getId())
        ->once()
        ->andReturn($product);
    $priceRepository = Mockery::mock(PriceRepository::class);
    $priceRepository->shouldReceive('existsByLayerIdAndProductId')
        ->with($layer->getId(), $product->getId())
        ->once()
        ->andReturn(true);

    $useCase = new CreatePrice(
        layerRepository: $layerRepository,
        priceRepository: $priceRepository,
        productRepository: $productRepository,
    );

    $input = new CreatePriceInput(
        layerId: $layer->getId(),
        productId: $product->getId(),
        value: 0
    );

    $useCase->execute(
        input: $input
    );
})->throws(PriceAlreadExistsException::class);

test('Deve criar um preço de uma layer base', function () {
    $layerRepository = Mockery::mock(LayerRepository::class);
    $layer = Layer::createSimpleLayer(
        code: 'layer',
    );
    $layerRepository->shouldReceive('findById')
        ->with($layer->getId())
        ->once()
        ->andReturn($layer);
    $productRepository = Mockery::mock(ProductRepository::class);
    $product = Product::create(
        name: 'Produto'
    );
    $productRepository->shouldReceive('findById')
        ->with($product->getId())
        ->once()
        ->andReturn($product);
    $priceRepository = Mockery::mock(PriceRepository::class);
    $priceRepository->shouldReceive('existsByLayerIdAndProductId')
        ->with($layer->getId(), $product->getId())
        ->once()
        ->andReturn(false);
    $priceRepository->shouldReceive('save')
        ->once();

    $useCase = new CreatePrice(
        layerRepository: $layerRepository,
        priceRepository: $priceRepository,
        productRepository: $productRepository,
    );

    $input = new CreatePriceInput(
        layerId: $layer->getId(),
        productId: $product->getId(),
        value: 250
    );

    $output = $useCase->execute(
        input: $input
    );

    expect($output)->toBeInstanceOf(CreatePriceOutput::class);
    expect($output->priceId)->toBeString();
});



test('Deve criar um preço de uma layer com desconto', function () {
    $layerRepository = Mockery::mock(LayerRepository::class);
    $baseLayer = Layer::createSimpleLayer(
        code: 'BASE',
    );
    $discountLayer = Layer::create(
        code: 'DISCOUNT_20',
        type: LayerType::PERCENTAGE_DISCOUNT->value,
        value: 15,
        parentId: $baseLayer->getId(),
    );
    $layerRepository->shouldReceive('findById')
        ->with($discountLayer->getId())
        ->once()
        ->andReturn($discountLayer);
    $productRepository = Mockery::mock(ProductRepository::class);
    $product = Product::create(
        name: 'Produto'
    );
    $productRepository->shouldReceive('findById')
        ->with($product->getId())
        ->once()
        ->andReturn($product);
    $priceRepository = Mockery::mock(PriceRepository::class);
    $priceRepository->shouldReceive('existsByLayerIdAndProductId')
        ->with($discountLayer->getId(), $product->getId())
        ->once()
        ->andReturn(false);
    $priceRepository->shouldReceive('save')
        ->once();

    $useCase = new CreatePrice(
        layerRepository: $layerRepository,
        priceRepository: $priceRepository,
        productRepository: $productRepository,
    );

    $input = new CreatePriceInput(
        layerId: $discountLayer->getId(),
        productId: $product->getId(),
        value: 250
    );

    $output = $useCase->execute(
        input: $input
    );

    expect($output)->toBeInstanceOf(CreatePriceOutput::class);
    expect($output->priceId)->toBeString();
});
