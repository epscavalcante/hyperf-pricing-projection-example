<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

abstract class Entity
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
}
