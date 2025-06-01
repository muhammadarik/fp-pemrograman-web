<?php
// file: classes/Pelanggan.php

class Pelanggan {
    private $conn;
    private $table_name = "pelanggan";

    public $id;
    public $user_id;
    public $nama;
    public $email;
    public $no_hp;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Membaca semua pelanggan
    public function readAll() {
        $query = "SELECT
                    id, user_id, nama, email, no_hp, created_at
                  FROM
                    " . $this->table_name . "
                  ORDER BY
                    created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Anda bisa menambahkan method lain seperti readOne(), create(), update(), delete() di sini
}
?>