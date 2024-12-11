<?php
require_once 'config/conection.php';
require_once 'vendor/autoload.php'; // Asegúrate de cargar las dependencias de Google2FA

use PragmaRX\Google2FA\Google2FA;

class UserModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function findUserByEmail($email) {
        try {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function save2FASecret($userId, $secret) {
        try {
            $query = "UPDATE users SET google2fa_secret = :secret WHERE id_user = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':secret', $secret);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function validate2FACode($userId, $code) {
        try {
            $query = "SELECT google2fa_secret FROM users WHERE id_user = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || empty($user['google2fa_secret'])) {
                return false; // 2FA no configurado
            }

            $google2fa = new Google2FA();
            return $google2fa->verifyKey($user['google2fa_secret'], $code);
        } catch (PDOException $e) {
            return false;
        }
    }
}
