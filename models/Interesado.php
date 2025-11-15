<?php
// models/Interesado.php

class Interesado extends BaseModel {
    protected $table_name = 'Interesado';

    public function getAllByAyuntamiento($idAyuntamiento) {
        $query = "
            SELECT
                i.idInteresado,
                i.DNI,
                i.nombreCompleto,
                i.email,
                i.telefono,
                CASE
                    WHEN p.idVoluntario IS NOT NULL THEN 'Voluntario Activo'
                    WHEN v.idVoluntario IS NOT NULL THEN 'Voluntario Inactivo'
                    ELSE 'Interesado'
                END AS estado
            FROM
                Interesado i
            JOIN
                InteresadoBolsa ib ON i.idInteresado = ib.idInteresado
            JOIN
                BolsaMunicipal bm ON ib.idBolsaMunicipal = bm.idBolsaMunicipal
            LEFT JOIN
                Voluntario v ON i.idInteresado = v.idInteresado
            LEFT JOIN
                -- Usamos una subconsulta para saber si un voluntario está en al menos un grupo
                (SELECT DISTINCT idVoluntario FROM Pertenencia) p ON v.idVoluntario = p.idVoluntario
            WHERE
                bm.idAyuntamiento = :idAyuntamiento
            -- Agrupamos por el interesado para asegurar una fila por persona
            GROUP BY i.idInteresado
            ORDER BY
                i.nombreCompleto ASC
        ";
        return $this->executeQuery($query, [':idAyuntamiento' => $idAyuntamiento]);
    }

    public function getById($id) {
        $query = "
            SELECT
                i.idInteresado,
                i.DNI,
                i.nombreCompleto,
                i.email,
                i.telefono,
                CASE WHEN v.idVoluntario IS NOT NULL THEN TRUE ELSE FALSE END AS es_voluntario
            FROM
                Interesado i
            LEFT JOIN
                Voluntario v ON i.idInteresado = v.idInteresado
            WHERE
                i.idInteresado = :id
            LIMIT 0,1
        ";
        return $this->executeQuery($query, [':id' => $id], false);
    }

    public function createInteresado($DNI, $nombreCompleto, $email, $telefono, $idAyuntamiento) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar Interesado
            $queryInteresado = "INSERT INTO Interesado (DNI, nombreCompleto, email, telefono) VALUES (:DNI, :nombreCompleto, :email, :telefono)";
            $stmtInteresado = $this->conn->prepare($queryInteresado);
            $stmtInteresado->bindParam(':DNI', $DNI);
            $stmtInteresado->bindParam(':nombreCompleto', $nombreCompleto);
            $stmtInteresado->bindParam(':email', $email);
            $stmtInteresado->bindParam(':telefono', $telefono);
            $stmtInteresado->execute();
            $idInteresado = $this->conn->lastInsertId();

            // 2. Asociar a la Bolsa Municipal del Ayuntamiento
            $queryBolsa = "SELECT idBolsaMunicipal FROM BolsaMunicipal WHERE idAyuntamiento = :idAyuntamiento LIMIT 0,1";
            $stmtBolsa = $this->conn->prepare($queryBolsa);
            $stmtBolsa->bindParam(':idAyuntamiento', $idAyuntamiento);
            $stmtBolsa->execute();
            $bolsa = $stmtBolsa->fetch(PDO::FETCH_OBJ);

