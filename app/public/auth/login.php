<?php
require_once __DIR__ . "/../../Controllers/Auth/LoginController.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new LoginController();
    $result = $controller->handle($_POST);

    http_response_code($result['status']);
    echo json_encode($result);
} else {
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
}
?>