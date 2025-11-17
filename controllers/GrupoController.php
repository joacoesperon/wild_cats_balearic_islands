<?php
// controllers/GrupoController.php

class GrupoController {
    private $grupoModel;
    private $voluntarioModel; // Para gestionar miembros del grupo

    public function __construct() {
        $this->grupoModel = new Grupo();
        $this->voluntarioModel = new Voluntario();
    }

    public function index() {
        AuthController::checkAyuntamientoAuth();
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $grupos = $this->grupoModel->getAllByAyuntamiento($ayuntamiento_id);
        require_once __DIR__ . '/../views/grupos/list.php';
    }

    public function create() {
        AuthController::checkAyuntamientoAuth();
        require_once __DIR__ . '/../views/grupos/form.php';
    }

    public function store() {
        AuthController::checkAyuntamientoAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreGrupo = $_POST['nombreGrupo'] ?? null;
            $idAyuntamiento = $_SESSION['ayuntamiento_id'];

            if ($this->grupoModel->createGrupo($nombreGrupo, $idAyuntamiento)) {
                $_SESSION['success_message'] = 'Grupo creado correctamente.';
                header('Location: ' . url('grupos'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al crear el grupo.';
            }
        }
        header('Location: ' . url('grupos/create'));
        exit();
    }

    public function show() {
        AuthController::checkAuth(); // Requiere estar logueado
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . url('')); // Redirigir si no hay ID
            exit();
        }

        $grupo = $this->grupoModel->getByIdWithDetails($id);
        if (!$grupo) {
            $_SESSION['error_message'] = 'Grupo no encontrado.';
            header('Location: ' . ($_SESSION['user_type'] === 'ayuntamiento' ? url('grupos') : url('voluntarios/show?id=' . $_SESSION['user_id'])));
            exit();
        }

        // Comprobar permisos
        $tienePermiso = false;
        if ($_SESSION['user_type'] === 'ayuntamiento' && $grupo->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
            $tienePermiso = true;
        } elseif ($_SESSION['user_type'] === 'voluntario' && $this->grupoModel->isMember($id, $_SESSION['user_id'])) {
            $tienePermiso = true;
        }

        if ($tienePermiso) {
            $miembros = $this->grupoModel->getMembers($id);
            $voluntariosDisponibles = [];
            if ($_SESSION['user_type'] === 'ayuntamiento') {
                $voluntariosDisponibles = $this->voluntarioModel->getDisponiblesParaGrupo($id, $grupo->idAyuntamiento);
            }
            require_once __DIR__ . '/../views/grupos/show.php';
            return;
        }

        $_SESSION['error_message'] = 'No tienes permisos para ver este grupo.';
        header('Location: ' . ($_SESSION['user_type'] === 'ayuntamiento' ? url('grupos') : url('voluntarios/show?id=' . $_SESSION['user_id'])));
        exit();
    }

    public function edit() {
        AuthController::checkAyuntamientoAuth();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $grupo = $this->grupoModel->getByIdWithDetails($id);
            if ($grupo && $grupo->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                require_once __DIR__ . '/../views/grupos/form.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Grupo no encontrado o no tienes permisos para editarlo.';
        header('Location: ' . url('grupos'));
        exit();
    }

    public function update() {
        AuthController::checkAyuntamientoAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idGrupo = $_POST['idGrupo'] ?? null;
            $nombreGrupo = $_POST['nombreGrupo'] ?? null;

            // Verificar que el grupo pertenece al ayuntamiento logueado
            $grupo = $this->grupoModel->getByIdWithDetails($idGrupo);
            if (!$grupo || $grupo->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para actualizar este grupo.';
                header('Location: ' . url('grupos'));
                exit();
            }

            if ($this->grupoModel->updateGrupo($idGrupo, $nombreGrupo)) {
                $_SESSION['success_message'] = 'Grupo actualizado correctamente.';
                header('Location: ' . url('grupos'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al actualizar el grupo.';
            }
        }
        header('Location: ' . url('grupos'));
        exit();
    }

    public function delete() {
        AuthController::checkAyuntamientoAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idGrupo = $_POST['idGrupo'] ?? null;

            // Verificar que el grupo pertenece al ayuntamiento logueado
            $grupo = $this->grupoModel->getByIdWithDetails($idGrupo);
            if (!$grupo || $grupo->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para eliminar este grupo.';
                header('Location: ' . url('grupos'));
                exit();
            }

            if ($this->grupoModel->delete($idGrupo)) { // Usar el método delete de BaseModel
                $_SESSION['success_message'] = 'Grupo eliminado correctamente.';
                header('Location: ' . url('grupos'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al eliminar el grupo.';
            }
        }
        header('Location: ' . url('grupos'));
        exit();
    }

    public function addMiembro() {
        AuthController::checkAyuntamientoAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idGrupo = $_POST['idGrupo'] ?? null;
            $idVoluntario = $_POST['idVoluntario'] ?? null;

            $grupo = $this->grupoModel->getByIdWithDetails($idGrupo);
            if ($grupo && $grupo->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                if ($this->grupoModel->addMember($idGrupo, $idVoluntario, false)) {
                    $_SESSION['success_message'] = 'Voluntario añadido al grupo.';
                } else {
                    $_SESSION['error_message'] = 'Error al añadir el voluntario.';
                }
                header('Location: ' . url('grupos/show?id=' . $idGrupo));
                exit();
            }
        }
        $_SESSION['error_message'] = 'Acción no permitida.';
        header('Location: ' . url('grupos'));
        exit();
    }

    public function removeMiembro() {
        AuthController::checkAyuntamientoAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idGrupo = $_POST['idGrupo'] ?? null;
            $idVoluntario = $_POST['idVoluntario'] ?? null;

            $grupo = $this->grupoModel->getByIdWithDetails($idGrupo);
            if ($grupo && $grupo->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                if ($this->grupoModel->removeMember($idGrupo, $idVoluntario)) {
                    $_SESSION['success_message'] = 'Voluntario eliminado del grupo.';
                } else {
                    $_SESSION['error_message'] = 'Error al eliminar el voluntario.';
                }
                header('Location: ' . url('grupos/show?id=' . $idGrupo));
                exit();
            }
        }
        $_SESSION['error_message'] = 'Acción no permitida.';
        header('Location: ' . url('grupos'));
        exit();
    }

    public function setResponsable() {
        AuthController::checkAyuntamientoAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idGrupo = $_POST['idGrupo'] ?? null;
            $idVoluntario = $_POST['idVoluntario'] ?? null;

            $grupo = $this->grupoModel->getByIdWithDetails($idGrupo);
            if ($grupo && $grupo->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                if ($this->grupoModel->setResponsible($idGrupo, $idVoluntario, true)) {
                    $_SESSION['success_message'] = 'Se ha asignado un nuevo responsable.';
                } else {
                    $_SESSION['error_message'] = 'Error al asignar el responsable.';
                }
                header('Location: ' . url('grupos/show?id=' . $idGrupo));
                exit();
            }
        }
        $_SESSION['error_message'] = 'Acción no permitida.';
        header('Location: ' . url('grupos'));
        exit();
    }
}