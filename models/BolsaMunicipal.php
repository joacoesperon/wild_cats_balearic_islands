<?php
// models/BolsaMunicipal.php

class BolsaMunicipal extends BaseModel {
    protected $table_name = 'BolsaMunicipal';

    /**
     * Busca una Bolsa Municipal por su ID de Ayuntamiento.
     * @param int $idAyuntamiento
     * @return object|false
     */
    public function findByAyuntamientoId($idAyuntamiento) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idAyuntamiento = :idAyuntamiento LIMIT 1";
        return $this->executeQuery($query, [':idAyuntamiento' => $idAyuntamiento], false);
    }
}