<?php
// controllers/AuthController.php

class AuthController {
    private $ayuntamientoModel;
    private $voluntarioModel;

    public function __construct() {
        $this->ayuntamientoModel = new Ayuntamiento();
        $this->voluntarioModel = new Voluntario();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // 1. Intentar como Ayuntamiento
            $ayuntamiento = $this->ayuntamientoModel->findByUsername($username);
            if ($ayuntamiento && $ayuntamiento->contrasenya === $password) { // Contraseña en texto plano
                $_SESSION['user_id'] = $ayuntamiento->idAyuntamiento;
                $_SESSION['username'] = $ayuntamiento->usuario;
                $_SESSION['user_type'] = 'ayuntamiento';
                $_SESSION['ayuntamiento_id'] = $ayuntamiento->idAyuntamiento;
                header('Location: ' . url('ayuntamientos')); // Redirigir al dashboard del ayuntamiento
                exit();
            }

            // 2. Si no, intentar como Voluntario
            $voluntario = $this->voluntarioModel->findByUsername($username);
            if ($voluntario && $voluntario->contrasenya === $password) { // Contraseña en texto plano
                $_SESSION['user_id'] = $voluntario->idVoluntario;
                $_SESSION['username'] = $voluntario->usuario;
                $_SESSION['user_type'] = 'voluntario';
                $_SESSION['is_responsable'] = $this->voluntarioModel->isResponsable($voluntario->idVoluntario); // Comprobar si es responsable
                header('Location: ' . url('voluntarios/show?id=' . $voluntario->idVoluntario)); // Redirigir a su perfil
                exit();
            }

            // 3. Si no se encuentra en ninguna tabla
            $_SESSION['error_message'] = 'Usuario o contraseña incorrectos.';
            header('Location: ' . url('login'));
            exit();
        }

        // Cargar la vista de login
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . url('login'));
        exit();
    }

    public static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit();
        }
    }

    public static function checkAyuntamientoAuth() {
        self::checkAuth();
        if ($_SESSION['user_type'] !== 'ayuntamiento') {
            header('Location: ' . url('login')); // O a una página de error de permisos
            exit();
        }
    }

    public static function checkVoluntarioAuth() {
        self::checkAuth();
        if ($_SESSION['user_type'] !== 'voluntario') {
            header('Location: ' . url('login')); // O a una página de error de permisos
            exit();
        }
    }

    public static function checkResponsableOrAyuntamientoAuth() {
        self::checkAuth();
        $isAyuntamiento = $_SESSION['user_type'] === 'ayuntamiento';
        $isResponsable = $_SESSION['user_type'] === 'voluntario' && !empty($_SESSION['is_responsable']);

        if (!$isAyuntamiento && !$isResponsable) {
            $_SESSION['error_message'] = 'No tienes permisos para acceder a esta página.';
            header('Location: ' . url('login'));
            exit();
        }
    }
}