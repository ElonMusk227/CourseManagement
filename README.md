# ESCEP School Management System

Un système complet de gestion scolaire développé en PHP/MySQL avec une interface moderne en HTML/CSS/Tailwind.

## Fonctionnalités

### Authentification
- Système de connexion sécurisé avec rôles (Étudiant, Enseignant, Administrateur)
- Gestion des sessions
- Protection des pages par rôle

### Espace Étudiant
- Consultation des notes avec moyennes
- Calcul automatique de la moyenne générale
- Affichage des matières non notées
- Recherche de matières

### Espace Enseignant
- Consultation des matières enseignées
- Recherche globale dans toutes les matières
- Calcul des moyennes par matière
- Profil enseignant avec informations

### Administration
- Gestion complète des matières (CRUD)
- Interface d'administration moderne
- Statistiques du système

## Structure du Projet

```
├── database/
│   └── schema.sql          # Structure de la base de données
├── config/
│   ├── database.php        # Configuration BDD
│   └── auth.php           # Système d'authentification
├── models/
│   ├── User.php           # Modèle utilisateur
│   ├── Subject.php        # Modèle matière
│   └── Grade.php          # Modèle note
├── api/
│   ├── login.php          # API connexion
│   ├── logout.php         # API déconnexion
│   ├── subjects.php       # API matières
│   └── grades.php         # API notes
├── student/
│   └── dashboard.html     # Tableau de bord étudiant
├── teacher/
│   └── dashboard.html     # Tableau de bord enseignant
├── admin/
│   └── dashboard.html     # Panneau d'administration
├── index.html             # Page d'accueil
├── login.html             # Page de connexion
└── unauthorized.html      # Page d'erreur 403
```

## Installation

1. **Base de données**
   - Créer une base de données MySQL
   - Importer le fichier `database/schema.sql`
   - Configurer les paramètres de connexion dans `config/database.php`

2. **Serveur Web**
   - Placer les fichiers dans le répertoire web
   - S'assurer que PHP et MySQL sont configurés
   - Activer les sessions PHP

## Comptes de Démonstration

- **Administrateur**: admin@escep.edu / admin123
- **Enseignant**: marie.dupont@escep.edu / teacher123
- **Étudiant**: john.doe@student.escep.edu / student123

## Technologies Utilisées

- **Backend**: PHP 7.4+, MySQL 5.7+
- **Frontend**: HTML5, CSS3, Tailwind CSS
- **Icons**: Font Awesome 6
- **Fonts**: Google Fonts (Inter)
