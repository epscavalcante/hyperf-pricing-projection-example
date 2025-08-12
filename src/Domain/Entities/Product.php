<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use Src\Domain\ValueObjects\ProductId;

class Product extends Entity
{
    private function __construct(
        private ProductId $id,
        public string $name,
    ) {
        parent::__construct($id->getValue());
    }

    public static function create($name): self
    {
        return new self(
            id: ProductId::create(),
            name: $name
        );
    }
    
    public static function restore(string $id, string $name): self
    {
        return new self(
            id: ProductId::restore($id),
            name: $name
        );
    }
}
