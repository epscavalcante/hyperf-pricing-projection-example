<?php

use Src\Application\UseCases\CreateLayer\CreateLayer;
use Src\Application\UseCases\CreateLayer\CreateLayerOutput;
use Src\Application\UseCases\CreateLayer\CreateLayerInput;
use Src\Domain\Entities\Layer;
use Src\Domain\Enums\LayerType;
use Src\Domain\Exceptions\LayerAlreadExistsException;
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

test('Deve criar uma layer', function () {
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
