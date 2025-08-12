<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ListLayers;

use Src\Domain\Repositories\LayerRepository;

class ListLayers
{
    public function __construct(
        private readonly LayerRepository $layerRepository
    ) {}

    public function execute(): ListLayersOutput
    {
        $layers = $this->layerRepository->list();

        return new ListLayersOutput(
            total: count($layers),
            items: $layers
        );
    }
}
