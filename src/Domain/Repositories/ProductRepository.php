<?php

declare(strict_types=1);

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Product;

interface ProductRepository
{
    public function findById(string $id): ?Product;

    public function save(Product $product): void;

    /** @return Product[] */
    public function list(): array;
}
