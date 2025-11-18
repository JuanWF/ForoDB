# Cumplimiento de Requisitos del Proyecto ForoDB

## 📋 Requisitos del Proyecto

### ✅ 1. Implementar una solución con MongoDB
**Estado: CUMPLIDO**

El proyecto utiliza MongoDB como base de datos principal:
- **Driver**: `mongodb/laravel-mongodb` (Laravel MongoDB)
- **Configuración**: `config/database.php` con conexión MongoDB
- **Modelos**: Usan `MongoDB\Laravel\Eloquent\Model`

**Archivos relevantes:**
- `app/Models/Post.php` - Modelo MongoDB para posts
- `app/Models/User.php` - Modelo MongoDB para usuarios
- `config/database.php` - Configuración de conexión MongoDB

---

### ✅ 2. Aplicación orientada a la Web
**Estado: CUMPLIDO**

ForoDB es una aplicación web completa desarrollada con Laravel + Livewire:
- **Framework**: Laravel 11 (PHP Web Framework)
- **Frontend**: Livewire para interactividad
- **Vistas**: Blade templates
- **Estilos**: Tailwind CSS
- **Funcionalidades web**:
  - Sistema de autenticación (login/registro)
  - CRUD de posts
  - Sistema de comentarios en tiempo real
  - Sistema de reacciones (likes)
  - Búsqueda de posts
  - Trending tags

**URLs principales:**
- `/` - Página principal
- `/posts` - Lista de posts del foro
- `/posts/{id}` - Detalle de un post
- `/posts/create` - Crear nuevo post
- `/settings/profile` - Ajustes de usuario

---

### ✅ 3. Más de 1 colección en MongoDB
**Estado: CUMPLIDO**

El proyecto utiliza **2 colecciones principales**:

#### **Colección 1: `users`**
Almacena información de usuarios del foro:
```javascript
{
  _id: ObjectId("..."),
  name: "admin",
  email: "admin@admin.com",
  password: "hashed_password",
  email_verified_at: ISODate("..."),
  created_at: ISODate("..."),
  updated_at: ISODate("...")
}
```

#### **Colección 2: `posts`**
Almacena los posts del foro con documentos embebidos (comentarios y reacciones):
```javascript
{
  _id: ObjectId("..."),
  title: "¿Cómo optimizar consultas en MongoDB?",
  body: "Estoy trabajando en un proyecto...",
  user_id: "6789abc...", // ← REFERENCIA a users
  author: {
    _id: "6789abc...",
    name: "admin",
    email: "admin@admin.com"
  },
  tags: ["MongoDB", "Performance", "Indexes"],
  comments: [
    {
      _id: "comment_id",
      user_id: "user_id",
      user_name: "juan_db",
      body: "Los índices son fundamentales...",
      reactions: [...],
      created_at: "2025-11-17T..."
    }
  ],
  reactions: [
    {
      user_id: "user_id",
      user_name: "admin",
      type: "like",
      created_at: "2025-11-17T..."
    }
  ],
  created_at: ISODate("..."),
  updated_at: ISODate("...")
}
```

**Archivos relevantes:**
- `app/Models/User.php` (línea 20): `protected $collection = 'users';`
- `app/Models/Post.php` (línea 11): `protected $collection = 'posts';`
- `database/seeders/ForumDemoSeeder.php` - Crea datos en ambas colecciones

---

### ✅ 4. Búsqueda por referencia
**Estado: CUMPLIDO**

El proyecto implementa **búsquedas por referencia** entre las colecciones `posts` y `users`.

#### **Implementación:**

**1. Campo de referencia en Post:**
```php
// app/Models/Post.php
protected $fillable = [
    'user_id',  // ← Referencia al _id de users
    'author',   // Datos embebidos (para comparación)
    // ...
];
```

**2. Método de búsqueda por referencia:**
```php
// app/Models/Post.php (líneas 69-90)
/**
 * Obtener el autor del post mediante búsqueda por referencia
 * Este método realiza una búsqueda en la colección 'users' usando el user_id
 * Esto demuestra una búsqueda por referencia entre colecciones
 */
public function getAuthorFromReference()
{
    if (!isset($this->user_id)) {
        return $this->author; // Fallback a datos embebidos
    }
    
    // BÚSQUEDA POR REFERENCIA: buscar en la colección users
    $user = User::where('_id', $this->user_id)->first();
    
    if ($user) {
        return [
            '_id' => (string) $user->_id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
    
    return $this->author; // Fallback
}
```

