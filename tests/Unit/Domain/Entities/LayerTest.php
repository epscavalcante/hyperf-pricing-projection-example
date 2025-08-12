<?php

use Src\Domain\Entities\Layer;
use Src\Domain\Enums\LayerType;

test('Deve criar uma layer normal', function () {
    $layer = Layer::create(
        code: 'LAYER',
        type: (string) LayerType::NORMAL->value,
        description: 'Descrição de exemplo',
    );

    expect($layer)->toBeInstanceOf(Layer::class);
    expect($layer->code)->toBe('LAYER');
    expect($layer->description)->toBe('Descrição de exemplo');
});