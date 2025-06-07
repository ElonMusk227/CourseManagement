<?php
require_once '../config/auth.php';
require_once '../config/database.php';

requireRole('admin');

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $nom = $_POST['nom'];
                $code = $_POST['code'];
                $credits = $_POST['credits'];
                $departement = $_POST['departement'];
                
                $query = "INSERT INTO subjects (nom, code, credits, departement) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$nom, $code, $credits, $departement])) {
                    $message = 'Matière créée avec succès';
                    $message_type = 'success';
                } else {
                    $message = 'Erreur lors de la création de la matière';
                    $message_type = 'error';
                }
                break;
                
            case 'update':
                $id = $_POST['id'];
                $nom = $_POST['nom'];
                $code = $_POST['code'];
                $credits = $_POST['credits'];
                $departement = $_POST['departement'];
                
                $query = "UPDATE subjects SET nom=?, code=?, credits=?, departement=? WHERE id=?";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$nom, $code, $credits, $departement, $id])) {
                    $message = 'Matière modifiée avec succès';
                    $message_type = 'success';
                } else {
                    $message = 'Erreur lors de la modification de la matière';
                    $message_type = 'error';
                }
                break;
                
            case 'delete':
                $id = $_POST['id'];
                
                $query = "DELETE FROM subjects WHERE id = ?";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$id])) {
                    $message = 'Matière supprimée avec succès';
                    $message_type = 'success';
                } else {
                    $message = 'Erreur lors de la suppression de la matière';
                    $message_type = 'error';
                }
                break;
        }
    }
}

$subjects_query = "SELECT * FROM subjects ORDER BY nom";
$subjects_stmt = $db->prepare($subjects_query);
$subjects_stmt->execute();
$subjects = $subjects_stmt->fetchAll(PDO::FETCH_ASSOC);


$search_results = $subjects;
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

$editing_subject = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_query = "SELECT * FROM subjects WHERE id = ?";
    $edit_stmt = $db->prepare($edit_query);
    $edit_stmt->bindParam(1, $_GET['edit']);
    $edit_stmt->execute();
    $editing_subject = $edit_stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - ESCEP</title>
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
                    <span class="ml-4 text-gray-700">Administration</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-user-shield mr-2"></i>
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
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl text-white p-8 mb-8">
            <h2 class="text-3xl font-bold mb-2">Panneau d'Administration</h2>
            <p class="text-purple-100">Gérez les matières et la configuration du système</p>
        </div>


        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-book text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Matières</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($subjects); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-chalkboard-teacher text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Enseignants</p>
                        <p class="text-2xl font-bold text-gray-900">5</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-graduation-cap text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Étudiants</p>
                        <p class="text-2xl font-bold text-gray-900">150</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-building text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Départements</p>
                        <p class="text-2xl font-bold text-gray-900">3</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Subject Form -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-plus text-purple-600 mr-2"></i>
                        <?php echo $editing_subject ? 'Modifier la Matière' : 'Nouvelle Matière'; ?>
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="<?php echo $editing_subject ? 'update' : 'create'; ?>">
                        <?php if ($editing_subject): ?>
                            <input type="hidden" name="id" value="<?php echo $editing_subject['id']; ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la matière</label>
                            <input type="text" name="nom" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   value="<?php echo $editing_subject ? htmlspecialchars($editing_subject['nom']) : ''; ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Code</label>
                            <input type="text" name="code" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   value="<?php echo $editing_subject ? htmlspecialchars($editing_subject['code']) : ''; ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Crédits</label>
                            <input type="number" name="credits" required min="1" max="10"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   value="<?php echo $editing_subject ? $editing_subject['credits'] : ''; ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Département</label>
                            <select name="departement" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Sélectionner un département</option>
                                <option value="Informatique" <?php echo ($editing_subject && $editing_subject['departement'] === 'Informatique') ? 'selected' : ''; ?>>Informatique</option>
                                <option value="Mathématiques" <?php echo ($editing_subject && $editing_subject['departement'] === 'Mathématiques') ? 'selected' : ''; ?>>Mathématiques</option>
                                <option value="Économie" <?php echo ($editing_subject && $editing_subject['departement'] === 'Économie') ? 'selected' : ''; ?>>Économie</option>
                                <option value="Commerce" <?php echo ($editing_subject && $editing_subject['departement'] === 'Commerce') ? 'selected' : ''; ?>>Commerce</option>
                            </select>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i><?php echo $editing_subject ? 'Modifier' : 'Créer'; ?>
                            </button>
                            <?php if ($editing_subject): ?>
                                <a href="dashboard.php" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition-colors text-center">
                                    Annuler
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-cog text-purple-600 mr-2"></i>Gestion des Matières
                        </h3>
                        <form method="GET" class="flex space-x-2">
                            <input type="text" name="search" placeholder="Rechercher une matière..."
                                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                            <p class="text-purple-800">Résultats pour: "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
                            <a href="dashboard.php" class="text-purple-600 text-sm">Effacer la recherche</a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="space-y-4">
                        <?php if (empty($search_results)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-600">Aucune matière trouvée</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($search_results as $subject): ?>
                                <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition-colors">
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
                                            <a href="?edit=<?php echo $subject['id']; ?>" 
                                               class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-sm transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Modifier
                                            </a>
                                            <form method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-sm transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>Supprimer
                                                </button>
                                            </form>
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