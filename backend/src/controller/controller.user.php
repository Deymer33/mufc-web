<?php 

require_once 'model/model.user.php';


class UserController{
    
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }


    public function register($data) {
        // Validar los campos
        if (empty($data['name']) || empty($data['surnames']) || empty($data['birth']) || empty($data['country']) || empty($data['city']) || empty($data['address']) || empty($data['email']) || empty($data['password'])) {
            return ['error' => 'Todos los campos son requeridos'];
        }
        // Validación de email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Email no válido'];
        }
        // Si pasa la validación, intenta registrar
        return  $this->userModel->register($data['name'], $data['surnames'], $data['birth'], $data['country'], $data['city'], $data['address'], $data['email'], $data['password']);
  
    }
   
}

