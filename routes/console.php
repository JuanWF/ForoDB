<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:mongo', function () {
    try {
        if (class_exists(\MongoDB\Client::class)) {
            $uri = env('MONGODB_URI');
            $client = new \MongoDB\Client($uri);
            $this->info('Conexión a MongoDB OK (MongoDB\\Client). Bases de datos:');
            foreach ($client->listDatabases() as $d) {
                $this->line('- ' . $d->getName());
            }
            return;
        }

        $conn = DB::connection('mongodb');
        if (method_exists($conn, 'getMongoClient')) {
            $client = $conn->getMongoClient();
            $this->info('Conexión a MongoDB OK (vía DB). Bases de datos:');
            foreach ($client->listDatabases() as $d) {
                $this->line('- ' . $d->getName());
            }
            return;
        }

        $this->error('No se pudo instanciar un cliente MongoDB. Revisa que la extensión y el paquete estén instalados.');
    } catch (\Throwable $e) {
        $this->error('Error conectando a MongoDB: ' . $e->getMessage());
    }
})->purpose('Probar conexión a MongoDB');

Artisan::command('mongo:ensure-indexes', function () {
    try {
        $dbName = env('MONGODB_DATABASE', 'laravel_app');
        $uri = env('MONGODB_URI', 'mongodb://127.0.0.1:27017');
        $client = new \MongoDB\Client($uri);
        $users = $client->selectDatabase($dbName)->selectCollection('users');
        $users->createIndex(['email' => 1], ['unique' => true]);
        $reactions = $client->selectDatabase($dbName)->selectCollection('reactions');
        $reactions->createIndex(['user_id' => 1, 'reactable_type' => 1, 'reactable_id' => 1], ['unique' => true]);
        $this->info('Índice único creado/asegurado: users.email');
        $this->info('Índice único creado/asegurado: reactions (user_id, reactable_type, reactable_id)');
    } catch (\Throwable $e) {
        $this->error('No se pudo crear el índice: '.$e->getMessage());
    }
})->purpose('Asegurar índices únicos en Mongo (users.email)');
