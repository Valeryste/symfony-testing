<?php

namespace App\Twig;

use App\Service\CityService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CityExtension extends AbstractExtension
{
    public function __construct(
        private readonly CityService $cityService
    ) {

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_country_name', [$this->cityService, 'getCountryName']),
        ];
    }
}