<?php
// controllers/AyuntamientoController.php

class AyuntamientoController {
    private $ayuntamientoModel;

    public function __construct() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden acceder a su dashboard
        $this->ayuntamientoModel = new Ayuntamiento();
    }

    public function index() {
        // Dashboard del ayuntamiento
        // Aquí se podría mostrar un resumen de colonias, gatos, etc.
        // Por ahora, solo una vista simple de bienvenida.
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $ayuntamiento = $this->ayuntamientoModel->getById($ayuntamiento_id);
        require_once __DIR__ . '/../views/ayuntamientos/dashboard.php';
    }

    // Métodos para CRUD de ayuntamientos (si fueran gestionables por un superadmin, por ejemplo)
    // Por ahora, solo el index es relevante para el ayuntamiento logueado.
}
