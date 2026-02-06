<?php

namespace App\Console\Commands;

use App\Services\SymfonyApiClient;
use Illuminate\Console\Command;

class GetDataFromSymfony extends Command
{
    /**
     * @var string
     */
    protected $signature = 'symfony:get-data';

    /**
     * @var string
     */
    protected $description = 'Get products, categories from symfony service';

    /**
     * Выполнить консольную команду.
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->info('Получение продуктов и категорий из симфони сервиса');
        $this->line('==========================================');

        $client = new SymfonyApiClient();

        $this->info($this->authenticate($client));

        $this->info($this->getProductsAndCategories($client));

    }

    /**
     * @throws \Exception
     */
    private function authenticate(SymfonyApiClient $client): string
    {
        $this->info('🚀 Запуск аутентификации...');

        return $client->authenticate();
    }

    private function getProductsAndCategories(SymfonyApiClient $client): string
    {
        $this->info('🚀 Получение продуктов...');

        return $client->getProducts();
    }
}
