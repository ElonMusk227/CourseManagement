<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESCEP - Système de Gestion Scolaire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-blue-600">ESCEP</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="gradient-bg py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold text-white mb-6">
                Système de Gestion Scolaire
            </h1>
            <p class="text-xl text-white opacity-90 mb-8 max-w-3xl mx-auto">
                Plateforme moderne pour la gestion des cours, étudiants et enseignants de l'ESCEP
            </p>
            <a href="login.php" class="bg-white text-blue-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition-colors inline-flex items-center">
                <i class="fas fa-arrow-right mr-2"></i>
                Accéder au système
            </a>
        </div>
    </div>
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fonctionnalités Principales</h2>
                <p class="text-lg text-gray-600">Une solution complète pour la gestion académique</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-100">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-graduate text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Espace Étudiant</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Consultation des notes</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Calcul de moyenne générale</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Recherche de matières</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Matières non notées</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-100">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Espace Enseignant</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-blue-500 mr-2"></i>Gestion des matières</li>
                            <li><i class="fas fa-check text-blue-500 mr-2"></i>Consultation des cours</li>
                            <li><i class="fas fa-check text-blue-500 mr-2"></i>Recherche avancée</li>
                            <li><i class="fas fa-check text-blue-500 mr-2"></i>Profil enseignant</li>
                        </ul>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-100">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-cog text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Administration</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-purple-500 mr-2"></i>Gestion des matières</li>
                            <li><i class="fas fa-check text-purple-500 mr-2"></i>Configuration système</li>
                            <li><i class="fas fa-check text-purple-500 mr-2"></i>Statistiques globales</li>
                            <li><i class="fas fa-check text-purple-500 mr-2"></i>Contrôle d'accès</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold mb-4">ESCEP</h3>
                <p class="text-gray-400 mb-4">École Supérieure des Communications Electroniques et de La Poste</p>
                <p class="text-gray-500 text-sm">© 2025 ESCEP. Tous droits réservés. powered By Assogba Isaac</p>
            </div>
        </div>
    </div>
</body>
</html>