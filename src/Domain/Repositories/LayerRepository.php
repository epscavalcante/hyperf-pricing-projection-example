<?php

declare(strict_types=1);

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Layer;

interface LayerRepository
{
    public function findById(string $id): ?Layer;

    public function findByCode(string $code): ?Layer;

    public function save(Layer $layer): void;

    /**
     * @return Layer[]
     */
    public function list(): array;
}
