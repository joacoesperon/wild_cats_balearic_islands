<?php
// models/Colonia.php

class Colonia extends BaseModel {
    protected $table_name = 'Colonia';

    public function getAllWithDetails($ayuntamiento_id = null) {
        $query = "
            SELECT
                c.idColonia,
                c.descripcion,
                c.comentarios,
                u.textoDescriptivo AS ubicacion_descripcion,
                coord.latitud,
                coord.longitud,
                a.nombreLocalidad AS ayuntamiento_nombre
            FROM
                Colonia c
            JOIN
                Ubicacion u ON c.idUbicacion = u.idUbicacion
            JOIN
                Coordenada coord ON u.idCoordenada = coord.idCoordenada
            JOIN
                Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
        ";
        $params = [];
        if ($ayuntamiento_id !== null) {
            $query .= " WHERE c.idAyuntamiento = :ayuntamiento_id";
            $params[':ayuntamiento_id'] = $ayuntamiento_id;
        }
        $query .= " ORDER BY c.descripcion ASC";
        return $this->executeQuery($query, $params);
    }

    public function getByIdWithDetails($id) {
        $query = "
            SELECT
                c.idColonia,
                c.descripcion,
                c.comentarios,
                u.textoDescriptivo AS ubicacion_descripcion,
                coord.latitud,
                coord.longitud,
                a.nombreLocalidad AS ayuntamiento_nombre,
                c.idUbicacion,
                u.idCoordenada,
                c.idAyuntamiento
            FROM
                Colonia c
            JOIN
                Ubicacion u ON c.idUbicacion = u.idUbicacion
            JOIN
                Coordenada coord ON u.idCoordenada = coord.idCoordenada
            JOIN
                Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
            WHERE
                c.idColonia = :id
            LIMIT 0,1
        ";
        return $this->executeQuery($query, [':id' => $id], false);
    }

    public function createColonia($descripcion, $comentarios, $latitud, $longitud, $ubicacion_descripcion, $idAyuntamiento) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar Coordenada
            $queryCoord = "INSERT INTO Coordenada (latitud, longitud) VALUES (:latitud, :longitud)";
            $stmtCoord = $this->conn->prepare($queryCoord);
            $stmtCoord->bindParam(':latitud', $latitud);
            $stmtCoord->bindParam(':longitud', $longitud);
            $stmtCoord->execute();
            $idCoordenada = $this->conn->lastInsertId();

            // 2. Insertar Ubicacion
            $queryUbic = "INSERT INTO Ubicacion (textoDescriptivo, idCoordenada) VALUES (:textoDescriptivo, :idCoordenada)";
            $stmtUbic = $this->conn->prepare($queryUbic);
            $stmtUbic->bindParam(':textoDescriptivo', $ubicacion_descripcion);
            $stmtUbic->bindParam(':idCoordenada', $idCoordenada);
            $stmtUbic->execute();
            $idUbicacion = $this->conn->lastInsertId();

            // 3. Insertar Colonia
            $queryColonia = "INSERT INTO Colonia (descripcion, comentarios, idUbicacion, idAyuntamiento) VALUES (:descripcion, :comentarios, :idUbicacion, :idAyuntamiento)";
            $stmtColonia = $this->conn->prepare($queryColonia);
            $stmtColonia->bindParam(':descripcion', $descripcion);
            $stmtColonia->bindParam(':comentarios', $comentarios);
            $stmtColonia->bindParam(':idUbicacion', $idUbicacion);
            $stmtColonia->bindParam(':idAyuntamiento', $idAyuntamiento);
            $stmtColonia->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al crear colonia: " . $e->getMessage());
            return false;
        }
    }

    public function updateColonia($idColonia, $descripcion, $comentarios, $latitud, $longitud, $ubicacion_descripcion, $idUbicacion, $idCoordenada) {
        try {
            $this->conn->beginTransaction();

            // 1. Actualizar Coordenada
            $queryCoord = "UPDATE Coordenada SET latitud = :latitud, longitud = :longitud WHERE idCoordenada = :idCoordenada";
            $stmtCoord = $this->conn->prepare($queryCoord);
            $stmtCoord->bindParam(':latitud', $latitud);
            $stmtCoord->bindParam(':longitud', $longitud);
            $stmtCoord->bindParam(':idCoordenada', $idCoordenada);
            $stmtCoord->execute();

            // 2. Actualizar Ubicacion
            $queryUbic = "UPDATE Ubicacion SET textoDescriptivo = :textoDescriptivo WHERE idUbicacion = :idUbicacion";
            $stmtUbic = $this->conn->prepare($queryUbic);
            $stmtUbic->bindParam(':textoDescriptivo', $ubicacion_descripcion);
            $stmtUbic->bindParam(':idUbicacion', $idUbicacion);
            $stmtUbic->execute();

            // 3. Actualizar Colonia
            $queryColonia = "UPDATE Colonia SET descripcion = :descripcion, comentarios = :comentarios WHERE idColonia = :idColonia";
            $stmtColonia = $this->conn->prepare($queryColonia);
            $stmtColonia->bindParam(':descripcion', $descripcion);
            $stmtColonia->bindParam(':comentarios', $comentarios);
            $stmtColonia->bindParam(':idColonia', $idColonia);
            $stmtColonia->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al actualizar colonia: " . $e->getMessage());
            return false;
        }
    }

    public function deleteColonia($idColonia, $idUbicacion, $idCoordenada) {
        try {
            $this->conn->beginTransaction();

            // Eliminar Colonia (CASCADE se encargará de Estancia)
            $queryColonia = "DELETE FROM Colonia WHERE idColonia = :idColonia";
            $stmtColonia = $this->conn->prepare($queryColonia);
            $stmtColonia->bindParam(':idColonia', $idColonia);
            $stmtColonia->execute();

            // Eliminar Ubicacion (CASCADE se encargará de Coordenada)
            $queryUbic = "DELETE FROM Ubicacion WHERE idUbicacion = :idUbicacion";
            $stmtUbic = $this->conn->prepare($queryUbic);
            $stmtUbic->bindParam(':idUbicacion', $idUbicacion);
            $stmtUbic->execute();

            // Eliminar Coordenada
            $queryCoord = "DELETE FROM Coordenada WHERE idCoordenada = :idCoordenada";
            $stmtCoord = $this->conn->prepare($queryCoord);
            $stmtCoord->bindParam(':idCoordenada', $idCoordenada);
            $stmtCoord->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar colonia: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene las colonias que un voluntario responsable puede visitar.
     * @param int $idVoluntario
     * @return array
     */
    public function getColoniasForResponsable($idVoluntario) {
        $query = "
            SELECT DISTINCT c.idColonia, c.descripcion, a.nombreLocalidad AS ayuntamiento_nombre
            FROM Colonia c
            JOIN Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
            WHERE c.idAyuntamiento IN (
                SELECT DISTINCT g.idAyuntamiento
                FROM Grupo g
                JOIN Pertenencia p ON g.idGrupo = p.idGrupo
                WHERE p.idVoluntario = :idVoluntario AND p.es_responsable = 1
            )
            ORDER BY c.descripcion ASC
        ";
        return $this->executeQuery($query, [':idVoluntario' => $idVoluntario]);
    }
}