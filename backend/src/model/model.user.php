<?php
require_once 'config/conection.php';


Class UserModel {
    private $conn;


    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($name, $surnames, $birth, $country, $city, $address, $email, $password) {
        try {
            // Verificar si el correo ya está en uso
            $checkQuery = "SELECT COUNT(*) FROM users WHERE email = :email";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            $emailExists = $checkStmt->fetchColumn();
    
            if ($emailExists) {
                return ['error' => 'El correo ya está registrado'];
            }
    
            // Continuar con el registro si el correo no está en uso
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (name, surnames, birth, country, city, address, email, password) 
                      VALUES (:name, :surnames, :birth, :country, :city, :address, :email, :password)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':surnames', $surnames);
            $stmt->bindParam(':birth', $birth);
            $stmt->bindParam(':country', $country);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();

              // Obtener el ID del usuario recién registrado
            $userId = $this->conn->lastInsertId();

        // Llamar a la función para asignar el bono

            $this->bonusRegister($birth, $userId);

            $controller = new TwoFactorAuthController();
            $response = $controller->enable2FA($email);

             if (isset($response['error'])) {
            return ['error' => 'Error al habilitar 2FA'];
             }

    
            return [
                'success' => 'User registrer successful',
                'qr_code_url' => $response['qr_code_url']
            ];
        } catch (PDOException $e) {
            return ['error' => 'Error at registrer user', 'details' => $e->getMessage()];
        }
    }

    function bonusRegister($birth, $userId) {

        $birthDate = new DateTime($birth);
        $currentDate = new DateTime();
        $age = $birthDate->diff($currentDate)->y;
        
        // Determinar la cantidad de bonos
        $quanty_MuFC = ($age < 35) ? 10 : 5;
    
        try {
            // Insertar el bono en la tabla `user_wallet`
            $queryBonus = "INSERT INTO user_wallet (user_id, quanty_MuFC) VALUES (:user_id, :quanty_MuFC)";
            $stmt = $this->conn->prepare($queryBonus);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':quanty_MuFC', $quanty_MuFC);
            $stmt->execute();
    
            // Obtener el ID del bono recién creado
            $walletId = $this->conn->lastInsertId();
    
            // Actualizar la tabla `users` para asociar el ID del wallet
            $queryUpdateUser = "UPDATE users SET id_wallet = :id_wallet WHERE id_user = :user_id";
            $stmtUpdateUser = $this->conn->prepare($queryUpdateUser);
            $stmtUpdateUser->bindParam(':id_wallet', $walletId);
            $stmtUpdateUser->bindParam(':user_id', $userId);
            $stmtUpdateUser->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al registrar el bono: " . $e->getMessage());
        }
    }

    
  
}