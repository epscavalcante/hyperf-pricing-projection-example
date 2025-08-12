<?php

declare(strict_types=1);

namespace Src\Application\UseCases\CreateLayer;

use Src\Domain\Entities\Layer;
use Src\Domain\Enums\LayerType;
use Src\Domain\Exceptions\LayerAlreadExistsException;
use Src\Domain\Repositories\LayerRepository;

class CreateLayer
{
    public function __construct(
        private readonly LayerRepository $layerRepository,
    ) {}

    public function execute(CreateLayerInput $input): CreateLayerOutput
    {
        $layerFound = $this->layerRepository->findByCode($input->code);
        if ($layerFound) {
            throw new LayerAlreadExistsException;
        }
        $layer = Layer::create(
            code: $input->code,
            type: $input->type,
            description: $input->description,
            value: $input->value,
        );

        $this->layerRepository->save($layer);

        // disparar um evento

        return new CreateLayerOutput(
            layerId: $layer->getId(),
        );
    }
}
