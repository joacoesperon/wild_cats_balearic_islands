<?php

// config/database.php

class Database {
    private static $host = 'localhost';
    private static $db_name = 'gatos_mallorca';
    private static $username = 'root'; // Default XAMPP username
    private static $password = '';     // Default XAMPP password (empty)
    private static $conn;

    /**
     * Obtiene la conexión a la base de datos.
     * @return PDO La conexión PDO.
     */
    public static function getConnection() {
        if (self::$conn === null) {
            try {
                // Detectar si estamos en localhost para usar credenciales por defecto
                // Esto es una simplificación para el requisito de XAMPP
                if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
                    // Ya están configuradas las credenciales por defecto
                } else {
                    // Aquí se podrían configurar credenciales para un entorno de producción
                    // Por ahora, se mantienen las de localhost para simplicidad del ejercicio
                }

                self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$db_name, self::$username, self::$password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->exec("set names utf8");
            } catch(PDOException $exception) {
                echo "Error de conexión a la base de datos: " . $exception->getMessage();
                exit(); // Terminar la ejecución si no se puede conectar a la BD
            }
        }
        return self::$conn;
    }

    /**
     * Asegura que los directorios de subida y backup existan.
     */
    public static function ensureDirectoriesExist() {
        $uploadDir = __DIR__ . '/../public/uploads/gatos/';
        $backupDir = __DIR__ . '/../backups/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
    }
}

// Asegurar que los directorios existan al cargar la configuración de la base de datos
Database::ensureDirectoriesExist();

?>