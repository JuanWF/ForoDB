<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestMongoConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mongo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar conexión a MongoDB y listar bases de datos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if (class_exists(\MongoDB\Client::class)) {
                $uri = env('MONGODB_URI');
                $client = new \MongoDB\Client($uri);
                $this->info('Conexión a MongoDB OK (MongoDB\\Client). Bases de datos:');
                foreach ($client->listDatabases() as $d) {
                    $this->line('- ' . $d->getName());
                }
                return self::SUCCESS;
            }

            $conn = DB::connection('mongodb');
            if (method_exists($conn, 'getMongoClient')) {
                $client = $conn->getMongoClient();
                $this->info('Conexión a MongoDB OK (vía DB). Bases de datos:');
                foreach ($client->listDatabases() as $d) {
                    $this->line('- ' . $d->getName());
                }
                return self::SUCCESS;
            }

            $this->error('No se pudo instanciar un cliente MongoDB. Revisa que la extensión y el paquete estén instalados.');
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Error conectando a MongoDB: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
