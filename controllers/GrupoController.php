<?php
// controllers/GrupoController.php

class GrupoController {
    private $grupoModel;
    private $voluntarioModel; // Para gestionar miembros del grupo

    public function __construct() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden gestionar grupos
        $this->grupoModel = new Grupo();
        $this->voluntarioModel = new Voluntario();
    }

    public function index() {
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $grupos = $this->grupoModel->getAllByAyuntamiento($ayuntamiento_id);
        require_once __DIR__ . '/../views/grupos/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../views/grupos/form.php';
    }

    public function store() {
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
        $id = $_GET['id'] ?? null;
        if ($id) {
            $grupo = $this->grupoModel->getByIdWithDetails($id);
            if ($grupo && $grupo->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                $miembros = $this->grupoModel->getMembers($id);
                $voluntariosDisponibles = $this->voluntarioModel->getDisponiblesParaGrupo($id, $grupo->idAyuntamiento);
                require_once __DIR__ . '/../views/grupos/show.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Grupo no encontrado o no tienes permisos para verlo.';
        header('Location: ' . url('grupos'));
        exit();
    }

    public function edit() {
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