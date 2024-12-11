<?php
require_once 'config/conection.php';

class LoginModel {

    private $conn;


    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function loginUser ($email, $password){

        try{
            $query = "SELECT name, email, password FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])){
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'name' => $row['name'],
                        'email' => $row['email']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid password'
                ];
            }
            } else {
                return [
                    'success' => false,
                    'message' => 'Email not found'
                    ];
                }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error during login',
                'error' => $e->getMessage()
            ];
        }
    }
}