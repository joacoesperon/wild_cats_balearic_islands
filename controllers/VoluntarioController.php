<?php
// controllers/VoluntarioController.php

class VoluntarioController {
    private $voluntarioModel;

    public function __construct() {
        AuthController::checkVoluntarioAuth(); // Solo voluntarios pueden acceder a su perfil
        $this->voluntarioModel = new Voluntario();
    }

    public function index() {
        // Redirigir al perfil del voluntario logueado
        header('Location: ' . url('voluntarios/show?id=' . $_SESSION['user_id']));
        exit();
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id && $id == $_SESSION['user_id']) { // Asegurar que solo vea su propio perfil
            $voluntario = $this->voluntarioModel->getById($id); // getById de BaseModel
            if ($voluntario) {
                // Obtener información adicional del interesado
                $interesadoModel = new Interesado();
                $interesado = $interesadoModel->getById($voluntario->idInteresado);

                // Obtener grupos, tareas y visitas
                $grupos = $this->voluntarioModel->getGrupos($id);
                $tareas = $this->voluntarioModel->getTareas($id);
                $visitas = $this->voluntarioModel->getVisitas($id);

                require_once __DIR__ . '/../views/voluntarios/show.php';
                return;
            }
        }
        $_SESSION['error_message'] = 'Perfil de voluntario no encontrado o no tienes permisos para verlo.';
        header('Location: ' . url('login')); // Redirigir al login si no tiene permisos
        exit();
    }

    // No hay create, edit, update, delete para voluntarios desde su propio perfil
    // Esas acciones se gestionan desde el bolsín de voluntarios por el ayuntamiento.
}