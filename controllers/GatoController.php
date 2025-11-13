<?php
// controllers/GatoController.php

class GatoController {
    private $gatoModel;
    private $coloniaModel;

    public function __construct() {
        AuthController::checkAyuntamientoAuth(); // Solo ayuntamientos pueden gestionar gatos
        $this->gatoModel = new Gato();
        $this->coloniaModel = new Colonia();
    }

    public function index() {
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $gatos = $this->gatoModel->getAllWithDetails($ayuntamiento_id);
        require_once __DIR__ . '/../views/gatos/list.php';
    }

    public function create() {
        $gato = new stdClass(); // Inicializar objeto vacío para el formulario
        $sexos = $this->gatoModel->getSexos();
        $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
        $colonias = $this->coloniaModel->getAllWithDetails($ayuntamiento_id);
        require_once __DIR__ . '/../views/gatos/form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? null;
            $descripcionAspecto = $_POST['descripcionAspecto'] ?? null;
            $numeroChip = $_POST['numeroChip'] ?? null;
            $idSexo = $_POST['idSexo'] ?? null;
            $idColonia = $_POST['idColonia'] ?? null;
            $foto = null;

            // Validación del número de chip
            if (!empty($numeroChip)) {
                if (!ctype_digit($numeroChip) || strlen($numeroChip) !== 4) {
                    $_SESSION['error_message'] = 'El número de chip debe ser de 4 dígitos.';
                    // Recargar datos para el formulario
                    $sexos = $this->gatoModel->getSexos();
                    $colonias = $this->coloniaModel->getAllWithDetails($_SESSION['ayuntamiento_id']);
                    $gato = (object)$_POST; // Para repoblar el formulario
                    require_once __DIR__ . '/../views/gatos/form.php';
                    return;
                }
                $chipExistente = $this->gatoModel->findByChip($numeroChip);
                if ($chipExistente) {
                    $_SESSION['error_message'] = 'El número de chip ya está registrado.';
                    // Recargar datos para el formulario
                    $sexos = $this->gatoModel->getSexos();
                    $colonias = $this->coloniaModel->getAllWithDetails($_SESSION['ayuntamiento_id']);
                    $gato = (object)$_POST; // Para repoblar el formulario
                    require_once __DIR__ . '/../views/gatos/form.php';
                    return;
                }
            }


            // Manejo de la subida de foto
            $uploadDir = __DIR__ . '/../public/uploads/gatos/';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                // Verificar directorio de subida
                if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                    $_SESSION['error_message'] = 'Error de servidor: El directorio de subida de fotos no existe o no tiene permisos de escritura.';
                    $sexos = $this->gatoModel->getSexos();
                    $colonias = $this->coloniaModel->getAllWithDetails($_SESSION['ayuntamiento_id']);
                    $gato = (object)$_POST;
                    require_once __DIR__ . '/../views/gatos/form.php';
                    return;
                }

