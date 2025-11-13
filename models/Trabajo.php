<?php
// models/Trabajo.php

class Trabajo extends BaseModel {
    protected $table_name = 'Trabajo';

    /**
     * Obtiene todas las tareas de un ayuntamiento con detalles del grupo.
     * @param int $idAyuntamiento
     * @return array
     */
    public function getAllByAyuntamiento($idAyuntamiento) {
        $query = "
            SELECT t.idTrabajo, t.descripcionTrabajo, t.completado, g.nombreGrupo
            FROM " . $this->table_name . " t
            JOIN Grupo g ON t.idGrupo = g.idGrupo
            WHERE t.idAyuntamiento = :idAyuntamiento
            ORDER BY t.completado ASC, g.nombreGrupo ASC
        ";
        return $this->executeQuery($query, [':idAyuntamiento' => $idAyuntamiento]);
    }

    /**
     * Obtiene una tarea por su ID con detalles.
     * @param int $idTrabajo
     * @return object|false
     */
    public function getByIdWithDetails($idTrabajo) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idTrabajo = :idTrabajo";
        return $this->executeQuery($query, [':idTrabajo' => $idTrabajo], false);
    }

    /**
     * Crea una nueva tarea.
     * @param string $descripcion
     * @param int $idGrupo
     * @param int $idAyuntamiento
     * @return bool
     */
    public function create($descripcion, $idGrupo, $idAyuntamiento) {
        $query = "INSERT INTO " . $this->table_name . " (descripcionTrabajo, completado, idGrupo, idAyuntamiento) VALUES (:descripcion, 0, :idGrupo, :idAyuntamiento)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->bindParam(':idAyuntamiento', $idAyuntamiento);
        return $stmt->execute();
    }

    /**
     * Actualiza una tarea existente.
     * @param int $idTrabajo
     * @param string $descripcion
     * @param int $idGrupo
     * @param bool $completado
     * @return bool
     */
    public function update($idTrabajo, $descripcion, $idGrupo, $completado) {
        $query = "UPDATE " . $this->table_name . " SET descripcionTrabajo = :descripcion, idGrupo = :idGrupo, completado = :completado WHERE idTrabajo = :idTrabajo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTrabajo', $idTrabajo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->bindParam(':completado', $completado, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    /**
     * Elimina una tarea.
     * @param int $idTrabajo
     * @return bool
     */
    public function delete($idTrabajo) {
        $query = "DELETE FROM " . $this->table_name . " WHERE idTrabajo = :idTrabajo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTrabajo', $idTrabajo);
        return $stmt->execute();
    }

    /**
     * Marca una tarea como completada. (Para voluntarios)
     * @param int $idTrabajo
     * @return bool
     */
    public function marcarComoCompletado($idTrabajo) {
        $query = "UPDATE " . $this->table_name . " SET completado = 1 WHERE idTrabajo = :idTrabajo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTrabajo', $idTrabajo);
        return $stmt->execute();
    }

    /**
     * Verifica si un voluntario pertenece al grupo asignado a una tarea. (Para voluntarios)
     * @param int $idTrabajo
     * @param int $idVoluntario
     * @return bool
     */
    public function verificarPertenenciaVoluntario($idTrabajo, $idVoluntario) {
        $query = "
            SELECT COUNT(*)
            FROM Trabajo t
            JOIN Pertenencia p ON t.idGrupo = p.idGrupo
            WHERE t.idTrabajo = :idTrabajo AND p.idVoluntario = :idVoluntario
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTrabajo', $idTrabajo);
        $stmt->bindParam(':idVoluntario', $idVoluntario);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