            if ($bolsa) {
                $queryInteresadoBolsa = "INSERT INTO InteresadoBolsa (idBolsaMunicipal, idInteresado) VALUES (:idBolsaMunicipal, :idInteresado)";
                $stmtInteresadoBolsa = $this->conn->prepare($queryInteresadoBolsa);
                $stmtInteresadoBolsa->bindParam(':idBolsaMunicipal', $bolsa->idBolsaMunicipal);
                $stmtInteresadoBolsa->bindParam(':idInteresado', $idInteresado);
                $stmtInteresadoBolsa->execute();
            } else {
                throw new Exception("No se encontró BolsaMunicipal para el ayuntamiento.");
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al crear interesado: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error lógico al crear interesado: " . $e->getMessage());
            return false;
        }
    }

    public function updateInteresado($idInteresado, $DNI, $nombreCompleto, $email, $telefono) {
        $query = "UPDATE " . $this->table_name . " SET DNI = :DNI, nombreCompleto = :nombreCompleto, email = :email, telefono = :telefono WHERE idInteresado = :idInteresado";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':DNI', $DNI);
        $stmt->bindParam(':nombreCompleto', $nombreCompleto);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':idInteresado', $idInteresado);
        return $stmt->execute();
    }

    public function deleteInteresado($idInteresado) {
        try {
            $this->conn->beginTransaction();

            // Eliminar de InteresadoBolsa
            $queryIB = "DELETE FROM InteresadoBolsa WHERE idInteresado = :idInteresado";
            $stmtIB = $this->conn->prepare($queryIB);
            $stmtIB->bindParam(':idInteresado', $idInteresado);
            $stmtIB->execute();

            // Si es voluntario, eliminar de Voluntario y Pertenencia
            $queryVoluntario = "SELECT idVoluntario FROM Voluntario WHERE idInteresado = :idInteresado LIMIT 0,1";
            $stmtVoluntario = $this->conn->prepare($queryVoluntario);
            $stmtVoluntario->bindParam(':idInteresado', $idInteresado);
            $stmtVoluntario->execute();
            $voluntario = $stmtVoluntario->fetch(PDO::FETCH_OBJ);

            if ($voluntario) {
                $queryPertenencia = "DELETE FROM Pertenencia WHERE idVoluntario = :idVoluntario";
                $stmtPertenencia = $this->conn->prepare($queryPertenencia);
                $stmtPertenencia->bindParam(':idVoluntario', $voluntario->idVoluntario);
                $stmtPertenencia->execute();

                $queryDeleteVoluntario = "DELETE FROM Voluntario WHERE idVoluntario = :idVoluntario";
                $stmtDeleteVoluntario = $this->conn->prepare($queryDeleteVoluntario);
                $stmtDeleteVoluntario->bindParam(':idVoluntario', $voluntario->idVoluntario);
                $stmtDeleteVoluntario->execute();
            }

            // Eliminar Interesado
            $queryInteresado = "DELETE FROM Interesado WHERE idInteresado = :idInteresado";
            $stmtInteresado = $this->conn->prepare($queryInteresado);
            $stmtInteresado->bindParam(':idInteresado', $idInteresado);
            $stmtInteresado->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar interesado: " . $e->getMessage());
            return false;
        }
    }

    public function belongsToAyuntamiento($idInteresado, $idAyuntamiento) {
        $query = "
            SELECT 1
            FROM InteresadoBolsa ib
            JOIN BolsaMunicipal bm ON ib.idBolsaMunicipal = bm.idBolsaMunicipal
            WHERE ib.idInteresado = :idInteresado AND bm.idAyuntamiento = :idAyuntamiento
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idInteresado', $idInteresado);
        $stmt->bindParam(':idAyuntamiento', $idAyuntamiento);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Crea un nuevo interesado sin asociarlo a una bolsa municipal inicialmente.
     * Utilizado para el formulario de inscripción pública.
     * @param string $DNI
     * @param string $nombreCompleto
     * @param string $email
     * @param string $telefono
     * @return int|false El ID del interesado creado o false en caso de error.
     */
    public function createInteresadoPublic($DNI, $nombreCompleto, $email, $telefono) {
        try {
            $queryInteresado = "INSERT INTO Interesado (DNI, nombreCompleto, email, telefono) VALUES (:DNI, :nombreCompleto, :email, :telefono)";
            $stmtInteresado = $this->conn->prepare($queryInteresado);
            $stmtInteresado->bindParam(':DNI', $DNI);
            $stmtInteresado->bindParam(':nombreCompleto', $nombreCompleto);
            $stmtInteresado->bindParam(':email', $email);
            $stmtInteresado->bindParam(':telefono', $telefono);
            $stmtInteresado->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al crear interesado público: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Asocia un interesado a una bolsa municipal.
     * @param int $idInteresado
     * @param int $idBolsaMunicipal
     * @return bool
     */
    public function addInteresadoToBolsa($idInteresado, $idBolsaMunicipal) {
        try {
            $queryInteresadoBolsa = "INSERT INTO InteresadoBolsa (idBolsaMunicipal, idInteresado) VALUES (:idBolsaMunicipal, :idInteresado)";
            $stmtInteresadoBolsa = $this->conn->prepare($queryInteresadoBolsa);
            $stmtInteresadoBolsa->bindParam(':idBolsaMunicipal', $idBolsaMunicipal);
            $stmtInteresadoBolsa->bindParam(':idInteresado', $idInteresado);
            return $stmtInteresadoBolsa->execute();
        } catch (PDOException $e) {
            error_log("Error al añadir interesado a bolsa: " . $e->getMessage());
            return false;
        }
    }
}
