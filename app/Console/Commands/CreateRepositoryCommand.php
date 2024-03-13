<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        $parts = explode('\\', $name);
        $repositoryName = array_pop($parts);
        $repositoryNamespace = implode('\\', $parts);

        $repositoryBaseDirectory = app_path('Repositories');

        $repositoryDirectory = $repositoryBaseDirectory . '/' . str_replace('\\', '/', $repositoryNamespace);

        if (!is_dir($repositoryDirectory)) {
            if (!mkdir($repositoryDirectory, 0755, true)) {
                $this->error('Failed to create directory: ' . $repositoryNamespace);
                return;
            }
        }

        $repositoryPath = $repositoryDirectory . '/' . $repositoryName . '.php';

        if (file_exists($repositoryPath)) {
            $this->error('Repository already exists!');
            return;
        }

        $repositoryContent = "<?php\n\nnamespace App\Repository\\$repositoryNamespace;\n\nclass $repositoryName\n{\n    // Your repository logic here...\n}\n";
        if (file_put_contents($repositoryPath, $repositoryContent) === false) {
            $this->error('Failed to create repository file!');
            return;
        }

        $this->info('Repository created successfully: ' . $name);
    }

}
