<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of CreateApplicationCommand
 *
 * @author lucianoc
 */
class CreateApplicationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:application-create';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates a new 'mi-univesidad' service application";
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->input->getOption('name');
        $description = $this->input->getOption('description');
        $auth_required = $this->input->getOption('auth_required');
        $auth_callback_url = $this->input->getOption('auth_callback_url');
        if (!$name) {
            $this->error("Argument 'name' required.");
        } else {
            $description = ($description?$description:$name);
            $this->info("Creating service app '{$name}'...");
            $application = \App\Application::create([ 'name' => $name, 'description' => $description, 'auth_required'=> $auth_required, 'auth_callback_url' => $auth_callback_url ]);
            $this->info('Service app created!');
            $this->table([], [['Name', $application->name], ['API key', $application->api_key], ['API secret', $application->api_secret]]);
        }
        
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['name', null, InputOption::VALUE_REQUIRED, 'Service application name'],
            ['description', null, InputOption::VALUE_OPTIONAL, 'Service application description', null],
            ['auth_required', null, InputOption::VALUE_OPTIONAL, 'It tells if the service needs user authentication', false],
            ['auth_callback_url', null, InputOption::VALUE_OPTIONAL, 'The authentication callback url', false],
        ];
    }

}
