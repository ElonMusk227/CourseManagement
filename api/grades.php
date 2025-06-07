<?php
header('Content-Type: application/json');
require_once '../config/auth.php';
require_once '../config/database.php';
require_once '../models/Grade.php';

requireAuth();

$database = new Database();
$db = $database->getConnection();
$grade = new Grade($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['student_id'])) {
            $student_id = $_GET['student_id'];
            
       
            $stmt = $grade->getByStudent($student_id);
            $grades = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $grades[] = $row;
            }
            
       
            $average = $grade->getStudentAverage($student_id);
            
            $stmt = $grade->getUngradedSubjects($student_id);
            $ungraded = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ungraded[] = $row;
            }
            
            echo json_encode([
                'grades' => $grades,
                'average' => $average,
                'ungraded_subjects' => $ungraded
            ]);
        } elseif (isset($_GET['subject_average']) && isset($_GET['subject_id'])) {
            $average = $grade->getSubjectAverage($_GET['subject_id']);
            echo json_encode(['average' => $average]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required parameters']);
        }
        break;
        
    case 'POST':
        requireRole('teacher');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($grade->create($input['student_id'], $input['subject_id'], $input['teacher_id'], $input['note'], $input['date_evaluation'])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create grade']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>