                $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadFile)) {
                    $foto = $fileName;
                } else {
                    $_SESSION['error_message'] = 'Error al subir la foto.';
                    header('Location: ' . url('gatos/create'));
                    exit();
                }
            }

            if ($this->gatoModel->createGato($nombre, $descripcionAspecto, $numeroChip, $foto, $idSexo, $idColonia)) {
                $_SESSION['success_message'] = 'Gato creado correctamente.';
                header('Location: ' . url('gatos'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al crear el gato.';
            }
        }
        header('Location: ' . url('gatos/create'));
        exit();
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $gato = $this->gatoModel->getByIdWithDetails($id);
            if ($gato) {
                // Verificar que el gato pertenece a una colonia del ayuntamiento logueado
                $colonia = $this->coloniaModel->getByIdWithDetails($gato->idColoniaActual);
                if ($colonia && $colonia->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                    $estancias = $this->gatoModel->getEstanciasByGatoId($id);
                    require_once __DIR__ . '/../views/gatos/show.php';
                    return;
                }
            }
        }
        $_SESSION['error_message'] = 'Gato no encontrado o no tienes permisos para verlo.';
        header('Location: ' . url('gatos'));
        exit();
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $gato = $this->gatoModel->getByIdWithDetails($id);
            if ($gato) {
                // Verificar que el gato pertenece a una colonia del ayuntamiento logueado
                $colonia = $this->coloniaModel->getByIdWithDetails($gato->idColoniaActual);
                if ($colonia && $colonia->idAyuntamiento == $_SESSION['ayuntamiento_id']) {
                    $sexos = $this->gatoModel->getSexos();
                    $ayuntamiento_id = $_SESSION['ayuntamiento_id'];
                    $colonias = $this->coloniaModel->getAllWithDetails($ayuntamiento_id);
                    require_once __DIR__ . '/../views/gatos/form.php';
                    return;
                }
            }
        }
        $_SESSION['error_message'] = 'Gato no encontrado o no tienes permisos para editarlo.';
        header('Location: ' . url('gatos'));
        exit();
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idGato = $_POST['idGato'] ?? null;
            $nombre = $_POST['nombre'] ?? null;
            $descripcionAspecto = $_POST['descripcionAspecto'] ?? null;
            $numeroChip = $_POST['numeroChip'] ?? null;
            $idSexo = $_POST['idSexo'] ?? null;
            $newIdColonia = $_POST['idColonia'] ?? null;
            $oldIdColonia = $_POST['oldIdColonia'] ?? null; // Colonia actual antes de la edición
            $fotoActual = $_POST['fotoActual'] ?? null;
            $foto = $fotoActual;

            // Verificar que el gato pertenece a una colonia del ayuntamiento logueado
            $gatoExistente = $this->gatoModel->getByIdWithDetails($idGato);
            if (!$gatoExistente) {
                $_SESSION['error_message'] = 'Gato no encontrado.';
                header('Location: ' . url('gatos'));
                exit();
            }
            $coloniaExistente = $this->coloniaModel->getByIdWithDetails($gatoExistente->idColoniaActual);
            if (!$coloniaExistente || $coloniaExistente->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para actualizar este gato.';
                header('Location: ' . url('gatos'));
                exit();
            }

            // Validación del número de chip
            if (!empty($numeroChip)) {
                if (!ctype_digit($numeroChip) || strlen($numeroChip) !== 4) {
                    $_SESSION['error_message'] = 'El número de chip debe ser de 4 dígitos.';
                    // Recargar datos para el formulario
                    $sexos = $this->gatoModel->getSexos();
                    $colonias = $this->coloniaModel->getAllWithDetails($_SESSION['ayuntamiento_id']);
                    $gato = (object)$_POST;
                    $gato->idGato = $idGato; // Asegurarse de que el ID se mantiene
                    require_once __DIR__ . '/../views/gatos/form.php';
                    return;
                }
                $chipExistente = $this->gatoModel->findByChip($numeroChip);
                if ($chipExistente && $chipExistente->idGato != $idGato) {
                    $_SESSION['error_message'] = 'El número de chip ya está registrado para otro gato.';
                    // Recargar datos para el formulario
                    $sexos = $this->gatoModel->getSexos();
                    $colonias = $this->coloniaModel->getAllWithDetails($_SESSION['ayuntamiento_id']);
                    $gato = (object)$_POST;
                    $gato->idGato = $idGato;
                    require_once __DIR__ . '/../views/gatos/form.php';
                    return;
                }
            }


            // Manejo de la subida de foto
            $uploadDir = __DIR__ . '/../public/uploads/gatos/';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                // Verificar directorio de subida
                if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                    $_SESSION['error_message'] = 'Error de servidor: El directorio de subida de fotos no existe o no tiene permisos de escritura.';
                    $sexos = $this->gatoModel->getSexos();
                    $colonias = $this->coloniaModel->getAllWithDetails($_SESSION['ayuntamiento_id']);
                    $gato = (object)$_POST;
                    $gato->idGato = $idGato;
                    require_once __DIR__ . '/../views/gatos/form.php';
                    return;
                }

                $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadFile)) {
                    $foto = $fileName;
                    // Opcional: eliminar la foto antigua si existe
                    if ($fotoActual && file_exists($uploadDir . $fotoActual)) {
                        unlink($uploadDir . $fotoActual);
                    }
                } else {
                    $_SESSION['error_message'] = 'Error al subir la nueva foto.';
                    header('Location: ' . url('gatos/edit?id=' . $idGato));
                    exit();
                }
            }

            if ($this->gatoModel->updateGato($idGato, $nombre, $descripcionAspecto, $numeroChip, $foto, $idSexo, $newIdColonia, $oldIdColonia)) {
                $_SESSION['success_message'] = 'Gato actualizado correctamente.';
                header('Location: ' . url('gatos'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al actualizar el gato.';
            }
        }
        header('Location: ' . url('gatos'));
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idGato = $_POST['idGato'] ?? null;

            // Verificar que el gato pertenece a una colonia del ayuntamiento logueado
            $gatoExistente = $this->gatoModel->getByIdWithDetails($idGato);
            if (!$gatoExistente) {
                $_SESSION['error_message'] = 'Gato no encontrado.';
                header('Location: ' . url('gatos'));
                exit();
            }
            $coloniaExistente = $this->coloniaModel->getByIdWithDetails($gatoExistente->idColoniaActual);
            if (!$coloniaExistente || $coloniaExistente->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
                $_SESSION['error_message'] = 'No tienes permisos para eliminar este gato.';
                header('Location: ' . url('gatos'));
                exit();
            }

            if ($this->gatoModel->deleteGato($idGato)) {
                // Opcional: eliminar el archivo de foto del servidor
                if ($gatoExistente->foto) {
                    $filePath = __DIR__ . '/../public/uploads/gatos/' . $gatoExistente->foto;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $_SESSION['success_message'] = 'Gato eliminado correctamente.';
                header('Location: ' . url('gatos'));
                exit();
            } else {
                $_SESSION['error_message'] = 'Error al eliminar el gato.';
            }
        }
        header('Location: ' . url('gatos'));
        exit();
    }
}