<?php
// php/auth.php
ob_start();
header('Content-Type: application/json; charset=utf-8');

// Simulamos tu base de datos de usuarios
$users = [
    [
        "username" => "surjal_superuser",
        "password" => "Reune2025*",
        "denominacion" => "Caja Solidaria Sur de Jalisco, S.C. de A.P. de R.L. de C.V.",
        "sector" => "Sociedades Cooperativas de Ahorro y Préstamo"
    ]
];

$action = $_GET['action'] ?? '';

if ($action === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $u = $input['username'] ?? '';
    $p = $input['password'] ?? '';

    foreach ($users as $user) {
        if ($user['username'] === $u && $user['password'] === $p) {
            ob_end_clean();
            echo json_encode([
                "token" => bin2hex(random_bytes(16)),
                "user" => [
                    "nombre" => $u, 
                    "empresa" => $user['denominacion'],
                    "sector" => $user['sector'] // <--- AGREGAR ESTO
                ]
            ]);
            exit;
        }
    }
    http_response_code(401);
    ob_end_clean();
    echo json_encode(["message" => "Error"]);
    exit;
}