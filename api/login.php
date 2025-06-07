<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['mail']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($user->login($input['mail'], $input['password'])) {
    session_start();
    $_SESSION['user_id'] = $user->id;
    $_SESSION['nom'] = $user->nom;
    $_SESSION['prenom'] = $user->prenom;
    $_SESSION['role'] = $user->role;
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'role' => $user->role
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
?>