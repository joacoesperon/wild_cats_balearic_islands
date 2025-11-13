<?php
// controllers/InteresadoController.php

class InteresadoController {
    private $interesadoModel;
    private $voluntarioModel; // Para convertir interesados en voluntarios

    public function __construct() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden gestionar interesados
        $this->interesadoModel = new Interesado();
        $this->voluntarioModel = new Voluntario();
    }

    public function index() {
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $interesados = $this->interesadoModel->getAllByAyuntamiento($ayuntamiento_id);
        require_once __DIR__ . '/../views/interesados/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../views/interesados/form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $DNI = $_POST['DNI'] ?? null;
            $nombreCompleto = $_POST['nombreCompleto'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefono = $_POST['telefono'] ?? null;
            $idAyuntamiento = $_SESSION['ayuntamiento_id'];

            if ($this->interesadoModel->createInteresado($DNI, $nombreCompleto, $email, $telefono, $idAyuntamiento)) {
                $_SESSION['success_message'] = 'Interesado registrado correctamente en el bolsín.';
                header('Location: ' . url('interesados'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al registrar interesado.';
            }
        }
        header('Location: ' . url('interesados/create'));
        exit();
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id && $this->interesadoModel->belongsToAyuntamiento($id, $_SESSION['ayuntamiento_id'])) {
            $interesado = $this->interesadoModel->getById($id);
            if ($interesado) {
                require_once __DIR__ . '/../views/interesados/form.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Interesado no encontrado o sin permisos.';
        header('Location: ' . url('interesados'));
        exit();
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idInteresado = $_POST['idInteresado'] ?? null;
            $DNI = $_POST['DNI'] ?? null;
            $nombreCompleto = $_POST['nombreCompleto'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefono = $_POST['telefono'] ?? null;

            if ($idInteresado && $this->interesadoModel->belongsToAyuntamiento($idInteresado, $_SESSION['ayuntamiento_id'])) {
                if ($this->interesadoModel->updateInteresado($idInteresado, $DNI, $nombreCompleto, $email, $telefono)) {
                    $_SESSION['success_message'] = 'Interesado actualizado correctamente.';
                    header('Location: ' . url('interesados'));
                    exit();
                } else {
                    $_SESSION['error_message'] = 'Error al actualizar interesado.';
                }
            } else {
                $_SESSION['error_message'] = 'Acción no permitida.';
            }
        }
        header('Location: ' . url('interesados'));
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idInteresado = $_POST['idInteresado'] ?? null;

            if ($idInteresado && $this->interesadoModel->belongsToAyuntamiento($idInteresado, $_SESSION['ayuntamiento_id'])) {
                if ($this->interesadoModel->deleteInteresado($idInteresado)) {
                    $_SESSION['success_message'] = 'Interesado eliminado correctamente.';
                } else {
                    $_SESSION['error_message'] = 'Error al eliminar interesado.';
                }
            } else {
                $_SESSION['error_message'] = 'Acción no permitida.';
            }
        }
        header('Location: ' . url('interesados'));
        exit();
    }

    public function accept() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idInteresado = $_POST['idInteresado'] ?? null;
            $usuario = $_POST['usuario'] ?? null;
            $contrasenya = $_POST['contrasenya'] ?? null;

            // 1. Verificar permisos
            if (!$idInteresado || !$this->interesadoModel->belongsToAyuntamiento($idInteresado, $_SESSION['ayuntamiento_id'])) {
                $_SESSION['error_message'] = 'Acción no permitida.';
                header('Location: ' . url('interesados'));
                exit();
            }

            // 2. Verificar que el interesado existe y no es voluntario
            $interesado = $this->interesadoModel->getById($idInteresado);
            if (!$interesado) {
                $_SESSION['error_message'] = 'Interesado no encontrado.';
                header('Location: ' . url('interesados'));
                exit();
            }
            if ($interesado->es_voluntario) {
                $_SESSION['error_message'] = 'Este interesado ya es un voluntario.';
                header('Location: ' . url('interesados'));
                exit();
            }

            // 3. Verificar que el nombre de usuario no exista
            if ($this->voluntarioModel->findByUsername($usuario)) {
                $_SESSION['error_message'] = 'El nombre de usuario "' . htmlspecialchars($usuario) . '" ya está en uso. Por favor, elige otro.';
                header('Location: ' . url('interesados'));
                exit();
            }

            // 4. Crear el voluntario
            if ($this->voluntarioModel->create($usuario, $contrasenya, $idInteresado)) {
                $_SESSION['success_message'] = 'Interesado aceptado como voluntario correctamente.';
            } else {
                $_SESSION['error_message'] = 'Error al aceptar interesado como voluntario.';
            }
        }
        header('Location: ' . url('interesados'));
        exit();
    }
}
