<?php
// controllers/TipoIncidenciaController.php

class TipoIncidenciaController {
    private $incidenciaModel;

    public function __construct() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden gestionar tipos de incidencia
        $this->incidenciaModel = new Incidencia();
    }

    public function index() {
        $tipos = $this->incidenciaModel->getTipos();
        require_once __DIR__ . '/../views/tipos_incidencia/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../views/tipos_incidencia/form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $textoDescriptivo = $_POST['textoDescriptivo'] ?? null;
            if (!empty($textoDescriptivo)) {
                if ($this->incidenciaModel->createTipo($textoDescriptivo)) {
                    $_SESSION['success_message'] = 'Tipo de incidencia creado correctamente.';
                } else {
                    $_SESSION['error_message'] = 'Error al crear el tipo de incidencia.';
                }
            } else {
                $_SESSION['error_message'] = 'La descripción no puede estar vacía.';
            }
        }
        header('Location: ' . url('tipos_incidencia'));
        exit();
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $tipo = $this->incidenciaModel->getTipoById($id);
            if ($tipo) {
                require_once __DIR__ . '/../views/tipos_incidencia/form.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Tipo de incidencia no encontrado.';
        header('Location: ' . url('tipos_incidencia'));
        exit();
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $textoDescriptivo = $_POST['textoDescriptivo'] ?? null;

            if ($id && !empty($textoDescriptivo)) {
                if ($this->incidenciaModel->updateTipo($id, $textoDescriptivo)) {
                    $_SESSION['success_message'] = 'Tipo de incidencia actualizado correctamente.';
                } else {
                    $_SESSION['error_message'] = 'Error al actualizar el tipo de incidencia.';
                }
            } else {
                $_SESSION['error_message'] = 'Faltan datos para actualizar.';
            }
        }
        header('Location: ' . url('tipos_incidencia'));
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                try {
                    if ($this->incidenciaModel->deleteTipo($id)) {
                        $_SESSION['success_message'] = 'Tipo de incidencia eliminado correctamente.';
                    } else {
                        $_SESSION['error_message'] = 'Error al eliminar el tipo de incidencia.';
                    }
                } catch (PDOException $e) {
                    // Error 1451: Foreign key constraint fails
                    if ($e->getCode() == '23000') {
                        $_SESSION['error_message'] = 'No se puede eliminar este tipo de incidencia porque ya está en uso en una o más incidencias ocurridas.';
                    } else {
                        $_SESSION['error_message'] = 'Error de base de datos: ' . $e->getMessage();
                    }
                }
            }
        }
        header('Location: ' . url('tipos_incidencia'));
        exit();
    }
}
