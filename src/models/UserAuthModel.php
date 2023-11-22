<?php

// Conexion DB

// Crear una clase que internamente maneje los querys

class UserAuthModel
{
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "tdd");

        if ($this->conn->connect_errno) {
            throw new RuntimeException('mysqli connection error: ' . $this->conn->connect_error);
        }
    }

    // register(){} Correos Unicos
    function register(string $username, string $password, string $email) {
        $query = "SELECT count(email) FROM Users WHERE email = ?";
        $registro = $this->conn->prepare($query);
        $registro->bind_param('s', $email);
        if (!$registro->execute()) {
            throw new RuntimeException('Error al consultar la base de datos');
        }
        if (!$registro->bind_result($email_existente)) {
            throw new RuntimeException('Error al consultar la base de datos');
        }
        $registro->fetch();
        $registro->close();
        if ($email_existente !== 0) {
            return false;
        }
        
        $hash = $this->encryptPassword($password);

        $query = "INSERT INTO Users (username, password, email) VALUES (?, ?, ?)";
        $registro = $this->conn->prepare($query);
        $registro->bind_param('sss', $username, $hash, $email);
        $result = $registro->execute();

        $registro->close();
        $this->conn->close();

        return $result;
    }

    // login(){}
    function login(string $input_email, string $input_password) {
        $query = "SELECT password FROM Users WHERE email=?";
        $login = $this->conn->prepare($query);
        $login->bind_param('s', $input_email);
        if (!$login->execute()) {
            throw new RuntimeException('Error al consultar la base de datos');
        }
        if (!$login->bind_result($bd_password)) {
            throw new RuntimeException('Error al consultar la base de datos');
        }
        $login->fetch();

        if (is_null($bd_password)) {
            return false;
        }
        
        $result = $this->checkPassword($input_password, $bd_password);

        $login->close();
        $this->conn->close();

        return $result;
    }

    private function encryptPassword(string $password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function checkPassword(string $inputPassword, string $bdPassword) {
        return password_verify($inputPassword, $bdPassword);
    }
}

