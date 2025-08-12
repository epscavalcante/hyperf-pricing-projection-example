<?php

declare(strict_types=1);

namespace Src\Infrastructure\Repositories;

use Hyperf\DbConnection\Db as DB;
use Src\Domain\Entities\Product;
use Src\Domain\Repositories\ProductRepository;

class ProductQueryBuilderRepository implements ProductRepository
{
    public function findById(string $id): ?Product
    {
        $product = DB::table('products')
            ->where('id', $id)
            ->first();
        if (is_null($product))
            return null;
        return Product::restore(
            id: $product->id,
            name: $product->name,
        );
    }

    public function save(Product $product): void
    {
        DB::table('products')
            ->insert([
                'id' => $product->getId(),
                'name' => $product->name,
            ]);
    }

    /** @return Product[] */
    public function list(): array
    {
        $products = DB::table('products')->get();

        return array_map(
            callback: fn($layerDb) => Product::restore(
                id: $layerDb->id,
                name: $layerDb->name,
            ),
            array: $products->all(),
        );
    }
}
