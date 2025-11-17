# 🚀 Guía Rápida de Ejecución - ForoDB

## Comandos para Ejecutar el Proyecto

### 1. Limpiar y Resetear Base de Datos (Opcional)

Si quieres empezar desde cero, limpia las colecciones de MongoDB:

```powershell
# Opción A: Usando mongosh (si tienes MongoDB instalado localmente)
mongosh
use forodb
db.posts.deleteMany({})
db.comments.deleteMany({})
db.reactions.deleteMany({})
exit
```

```powershell
# Opción B: Usando el comando de Laravel
php artisan tinker
DB::connection('mongodb')->collection('posts')->truncate();
DB::connection('mongodb')->collection('comments')->truncate();
DB::connection('mongodb')->collection('reactions')->truncate();
exit
```

### 2. Ejecutar el Seeder

Ejecuta el seeder para cargar datos de prueba:

```powershell
php artisan db:seed --class=ForumDemoSeeder
```

### 3. Iniciar el Servidor

```powershell
php artisan serve
```

### 4. Compilar Assets (si es necesario)

Si los estilos no se cargan correctamente:

```powershell
npm run dev
```

O para producción:

```powershell
npm run build
```

## URLs del Foro

Una vez que el servidor esté corriendo:

- **Home**: http://localhost:8000
- **Lista de Posts**: http://localhost:8000/posts
- **Crear Post** (requiere login): http://localhost:8000/posts/create
- **Ver Post**: http://localhost:8000/posts/{id}

## Usuarios de Prueba

El seeder crea estos usuarios automáticamente:

### Usuario 1: admin_mongo
- **Email**: admin_mongo@example.com
- **Password**: secret1234

### Usuario 2: juan_db
- **Email**: juan_db@example.com
- **Password**: secret1234

## Login

1. Ve a: http://localhost:8000/login
2. Usa uno de los usuarios de prueba
3. Una vez autenticado, podrás:
   - Crear posts
   - Comentar
   - Dar reacciones

## Verificar que Todo Funciona

### Checklist de Funcionalidades

1. ✅ **Lista de Posts**
   - Ve a `/posts`
   - Deberías ver 4 posts de ejemplo
   - Busca con el buscador
   - Verifica que las tendencias se muestren en el sidebar

2. ✅ **Ver Post**
   - Haz clic en cualquier post
   - Deberías ver el detalle completo
   - Verifica que se muestren comentarios
   - Verifica que se muestren reacciones

3. ✅ **Reacciones** (requiere login)
   - Haz login
   - Ve a cualquier post
   - Haz clic en los botones de reacciones (👍 ❤️ 😄 💡)
   - Verifica que el contador aumente/disminuya
   - Intenta dar otra reacción - debería cambiar

4. ✅ **Comentar** (requiere login)
   - En el detalle de un post
   - Escribe un comentario
   - Presiona "Publicar comentario"
   - El comentario debería aparecer inmediatamente

5. ✅ **Reacciones en Comentarios** (requiere login)
   - Haz clic en las reacciones de un comentario
   - Verifica que funcione igual que en posts

6. ✅ **Crear Post** (requiere login)
   - Ve a `/posts/create`
   - Llena el formulario
   - Agrega tags separadas por comas
   - Publica el post
   - Deberías ser redirigido al detalle del nuevo post

## Troubleshooting

### Problema: Las vistas no se cargan

**Solución**: Limpia la caché de vistas
```powershell
php artisan view:clear
php artisan cache:clear
```

### Problema: Errores de Livewire

**Solución**: Regenera los archivos de Livewire
```powershell
php artisan livewire:discover
```

### Problema: Los estilos no se aplican

**Solución**: Recompila los assets
```powershell
npm run dev
```

### Problema: Error de conexión a MongoDB

**Solución**: Verifica tu configuración en `.env`
```env
DB_CONNECTION=mongodb
MONGO_DB_HOST=127.0.0.1
MONGO_DB_PORT=27017
MONGO_DB_DATABASE=forodb
MONGO_DB_USERNAME=null
MONGO_DB_PASSWORD=null
```

### Problema: "Class not found"

**Solución**: Regenera el autoloader
```powershell
composer dump-autoload
```

## Verificar Estructura en MongoDB

Puedes verificar la estructura de los documentos directamente:

```javascript
// En mongosh
use forodb

// Ver un post completo con comentarios y reacciones
db.posts.findOne()

// Ver todos los posts
db.posts.find().pretty()

// Contar posts
db.posts.countDocuments()
```

## Logs para Debugging

Si algo no funciona, revisa los logs:

```powershell
# Ver logs en tiempo real
Get-Content storage/logs/laravel.log -Wait -Tail 50
```

## Comandos Útiles

### Limpiar todo el caché
```powershell
php artisan optimize:clear
```

### Ver rutas disponibles
```powershell
php artisan route:list
```

### Ver componentes Livewire
```powershell
php artisan livewire:list
```

---

## 🎉 ¡Todo Listo!

Si seguiste todos los pasos, tu foro debería estar funcionando perfectamente con:

- ✅ Diseño moderno con Tailwind
- ✅ Estructura MongoDB correcta (documentos embebidos)
- ✅ Sistema de reacciones funcional
- ✅ Comentarios embebidos
- ✅ Búsqueda con regex
- ✅ Tendencias dinámicas

¡Disfruta tu foro ForoDB! 🚀
