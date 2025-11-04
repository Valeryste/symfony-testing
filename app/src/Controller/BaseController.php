<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected function transformedFilters(array $filters, string $filtersEnumClass): array
    {
        if (!enum_exists($filtersEnumClass)) {
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

    protected function transformedSearch(string $searchEnumClass, string $search = '', ): array
    {
        if (!enum_exists($searchEnumClass)) {
            throw new \InvalidArgumentException('Invalid BackedEnum class provided');
        }

        if(empty($search)) {
            return [];
        }

        return [
            'fields' => $searchEnumClass::getSearchCases(),
            'value' => $search
        ];

    }

}