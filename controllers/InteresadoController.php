<?php
// controllers/InteresadoController.php

class InteresadoController {
    private $interesadoModel;
    private $voluntarioModel; // Para convertir interesados en voluntarios

    public function __construct() {
        $this->interesadoModel = new Interesado();
        $this->voluntarioModel = new Voluntario(); // Para convertir interesados en voluntarios
    }

    public function index() {
        AuthController::checkAyuntamientoAuth();
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $interesados = $this->interesadoModel->getAllByAyuntamiento($ayuntamiento_id);
        require_once __DIR__ . '/../views/interesados/list.php';
    }

    public function create() {
        AuthController::checkAyuntamientoAuth();
        require_once __DIR__ . '/../views/interesados/form.php';
    }

    public function store() {
        AuthController::checkAyuntamientoAuth();
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
        AuthController::checkAyuntamientoAuth();
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
        AuthController::checkAyuntamientoAuth();
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
        AuthController::checkAyuntamientoAuth();
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
        AuthController::checkAyuntamientoAuth();
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

    /**
     * Muestra la pantalla de bienvenida para la inscripción pública.
     */
    public function publicWelcome() {
        require_once __DIR__ . '/../views/interesados/welcome.php';
    }

    /**
     * Muestra el formulario público de inscripción de interesados.
     */
    public function publicCreate() {
        $ayuntamientoModel = new Ayuntamiento(); // Necesitamos el modelo de Ayuntamiento
        $ayuntamientos = $ayuntamientoModel->getAll(); // Obtener todos los ayuntamientos
        $interesado = new stdClass(); // Objeto vacío para el formulario
        require_once __DIR__ . '/../views/interesados/public_form.php';
    }

    /**
     * Almacena un nuevo interesado desde el formulario público.
     */
    public function publicStore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $DNI = $_POST['DNI'] ?? null;
            $nombreCompleto = $_POST['nombreCompleto'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefono = $_POST['telefono'] ?? null;
            $idAyuntamiento = $_POST['idAyuntamiento'] ?? null;

            // Validaciones básicas
            if (empty($DNI) || empty($nombreCompleto) || empty($email) || empty($idAyuntamiento)) {
                $_SESSION['error_message'] = 'Por favor, rellena todos los campos obligatorios.';
                header('Location: ' . url('interesados/public_create'));
                exit();
            }

            // Crear el interesado
            $idInteresado = $this->interesadoModel->createInteresadoPublic($DNI, $nombreCompleto, $email, $telefono);

            if ($idInteresado) {
                // Asociar al interesado con la Bolsa Municipal del Ayuntamiento seleccionado
                $bolsaMunicipalModel = new BolsaMunicipal();
                $bolsa = $bolsaMunicipalModel->findByAyuntamientoId($idAyuntamiento);

                if ($bolsa) {
                    $this->interesadoModel->addInteresadoToBolsa($idInteresado, $bolsa->idBolsaMunicipal);
                    $_SESSION['success_message'] = 'Tu solicitud ha sido enviada. Nos pondremos en contacto contigo pronto.';
                    header('Location: ' . url('login'));
                    exit();
                } else {
                    // Esto no debería pasar si el ayuntamiento existe, pero por seguridad
                    $_SESSION['error_message'] = 'Error: No se encontró la bolsa municipal para el ayuntamiento seleccionado.';
                    // Eliminar interesado si no se pudo añadir a la bolsa
                    $this->interesadoModel->deleteInteresado($idInteresado);
                    header('Location: ' . url('interesados/public_create'));
                    exit();
                }
            } else {
                $_SESSION['error_message'] = 'Error al enviar tu solicitud.';
                header('Location: ' . url('interesados/public_create'));
                exit();
            }
        }
        header('Location: ' . url('interesados/public_create'));
        exit();
    }
}
