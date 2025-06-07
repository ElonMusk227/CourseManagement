<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        header('Location: /unauthorized.php');
        exit();
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'nom' => $_SESSION['nom'],
        'prenom' => $_SESSION['prenom'],
        'role' => $_SESSION['role']
    ];
}
?>