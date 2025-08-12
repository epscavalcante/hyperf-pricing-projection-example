<?php

declare(strict_types=1);

namespace Src\Application\UseCases\CreateLayer;

readonly class CreateLayerOutput
{
    public function __construct(
        public string $layerId,
    ) {}
}
