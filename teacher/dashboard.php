<?php
require_once '../config/auth.php';
require_once '../config/database.php';

requireRole('teacher');

$database = new Database();
$db = $database->getConnection();

$teacher_query = "SELECT id, departement FROM teachers WHERE user_id = ?";
$teacher_stmt = $db->prepare($teacher_query);
$teacher_stmt->bindParam(1, $_SESSION['user_id']);
$teacher_stmt->execute();
$teacher = $teacher_stmt->fetch(PDO::FETCH_ASSOC);
$teacher_id = $teacher['id'];
$department = $teacher['departement'];
$subjects_query = "SELECT s.*, ts.teacher_id 
                   FROM subjects s
                   INNER JOIN teacher_subjects ts ON s.id = ts.subject_id
                   WHERE ts.teacher_id = ?
                   ORDER BY s.nom";

$subjects_stmt = $db->prepare($subjects_query);
$subjects_stmt->bindParam(1, $teacher_id);
$subjects_stmt->execute();
$subjects = $subjects_stmt->fetchAll(PDO::FETCH_ASSOC);
$search_results = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_query = "SELECT * FROM subjects 
                     WHERE nom LIKE ? OR code LIKE ? OR departement LIKE ?
                     ORDER BY nom";
    
    $search_stmt = $db->prepare($search_query);
    $search_stmt->bindParam(1, $search_term);
    $search_stmt->bindParam(2, $search_term);
    $search_stmt->bindParam(3, $search_term);
    $search_stmt->execute();
    $search_results = $search_stmt->fetchAll(PDO::FETCH_ASSOC);
}
$subject_average = null;
if (isset($_GET['subject_average']) && isset($_GET['subject_id'])) {
    $avg_query = "SELECT AVG(g.note) as moyenne FROM grades g WHERE g.subject_id = ?";
    $avg_stmt = $db->prepare($avg_query);
    $avg_stmt->bindParam(1, $_GET['subject_id']);
    $avg_stmt->execute();
    $avg_result = $avg_stmt->fetch(PDO::FETCH_ASSOC);
    $subject_average = $avg_result['moyenne'] ? round($avg_result['moyenne'], 2) : 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Enseignant - ESCEP</title>
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
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">ESCEP</h1>
                    <span class="ml-4 text-gray-500">|</span>
                    <span class="ml-4 text-gray-700">Espace Enseignant</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
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
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl text-white p-8 mb-8">
            <h2 class="text-3xl font-bold mb-2">Espace Enseignant</h2>
            <p class="text-blue-100">Gérez vos matières et consultez vos cours</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-book text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Matières Enseignées</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($subjects); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-users text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Département</p>
                        <p class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($department); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Ancienneté</p>
                        <p class="text-lg font-bold text-gray-900">4 ans</p>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($subject_average !== null): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-chart-bar mr-2"></i>
                Moyenne de la matière: <?php echo $subject_average; ?>/20
            </div>
        <?php endif; ?>
        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>Mes Matières
                    </h3>
                    <form method="GET" class="flex space-x-3">
                        <input type="text" name="search" placeholder="Rechercher une matière..."
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>Recherche Globale
                        </button>
                    </form>
                </div>
            </div>
            <div class="p-6">
                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-blue-800">Résultats de recherche globale pour: "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
                        <a href="dashboard.php" class="text-blue-600 text-sm">Retour à mes matières</a>
                    </div>
                    <?php if (empty($search_results)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600">Aucun résultat trouvé</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($search_results as $subject): ?>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($subject['nom']); ?></h4>
                                    <p class="text-sm text-gray-600">Code: <?php echo htmlspecialchars($subject['code']); ?> • <?php echo $subject['credits']; ?> crédits • <?php echo htmlspecialchars($subject['departement']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php if (empty($subjects)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-600">Aucune matière assignée</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($subjects as $subject): ?>
                                <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition-colors card-hover">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($subject['nom']); ?></h4>
                                            <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-600">
                                                <div>
                                                    <i class="fas fa-code mr-2 text-blue-500"></i>
                                                    <strong>Code:</strong> <?php echo htmlspecialchars($subject['code']); ?>
                                                </div>
                                                <div>
                                                    <i class="fas fa-star mr-2 text-yellow-500"></i>
                                                    <strong>Crédits:</strong> <?php echo $subject['credits']; ?>
                                                </div>
                                                <div>
                                                    <i class="fas fa-building mr-2 text-green-500"></i>
                                                    <strong>Département:</strong> <?php echo htmlspecialchars($subject['departement']); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2 ml-4">
                                            <a href="?subject_average=1&subject_id=<?php echo $subject['id']; ?>" 
                                               class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-sm transition-colors">
                                                <i class="fas fa-chart-bar mr-1"></i>Moyenne
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>