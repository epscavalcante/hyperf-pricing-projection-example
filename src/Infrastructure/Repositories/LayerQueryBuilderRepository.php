<?php

declare(strict_types=1);

namespace Src\Infrastructure\Repositories;

use Src\Domain\Entities\Layer;
use Src\Domain\Repositories\LayerRepository;
use Hyperf\DbConnection\Db as DB;

class LayerQueryBuilderRepository implements LayerRepository
{
    public function findById(string $id): ?Layer
    {
        $layer = DB::table('layers')
            ->where('id', $id)
            ->first();
        if (is_null($layer))
            return null;
        return Layer::restore(
            id: $layer->id,
            code: $layer->code,
            type: $layer->type,
            description: $layer->description,
            // value: $layer->value
        );
    }

    public function findByCode(string $code): ?Layer
    {
        $layer = DB::table('layers')
            ->where('code', $code)
            ->first();
        if (is_null($layer))
            return null;
        return Layer::restore(
            id: $layer->id,
            code: $layer->code,
            type: $layer->type,
            description: $layer->description,
            // value: $layer->value
        );
    }

    public function save(Layer $layer): void
    {
        DB::table('layers')
            ->insert([
                'id' => $layer->getId(),
                'code' => $layer->code,
                'type' => $layer->getType(),
                'description' => $layer->description,
                //'value' => $layer->value,
            ]);
    }

    /**
     * @return Layer[]
     */
    public function list(): array
    {
        $layers = DB::table('layers')->get();

        return array_map(
            callback: fn($layerDb) => Layer::restore(
                id: $layerDb->id,
                code: $layerDb->code,
                description: $layerDb->description,
                type: $layerDb->type,
                // value: $layerDb->vaue
            ),
            array: $layers->all(),
        );
    }
}
