<?php
// controllers/BaseController.php

class BaseController {

    /**
     * Verifica que una entidad pertenece al ayuntamiento actualmente logueado.
     *
     * @param object $model El modelo de la entidad a verificar.
     * @param int $id El ID de la entidad.
     * @param string $redirect_path La ruta a la que redirigir en caso de fallo.
     * @return object La entidad si la verificación es exitosa.
     */
    protected function checkOwnership($model, $id, $redirect_path) {
        if (!$id) {
            $_SESSION['error_message'] = 'ID de entidad no proporcionado.';
            header('Location: ' . url($redirect_path));
            exit();
        }

        $entity = $model->getByIdWithDetails($id);

        if (!$entity || !isset($entity->idAyuntamiento) || $entity->idAyuntamiento != $_SESSION['ayuntamiento_id']) {
            $_SESSION['error_message'] = 'Entidad no encontrada o no tienes permisos para acceder a ella.';
            header('Location: ' . url($redirect_path));
            exit();
        }

        return $entity;
    }
}
