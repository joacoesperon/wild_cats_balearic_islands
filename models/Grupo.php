<?php
// models/Grupo.php

class Grupo extends BaseModel {
    protected $table_name = 'Grupo';

    public function getAllByAyuntamiento($idAyuntamiento) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idAyuntamiento = :idAyuntamiento ORDER BY nombreGrupo ASC";
        return $this->executeQuery($query, [':idAyuntamiento' => $idAyuntamiento]);
    }

    public function getByIdWithDetails($idGrupo) {
        $query = "
            SELECT
                g.idGrupo,
                g.nombreGrupo,
                g.idAyuntamiento,
                a.nombreLocalidad AS ayuntamiento_nombre
            FROM
                Grupo g
            JOIN
                Ayuntamiento a ON g.idAyuntamiento = a.idAyuntamiento
            WHERE
                g.idGrupo = :idGrupo
            LIMIT 0,1
        ";
        return $this->executeQuery($query, [':idGrupo' => $idGrupo], false);
    }

    public function getMembers($idGrupo) {
        $query = "
            SELECT
                v.idVoluntario,
                v.usuario,
                i.nombreCompleto,
                p.es_responsable
            FROM
                Pertenencia p
            JOIN
                Voluntario v ON p.idVoluntario = v.idVoluntario
            JOIN
                Interesado i ON v.idInteresado = i.idInteresado
            WHERE
                p.idGrupo = :idGrupo
            ORDER BY
                p.es_responsable DESC, i.nombreCompleto ASC
        ";
        return $this->executeQuery($query, [':idGrupo' => $idGrupo]);
    }

    public function createGrupo($nombreGrupo, $idAyuntamiento) {
        $query = "INSERT INTO " . $this->table_name . " (nombreGrupo, idAyuntamiento) VALUES (:nombreGrupo, :idAyuntamiento)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombreGrupo', $nombreGrupo);
        $stmt->bindParam(':idAyuntamiento', $idAyuntamiento);
        return $stmt->execute();
    }

    public function updateGrupo($idGrupo, $nombreGrupo) {
        $query = "UPDATE " . $this->table_name . " SET nombreGrupo = :nombreGrupo WHERE idGrupo = :idGrupo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombreGrupo', $nombreGrupo);
        $stmt->bindParam(':idGrupo', $idGrupo);
        return $stmt->execute();
    }

    public function addMember($idGrupo, $idVoluntario, $es_responsable) {
        $query = "INSERT INTO Pertenencia (idGrupo, idVoluntario, es_responsable) VALUES (:idGrupo, :idVoluntario, :es_responsable)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->bindParam(':idVoluntario', $idVoluntario);
        $stmt->bindParam(':es_responsable', $es_responsable, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function removeMember($idGrupo, $idVoluntario) {
        $query = "DELETE FROM Pertenencia WHERE idGrupo = :idGrupo AND idVoluntario = :idVoluntario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->bindParam(':idVoluntario', $idVoluntario);
        return $stmt->execute();
    }

    public function setResponsible($idGrupo, $idVoluntario, $es_responsable) {
        // Asegurarse de que solo haya un responsable por grupo
        if ($es_responsable) {
            $this->conn->beginTransaction();
            try {
                $queryReset = "UPDATE Pertenencia SET es_responsable = FALSE WHERE idGrupo = :idGrupo";
                $stmtReset = $this->conn->prepare($queryReset);
                $stmtReset->bindParam(':idGrupo', $idGrupo);
                $stmtReset->execute();

                $querySet = "UPDATE Pertenencia SET es_responsable = TRUE WHERE idGrupo = :idGrupo AND idVoluntario = :idVoluntario";
                $stmtSet = $this->conn->prepare($querySet);
                $stmtSet->bindParam(':idGrupo', $idGrupo);
                $stmtSet->bindParam(':idVoluntario', $idVoluntario);
                $stmtSet->execute();
                $this->conn->commit();
                return true;
            } catch (PDOException $e) {
                $this->conn->rollBack();
                error_log("Error al establecer responsable: " . $e->getMessage());
                return false;
            }
        } else {
            $querySet = "UPDATE Pertenencia SET es_responsable = FALSE WHERE idGrupo = :idGrupo AND idVoluntario = :idVoluntario";
            $stmtSet = $this->conn->prepare($querySet);
            $stmtSet->bindParam(':idGrupo', $idGrupo);
            $stmtSet->bindParam(':idVoluntario', $idVoluntario);
            return $stmtSet->execute();
        }
    }

    /**
     * Verifica si un voluntario pertenece a un grupo específico.
     * @param int $idGrupo
     * @param int $idVoluntario
     * @return bool
     */
    public function isMember($idGrupo, $idVoluntario) {
        $query = "SELECT 1 FROM Pertenencia WHERE idGrupo = :idGrupo AND idVoluntario = :idVoluntario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->bindParam(':idVoluntario', $idVoluntario);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }
}