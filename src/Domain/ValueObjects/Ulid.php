<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

use Ramsey\Identifier\Ulid\UlidFactory;
use Stringable;

class Ulid implements Stringable
{
    private function __construct(private readonly string $value) {}

    public static function create(): static
    {
        $uuid = (new UlidFactory())->create();

        return new static((string) $uuid);
    }

    public static function restore(string $value): static
    {
        $uuid = (new UlidFactory)->createFromString($value);

        return new static((string) $uuid);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
