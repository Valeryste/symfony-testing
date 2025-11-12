<?php

namespace App\Twig;

use App\Service\CityService;
use App\Service\ShopService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EntityExtension extends AbstractExtension
{
    public function __construct(
        private readonly CityService $cityService,
        private readonly ShopService $shopService
    ) {

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_country_name', [$this->cityService, 'getCountryName']),
            new TwigFunction('get_city_name', [$this->shopService, 'getCityName'])
        ];
    }
}