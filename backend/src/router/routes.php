<?php
require_once 'controller/controller.user.php';
require_once 'controller/controller.login.php';

class Router {
    private $userController;
    private $loginController;

    public function __construct() {
        $this->userController = new UserController();
        $this->loginController = new LoginController();
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($method === 'POST' && $path === '/register') {
            $data = json_decode(file_get_contents("php://input"), true);
            $response = $this->userController->register($data);
            echo json_encode($response);

        } elseif ($method === 'POST' && $path === '/login') {
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar datos
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;

            // Llamar al controlador
            $response = $this->loginController->login($email, $password);
            echo json_encode($response);

        } else {
            http_response_code(404); // Enviar código de error HTTP
            echo json_encode(['error' => 'Ruta no encontrada']);
        }
    }
}
