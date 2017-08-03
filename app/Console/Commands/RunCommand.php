<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of CreateApplicationCommand
 *
 * @author lucianoc
 */
class RunCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:run';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Runs a PHP local webservice development instance. Default: http://localhost:8800 ";
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        chdir(base_path('public'));
        $host = $this->input->getOption('host');
        $port = $this->input->getOption('port');
        $base = $this->laravel->basePath();
        $this->info("Lumen development server started on http://{$host}:{$port}/");
        passthru('"' . PHP_BINARY . '"' . " -S {$host}:{$port} -t \"{$base}\" \"{$base}\"/index.php");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', 'localhost'],
            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8800],
        ];
    }

}
