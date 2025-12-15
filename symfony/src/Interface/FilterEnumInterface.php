<?php

namespace App\Interface;

interface FilterEnumInterface
{
    public static function getAvailableFilters(): array;

    public function getInputType(): string;

    public function getDisplayName(): string;

    public function getEntityFieldType(): string;

    public function getEntityField(): string;

    public function getOperator(mixed $value = null): string;

    public function getTemplateVariableName(): ?string;
}