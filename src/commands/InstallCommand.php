<?php


namespace Leeto\CashBox\commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;


class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cashbox:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->confirm('Create purchase controller?')) {
            $class = $this->ask('Type controller name');

            $stub = Str::of(file_get_contents(__DIR__ . '/../stubs/controller.stub'))
                ->replace('{{ class }}', $class);

            $this->laravel['files']->put(
                app_path("/Http/Controllers/$class.php"),
                $stub
            );

            $this->info('Add to routes');
            $this->info("Route::post('/', [App\Http\Controllers\\$class::class, 'index'])");
            $this->info("Route::get('/success', [App\Http\Controllers\\$class}::class, 'success'])");
            $this->info("Route::post('/check', [App\Http\Controllers\\$class}::class, 'check'])");
        }

        return self::SUCCESS;
    }
}