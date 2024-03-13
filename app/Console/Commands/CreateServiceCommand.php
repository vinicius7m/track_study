<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Separar o nome do serviço e o caminho do namespace
        $parts = explode('\\', $name);
        $serviceName = array_pop($parts);
        $serviceNamespace = implode('\\', $parts);

        // Diretório base para os serviços
        $serviceBaseDirectory = app_path('Services');

        // Diretório específico para o serviço
        $serviceDirectory = $serviceBaseDirectory . '/' . str_replace('\\', '/', $serviceNamespace);

        // Verifica se o diretório específico existe e, se não, tenta criá-lo
        if (!is_dir($serviceDirectory)) {
            if (!mkdir($serviceDirectory, 0755, true)) {
                $this->error('Failed to create directory: ' . $serviceNamespace);
                return;
            }
        }

        // Caminho completo para o serviço
        $servicePath = $serviceDirectory . '/' . $serviceName . '.php';

        // Verifica se o serviço já existe
        if (file_exists($servicePath)) {
            $this->error('Service already exists!');
            return;
        }

        // Cria o arquivo do serviço
        $serviceContent = "<?php\n\nnamespace App\Services\\$serviceNamespace;\n\nclass $serviceName\n{\n    // Your service logic here...\n}\n";
        if (file_put_contents($servicePath, $serviceContent) === false) {
            $this->error('Failed to create service file!');
            return;
        }

        $this->info('Service created successfully: ' . $name);
    }

}
