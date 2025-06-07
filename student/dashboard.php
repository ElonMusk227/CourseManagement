<?php
require_once '../config/auth.php';
require_once '../config/database.php';

requireRole('student');
$database = new Database();
$db = $database->getConnection();
$student_query = "SELECT id FROM students WHERE user_id = ?";
$student_stmt = $db->prepare($student_query);
$student_stmt->bindParam(1, $_SESSION['user_id']);
$student_stmt->execute();
$student = $student_stmt->fetch(PDO::FETCH_ASSOC);
$student_id = $student['id'];
$grades_query = "SELECT g.*, s.nom as subject_name, s.code as subject_code, s.credits,
                        u.nom as teacher_nom, u.prenom as teacher_prenom
                 FROM grades g
                 INNER JOIN subjects s ON g.subject_id = s.id
                 INNER JOIN teachers t ON g.teacher_id = t.id
                 INNER JOIN users u ON t.user_id = u.id
                 WHERE g.student_id = ?
                 ORDER BY g.date_evaluation DESC";

$grades_stmt = $db->prepare($grades_query);
$grades_stmt->bindParam(1, $student_id);
$grades_stmt->execute();
$grades = $grades_stmt->fetchAll(PDO::FETCH_ASSOC);
$average_query = "SELECT AVG(g.note) as moyenne FROM grades g WHERE g.student_id = ?";
$average_stmt = $db->prepare($average_query);
$average_stmt->bindParam(1, $student_id);
$average_stmt->execute();
$average_result = $average_stmt->fetch(PDO::FETCH_ASSOC);
$average = $average_result['moyenne'] ? round($average_result['moyenne'], 2) : 0;
$ungraded_query = "SELECT s.*
                   FROM subjects s
                   INNER JOIN student_subjects ss ON s.id = ss.subject_id
                   LEFT JOIN grades g ON s.id = g.subject_id AND g.student_id = ?
                   WHERE ss.student_id = ? AND g.id IS NULL
                   ORDER BY s.nom";

$ungraded_stmt = $db->prepare($ungraded_query);
$ungraded_stmt->bindParam(1, $student_id);
$ungraded_stmt->bindParam(2, $student_id);
$ungraded_stmt->execute();
$ungraded_subjects = $ungraded_stmt->fetchAll(PDO::FETCH_ASSOC);
$search_results = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_query = "SELECT s.*, ss.student_id 
                     FROM subjects s
                     INNER JOIN student_subjects ss ON s.id = ss.subject_id
                     WHERE ss.student_id = ? AND (s.nom LIKE ? OR s.code LIKE ?)
                     ORDER BY s.nom";
    
    $search_stmt = $db->prepare($search_query);
    $search_stmt->bindParam(1, $student_id);
    $search_stmt->bindParam(2, $search_term);
    $search_stmt->bindParam(3, $search_term);
    $search_stmt->execute();
    $search_results = $search_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Étudiant - ESCEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">ESCEP</h1>
                    <span class="ml-4 text-gray-500">|</span>
                    <span class="ml-4 text-gray-700">Espace Étudiant</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-user-circle mr-2"></i>
                        <span><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></span>
                    </div>
                    <a href="../logout.php" class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-xl text-white p-8 mb-8">
            <h2 class="text-3xl font-bold mb-2">Bienvenue dans votre espace étudiant</h2>
            <p class="text-green-100">Consultez vos notes, moyennes et matières</p>
        </div>
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-chart-line text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Moyenne Générale</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $average ? $average . '/20' : 'N/A'; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-graduation-cap text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Notes Obtenues</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($grades); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">En Attente</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($ungraded_subjects); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-book text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Matières</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($grades) + count($ungraded_subjects); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>Mes Notes
                        </h3>
                        <form method="GET" class="flex space-x-2">
                            <input type="text" name="search" placeholder="Rechercher une matière..."
                                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-blue-800">Résultats pour: "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
                            <a href="dashboard.php" class="text-blue-600 text-sm">Effacer la recherche</a>
                        </div>
                        <?php if (empty($search_results)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-600">Aucune matière trouvée</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($search_results as $subject): ?>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($subject['nom']); ?></h4>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($subject['code']); ?> • <?php echo $subject['credits']; ?> crédits</p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php if (empty($grades)): ?>
                                <div class="text-center py-8">
                                    <i class="fas fa-graduation-cap text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-600">Aucune note disponible</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($grades as $grade): ?>
                                    <?php
                                    $noteColor = $grade['note'] >= 16 ? 'text-green-600 bg-green-100' : 
                                               ($grade['note'] >= 12 ? 'text-blue-600 bg-blue-100' : 
                                               ($grade['note'] >= 10 ? 'text-yellow-600 bg-yellow-100' : 'text-red-600 bg-red-100'));
                                    ?>
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($grade['subject_name']); ?></h4>
                                                <p class="text-sm text-gray-600">Code: <?php echo htmlspecialchars($grade['subject_code']); ?> • <?php echo $grade['credits']; ?> crédits</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($grade['teacher_prenom'] . ' ' . $grade['teacher_nom']); ?>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-calendar mr-1"></i><?php echo date('d/m/Y', strtotime($grade['date_evaluation'])); ?>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $noteColor; ?>">
                                                    <?php echo $grade['note']; ?>/20
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>Matières Sans Note
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <?php if (empty($ungraded_subjects)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                                <p class="text-gray-600">Toutes les matières sont notées</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($ungraded_subjects as $subject): ?>
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-orange-500 mr-3"></i>
                                        <div>
                                            <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($subject['nom']); ?></h4>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($subject['code']); ?> • <?php echo $subject['credits']; ?> crédits</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>