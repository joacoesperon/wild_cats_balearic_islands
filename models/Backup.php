<?php
// models/Backup.php

class Backup {

    public static function getAll() {
        global $pdo;
        $sql = 'SELECT id, fecha FROM backups ORDER BY fecha DESC';
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        global $pdo;
        $sql = 'SELECT * FROM backups WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
