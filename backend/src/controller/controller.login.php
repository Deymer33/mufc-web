<?php

require_once 'model/model.login.php';

class LoginController{
    
    private $modelLogin;

    public function __construct(){
       $this->modelLogin = new LoginModel();
    }

    public function login($email, $password){

        if(empty($email) || empty($password))
        return ['error' => 'Todos los campos son requeridos'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Email no válido'];
        }

        return $this->modelLogin->loginUser($email, $password);

    }
}