<?php
// controllers/BackupController.php

class BackupController {
    public function __construct() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden gestionar backups
    }

    public function index() {
        $backupDir = __DIR__ . '/../backups/';
        $backups = [];

        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if (preg_match('/^backup_gatos_(\d{8})\.sql$/', $file, $matches)) {
                    $backups[] = [
                        'name' => $file,
                        'date' => DateTime::createFromFormat('Ymd', $matches[1])->format('d-m-Y'),
                        'path' => $backupDir . $file
                    ];
                }
            }
            // Ordenar por fecha, el más reciente primero
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        require_once __DIR__ . '/../views/backups/list.php';
    }

    public function generate() {
        // Database credentials from config/database.php (as per XAMPP default)
        $host = 'localhost';
        $dbname = 'gatos_mallorca';
        $user = 'root';
        $pass = ''; // Default XAMPP password is empty

        $backupDir = __DIR__ . '/../backups/';
        $fileName = 'backup_gatos_' . date('Ymd') . '.sql';
        $filePath = $backupDir . $fileName;

        // Ensure the backup directory exists and is writable
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        // --- Determine mysqldump path based on OS for cross-platform compatibility ---
        $mysqldumpPath = 'mysqldump'; // Default, hope it's in PATH

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $xamppPath = getenv('XAMPP_HOME') ?: 'C:\xampp'; // Try environment variable or default
            if (file_exists($xamppPath . '\mysql\bin\mysqldump.exe')) {
                $mysqldumpPath = '"' . $xamppPath . '\mysql\bin\mysqldump.exe"'; // Quote path for spaces
            } elseif (file_exists('C:\xampp\mysql\bin\mysqldump.exe')) {
                $mysqldumpPath = '"C:\xampp\mysql\bin\mysqldump.exe"';
            }
        } elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
            // macOS
            if (file_exists('/Applications/XAMPP/xamppfiles/bin/mysqldump')) {
                $mysqldumpPath = '/Applications/XAMPP/xamppfiles/bin/mysqldump';
            }
        } else {
            // Linux (and other Unix-like)
            if (file_exists('/opt/lampp/bin/mysqldump')) {
                $mysqldumpPath = '/opt/lampp/bin/mysqldump';
            }
        }
        // --- End mysqldump path determination ---

        // Construct the mysqldump command
        $passArg = ($pass !== '') ? '--password=' . escapeshellarg($pass) : '';
        $command = sprintf(
            '%s --user=%s %s --host=%s %s > %s 2>&1', // 2>&1 redirects stderr to stdout to capture errors
            $mysqldumpPath,
            escapeshellarg($user),
            $passArg,
            escapeshellarg($host),
            escapeshellarg($dbname),
            escapeshellarg($filePath)
        );

        // Execute the command
        $output = [];
        $return_var = -1;
        exec($command, $output, $return_var);

        // Also call the stored procedure to log the backup attempt, as per requirements
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("CALL sp_backup_gatos_mallorca()");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error calling backup stored procedure: " . $e->getMessage());
        }

        // Check if the command was successful and the file was created and is not empty
        if ($return_var === 0 && file_exists($filePath) && filesize($filePath) > 0) {
            $_SESSION['success_message'] = 'Backup real generado correctamente: ' . $fileName;
        } else {
            $errorDetails = empty($output) ? 'No output from command.' : implode("\n", $output);
            $_SESSION['error_message'] = 'Error al generar el backup. Asegúrese de que `mysqldump` está en el PATH del sistema o que la ruta configurada para su OS es correcta. Detalles: ' . $errorDetails;
        }

        header('Location: ' . url('backups'));
        exit();
    }

    public function download() {
        $fileName = $_GET['file'] ?? null;
        $backupDir = __DIR__ . '/../backups/';
        // Sanitize filename to prevent directory traversal
        $fileName = basename($fileName); // Removes path components
        $filePath = $backupDir . $fileName;

        if ($fileName && file_exists($filePath) && preg_match('/^backup_gatos_(\d{8})\.sql$/', $fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit();
        } else {
            $_SESSION['error_message'] = 'Archivo de backup no encontrado o inválido.';
            header('Location: ' . url('backups'));
            exit();
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileName = $_POST['fileName'] ?? null;
            $backupDir = __DIR__ . '/../backups/';

            // Sanitize filename to prevent directory traversal
            $fileName = basename($fileName); // Removes path components
            $filePath = $backupDir . $fileName;

            // Validate filename format
            if ($fileName && preg_match('/^backup_gatos_(\d{8})\.sql$/', $fileName) && file_exists($filePath)) {
                if (unlink($filePath)) {
                    $_SESSION['success_message'] = 'Backup "' . htmlspecialchars($fileName) . '" eliminado correctamente.';
                } else {
                    $_SESSION['error_message'] = 'Error al eliminar el backup "' . htmlspecialchars($fileName) . '".';
                }
            } else {
                $_SESSION['error_message'] = 'Archivo de backup no válido o no encontrado.';
            }
        }
        header('Location: ' . url('backups'));
        exit();
    }
}