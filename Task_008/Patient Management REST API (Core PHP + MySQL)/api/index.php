<?php

require_once __DIR__ . "/middlewares/JsonMiddleware.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/controllers/PatientController.php";


$request = $_GET['request'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$parts = explode("/", $request);
$id = $parts[1] ?? null;

if ($parts[0] == "patients") {

    PatientController::handle($conn, $method, $id);

} else {

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Route not found"
    ]);
}

?>