<?php
// controllers/TrabajoController.php

class TrabajoController {
    private $trabajoModel;
    private $grupoModel;

    public function __construct() {
        $this->trabajoModel = new Trabajo();
        $this->grupoModel = new Grupo();

        // Enrutamiento de autenticación basado en la acción
        $action = $_GET['action'] ?? (explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'))[1] ?? 'index');

        if ($action === 'completar') {
            AuthController::checkVoluntarioAuth();
        } else {
            AuthController::checkAyuntamientoAuth();
        }
    }

    /**
     * Muestra la lista de tareas para el ayuntamiento.
     */
    public function index() {
        $idAyuntamiento = $_SESSION['ayuntamiento_id'];
        $trabajos = $this->trabajoModel->getAllByAyuntamiento($idAyuntamiento);
        require_once __DIR__ . '/../views/trabajos/list.php';
    }

    /**
     * Muestra el formulario para crear una nueva tarea.
     */
    public function create() {
        $idAyuntamiento = $_SESSION['ayuntamiento_id'];
        $grupos = $this->grupoModel->getAllByAyuntamiento($idAyuntamiento);
        $trabajo = new stdClass(); // Objeto vacío para el formulario
        require_once __DIR__ . '/../views/trabajos/form.php';
    }

    /**
     * Almacena una nueva tarea en la base de datos.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descripcion = $_POST['descripcionTrabajo'] ?? null;
            $idGrupo = $_POST['idGrupo'] ?? null;
            $idAyuntamiento = $_SESSION['ayuntamiento_id'];

            if ($descripcion && $idGrupo) {
                if ($this->trabajoModel->create($descripcion, $idGrupo, $idAyuntamiento)) {
                    $_SESSION['success_message'] = 'Tarea asignada correctamente.';
                    header('Location: ' . url('trabajos'));
                    exit();
                } else {
                    $_SESSION['error_message'] = 'Error al asignar la tarea.';
                }
            } else {
                $_SESSION['error_message'] = 'Todos los campos son obligatorios.';
            }
        }
        header('Location: ' . url('trabajos/create'));
        exit();
    }

    /**
     * Muestra el formulario para editar una tarea.
     */
    public function edit() {
        $idTrabajo = $_GET['id'] ?? null;
        if (!$idTrabajo) {
            header('Location: ' . url('trabajos'));
            exit();
        }

        $trabajo = $this->trabajoModel->getByIdWithDetails($idTrabajo);
        // Verificar que la tarea pertenece al ayuntamiento
        $grupo = $this->grupoModel->getByIdWithDetails($trabajo->idGrupo);
        if (!$trabajo || $grupo->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
            $_SESSION['error_message'] = 'Tarea no encontrada o sin permisos para editar.';
            header('Location: ' . url('trabajos'));
            exit();
        }

        $idAyuntamiento = $_SESSION['ayuntamiento_id'];
        $grupos = $this->grupoModel->getAllByAyuntamiento($idAyuntamiento);
        require_once __DIR__ . '/../views/trabajos/form.php';
    }

    /**
     * Actualiza una tarea existente en la base de datos.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTrabajo = $_POST['idTrabajo'] ?? null;
            $descripcion = $_POST['descripcionTrabajo'] ?? null;
            $idGrupo = $_POST['idGrupo'] ?? null;
            $completado = !empty($_POST['completado']) ? 1 : 0;

            // Verificar que la tarea pertenece al ayuntamiento
            $trabajoExistente = $this->trabajoModel->getByIdWithDetails($idTrabajo);
            $grupo = $this->grupoModel->getByIdWithDetails($trabajoExistente->idGrupo);
            if (!$trabajoExistente || $grupo->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para actualizar esta tarea.';
                header('Location: ' . url('trabajos'));
                exit();
            }

            if ($idTrabajo && $descripcion && $idGrupo) {
                if ($this->trabajoModel->update($idTrabajo, $descripcion, $idGrupo, $completado)) {
                    $_SESSION['success_message'] = 'Tarea actualizada correctamente.';
                    header('Location: ' . url('trabajos'));
                    exit();
                } else {
                    $_SESSION['error_message'] = 'Error al actualizar la tarea.';
                }
            } else {
                $_SESSION['error_message'] = 'Todos los campos son obligatorios.';
            }
        }
        header('Location: ' . url('trabajos'));
        exit();
    }

    /**
     * Elimina una tarea.
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTrabajo = $_POST['idTrabajo'] ?? null;

            // Verificar que la tarea pertenece al ayuntamiento
            $trabajoExistente = $this->trabajoModel->getByIdWithDetails($idTrabajo);
            $grupo = $this->grupoModel->getByIdWithDetails($trabajoExistente->idGrupo);
            if (!$trabajoExistente || $grupo->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para eliminar esta tarea.';
                header('Location: ' . url('trabajos'));
                exit();
            }

            if ($this->trabajoModel->delete($idTrabajo)) {
                $_SESSION['success_message'] = 'Tarea eliminada correctamente.';
            } else {
                $_SESSION['error_message'] = 'Error al eliminar la tarea.';
            }
        }
        header('Location: ' . url('trabajos'));
        exit();
    }

    /**
     * Maneja la acción de marcar una tarea como completada (para voluntarios).
     */
    public function completar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTrabajo = $_POST['idTrabajo'] ?? null;
            $idVoluntario = $_SESSION['user_id'] ?? null;

            if ($idTrabajo && $idVoluntario) {
                if ($this->trabajoModel->verificarPertenenciaVoluntario($idTrabajo, $idVoluntario)) {
                    if ($this->trabajoModel->marcarComoCompletado($idTrabajo)) {
                        $_SESSION['success_message'] = 'Tarea marcada como completada.';
                    } else {
                        $_SESSION['error_message'] = 'Error al actualizar la tarea.';
                    }
                } else {
                    $_SESSION['error_message'] = 'No tienes permisos para modificar esta tarea.';
                }
            } else {
                $_SESSION['error_message'] = 'Datos insuficientes para completar la tarea.';
            }
        }
        header('Location: ' . url('voluntarios/show?id=' . $_SESSION['user_id']));
        exit();
    }
}
