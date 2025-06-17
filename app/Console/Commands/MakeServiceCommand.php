<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->info("Creating service: {$name}");

        // Create the service directory if it doesn't exist
        $serviceDir = app_path('Services');
        if (!$this->files->exists($serviceDir)) {
            $this->files->makeDirectory($serviceDir, 0755, true);
        }

        // Create the service file
        $serviceFile = $serviceDir . "/{$name}.php";
        if ($this->files->exists($serviceFile)) {
            $this->error("Service {$name} already exists!");
            return 1;
        }

        $this->files->put($serviceFile, $this->generateServiceStub($name));
        $this->info("Service {$name} created successfully.");
        return 0;
    }

    protected function generateServiceStub($name)
    {
        return <<<EOT
<?php

namespace App\Services;

class {$name}
{
    public function handle()
    {
        // Handle the service logic
    }
}

EOT;
    }
}
