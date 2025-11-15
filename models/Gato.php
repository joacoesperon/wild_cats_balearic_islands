<?php
// models/Gato.php

class Gato extends BaseModel {
    protected $table_name = 'Gato';

    public function getAllWithDetails($ayuntamiento_id = null) {
        $query = "
            SELECT
                g.idGato,
                g.nombre,
                g.descripcionAspecto,
                g.numeroChip,
                g.foto,
                s.sexo,
                c.descripcion AS colonia_actual,
                c.idColonia AS idColoniaActual
            FROM
                Gato g
            JOIN
                Sexo s ON g.idSexo = s.idSexo
            LEFT JOIN
                Estancia e ON g.idGato = e.idGato AND e.fechaFin IS NULL
            LEFT JOIN
                Colonia c ON e.idColonia = c.idColonia
        ";
        $params = [];
        if ($ayuntamiento_id !== null) {
            $query .= " WHERE c.idAyuntamiento = :ayuntamiento_id";
            $params[':ayuntamiento_id'] = $ayuntamiento_id;
        }
        $query .= " ORDER BY g.nombre ASC";
        return $this->executeQuery($query, $params);
    }

    public function getByIdWithDetails($id) {
        $query = "
            SELECT
                g.idGato,
                g.nombre,
                g.descripcionAspecto,
                g.numeroChip,
                g.foto,
                g.idSexo,
                s.sexo,
                c.idColonia AS idColoniaActual,
                c.descripcion AS colonia_actual,
                e.fechaInicio AS fechaInicioEstancia
            FROM
                Gato g
            JOIN
                Sexo s ON g.idSexo = s.idSexo
            LEFT JOIN
                Estancia e ON g.idGato = e.idGato AND e.fechaFin IS NULL
            LEFT JOIN
                Colonia c ON e.idColonia = c.idColonia
            WHERE
                g.idGato = :id
            LIMIT 0,1
        ";
        return $this->executeQuery($query, [':id' => $id], false);
    }

    public function getEstanciasByGatoId($idGato) {
        $query = "
            SELECT
                e.fechaInicio,
                e.fechaFin,
                c.descripcion AS colonia_nombre,
                a.nombreLocalidad AS ayuntamiento_nombre
            FROM
                Estancia e
            JOIN
                Colonia c ON e.idColonia = c.idColonia
            JOIN
                Ayuntamiento a ON c.idAyuntamiento = a.idAyuntamiento
            WHERE
                e.idGato = :idGato
            ORDER BY
                e.fechaInicio DESC
        ";
        return $this->executeQuery($query, [':idGato' => $idGato]);
    }

    public function createGato($nombre, $descripcionAspecto, $numeroChip, $foto, $idSexo, $idColonia) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar Gato
            $queryGato = "INSERT INTO Gato (nombre, descripcionAspecto, numeroChip, foto, idSexo) VALUES (:nombre, :descripcionAspecto, :numeroChip, :foto, :idSexo)";
            $stmtGato = $this->conn->prepare($queryGato);
            $stmtGato->bindParam(':nombre', $nombre);
            $stmtGato->bindParam(':descripcionAspecto', $descripcionAspecto);
            $stmtGato->bindParam(':numeroChip', $numeroChip);
            $stmtGato->bindParam(':foto', $foto);
            $stmtGato->bindParam(':idSexo', $idSexo);
            $stmtGato->execute();
            $idGato = $this->conn->lastInsertId();

