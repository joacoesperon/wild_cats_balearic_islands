<?php
// index.php - Punto de entrada y enrutador principal

// Iniciar sesión
session_start();

// Incluir la configuración de la base de datos y otras utilidades
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/BaseModel.php'; // Clase base para todos los modelos

// --- URL Helper ---
// Definir la URL base del proyecto para no depender de .htaccess
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/index.php');

/**
 * Genera una URL completa para una ruta dada.
 * @param string $path La ruta deseada (ej. 'colonias/create').
 * @return string La URL completa.
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}
// --- Fin URL Helper ---


// Autocargar clases de controladores y modelos
spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/controllers/',
        __DIR__ . '/models/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Obtener la ruta de la URL sin .htaccess
$request_uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
$request_uri = trim($request_uri, '/');
if (empty($request_uri)) {
    $request_uri = 'login'; // Página por defecto
}


// Definir rutas
$routes = [
    '' => ['controller' => 'AuthController', 'method' => 'login'], // Página de inicio por defecto
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'logout' => ['controller' => 'AuthController', 'method' => 'logout'],

    // Rutas para Ayuntamientos
    'ayuntamientos' => ['controller' => 'AyuntamientoController', 'method' => 'index'],
    'ayuntamientos/create' => ['controller' => 'AyuntamientoController', 'method' => 'create'],
    'ayuntamientos/store' => ['controller' => 'AyuntamientoController', 'method' => 'store'],
    'ayuntamientos/edit' => ['controller' => 'AyuntamientoController', 'method' => 'edit'],
    'ayuntamientos/update' => ['controller' => 'AyuntamientoController', 'method' => 'update'],
    'ayuntamientos/delete' => ['controller' => 'AyuntamientoController', 'method' => 'delete'],

    // Rutas para Colonias
    'colonias' => ['controller' => 'ColoniaController', 'method' => 'index'],
    'colonias/create' => ['controller' => 'ColoniaController', 'method' => 'create'],
    'colonias/store' => ['controller' => 'ColoniaController', 'method' => 'store'],
    'colonias/show' => ['controller' => 'ColoniaController', 'method' => 'show'],
    'colonias/edit' => ['controller' => 'ColoniaController', 'method' => 'edit'],
    'colonias/update' => ['controller' => 'ColoniaController', 'method' => 'update'],
    'colonias/delete' => ['controller' => 'ColoniaController', 'method' => 'delete'],

    // Rutas para Gatos
    'gatos' => ['controller' => 'GatoController', 'method' => 'index'],
    'gatos/create' => ['controller' => 'GatoController', 'method' => 'create'],
    'gatos/store' => ['controller' => 'GatoController', 'method' => 'store'],
    'gatos/show' => ['controller' => 'GatoController', 'method' => 'show'],
    'gatos/edit' => ['controller' => 'GatoController', 'method' => 'edit'],
    'gatos/update' => ['controller' => 'GatoController', 'method' => 'update'],
    'gatos/delete' => ['controller' => 'GatoController', 'method' => 'delete'],
    'gatos/ajax_get_by_colonia' => ['controller' => 'GatoController', 'method' => 'ajaxGetByColonia'],

    // Rutas para Grupos
    'grupos' => ['controller' => 'GrupoController', 'method' => 'index'],
    'grupos/create' => ['controller' => 'GrupoController', 'method' => 'create'],
    'grupos/store' => ['controller' => 'GrupoController', 'method' => 'store'],
    'grupos/show' => ['controller' => 'GrupoController', 'method' => 'show'],
    'grupos/edit' => ['controller' => 'GrupoController', 'method' => 'edit'],
    'grupos/update' => ['controller' => 'GrupoController', 'method' => 'update'],
    'grupos/delete' => ['controller' => 'GrupoController', 'method' => 'delete'],
    'grupos/addmiembro' => ['controller' => 'GrupoController', 'method' => 'addMiembro'],
    'grupos/removemiembro' => ['controller' => 'GrupoController', 'method' => 'removeMiembro'],
    'grupos/setresponsable' => ['controller' => 'GrupoController', 'method' => 'setResponsable'],

    // Rutas para Interesados (Bolsín de Voluntarios)
    'interesados' => ['controller' => 'InteresadoController', 'method' => 'index'],
    'interesados/create' => ['controller' => 'InteresadoController', 'method' => 'create'],
    'interesados/store' => ['controller' => 'InteresadoController', 'method' => 'store'],
    'interesados/edit' => ['controller' => 'InteresadoController', 'method' => 'edit'],
    'interesados/update' => ['controller' => 'InteresadoController', 'method' => 'update'],
    'interesados/delete' => ['controller' => 'InteresadoController', 'method' => 'delete'],
    'interesados/accept' => ['controller' => 'InteresadoController', 'method' => 'accept'], // Para convertir en voluntario

    // Rutas públicas para inscripción de voluntarios
    'interesados/public_welcome' => ['controller' => 'InteresadoController', 'method' => 'publicWelcome'],
    'interesados/public_create' => ['controller' => 'InteresadoController', 'method' => 'publicCreate'],
    'interesados/public_store' => ['controller' => 'InteresadoController', 'method' => 'publicStore'],

    // Rutas para Voluntarios (ya aceptados)
    'voluntarios' => ['controller' => 'VoluntarioController', 'method' => 'index'],
    'voluntarios/show' => ['controller' => 'VoluntarioController', 'method' => 'show'],
    'voluntarios/edit' => ['controller' => 'VoluntarioController', 'method' => 'edit'],
    'voluntarios/update' => ['controller' => 'VoluntarioController', 'method' => 'update'],
    'voluntarios/delete' => ['controller' => 'VoluntarioController', 'method' => 'delete'],

    // Rutas para Tareas (Trabajos)
    'trabajos' => ['controller' => 'TrabajoController', 'method' => 'index'],
    'trabajos/create' => ['controller' => 'TrabajoController', 'method' => 'create'],
    'trabajos/store' => ['controller' => 'TrabajoController', 'method' => 'store'],
    'trabajos/edit' => ['controller' => 'TrabajoController', 'method' => 'edit'],
    'trabajos/update' => ['controller' => 'TrabajoController', 'method' => 'update'],
    'trabajos/delete' => ['controller' => 'TrabajoController', 'method' => 'delete'],
    'trabajos/completar' => ['controller' => 'TrabajoController', 'method' => 'completar'],

    // Rutas para Visitas
    'visitas' => ['controller' => 'VisitaController', 'method' => 'index'],
    'visitas/create' => ['controller' => 'VisitaController', 'method' => 'create'],
    'visitas/store' => ['controller' => 'VisitaController', 'method' => 'store'],
    'visitas/show' => ['controller' => 'VisitaController', 'method' => 'show'],
    'visitas/edit' => ['controller' => 'VisitaController', 'method' => 'edit'],
    'visitas/update' => ['controller' => 'VisitaController', 'method' => 'update'],
    'visitas/delete' => ['controller' => 'VisitaController', 'method' => 'delete'],

    // Rutas para Incidencias Ocurridas
    'ocurrencias' => ['controller' => 'OcurrenciaController', 'method' => 'index'],
    'ocurrencias/show' => ['controller' => 'OcurrenciaController', 'method' => 'show'],
    'ocurrencias/edit' => ['controller' => 'OcurrenciaController', 'method' => 'edit'],
    'ocurrencias/update' => ['controller' => 'OcurrenciaController', 'method' => 'update'],
    'ocurrencias/delete' => ['controller' => 'OcurrenciaController', 'method' => 'delete'],

    // Rutas para Tipos de Incidencia
    'tipos_incidencia' => ['controller' => 'TipoIncidenciaController', 'method' => 'index'],
    'tipos_incidencia/create' => ['controller' => 'TipoIncidenciaController', 'method' => 'create'],
    'tipos_incidencia/store' => ['controller' => 'TipoIncidenciaController', 'method' => 'store'],
    'tipos_incidencia/edit' => ['controller' => 'TipoIncidenciaController', 'method' => 'edit'],
    'tipos_incidencia/update' => ['controller' => 'TipoIncidenciaController', 'method' => 'update'],
    'tipos_incidencia/delete' => ['controller' => 'TipoIncidenciaController', 'method' => 'delete'],

    // Rutas para Backups
    'backups' => ['controller' => 'BackupController', 'method' => 'index'],
    'backups/download' => ['controller' => 'BackupController', 'method' => 'download'],
    'backups/generate' => ['controller' => 'BackupController', 'method' => 'generate'], // Para generar backup manualmente
    'backups/delete' => ['controller' => 'BackupController', 'method' => 'delete'],
];

// Verificar si la ruta solicitada existe
if (array_key_exists($request_uri, $routes)) {
    $controller_name = $routes[$request_uri]['controller'];
    $method_name = $routes[$request_uri]['method'];

    // Instanciar el controlador y llamar al método
    $controller = new $controller_name();
    $controller->$method_name();
} else {
    // Si la ruta no existe, mostrar un error 404 o redirigir a una página de inicio
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}
?>