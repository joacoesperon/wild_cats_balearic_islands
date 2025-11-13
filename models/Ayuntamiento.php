<?php
// models/Ayuntamiento.php

class Ayuntamiento extends BaseModel {
    protected $table_name = 'Ayuntamiento';

    public function findByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function create($nombreLocalidad, $usuario, $contrasenya, $idProvincia) {
        $query = "INSERT INTO " . $this->table_name . " (nombreLocalidad, usuario, contrasenya, idProvincia) VALUES (:nombreLocalidad, :usuario, :contrasenya, :idProvincia)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombreLocalidad', $nombreLocalidad);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasenya', $contrasenya); // Contraseña en texto plano
        $stmt->bindParam(':idProvincia', $idProvincia);
        return $stmt->execute();
    }

    public function update($idAyuntamiento, $nombreLocalidad, $usuario, $contrasenya, $idProvincia) {
        $query = "UPDATE " . $this->table_name . " SET nombreLocalidad = :nombreLocalidad, usuario = :usuario, contrasenya = :contrasenya, idProvincia = :idProvincia WHERE idAyuntamiento = :idAyuntamiento";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombreLocalidad', $nombreLocalidad);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasenya', $contrasenya); // Contraseña en texto plano
        $stmt->bindParam(':idProvincia', $idProvincia);
        $stmt->bindParam(':idAyuntamiento', $idAyuntamiento);
        return $stmt->execute();
    }
}