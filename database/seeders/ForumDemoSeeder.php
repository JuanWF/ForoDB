<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ForumDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario admin para el foro
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => 'admin1234',
            ]
        );

        $juan = User::query()->firstOrCreate(
            ['email' => 'juan_db@hotmail.com'],
            [
                'name' => 'juan_db',
                'password' => 'juan1234',
            ]
        );

        $now = Carbon::now();

        // Posts con estructura MongoDB (documentos embebidos + referencias)
        // Los posts ahora tienen user_id (referencia) además de author (embebido)
        // Esto permite hacer búsquedas por referencia entre colecciones
        $postsData = [
            [
                'title' => 'Problemas al usar $lookup en MongoDB?',
                'body' => 'Hola a todos, estoy teniendo problemas al usar el operador $lookup en MongoDB para unir colecciones. Me gustaría saber si alguien ha tenido experiencias similares y qué soluciones han encontrado. Específicamente, estoy notando lentitud cuando trabajo con colecciones grandes.',
                'user_id' => (string) $juan->_id, // Referencia a la colección users
                'author' => [
                    '_id' => (string) $juan->_id,
                    'name' => $juan->name,
                    'email' => $juan->email,
                ],
                'tags' => ['MongoDB', 'Aggregation', '$lookup', 'Performance'],
                'comments' => [
                    [
                        '_id' => bin2hex(random_bytes(12)),
                        'user_id' => (string) $admin->_id,
                        'user_name' => $admin->name,
                        'body' => 'Hola @usuario, el operador $lookup realiza una unión entre dos colecciones que pertenecen a la misma base de datos. Al utilizar $lookup, es necesario especificar el nombre de la colección que se va a unir. Asegúrate de que el nombre de la colección esté escrito correctamente.',
                        'reactions' => [
                            [
                                'user_id' => (string) $juan->_id,
                                'user_name' => $juan->name,
                                'type' => 'like',
                                'created_at' => $now->copy()->subHours(3)->toISOString(),
                            ],
                        ],
                        'created_at' => $now->copy()->subHours(4)->toISOString(),
                    ],
                ],
                'reactions' => [
                    [
                        'user_id' => (string) $admin->_id,
                        'user_name' => $admin->name,
                        'type' => 'like',
                        'created_at' => $now->copy()->subHours(4)->toISOString(),
                    ],
                    [
                        'user_id' => (string) $admin->_id,
                        'user_name' => $admin->name,
                        'type' => 'insightful',
                        'created_at' => $now->copy()->subHours(4)->toISOString(),
                    ],
                ],
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],
            [
                'title' => '¿Cómo optimizar consultas en MongoDB?',
                'body' => 'Estoy trabajando en un proyecto con MongoDB y he notado que algunas consultas son muy lentas. ¿Alguien puede compartir consejos sobre cómo optimizar consultas? He oído hablar de índices, pero no estoy seguro de cómo implementarlos correctamente.',
                'user_id' => (string) $admin->_id, // Referencia a la colección users
                'author' => [
                    '_id' => (string) $admin->_id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                ],
                'tags' => ['MongoDB', 'Performance', 'Indexes', 'Optimization'],
                'comments' => [
                    [
                        '_id' => bin2hex(random_bytes(12)),
                        'user_id' => (string) $juan->_id,
                        'user_name' => $juan->name,
                        'body' => 'Los índices son fundamentales. Usa explain() para ver el plan de ejecución de tus consultas y detectar cuellos de botella.',
                        'reactions' => [],
                        'created_at' => $now->copy()->subHours(2)->toISOString(),
                    ],
                    [
                        '_id' => bin2hex(random_bytes(12)),
                        'user_id' => (string) $admin->_id,
                        'user_name' => $admin->name,
                        'body' => 'También considera usar proyecciones para reducir la cantidad de datos que retornas en cada consulta.',
                        'reactions' => [
                            [
                                'user_id' => (string) $juan->_id,
                                'user_name' => $juan->name,
                                'type' => 'insightful',
                                'created_at' => $now->copy()->subHour()->toISOString(),
                            ],
                        ],
                        'created_at' => $now->copy()->subHour()->toISOString(),
                    ],
                ],
                'reactions' => [
                    [
                        'user_id' => (string) $juan->_id,
                        'user_name' => $juan->name,
                        'type' => 'like',
                        'created_at' => $now->copy()->subHours(3)->toISOString(),
                    ],
                ],
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(6),
            ],
            [
                'title' => 'Comparación: SQL vs NoSQL',
                'body' => 'He trabajado principalmente con bases de datos SQL como MySQL y PostgreSQL. Recientemente empecé a explorar MongoDB y otras bases NoSQL. ¿Alguien puede compartir sus experiencias sobre cuándo usar una u otra?',
                'user_id' => (string) $juan->_id, // Referencia a la colección users
                'author' => [
                    '_id' => (string) $juan->_id,
                    'name' => $juan->name,
                    'email' => $juan->email,
                ],
                'tags' => ['SQL', 'NoSQL', 'MySQL', 'MongoDB', 'Databases'],
                'comments' => [],
                'reactions' => [
                    [
                        'user_id' => (string) $admin->_id,
                        'user_name' => $admin->name,
                        'type' => 'love',
                        'created_at' => $now->copy()->subDay()->toISOString(),
                    ],
                ],
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
            [
                'title' => 'Mejores prácticas para modelado de datos en MongoDB',
                'body' => 'Vengo del mundo relacional y estoy adaptándome al modelado de datos en MongoDB. ¿Cuáles son las mejores prácticas para diseñar esquemas? ¿Cuándo embebed vs referencias?',
                'user_id' => (string) $admin->_id, // Referencia a la colección users
                'author' => [
                    '_id' => (string) $admin->_id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                ],
                'tags' => ['MongoDB', 'Data Modeling', 'Schema Design', 'Best Practices'],
                'comments' => [
                    [
                        '_id' => bin2hex(random_bytes(12)),
                        'user_id' => (string) $juan->_id,
                        'user_name' => $juan->name,
                        'body' => 'Regla general: embebe cuando necesites leer los datos juntos, usa referencias cuando los datos crezcan mucho o se actualicen independientemente.',
                        'reactions' => [
                            [
                                'user_id' => (string) $admin->_id,
                                'user_name' => $admin->name,
                                'type' => 'insightful',
                                'created_at' => $now->copy()->subHours(8)->toISOString(),
                            ],
                            [
                                'user_id' => (string) $admin->_id,
                                'user_name' => $admin->name,
                                'type' => 'like',
                                'created_at' => $now->copy()->subHours(8)->toISOString(),
                            ],
                        ],
                        'created_at' => $now->copy()->subHours(9)->toISOString(),
                    ],
                ],
                'reactions' => [],
                'created_at' => $now->copy()->subHours(10),
                'updated_at' => $now->copy()->subHours(10),
            ],
        ];

        // Crear posts con la nueva estructura
        foreach ($postsData as $postData) {
            $existing = Post::where('title', $postData['title'])->first();
            if (!$existing) {
                Post::create($postData);
            }
        }

        $this->command->info('✅ Foro demo seeded con estructura MongoDB correcta!');
    }
}

