<?php
// controllers/VisitaController.php

class VisitaController {
    private $visitaModel;
    private $coloniaModel;
    private $voluntarioModel;
    private $incidenciaModel;
    private $gatoModel;

    public function __construct() {
        AuthController::checkAuth(); // Ambos tipos de usuario pueden ver visitas
        $this->visitaModel = new Visita();
        $this->coloniaModel = new Colonia();
        $this->voluntarioModel = new Voluntario();
        $this->incidenciaModel = new Incidencia();
        $this->gatoModel = new Gato();
    }

    public function index() {
        $user_type = $_SESSION['user_type'];
        $user_id = $_SESSION['user_id'];
        $visitas = $this->visitaModel->getAllWithDetails($user_type, $user_id);
        require_once __DIR__ . '/../views/visitas/list.php';
    }

    public function create() {
        AuthController::checkResponsableOrAyuntamientoAuth(); // Ayuntamientos o responsables pueden crear visitas
        $ayuntamiento_id = null; // Inicializar para evitar undefined
        $voluntarios = []; // Inicializar para evitar undefined

        if ($_SESSION['user_type'] === 'ayuntamiento') {
            $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
            $colonias = $this->coloniaModel->getAllWithDetails($ayuntamiento_id);
            $voluntarios = $this->voluntarioModel->getAllByAyuntamiento($ayuntamiento_id);
        } else { // Es un voluntario responsable
            $colonias = $this->coloniaModel->getColoniasForResponsable($_SESSION['user_id']);
            $voluntarios = $this->voluntarioModel->getVoluntariosManagedByResponsable($_SESSION['user_id']);
            // Para el caso de responsables, el $ayuntamiento_id no es único,
            // por lo que la lista inicial de gatos se dejará vacía y se cargará por JS.
        }
        $tipos_incidencia = $this->incidenciaModel->getTipos();
        $gatos = []; // La lista inicial de gatos se carga dinámicamente por JS al seleccionar la colonia
        require_once __DIR__ . '/../views/visitas/form.php';
    }

    public function store() {
        AuthController::checkResponsableOrAyuntamientoAuth(); // Ayuntamientos o responsables pueden crear visitas
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fechaVisita = $_POST['fechaVisita'] ?? null;
            $idColonia = $_POST['idColonia'] ?? null;
            $voluntarios = $_POST['voluntarios'] ?? []; // Array de IDs de voluntarios
            $incidencia_tipos = $_POST['incidencia_tipos'] ?? [];
            $incidencia_gatos = $_POST['incidencia_gatos'] ?? [];

            // Reorganizar incidencias
            $incidencias = [];
            foreach ($incidencia_tipos as $key => $tipo_id) {
                if (!empty($tipo_id)) {
                    $incidencias[] = [
                        'tipo_id' => $tipo_id,
                        'gato_id' => $incidencia_gatos[$key] ?? null
                    ];
                }
            }

            if ($this->visitaModel->createVisitaConIncidencias($fechaVisita, $idColonia, $voluntarios, $incidencias)) {
                $_SESSION['success_message'] = 'Visita y sus incidencias registradas correctamente.';
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
                        if ($_SESSION['user_type'] === 'ayuntamiento') {
                            $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
                            $colonias = $this->coloniaModel->getAllWithDetails($ayuntamiento_id);
                            $voluntarios = $this->voluntarioModel->getAllByAyuntamiento($ayuntamiento_id);
                                } else { // Es un voluntario responsable
                                    $colonias = $this->coloniaModel->getColoniasForResponsable($_SESSION['user_id']);
                                    $voluntarios = $this->voluntarioModel->getVoluntariosManagedByResponsable($_SESSION['user_id']);
                                    // --- DEBUG LOG ---
                                    $log_message = "Timestamp: " . date("Y-m-d H:i:s") . "\n";
                                    $log_message .= "Responsable ID: " . $_SESSION['user_id'] . "\n";
                                    $log_message .= "Voluntarios obtenidos: " . print_r($voluntarios, true) . "\n\n";
                                    file_put_contents(__DIR__ . '/../debug_voluntarios.log', $log_message, FILE_APPEND);
                                    // --- FIN DEBUG LOG ---
                                }                $voluntariosParticipantes = $this->visitaModel->getVoluntariosByVisitaId($id);
                $voluntariosSeleccionados = array_map(function($v) { return $v->idVoluntario; }, $voluntariosParticipantes);

                // No se permite editar incidencias desde aquí en esta versión
                $tipos_incidencia = [];
                $gatos = [];

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

            // La lógica de incidencias no se actualiza en esta pantalla para simplificar
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