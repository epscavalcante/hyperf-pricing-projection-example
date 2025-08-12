<?php

declare(strict_types=1);

namespace Src\Application\UseCases\CreateProduct;

readonly class CreateProductInput
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
