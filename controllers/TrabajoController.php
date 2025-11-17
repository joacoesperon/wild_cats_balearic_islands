<?php
// controllers/TrabajoController.php

class TrabajoController {
    private $trabajoModel;
    private $grupoModel;
    private $voluntarioModel; // Añadido para la lógica de responsables

    public function __construct() {
        $this->trabajoModel = new Trabajo();
        $this->grupoModel = new Grupo();
        $this->voluntarioModel = new Voluntario(); // Añadido

        // Enrutamiento de autenticación basado en la acción
        $action = $_GET['action'] ?? (explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'))[1] ?? 'index');

        if ($action === 'completar') {
            AuthController::checkVoluntarioAuth();
        } elseif (in_array($action, ['index'])) {
            AuthController::checkResponsableOrAyuntamientoAuth();
        } else {
            // Para create, store, edit, update, delete, solo ayuntamiento
            AuthController::checkAyuntamientoAuth();
        }
    }

    /**
     * Muestra la lista de tareas.
     */
    public function index() {
        $idAyuntamiento = $_SESSION['ayuntamiento_id'] ?? null;
        // Si es voluntario, necesitamos el id de ayuntamiento de uno de sus grupos.
        if ($_SESSION['user_type'] === 'voluntario') {
            $grupos = $this->voluntarioModel->getGrupos($_SESSION['user_id']);
            if (!empty($grupos)) {
                // Tomamos el ayuntamiento del primer grupo, asumiendo que todos son del mismo.
                $unGrupo = $this->grupoModel->getByIdWithDetails($grupos[0]->idGrupo);
                $idAyuntamiento = $unGrupo->idAyuntamiento;
            } else {
                $idAyuntamiento = null; // No pertenece a ningún grupo, no verá tareas.
            }
        }
        
        $trabajos = $idAyuntamiento ? $this->trabajoModel->getAllByAyuntamiento($idAyuntamiento) : [];
        require_once __DIR__ . '/../views/trabajos/list.php';
    }

    /**
     * Muestra el formulario para crear una nueva tarea.
     */
    public function create() {
        if ($_SESSION['user_type'] === 'ayuntamiento') {
            $idAyuntamiento = $_SESSION['ayuntamiento_id'];
            $grupos = $this->grupoModel->getAllByAyuntamiento($idAyuntamiento);
        } else { // Es un voluntario responsable
            $idVoluntario = $_SESSION['user_id'];
            $grupos = $this->voluntarioModel->getGruposResponsable($idVoluntario);
        }
        
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
            $idAyuntamiento = null;

            if ($_SESSION['user_type'] === 'ayuntamiento') {
                $idAyuntamiento = $_SESSION['ayuntamiento_id'];
                // Validar que el grupo pertenece al ayuntamiento
                $grupo = $this->grupoModel->getByIdWithDetails($idGrupo);
                if (!$grupo || $grupo->idAyuntamiento != $idAyuntamiento) {
                    $_SESSION['error_message'] = 'El grupo seleccionado no es válido.';
                    header('Location: ' . url('trabajos/create'));
                    exit();
                }
            } else { // Es un voluntario responsable
                $idVoluntario = $_SESSION['user_id'];
                $gruposResponsable = $this->voluntarioModel->getGruposResponsable($idVoluntario);
                $esGrupoValido = false;
                foreach ($gruposResponsable as $grupo) {
                    if ($grupo->idGrupo == $idGrupo) {
                        $esGrupoValido = true;
                        // Necesitamos el id del ayuntamiento para guardarlo en la tarea
                        $grupoDetalles = $this->grupoModel->getByIdWithDetails($idGrupo);
                        $idAyuntamiento = $grupoDetalles->idAyuntamiento;
                        break;
                    }
                }
                if (!$esGrupoValido) {
                    $_SESSION['error_message'] = 'No tienes permisos para asignar tareas a este grupo.';
                    header('Location: ' . url('trabajos/create'));
                    exit();
                }
            }

            if ($descripcion && $idGrupo && $idAyuntamiento) {
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
            $return_to = $_POST['return_to'] ?? 'profile';

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

        if (isset($return_to) && $return_to === 'list') {
            header('Location: ' . url('trabajos'));
        } else {
            header('Location: ' . url('voluntarios/show?id=' . $_SESSION['user_id']));
        }
        exit();
    }
}
