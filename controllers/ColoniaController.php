<?php
// controllers/ColoniaController.php

class ColoniaController {
    private $coloniaModel;
    private $ayuntamientoModel; // Necesario para obtener el id del ayuntamiento logueado

    public function __construct() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden gestionar colonias
        $this->coloniaModel = new Colonia();
        $this->ayuntamientoModel = new Ayuntamiento();
    }

    public function index() {
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $colonias = $this->coloniaModel->getAllWithDetails($ayuntamiento_id);
        require_once __DIR__ . '/../views/colonias/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../views/colonias/form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descripcion = $_POST['descripcion'] ?? '';
            $comentarios = $_POST['comentarios'] ?? '';
            $latitud = $_POST['latitud'] ?? '';
            $longitud = $_POST['longitud'] ?? '';
            $ubicacion_descripcion = $_POST['ubicacion_descripcion'] ?? '';
            $idAyuntamiento = $_SESSION['ayuntamiento_id'];

            if ($this->coloniaModel->createColonia($descripcion, $comentarios, $latitud, $longitud, $ubicacion_descripcion, $idAyuntamiento)) {
                $_SESSION['success_message'] = 'Colonia creada correctamente.';
                header('Location: ' . url('colonias'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al crear la colonia.';
            }
        }
        header('Location: ' . url('colonias/create'));
        exit();
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $colonia = $this->coloniaModel->getByIdWithDetails($id);
            if ($colonia && $colonia->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                // Cargar los gatos de la colonia
                $gatoModel = new Gato();
                $gatos = $gatoModel->findByColoniaId($id);

                require_once __DIR__ . '/../views/colonias/show.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Colonia no encontrada o no tienes permisos para verla.';
        header('Location: ' . url('colonias'));
        exit();
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $colonia = $this->coloniaModel->getByIdWithDetails($id);
            if ($colonia && $colonia->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                require_once __DIR__ . '/../views/colonias/form.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Colonia no encontrada o no tienes permisos para editarla.';
        header('Location: ' . url('colonias'));
        exit();
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idColonia = $_POST['idColonia'] ?? null;
            $descripcion = $_POST['descripcion'] ?? '';
            $comentarios = $_POST['comentarios'] ?? '';
            $latitud = $_POST['latitud'] ?? '';
            $longitud = $_POST['longitud'] ?? '';
            $ubicacion_descripcion = $_POST['ubicacion_descripcion'] ?? '';
            $idUbicacion = $_POST['idUbicacion'] ?? null;
            $idCoordenada = $_POST['idCoordenada'] ?? null;

            // Verificar que la colonia pertenece al ayuntamiento logueado
            $colonia = $this->coloniaModel->getByIdWithDetails($idColonia);
            if (!$colonia || $colonia->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para actualizar esta colonia.';
                header('Location: ' . url('colonias'));
                exit();
            }

            if ($this->coloniaModel->updateColonia($idColonia, $descripcion, $comentarios, $latitud, $longitud, $ubicacion_descripcion, $idUbicacion, $idCoordenada)) {
                $_SESSION['success_message'] = 'Colonia actualizada correctamente.';
                header('Location: ' . url('colonias'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al actualizar la colonia.';
            }
        }
        header('Location: ' . url('colonias'));
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idColonia = $_POST['idColonia'] ?? null;
            $idUbicacion = $_POST['idUbicacion'] ?? null;
            $idCoordenada = $_POST['idCoordenada'] ?? null;

            // Verificar que la colonia pertenece al ayuntamiento logueado
            $colonia = $this->coloniaModel->getByIdWithDetails($idColonia);
            if (!$colonia || $colonia->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para eliminar esta colonia.';
                header('Location: ' . url('colonias'));
                exit();
            }

            if ($this->coloniaModel->deleteColonia($idColonia, $idUbicacion, $idCoordenada)) {
                $_SESSION['success_message'] = 'Colonia eliminada correctamente.';
                header('Location: ' . url('colonias'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al eliminar la colonia.';
            }
        }
        header('Location: ' . url('colonias'));
        exit();
    }
}