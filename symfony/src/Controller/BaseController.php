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

        $result = [];

        foreach ($filters as $key => $value) {
            $enumCase = $filtersEnumClass::tryFrom($key);

            if (!$enumCase) {
                continue;
            }

            $result[] = [
                'field' => $enumCase->getEntityField(),
                'value' => $value,
                'fieldType' => $enumCase->getEntityFieldType(),
                'operator' => $enumCase->getOperator($value)
            ];
        }

        return $result;
    }

    protected function transformedSearch(string $searchEnumClass, string $search = ''): array
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