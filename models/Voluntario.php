<?php
// models/Voluntario.php

class Voluntario extends BaseModel {
    protected $table_name = 'Voluntario';

    public function findByUsername($username) {
        $query = "SELECT v.*, i.nombreCompleto, i.email FROM " . $this->table_name . " v JOIN Interesado i ON v.idInteresado = i.idInteresado WHERE v.usuario = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function create($usuario, $contrasenya, $idInteresado) {
        $query = "INSERT INTO " . $this->table_name . " (usuario, contrasenya, idInteresado) VALUES (:usuario, :contrasenya, :idInteresado)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasenya', $contrasenya); // Contraseña en texto plano
        $stmt->bindParam(':idInteresado', $idInteresado);
        return $stmt->execute();
    }

    public function update($idVoluntario, $usuario, $contrasenya, $idInteresado) {
        $query = "UPDATE " . $this->table_name . " SET usuario = :usuario, contrasenya = :contrasenya, idInteresado = :idInteresado WHERE idVoluntario = :idVoluntario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasenya', $contrasenya); // Contraseña en texto plano
        $stmt->bindParam(':idInteresado', $idInteresado);
        $stmt->bindParam(':idVoluntario', $idVoluntario);
        return $stmt->execute();
    }

    public function getAllByAyuntamiento($idAyuntamiento) {
        $query = "
            SELECT v.idVoluntario, i.nombreCompleto, v.usuario
            FROM Voluntario v
            JOIN Interesado i ON v.idInteresado = i.idInteresado
            JOIN InteresadoBolsa ib ON i.idInteresado = ib.idInteresado
            JOIN BolsaMunicipal bm ON ib.idBolsaMunicipal = bm.idBolsaMunicipal
            WHERE bm.idAyuntamiento = :idAyuntamiento
            ORDER BY i.nombreCompleto ASC
        ";
        return $this->executeQuery($query, [':idAyuntamiento' => $idAyuntamiento]);
    }

    public function getDisponiblesParaGrupo($idGrupo, $idAyuntamiento) {
        $query = "
            SELECT v.idVoluntario, i.nombreCompleto, v.usuario
            FROM Voluntario v
            JOIN Interesado i ON v.idInteresado = i.idInteresado
            JOIN InteresadoBolsa ib ON i.idInteresado = ib.idInteresado
            JOIN BolsaMunicipal bm ON ib.idBolsaMunicipal = bm.idBolsaMunicipal
            WHERE bm.idAyuntamiento = :idAyuntamiento
            AND v.idVoluntario NOT IN (
                SELECT idVoluntario FROM Pertenencia WHERE idGrupo = :idGrupo
            )
            ORDER BY i.nombreCompleto ASC
        ";
        return $this->executeQuery($query, [':idGrupo' => $idGrupo, ':idAyuntamiento' => $idAyuntamiento]);
    }

    /**
     * Obtiene los grupos a los que pertenece un voluntario.
     * @param int $idVoluntario
     * @return array
     */
    public function getGrupos($idVoluntario) {
        $query = "
            SELECT g.idGrupo, g.nombreGrupo, p.es_responsable
            FROM Grupo g
            JOIN Pertenencia p ON g.idGrupo = p.idGrupo
            WHERE p.idVoluntario = :idVoluntario
            ORDER BY g.nombreGrupo ASC
        ";
        return $this->executeQuery($query, [':idVoluntario' => $idVoluntario]);
    }

    /**
     * Obtiene las tareas asignadas a los grupos de un voluntario.
     * @param int $idVoluntario
     * @return array
     */
    public function getTareas($idVoluntario) {
        $query = "
            SELECT t.idTrabajo, t.descripcionTrabajo, t.completado, g.nombreGrupo
            FROM Trabajo t
            JOIN Grupo g ON t.idGrupo = g.idGrupo
            WHERE t.idGrupo IN (
                SELECT idGrupo FROM Pertenencia WHERE idVoluntario = :idVoluntario
            )
            ORDER BY t.completado ASC, g.nombreGrupo ASC
        ";
        return $this->executeQuery($query, [':idVoluntario' => $idVoluntario]);
    }

    /**
     * Obtiene las visitas asignadas a un voluntario.
     * @param int $idVoluntario
     * @return array
     */
    public function getVisitas($idVoluntario) {
        $query = "
            SELECT v.idVisita, v.fechaVisita, c.descripcion as nombreColonia
            FROM Visita v
            JOIN VisitaVoluntario vv ON v.idVisita = vv.idVisita
            JOIN Colonia c ON v.idColonia = c.idColonia
            WHERE vv.idVoluntario = :idVoluntario
            ORDER BY v.fechaVisita DESC
        ";
        return $this->executeQuery($query, [':idVoluntario' => $idVoluntario]);
    }

    /**
     * Comprueba si un voluntario es responsable de al menos un grupo.
     * @param int $idVoluntario
     * @return bool
     */
    public function isResponsable($idVoluntario) {
        $query = "SELECT 1 FROM Pertenencia WHERE idVoluntario = :idVoluntario AND es_responsable = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idVoluntario', $idVoluntario);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Obtiene los grupos de los que un voluntario es responsable.
     * @param int $idVoluntario
     * @return array
     */
    public function getGruposResponsable($idVoluntario) {
        $query = "
            SELECT g.idGrupo, g.nombreGrupo
            FROM Grupo g
            JOIN Pertenencia p ON g.idGrupo = p.idGrupo
            WHERE p.idVoluntario = :idVoluntario AND p.es_responsable = 1
            ORDER BY g.nombreGrupo ASC
        ";
        return $this->executeQuery($query, [':idVoluntario' => $idVoluntario]);
    }

    /**
     * Obtiene los voluntarios que pertenecen a los grupos gestionados por un responsable.
     * @param int $idResponsable El ID del voluntario que es responsable.
     * @return array
     */
    public function getVoluntariosManagedByResponsable($idResponsable) {
        $query = "
            SELECT DISTINCT v.idVoluntario, i.nombreCompleto, v.usuario
            FROM Voluntario v
            JOIN Interesado i ON v.idInteresado = i.idInteresado
            JOIN Pertenencia p_voluntario ON v.idVoluntario = p_voluntario.idVoluntario
            WHERE p_voluntario.idGrupo IN (
                SELECT idGrupo
                FROM Pertenencia
                WHERE idVoluntario = :idResponsable AND es_responsable = 1
            )
            ORDER BY i.nombreCompleto ASC
        ";
        return $this->executeQuery($query, [':idResponsable' => $idResponsable]);
    }
}