**3. Uso en el componente Livewire:**
```php
// app/Livewire/Posts/Show.php (líneas 103-106)
public function render()
{
    $post = $this->post;
    
    // Búsqueda por referencia: obtener datos del autor desde la colección users
    $authorFromReference = $post->getAuthorFromReference();
    
    return view('livewire.posts.show', [
        'post' => $post,
        'authorFromReference' => $authorFromReference, // ← Autor obtenido por referencia
        'trending' => $trending,
    ]);
}
```

**4. Datos del seeder:**
```php
// database/seeders/ForumDemoSeeder.php
$postsData = [
    [
        'title' => 'Problemas al usar $lookup en MongoDB?',
        'user_id' => (string) $juan->_id, // ← REFERENCIA a users
        'author' => [  // ← Datos embebidos (redundantes para comparar)
            '_id' => (string) $juan->_id,
            'name' => $juan->name,
            'email' => $juan->email,
        ],
        // ...
    ]
];
```

#### **Cómo funciona:**
1. Cada post tiene un campo `user_id` que apunta al `_id` del usuario en la colección `users`
2. El método `getAuthorFromReference()` realiza una consulta a la colección `users` usando ese `user_id`
3. La consulta retorna los datos actualizados del usuario desde la colección `users`
4. También mantiene datos embebidos en `author` para comparación y fallback

#### **Ventajas de este enfoque híbrido:**
- ✅ **Demuestra búsqueda por referencia** (requisito del proyecto)
- ✅ **Permite actualización centralizada** del usuario
- ✅ **Mantiene performance** con datos embebidos cuando no se necesita info actualizada
- ✅ **Flexible** - puede usar referencia o datos embebidos según el caso

---

## 🎯 Resumen de Cumplimiento

| Requisito | Estado | Evidencia |
|-----------|--------|-----------|
| MongoDB | ✅ CUMPLIDO | Conexión y modelos MongoDB configurados |
| Aplicación Web | ✅ CUMPLIDO | Laravel + Livewire con interfaz web completa |
| Más de 1 colección | ✅ CUMPLIDO | `users` y `posts` (2 colecciones) |
| Búsqueda por referencia | ✅ CUMPLIDO | Método `getAuthorFromReference()` en Post.php |

---

## 📂 Archivos Clave

### Modelos
- `app/Models/User.php` - Modelo de usuario (colección users)
- `app/Models/Post.php` - Modelo de post (colección posts) con búsqueda por referencia

### Componentes Livewire
- `app/Livewire/Posts/Index.php` - Lista de posts
- `app/Livewire/Posts/Show.php` - Detalle de post (usa búsqueda por referencia)
- `app/Livewire/Posts/Create.php` - Crear post

### Base de Datos
- `database/seeders/ForumDemoSeeder.php` - Seeder con datos de demostración
- `config/database.php` - Configuración de MongoDB

### Vistas
- `resources/views/livewire/posts/*.blade.php` - Vistas del foro

---

## 🚀 Cómo Probar la Búsqueda por Referencia

1. Ejecutar el seeder:
```bash
php artisan db:seed --class=ForumDemoSeeder
```

2. Acceder a cualquier post:
```
http://localhost:8000/posts/{id}
```

3. El componente `Show.php` ejecutará automáticamente `getAuthorFromReference()` que:
   - Toma el `user_id` del post
   - Busca en la colección `users` ese ID
   - Retorna los datos actualizados del usuario

4. Para verificar en código, revisar:
   - `app/Models/Post.php` (líneas 69-90) - Método de búsqueda
   - `app/Livewire/Posts/Show.php` (líneas 103-106) - Uso del método

---

## 📊 Estructura de Datos

### Diseño Híbrido: Embebido + Referencia

El proyecto usa un **diseño híbrido** que combina:
- **Datos embebidos** (`author`, `comments`, `reactions`) - Para performance
- **Referencias** (`user_id`) - Para integridad y actualizaciones

Esto demuestra comprensión de:
- ✅ Cuándo usar documentos embebidos
- ✅ Cuándo usar referencias
- ✅ Búsquedas entre colecciones
- ✅ Trade-offs de cada enfoque

---

## 🎓 Conclusión

El proyecto **ForoDB cumple con TODOS los requisitos**:
1. ✅ Usa MongoDB como base de datos
2. ✅ Es una aplicación web completa
3. ✅ Utiliza múltiples colecciones (users, posts)
4. ✅ Implementa búsquedas por referencia entre colecciones

El código demuestra conocimiento de:
- Modelado de datos en MongoDB
- Documentos embebidos vs referencias
- Consultas entre colecciones
- Desarrollo web con Laravel y MongoDB
