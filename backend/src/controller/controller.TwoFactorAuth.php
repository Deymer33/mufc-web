<?php

require_once 'model.user.php';
require_once 'vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthController {
    private $userModel;
    private $google2fa;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->google2fa = new Google2FA();
    }

    // Habilitar 2FA para un usuario
    public function enable2FA($email) {
        $user = $this->userModel->findUserByEmail($email);

        if (!$user) {
            return ['error' => 'Usuario no encontrado'];
        }

        // Generar un nuevo secreto 2FA
        $secret = $this->google2fa->generateSecretKey();

        // Guardar el secreto en la base de datos
        if (!$this->userModel->save2FASecret($user['id_user'], $secret)) {
            return ['error' => 'Error al guardar el secreto 2FA'];
        }

        // Generar URL para un código QR
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            'TuAplicacion',
            $email,
            $secret
        );

        return [
            'message' => '2FA habilitado con éxito',
            'qr_code_url' => $qrCodeUrl,
        ];
    }

    // Validar código 2FA
    public function validate2FA($email, $code) {
        $user = $this->userModel->findUserByEmail($email);

        if (!$user) {
            return ['error' => 'Usuario no encontrado'];
        }

        // Validar el código
        $isValid = $this->userModel->validate2FACode($user['id_user'], $code);

        if ($isValid) {
            return ['message' => 'Código válido'];
        } else {
            return ['error' => 'Código inválido'];
        }
    }
}
