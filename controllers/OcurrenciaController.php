<?php
// controllers/OcurrenciaController.php

class OcurrenciaController {
    private $incidenciaModel;
    private $visitaModel;
    private $gatoModel;

    public function __construct() {
        AuthController::checkAuth(); // Ambos tipos de usuario pueden ver incidencias
        $this->incidenciaModel = new Incidencia();
        $this->visitaModel = new Visita();
        $this->gatoModel = new Gato();
    }

    public function index() {
        $user_type = $_SESSION['user_type'];
        $user_id = $_SESSION['user_id'];
        $incidencias = $this->incidenciaModel->getOcurrenciasWithDetails($user_type, $user_id);
        require_once __DIR__ . '/../views/ocurrencias/list.php';
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $incidencia = $this->incidenciaModel->getOcurrenciaById($id);
            if ($incidencia) {
                // Verificar permisos
                if ($_SESSION['user_type'] === 'ayuntamiento' && $incidencia->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                    $_SESSION['error_message'] = 'No tienes permisos para ver esta incidencia.';
                    header('Location: ' . url('ocurrencias'));
                    exit();
                }
                // Para voluntarios, se debería verificar si el voluntario está asociado a la visita de la incidencia
                // Por simplicidad, si es voluntario y la incidencia existe, se muestra.
                // Una implementación más robusta requeriría una consulta para verificar VisitaVoluntario.

                require_once __DIR__ . '/../views/ocurrencias/show.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Incidencia no encontrada.';
        header('Location: ' . url('ocurrencias'));
        exit();
    }

    public function edit() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden editar incidencias
        $id = $_GET['id'] ?? null;
        if ($id) {
            $incidencia = $this->incidenciaModel->getByIdWithDetails($id);
            if ($incidencia && $incidencia->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
                $visitas = $this->visitaModel->getAllWithDetails('ayuntamiento', $ayuntamiento_id);
                $gatos = $this->gatoModel->getAllWithDetails($ayuntamiento_id);
                require_once __DIR__ . '/../views/ocurrencias/form.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Incidencia no encontrada o no tienes permisos para editarla.';
        header('Location: ' . url('ocurrencias'));
        exit();
    }

    public function update() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden actualizar incidencias
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idIncidencia = $_POST['idIncidencia'] ?? null;
            $textoDescriptivo = $_POST['textoDescriptivo'] ?? null;
            $idVisita = $_POST['idVisita'] ?? null;
            $idGato = $_POST['idGato'] ?? null;

            // Verificar que la incidencia pertenece al ayuntamiento logueado
            $incidenciaExistente = $this->incidenciaModel->getByIdWithDetails($idIncidencia);
            if (!$incidenciaExistente || $incidenciaExistente->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para actualizar esta incidencia.';
                header('Location: ' . url('ocurrencias'));
                exit();
            }

            if ($this->incidenciaModel->updateIncidencia($idIncidencia, $textoDescriptivo, $idVisita, $idGato)) {
                $_SESSION['success_message'] = 'Incidencia actualizada correctamente.';
                header('Location: ' . url('ocurrencias'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al actualizar la incidencia.';
            }
        }
        header('Location: ' . url('ocurrencias'));
        exit();
    }

    public function delete() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden eliminar incidencias
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idIncidencia = $_POST['idIncidencia'] ?? null;

            // Verificar que la incidencia pertenece al ayuntamiento logueado
            $incidenciaExistente = $this->incidenciaModel->getByIdWithDetails($idIncidencia);
            if (!$incidenciaExistente || $incidenciaExistente->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para eliminar esta incidencia.';
                header('Location: ' . url('ocurrencias'));
                exit();
            }

            if ($this->incidenciaModel->deleteIncidencia($idIncidencia)) {
                $_SESSION['success_message'] = 'Incidencia eliminada correctamente.';
                header('Location: ' . url('ocurrencias'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al eliminar la incidencia.';
            }
        }
        header('Location: ' . url('ocurrencias'));
        exit();
    }
}