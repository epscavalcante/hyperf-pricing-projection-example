<?php

declare(strict_types=1);

namespace Tests\Fakes\Repositories;

use Src\Domain\Entities\Layer;
use Src\Domain\Repositories\LayerRepository;

class LayerFakeRepository implements LayerRepository
{
    /**
     * @var Layer[]
     */
    private array $layers = [];

    public function findById(string $id): ?Layer
    {
        $layer = array_find(
            array: $this->layers,
            callback: fn(Layer $layer) => $layer->id === $id
        );

        return $layer;
    }

    public function save(Layer $layer): void
    {
        $this->layers[] = $layer;
    }
}
