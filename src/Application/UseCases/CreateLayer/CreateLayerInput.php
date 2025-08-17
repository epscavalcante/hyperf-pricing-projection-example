<?php

declare(strict_types=1);

namespace Src\Application\UseCases\CreateLayer;

readonly class CreateLayerInput
{
    public function __construct(
        public string $code,
        public string $type,
        public ?string $parentId = null,
        public ?string $description = null,
        public ?int $value = 0,
    ) {}
}
