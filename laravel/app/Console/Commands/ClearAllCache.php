<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCache extends Command
{
    /**
     * @var string
     */
    protected $signature = 'cache:clear-all';

    /**
     * @var string
     */
    protected $description = 'Clear cache: view, route, config';

    /**
     * Выполнить консольную команду.
     */
    public function handle(): void
    {
        $this->call('view:clear');
        $this->info('cache view clearing');

        $this->call('route:clear');
        $this->info('cache route clearing');

        $this->call('config:clear');
        $this->info('cache config clearing');
    }
}
