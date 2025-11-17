<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForumDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure admin user
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin_mongo@example.com'],
            [
                'name' => 'admin_mongo',
                'password' => 'secret1234',
            ]
        );

        $authorId = (string) $admin->_id;
        $authorName = $admin->name;

        $now = Carbon::now();

        $posts = [
            [
                'title' => 'Problemas al usar $lookup en MongoDB',
                'body' => '¿Alguien ha tenido problemas con $lookup en colecciones grandes? Compartan tips y mejores prácticas.',
                'author_id' => $authorId,
                'author_name' => $authorName,
                'score' => 42,
                'comments_count' => 8,
                'tags' => ['mongodb', 'aggregation', 'lookup'],
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'title' => '¿Dónde alojamos los datos en MongoDB?',
                'body' => 'Atlas vs self-hosted: pros y contras reales en producción.',
                'author_id' => $authorId,
                'author_name' => $authorName,
                'score' => 21,
                'comments_count' => 5,
                'tags' => ['mongodb', 'deploy', 'atlas'],
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],
            [
                'title' => 'Optimización de consultas con MongoDB',
                'body' => 'Índices compuestos, proyecciones y stage $match temprano. Consejos prácticos.',
                'author_id' => $authorId,
                'author_name' => $authorName,
                'score' => 37,
                'comments_count' => 4,
                'tags' => ['performance', 'indexes'],
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
        ];

        $postsCol = DB::connection('mongodb')->selectCollection('posts');
        $commentsCol = DB::connection('mongodb')->selectCollection('comments');

        foreach ($posts as $p) {
            // Upsert by title to avoid duplicates on re-seed
            $existing = $postsCol->findOne(['title' => $p['title']]);
            if (!$existing) {
                $result = $postsCol->insertOne($p);
                $postId = (string) $result->getInsertedId();

                // Seed a couple of comments per post
                $comments = [
                    [
                        'post_id' => (string) $postId,
                        'user_id' => $authorId,
                        'user_name' => $authorName,
                        'body' => 'Buen tema, ¿probaste con índices adecuados en campos de unión?',
                        'created_at' => $now,
                    ],
                    [
                        'post_id' => (string) $postId,
                        'user_id' => $authorId,
                        'user_name' => $authorName,
                        'body' => 'También puedes limitar campos con $project para mejorar rendimiento.',
                        'created_at' => $now,
                    ],
                ];
                $commentsCol->insertMany($comments);
            }
        }
    }
}
