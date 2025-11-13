<?php
// models/Visita.php

class Visita extends BaseModel {
    protected $table_name = 'Visita';

    public function getAllWithDetails($user_type, $user_id) {
        $query = "
            SELECT
                v.idVisita,
                v.fechaVisita,
                c.descripcion AS colonia_nombre,
                a.nombreLocalidad AS ayuntamiento_nombre
            FROM
                Visita v
            JOIN
                Colonia c ON v.idColonia = c.idColonia
            JOIN
                Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
        ";
        $params = [];

        if ($user_type === 'ayuntamiento') {
            $query .= " WHERE c.idAyuntamiento = :user_id";
            $params[':user_id'] = $user_id;
        } elseif ($user_type === 'voluntario') {
            $query .= " JOIN VisitaVoluntario vv ON v.idVisita = vv.idVisita WHERE vv.idVoluntario = :user_id";
            $params[':user_id'] = $user_id;
        }
        $query .= " ORDER BY v.fechaVisita DESC";
        return $this->executeQuery($query, $params);
    }

    public function getByIdWithDetails($id) {
        $query = "
            SELECT
                v.idVisita,
                v.fechaVisita,
                v.idColonia,
                c.descripcion AS colonia_nombre,
                a.nombreLocalidad AS ayuntamiento_nombre,
                a.idAyuntamiento
            FROM
                Visita v
            JOIN
                Colonia c ON v.idColonia = c.idColonia
            JOIN
                Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
            WHERE
                v.idVisita = :id
            LIMIT 0,1
        ";
        return $this->executeQuery($query, [':id' => $id], false);
    }

    public function getVoluntariosByVisitaId($idVisita) {
        $query = "
            SELECT
                vv.idVisitaVoluntario,
                vol.idVoluntario,
                i.nombreCompleto AS voluntario_nombre
            FROM
                VisitaVoluntario vv
            JOIN
                Voluntario vol ON vv.idVoluntario = vol.idVoluntario
            JOIN
                Interesado i ON vol.idInteresado = i.idInteresado
            WHERE
                vv.idVisita = :idVisita
            ORDER BY
                i.nombreCompleto ASC
        ";
        return $this->executeQuery($query, [':idVisita' => $idVisita]);
    }

    public function createVisita($fechaVisita, $idColonia, $voluntarios = []) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar Visita
            $queryVisita = "INSERT INTO Visita (fechaVisita, idColonia) VALUES (:fechaVisita, :idColonia)";
            $stmtVisita = $this->conn->prepare($queryVisita);
            $stmtVisita->bindParam(':fechaVisita', $fechaVisita);
            $stmtVisita->bindParam(':idColonia', $idColonia);
            $stmtVisita->execute();
            $idVisita = $this->conn->lastInsertId();

            // 2. Asociar Voluntarios a la Visita
            if (!empty($voluntarios)) {
                $queryVV = "INSERT INTO VisitaVoluntario (idVisita, idVoluntario) VALUES (:idVisita, :idVoluntario)";
                $stmtVV = $this->conn->prepare($queryVV);
                foreach ($voluntarios as $idVoluntario) {
                    $stmtVV->bindParam(':idVisita', $idVisita);
                    $stmtVV->bindParam(':idVoluntario', $idVoluntario);
                    $stmtVV->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al crear visita: " . $e->getMessage());
            return false;
        }
    }

    public function updateVisita($idVisita, $fechaVisita, $idColonia, $voluntarios = []) {
        try {
            $this->conn->beginTransaction();

            // 1. Actualizar Visita
            $queryVisita = "UPDATE Visita SET fechaVisita = :fechaVisita, idColonia = :idColonia WHERE idVisita = :idVisita";
            $stmtVisita = $this->conn->prepare($queryVisita);
            $stmtVisita->bindParam(':fechaVisita', $fechaVisita);
            $stmtVisita->bindParam(':idColonia', $idColonia);
            $stmtVisita->bindParam(':idVisita', $idVisita);
            $stmtVisita->execute();

            // 2. Actualizar Voluntarios asociados (eliminar todos y reinsertar)
            $queryDeleteVV = "DELETE FROM VisitaVoluntario WHERE idVisita = :idVisita";
            $stmtDeleteVV = $this->conn->prepare($queryDeleteVV);
            $stmtDeleteVV->bindParam(':idVisita', $idVisita);
            $stmtDeleteVV->execute();

            if (!empty($voluntarios)) {
                $queryInsertVV = "INSERT INTO VisitaVoluntario (idVisita, idVoluntario) VALUES (:idVisita, :idVoluntario)";
                $stmtInsertVV = $this->conn->prepare($queryInsertVV);
                foreach ($voluntarios as $idVoluntario) {
                    $stmtInsertVV->bindParam(':idVisita', $idVisita);
                    $stmtInsertVV->bindParam(':idVoluntario', $idVoluntario);
                    $stmtInsertVV->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al actualizar visita: " . $e->getMessage());
            return false;
        }
    }

    public function deleteVisita($idVisita) {
        try {
            $this->conn->beginTransaction();

            // Eliminar IncidenciaVisita asociadas
            $queryIncidenciaVisita = "DELETE FROM IncidenciaVisita WHERE idVisita = :idVisita";
            $stmtIncidenciaVisita = $this->conn->prepare($queryIncidenciaVisita);
            $stmtIncidenciaVisita->bindParam(':idVisita', $idVisita);
            $stmtIncidenciaVisita->execute();

            // Eliminar VisitaVoluntario asociadas
            $queryVV = "DELETE FROM VisitaVoluntario WHERE idVisita = :idVisita";
            $stmtVV = $this->conn->prepare($queryVV);
            $stmtVV->bindParam(':idVisita', $idVisita);
            $stmtVV->execute();

            // Eliminar Visita
            $queryVisita = "DELETE FROM Visita WHERE idVisita = :idVisita";
            $stmtVisita = $this->conn->prepare($queryVisita);
            $stmtVisita->bindParam(':idVisita', $idVisita);
            $stmtVisita->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar visita: " . $e->getMessage());
            return false;
        }
    }
}