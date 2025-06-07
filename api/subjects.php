<?php
header('Content-Type: application/json');
require_once '../config/auth.php';
require_once '../config/database.php';
require_once '../models/Subject.php';

requireAuth();

$database = new Database();
$db = $database->getConnection();
$subject = new Subject($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['search'])) {
            $stmt = $subject->search($_GET['search']);
        } elseif (isset($_GET['teacher_id'])) {
            $stmt = $subject->getByTeacher($_GET['teacher_id']);
        } elseif (isset($_GET['student_id'])) {
            $stmt = $subject->getByStudent($_GET['student_id']);
        } else {
            $stmt = $subject->getAll();
        }
        
        $subjects = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subjects[] = $row;
        }
        
        echo json_encode($subjects);
        break;
        
    case 'POST':
        requireRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($subject->create($input['nom'], $input['code'], $input['credits'], $input['departement'])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create subject']);
        }
        break;
        
    case 'PUT':
        requireRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($subject->update($input['id'], $input['nom'], $input['code'], $input['credits'], $input['departement'])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update subject']);
        }
        break;
        
    case 'DELETE':
        requireRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($subject->delete($input['id'])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete subject']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>