<?php

require_once __DIR__."/../models/UserAuthModel.php";

// Crear una clase de Autenticación
class userAuth
{
    public function register(string $username, string $password, string $email){

        if (!$registro = new UserAuthModel()) {
            return false;
        }

        return $registro->register($username, $password, $email);
    }

    public function login(string $email, string $password){

        if (!$login = new UserAuthModel()) {
            return false;
        }

        return $login->login($email, $password);
    }
}

// Register y Login Retornan True o False

