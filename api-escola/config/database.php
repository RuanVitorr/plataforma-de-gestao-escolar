<?php

class Database {
    private $host = "localhost";
    private $db_name = "escola";
    private $username = "postgres";
    private $password = "12345678A@"; 
    

    public function connect(): PDO {
        try {
            $conn = new PDO(
                "pgsql:host={$this->host};dbname={$this->db_name};options='--client_encoding=UTF8'",
                $this->username,
                $this->password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro na conexão",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }
}