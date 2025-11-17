<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure useful MongoDB indexes for search and lookups
        if (Config::get('database.default') === 'mongodb') {
            try {
                $client = app('mongo');
                $dbName = Config::get('database.connections.mongodb.database');
                if ($client && $dbName) {
                    $db = $client->selectDatabase($dbName);
                    $posts = $db->selectCollection('posts');
                    // Text index for title/body (for $text queries) and single-field indexes for potential anchored regex
                    $posts->createIndex(['title' => 'text', 'body' => 'text'], ['name' => 'text_title_body']);
                    $posts->createIndex(['title' => 1], ['name' => 'idx_title']);
                    $posts->createIndex(['body' => 1], ['name' => 'idx_body']);
                }
            } catch (\Throwable $e) {
                // noop: index creation is best-effort
            }
        }
    }
}
