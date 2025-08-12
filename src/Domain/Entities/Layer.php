<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

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
    ) {
        parent::__construct($id->getValue());
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

    public static function create(string $code, string $type, ?string $description = null, ?int $value = 0)
    {
        return new self(
            id: LayerId::create(),
            code: $code,
            type: LayerType::tryFrom($type),
            description: $description,
            value: $value
        );
    }

    public static function restore(string $id, string $code, string $type, ?string $description = null, ?int $value = 0)
    {
        return new self(
            id: LayerId::restore($id),
            code: $code,
            type: LayerType::tryFrom($type),
            description: $description,
            value: $value,
        );
    }

    public function getType(): string
    {
        return $this->type->value;
    }
}
