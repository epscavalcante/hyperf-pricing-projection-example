<?php

declare(strict_types=1);

namespace Src\Application\UseCases\CreateDiscountLayer;

use Exception;
use Src\Domain\Entities\Layer;
use Src\Domain\Exceptions\LayerAlreadExistsException;
use Src\Domain\Exceptions\LayerNotFoundException;
use Src\Domain\Exceptions\ParentLayerNotFoundException;
use Src\Domain\Exceptions\ProductNotFoundException;
use Src\Domain\Repositories\LayerRepository;
use Src\Domain\Repositories\ProductRepository;

class CreateDiscountLayer
{
    public function __construct(
        private readonly LayerRepository $layerRepository,
        private readonly ProductRepository $productRepository,
    ) {}

    public function execute(CreateDiscountLayerInput $input): CreateDiscountLayerOutput
    {
        $layerFound = $this->layerRepository->findByCode($input->code);
        if ($layerFound) {
            throw new LayerAlreadExistsException;
        }

        $parentLayer = $this->layerRepository->findById($input->parentId);
        if (is_null($parentLayer)) {
            throw new ParentLayerNotFoundException();
        }

        $products = [];

        if (count($input->productIds) > 0) {
            $products = $this->productRepository->findByIds($input->productIds);

            if (count($products) != count($input->productIds)) {
                throw new ProductNotFoundException();
            }
        }

        $layer = Layer::create(
            code: $input->code,
            type: $input->type,
            description: $input->description,
            value: $input->value,
            parentId: $parentLayer->getId(),
        );

        $this->layerRepository->save($layer);

        // disparar um evento

        return new CreateDiscountLayerOutput(
            layerId: $layer->getId(),
        );
    }
}
