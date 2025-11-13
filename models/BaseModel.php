<?php
// models/BaseModel.php

class BaseModel {
    protected $conn;
    protected $table_name;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Obtiene todos los registros de la tabla.
     * @return array Un array de objetos con los registros.
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtiene un registro por su ID.
     * @param int $id El ID del registro.
     * @return object|false El objeto del registro o false si no se encuentra.
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id" . ucfirst($this->table_name) . " = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Elimina un registro por su ID.
     * @param int $id El ID del registro a eliminar.
     * @return bool True si se eliminó correctamente, false en caso contrario.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id" . ucfirst($this->table_name) . " = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Ejecuta una consulta SQL y devuelve los resultados.
     * @param string $query La consulta SQL a ejecutar.
     * @param array $params Parámetros para la consulta preparada.
     * @param bool $fetch_all Si se deben obtener todos los resultados o solo uno.
     * @return array|object|false Los resultados de la consulta.
     */
    protected function executeQuery($query, $params = [], $fetch_all = true) {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        if ($fetch_all) {
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
?>