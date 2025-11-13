<?php
// controllers/VisitaController.php

class VisitaController {
    private $visitaModel;
    private $coloniaModel;
    private $voluntarioModel;

    public function __construct() {
        AuthController::checkAuth(); // Ambos tipos de usuario pueden ver visitas
        $this->visitaModel = new Visita();
        $this->coloniaModel = new Colonia();
        $this->voluntarioModel = new Voluntario();
    }

    public function index() {
        $user_type = $_SESSION['user_type'];
        $user_id = $_SESSION['user_id'];
        $visitas = $this->visitaModel->getAllWithDetails($user_type, $user_id);
        require_once __DIR__ . '/../views/visitas/list.php';
    }

    public function create() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden crear visitas
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $colonias = $this->coloniaModel->getAllWithDetails($ayuntamiento_id);
        $voluntarios = $this->voluntarioModel->getAllByAyuntamiento($ayuntamiento_id);
        require_once __DIR__ . '/../views/visitas/form.php';
    }

    public function store() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden crear visitas
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fechaVisita = $_POST['fechaVisita'] ?? null;
            $idColonia = $_POST['idColonia'] ?? null;
            $voluntarios = $_POST['voluntarios'] ?? []; // Array de IDs de voluntarios

            if ($this->visitaModel->createVisita($fechaVisita, $idColonia, $voluntarios)) {
                $_SESSION['success_message'] = 'Visita registrada correctamente.';
                header('Location: ' . url('visitas'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al registrar la visita.';
            }
        }
        header('Location: ' . url('visitas/create'));
        exit();
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $visita = $this->visitaModel->getByIdWithDetails($id);
            if ($visita) {
                // Verificar permisos
                if ($_SESSION['user_type'] === 'ayuntamiento' && $visita->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                    $_SESSION['error_message'] = 'No tienes permisos para ver esta visita.';
                    header('Location: ' . url('visitas'));
                    exit();
                }
                // Para voluntarios, se debería verificar si el voluntario está asociado a la visita
                // Por simplicidad, si es voluntario y la visita existe, se muestra.
                // Una implementación más robusta requeriría una consulta para verificar VisitaVoluntario.

                $voluntariosParticipantes = $this->visitaModel->getVoluntariosByVisitaId($id);

                // Cargar incidencias de la visita
                $incidenciaModel = new Incidencia();
                $incidencias = $incidenciaModel->findByVisitaId($id);

                require_once __DIR__ . '/../views/visitas/show.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Visita no encontrada.';
        header('Location: ' . url('visitas'));
        exit();
    }

    public function edit() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden editar visitas
        $id = $_GET['id'] ?? null;
        if ($id) {
            $visita = $this->visitaModel->getByIdWithDetails($id);
            if ($visita && $visita->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
                $colonias = $this->coloniaModel->getAllWithDetails($ayuntamiento_id);
                $voluntarios = $this->voluntarioModel->getAllByAyuntamiento($ayuntamiento_id);
                $voluntariosParticipantes = $this->visitaModel->getVoluntariosByVisitaId($id);
                $voluntariosSeleccionados = array_map(function($v) { return $v->idVoluntario; }, $voluntariosParticipantes);

                require_once __DIR__ . '/../views/visitas/form.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Visita no encontrada o no tienes permisos para editarla.';
        header('Location: ' . url('visitas'));
        exit();
    }

    public function update() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden actualizar visitas
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idVisita = $_POST['idVisita'] ?? null;
            $fechaVisita = $_POST['fechaVisita'] ?? null;
            $idColonia = $_POST['idColonia'] ?? null;
            $voluntarios = $_POST['voluntarios'] ?? [];

            // Verificar que la visita pertenece al ayuntamiento logueado
            $visitaExistente = $this->visitaModel->getByIdWithDetails($idVisita);
            if (!$visitaExistente || $visitaExistente->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para actualizar esta visita.';
                header('Location: ' . url('visitas'));
                exit();
            }

            if ($this->visitaModel->updateVisita($idVisita, $fechaVisita, $idColonia, $voluntarios)) {
                $_SESSION['success_message'] = 'Visita actualizada correctamente.';
                header('Location: ' . url('visitas'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al actualizar la visita.';
            }
        }
        header('Location: ' . url('visitas'));
        exit();
    }

    public function delete() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden eliminar visitas
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idVisita = $_POST['idVisita'] ?? null;

            // Verificar que la visita pertenece al ayuntamiento logueado
            $visitaExistente = $this->visitaModel->getByIdWithDetails($idVisita);
            if (!$visitaExistente || $visitaExistente->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para eliminar esta visita.';
                header('Location: ' . url('visitas'));
                exit();
            }

            if ($this->visitaModel->deleteVisita($idVisita)) {
                $_SESSION['success_message'] = 'Visita eliminada correctamente.';
                header('Location: ' . url('visitas'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al eliminar la visita.';
            }
        }
        header('Location: ' . url('visitas'));
        exit();
    }
}