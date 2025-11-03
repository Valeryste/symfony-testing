<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected function transformedFilters(array $filters, string $filtersEnumClass): array
    {
        if (!class_exists($filtersEnumClass) || !is_subclass_of($filtersEnumClass, \BackedEnum::class)) {
            throw new \InvalidArgumentException('Invalid BackedEnum class provided');
        }

        return array_filter(
            array_map(
                function ($key, $filter) use ($filtersEnumClass) {
                    $enumCase = $filtersEnumClass::tryFrom($key);

                    if (!$enumCase) {
                        return null;
                    }

                    return [
                        'field' => $key,
                        'value' => $filter,
                        'fieldType' => $enumCase->getFieldType()
                    ];
                },
                array_keys($filters),
                $filters
            )
        );
    }

}