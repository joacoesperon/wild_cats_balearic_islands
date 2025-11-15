<?php
// models/Incidencia.php

class Incidencia extends BaseModel {
    protected $table_name = 'Incidencia';

    public function getAllWithDetails($user_type, $user_id) {
        $query = "
            SELECT
                i.idIncidencia,
                i.textoDescriptivo,
                v.fechaVisita,
                c.descripcion AS colonia_nombre,
                g.nombre AS gato_nombre,
                a.nombreLocalidad AS ayuntamiento_nombre
            FROM
                Incidencia i
            JOIN
                IncidenciaVisita iv ON i.idIncidencia = iv.idIncidencia
            JOIN
                Visita v ON iv.idVisita = v.idVisita
            JOIN
                Colonia c ON v.idColonia = c.idColonia
            JOIN
                Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
            LEFT JOIN
                Gato g ON iv.idGato = g.idGato
        ";
        $params = [];

        if ($user_type === 'ayuntamiento') {
            $query .= " WHERE c.idAyuntamiento = :user_id";
            $params[':user_id'] = $user_id;
        } elseif ($user_type === 'voluntario') {
            // Para voluntarios, solo mostrar incidencias de visitas en las que participó
            $query .= " JOIN VisitaVoluntario vv ON v.idVisita = vv.idVisita WHERE vv.idVoluntario = :user_id";
            $params[':user_id'] = $user_id;
        }
        $query .= " ORDER BY v.fechaVisita DESC, i.idIncidencia DESC";
        return $this->executeQuery($query, $params);
    }

    public function getByIdWithDetails($id) {
        $query = "
            SELECT
                i.idIncidencia,
                i.textoDescriptivo,
                iv.idVisita,
                v.fechaVisita,
                v.idColonia,
                c.descripcion AS colonia_nombre,
                iv.idGato,
                g.nombre AS gato_nombre,
                a.nombreLocalidad AS ayuntamiento_nombre,
                a.idAyuntamiento
            FROM
                Incidencia i
            JOIN
                IncidenciaVisita iv ON i.idIncidencia = iv.idIncidencia
            JOIN
                Visita v ON iv.idVisita = v.idVisita
            JOIN
                Colonia c ON v.idColonia = c.idColonia
            JOIN
                Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
            LEFT JOIN
                Gato g ON iv.idGato = g.idGato
            WHERE
                i.idIncidencia = :id
            LIMIT 0,1
        ";
        return $this->executeQuery($query, [':id' => $id], false);
    }

    public function createIncidencia($textoDescriptivo, $idVisita, $idGato = null) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar Incidencia
            $queryIncidencia = "INSERT INTO Incidencia (textoDescriptivo) VALUES (:textoDescriptivo)";
            $stmtIncidencia = $this->conn->prepare($queryIncidencia);
            $stmtIncidencia->bindParam(':textoDescriptivo', $textoDescriptivo);
            $stmtIncidencia->execute();
            $idIncidencia = $this->conn->lastInsertId();

            // 2. Insertar en IncidenciaVisita
            $queryIV = "INSERT INTO IncidenciaVisita (idIncidencia, idVisita, idGato) VALUES (:idIncidencia, :idVisita, :idGato)";
            $stmtIV = $this->conn->prepare($queryIV);
            $stmtIV->bindParam(':idIncidencia', $idIncidencia);
            $stmtIV->bindParam(':idVisita', $idVisita);
            $stmtIV->bindParam(':idGato', $idGato);
            $stmtIV->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al crear incidencia: " . $e->getMessage());
            return false;
        }
    }

    public function updateIncidencia($idIncidencia, $textoDescriptivo, $idVisita, $idGato = null) {
        try {
            $this->conn->beginTransaction();

            // 1. Actualizar Incidencia
            $queryIncidencia = "UPDATE Incidencia SET textoDescriptivo = :textoDescriptivo WHERE idIncidencia = :idIncidencia";
            $stmtIncidencia = $this->conn->prepare($queryIncidencia);
            $stmtIncidencia->bindParam(':textoDescriptivo', $textoDescriptivo);
            $stmtIncidencia->bindParam(':idIncidencia', $idIncidencia);
            $stmtIncidencia->execute();

            // 2. Actualizar IncidenciaVisita
            $queryIV = "UPDATE IncidenciaVisita SET idVisita = :idVisita, idGato = :idGato WHERE idIncidencia = :idIncidencia";
            $stmtIV = $this->conn->prepare($queryIV);
            $stmtIV->bindParam(':idVisita', $idVisita);
            $stmtIV->bindParam(':idGato', $idGato);
            $stmtIV->bindParam(':idIncidencia', $idIncidencia);
            $stmtIV->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al actualizar incidencia: " . $e->getMessage());
            return false;
        }
    }

    public function deleteIncidencia($idIncidencia) {
        try {
            $this->conn->beginTransaction();

            // Eliminar de IncidenciaVisita
            $queryIV = "DELETE FROM IncidenciaVisita WHERE idIncidencia = :idIncidencia";
            $stmtIV = $this->conn->prepare($queryIV);
            $stmtIV->bindParam(':idIncidencia', $idIncidencia);
            $stmtIV->execute();

            // Eliminar Incidencia
            $queryIncidencia = "DELETE FROM Incidencia WHERE idIncidencia = :idIncidencia";
            $stmtIncidencia = $this->conn->prepare($queryIncidencia);
            $stmtIncidencia->bindParam(':idIncidencia', $idIncidencia);
            $stmtIncidencia->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar incidencia: " . $e->getMessage());
            return false;
        }
    }

    public function findByVisitaId($idVisita) {
        $query = "
            SELECT
                i.idIncidencia,
                i.textoDescriptivo,
                iv.idGato,
                g.nombre AS gato_nombre
            FROM
                Incidencia i
            JOIN
                IncidenciaVisita iv ON i.idIncidencia = iv.idIncidencia
            LEFT JOIN
                Gato g ON iv.idGato = g.idGato
            WHERE
                iv.idVisita = :idVisita
            ORDER BY
                i.idIncidencia DESC
        ";
        return $this->executeQuery($query, [':idVisita' => $idVisita]);
    }

    // --- Métodos para CRUD de Tipos de Incidencia ---

    public function getTipos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY textoDescriptivo ASC";
        return $this->executeQuery($query);
    }

    public function getTipoById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idIncidencia = :id LIMIT 0,1";
        return $this->executeQuery($query, [':id' => $id], false);
    }

    public function createTipo($textoDescriptivo) {
        $query = "INSERT INTO " . $this->table_name . " (textoDescriptivo) VALUES (:textoDescriptivo)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':textoDescriptivo', $textoDescriptivo);
        return $stmt->execute();
    }

    public function updateTipo($id, $textoDescriptivo) {
        $query = "UPDATE " . $this->table_name . " SET textoDescriptivo = :textoDescriptivo WHERE idIncidencia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':textoDescriptivo', $textoDescriptivo);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteTipo($id) {
        // Nota: Esto fallará si el tipo de incidencia está en uso en IncidenciaVisita
        // debido a las restricciones de clave foránea. La UI debería manejar esto.
        $query = "DELETE FROM " . $this->table_name . " WHERE idIncidencia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}