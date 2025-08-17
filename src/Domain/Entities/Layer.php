<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use Exception;
use Src\Domain\Enums\LayerType;
use Src\Domain\ValueObjects\LayerId;

class Layer extends Entity
{
    private function __construct(
        private LayerId $id,
        public string $code,
        private LayerType $type,
        public int $value = 0,
        public ?string $description = null,
        private ?LayerId $parentId = null,
    ) {
        parent::__construct($id->getValue());
        $this->validate();
    }

    public static function createSimpleLayer(string $code, ?string $description = null)
    {
        return self::create(
            code: $code,
            description: $description,
            type: LayerType::NORMAL->value,
            value: 0,
        );
    }

    public static function createNormalLayer(string $code, ?string $description = null)
    {
        return self::create(
            code: $code,
            description: $description,
            type: LayerType::NORMAL->value,
        );
    }

    public static function createPercentualDiscount(string $code, int $percentualDiscount, string $parentId, ?string $description = null)
    {
        //poderia ser um VO, o discount não pode ser decimal
        if ($percentualDiscount <= 0) {
            throw new Exception('O percentual deve maior ou igual a 0');
        }

        return self::create(
            code: $code,
            value: $percentualDiscount,
            parentId: $parentId,
            type: LayerType::PERCENTAGE_DISCOUNT->value,
            description: $description,
        );
    }

    public static function create(string $code, string $type, ?string $description = null, ?int $value = 0, ?string $parentId = null)
    {
        return new self(
            id: LayerId::create(),
            code: $code,
            type: LayerType::tryFrom($type),
            description: $description,
            value: $value,
            parentId: $parentId ? LayerId::restore($parentId) : null
        );
    }

    public static function restore(string $id, string $code, string $type, ?string $description = null, ?int $value = 0, ?string $parentId = null)
    {
        return new self(
            id: LayerId::restore($id),
            code: $code,
            type: LayerType::tryFrom($type),
            description: $description,
            value: $value,
            parentId: $parentId ? LayerId::restore($parentId) : null
        );
    }

    public function getType(): string
    {
        return $this->type->value;
    }

    public function getParentId(): ?string
    {
        return $this->parentId?->getValue();
    }

    public function hasParent(): bool
    {
        return $this->getParentId() !== null;
    }

    public function isBase(): bool
    {
        return is_null($this->parentId);
    }

    public function isPercentualDiscountType(): bool
    {
        return $this->type === LayerType::PERCENTAGE_DISCOUNT;
    }

    private function validate()
    {
        $isNormal = $this->type->isNormal($this->type->value);
        $hasParent = $this->hasParent();

        if ($isNormal && $hasParent) {
            throw new Exception('Layers do tipo NORMAL não possui parent');
        }

        if (!$isNormal && !$hasParent) {
            throw new Exception('Layers do tipo !NORMAL devem ter um parent');
        }
    }
}