            // 2. Crear Estancia inicial
            if ($idColonia) {
                $queryEstancia = "INSERT INTO Estancia (fechaInicio, idGato, idColonia) VALUES (CURDATE(), :idGato, :idColonia)";
                $stmtEstancia = $this->conn->prepare($queryEstancia);
                $stmtEstancia->bindParam(':idGato', $idGato);
                $stmtEstancia->bindParam(':idColonia', $idColonia);
                $stmtEstancia->execute();
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al crear gato: " . $e->getMessage());
            return false;
        }
    }

    public function updateGato($idGato, $nombre, $descripcionAspecto, $numeroChip, $foto, $idSexo, $newIdColonia, $oldIdColonia) {
        try {
            $this->conn->beginTransaction();

            // 1. Actualizar los datos principales del gato
            $queryGato = "UPDATE Gato SET nombre = :nombre, descripcionAspecto = :descripcionAspecto, numeroChip = :numeroChip, foto = :foto, idSexo = :idSexo WHERE idGato = :idGato";
            $stmtGato = $this->conn->prepare($queryGato);
            $stmtGato->bindParam(':nombre', $nombre);
            $stmtGato->bindParam(':descripcionAspecto', $descripcionAspecto);
            $stmtGato->bindParam(':numeroChip', $numeroChip);
            $stmtGato->bindParam(':foto', $foto);
            $stmtGato->bindParam(':idSexo', $idSexo);
            $stmtGato->bindParam(':idGato', $idGato);
            $stmtGato->execute();

            // 2. Gestionar el cambio de colonia si ha cambiado
            if ($newIdColonia != $oldIdColonia) {
                // Finalizar la estancia activa anterior, si existía
                if ($oldIdColonia) {
                    $queryEndEstancia = "UPDATE Estancia SET fechaFin = CURDATE() WHERE idGato = :idGato AND idColonia = :oldIdColonia AND fechaFin IS NULL";
                    $stmtEndEstancia = $this->conn->prepare($queryEndEstancia);
                    $stmtEndEstancia->bindParam(':idGato', $idGato);
                    $stmtEndEstancia->bindParam(':oldIdColonia', $oldIdColonia);
                    $stmtEndEstancia->execute();
                }

                // Iniciar una nueva estancia si se ha asignado una nueva colonia
                if ($newIdColonia) {
                    $queryNewEstancia = "INSERT INTO Estancia (fechaInicio, idGato, idColonia) VALUES (CURDATE(), :idGato, :idColonia)";
                    $stmtNewEstancia = $this->conn->prepare($queryNewEstancia);
                    $stmtNewEstancia->bindParam(':idGato', $idGato);
                    $stmtNewEstancia->bindParam(':idColonia', $newIdColonia);
                    $stmtNewEstancia->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al actualizar gato: " . $e->getMessage());
            return false;
        }
    }

    public function deleteGato($idGato) {
        try {
            $this->conn->beginTransaction();

            // Eliminar estancias asociadas (debido a ON DELETE CASCADE en Estancia)
            $queryEstancia = "DELETE FROM Estancia WHERE idGato = :idGato";
            $stmtEstancia = $this->conn->prepare($queryEstancia);
            $stmtEstancia->bindParam(':idGato', $idGato);
            $stmtEstancia->execute();

            // Eliminar IncidenciaVisita asociadas (si el gato es el afectado)
            $queryIncidenciaVisita = "UPDATE IncidenciaVisita SET idGato = NULL WHERE idGato = :idGato";
            $stmtIncidenciaVisita = $this->conn->prepare($queryIncidenciaVisita);
            $stmtIncidenciaVisita->bindParam(':idGato', $idGato);
            $stmtIncidenciaVisita->execute();

            // Eliminar Gato
            $queryGato = "DELETE FROM Gato WHERE idGato = :idGato";
            $stmtGato = $this->conn->prepare($queryGato);
            $stmtGato->bindParam(':idGato', $idGato);
            $stmtGato->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar gato: " . $e->getMessage());
            return false;
        }
    }

    public function getSexos() {
        $query = "SELECT * FROM Sexo ORDER BY sexo ASC";
        return $this->executeQuery($query);
    }

    public function findByColoniaId($colonia_id) {
        $query = "
            SELECT
                g.idGato,
                g.nombre,
                g.numeroChip,
                g.foto,
                s.sexo
            FROM
                Gato g
            JOIN
                Sexo s ON g.idSexo = s.idSexo
            JOIN
                Estancia e ON g.idGato = e.idGato
            WHERE
                e.idColonia = :colonia_id AND e.fechaFin IS NULL
            ORDER BY
                g.nombre ASC
        ";
        return $this->executeQuery($query, [':colonia_id' => $colonia_id]);
    }

    public function findByChip($numeroChip) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE numeroChip = :numeroChip LIMIT 0,1";
        return $this->executeQuery($query, [':numeroChip' => $numeroChip], false);
    }
}