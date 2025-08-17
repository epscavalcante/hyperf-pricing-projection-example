<?php

use Src\Application\UseCases\CreateLayer\CreateLayer;
use Src\Application\UseCases\CreateLayer\CreateLayerOutput;
use Src\Application\UseCases\CreateLayer\CreateLayerInput;
use Src\Domain\Entities\Layer;
use Src\Domain\Enums\LayerType;
use Src\Domain\Exceptions\LayerAlreadExistsException;
use Src\Domain\Exceptions\ParentLayerNotFoundException;
use Src\Domain\Repositories\LayerRepository;

test('Deve falhar ao criar uma que ja existe uma layer', function () {
    $layer = Layer::createSimpleLayer(
        code: 'EXISTS',
    );
    $input = new CreateLayerInput(
        code: $layer->code,
        type: LayerType::NORMAL->value,
        value: 0
    );

    $layerFakeRepository = Mockery::mock(LayerRepository::class);
    $layerFakeRepository->shouldReceive('findByCode')->once()->andReturn($layer);
    $createLayer = new CreateLayer(
        layerRepository: $layerFakeRepository,
    );

    $createLayer->execute(
        input: $input
    );
})->throws(LayerAlreadExistsException::class);

test('Deve falhar ao tentar criar uma parent layer onde a parent não existe', function () {
    $parentLayer = Layer::create(
        code: 'BASE',
        type: 'NORMAL',
    );
    
    $input = new CreateLayerInput(
        code: 'Layer Example',
        type: LayerType::NORMAL->value,
        parentId: $parentLayer->getId(),
        value: 0
    );

    $layerFakeRepository = Mockery::mock(LayerRepository::class);
    $layerFakeRepository->shouldReceive('findByCode')->once()->andReturnNull();
    // buscar a parent
    $layerFakeRepository->shouldReceive('findById')->once()->andReturnNull();
    $layerFakeRepository->shouldReceive('save')->never();
    $createLayer = new CreateLayer(
        layerRepository: $layerFakeRepository,
    );

    $createLayer->execute(
        input: $input
    );
})->throws(ParentLayerNotFoundException::class);

test('Deve falhar ao tentar criar uma layer base com um parent layer', function () {
    $parentLayer = Layer::create(
        code: 'BASE',
        type: 'NORMAL',
    );
    
    $input = new CreateLayerInput(
        code: 'Layer Example',
        type: LayerType::NORMAL->value,
        parentId: $parentLayer->getId(),
        value: 0
    );

    $layerFakeRepository = Mockery::mock(LayerRepository::class);
    $layerFakeRepository->shouldReceive('findByCode')->once()->andReturnNull();
    $layerFakeRepository->shouldReceive('findById')->once()->andReturn($parentLayer);
    $layerFakeRepository->shouldReceive('save')->never();
    $createLayer = new CreateLayer(
        layerRepository: $layerFakeRepository,
    );

    $createLayer->execute(
        input: $input
    );
})->throws(Exception::class, 'Layers do tipo NORMAL não possui parent');


test('Deve falhar ao tentar criar uma layer de desconto sem a base', function () {
    $input = new CreateLayerInput(
        code: 'Layer Example',
        type: LayerType::PERCENTAGE_DISCOUNT->value,
        value: 0
    );

    $layerFakeRepository = Mockery::mock(LayerRepository::class);
    $layerFakeRepository->shouldReceive('findByCode')->once()->andReturnNull();
    $layerFakeRepository->shouldReceive('findById')->never();
    $layerFakeRepository->shouldReceive('save')->never();
    $createLayer = new CreateLayer(
        layerRepository: $layerFakeRepository,
    );

    $createLayer->execute(
        input: $input
    );
})->throws(Exception::class, 'Layers do tipo !NORMAL devem ter um parent');

test('Deve criar uma layer base', function () {
    $input = new CreateLayerInput(
        code: 'Layer Example',
        type: LayerType::NORMAL->value,
        value: 0
    );

    $layerFakeRepository = Mockery::mock(LayerRepository::class);
    $layerFakeRepository->shouldReceive('findByCode')->once()->andReturnNull();
    $layerFakeRepository->shouldReceive('save')->once();
    $createLayer = new CreateLayer(
        layerRepository: $layerFakeRepository,
    );

    $output = $createLayer->execute(
        input: $input
    );

    expect($output)->toBeInstanceOf(CreateLayerOutput::class);
    expect($output->layerId)->toBeString();
});

test('Deve criar uma layer de desconto', function () {
    $baseLayer = Layer::create(
        code: 'BASE',
        type: 'NORMAL'
    );
    $input = new CreateLayerInput(
        code: 'Layer Example',
        type: LayerType::PERCENTAGE_DISCOUNT->value,
        parentId: $baseLayer->getId(),
        value: 15
    );
    $layerFakeRepository = Mockery::mock(LayerRepository::class);
    $layerFakeRepository->shouldReceive('findByCode')->once()->andReturnNull();
    $layerFakeRepository->shouldReceive('findById')->once()->andReturn($baseLayer);
    $layerFakeRepository->shouldReceive('save')->once();
    $createLayer = new CreateLayer(
        layerRepository: $layerFakeRepository,
    );

    $output = $createLayer->execute(
        input: $input
    );

    expect($output)->toBeInstanceOf(CreateLayerOutput::class);
    expect($output->layerId)->toBeString();
});
