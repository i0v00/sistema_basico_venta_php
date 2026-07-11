<?php
namespace App\Controllers;

use Core\Auth;
use Core\Database;

class AuthController {
    public function showLogin() {
        if (Auth::check()) {
            redirect('/');
        }
        view('auth/login');
    }

    public function login() {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            Auth::setFlash('error', 'Por favor ingresa usuario y contraseña.');
            redirect('/login');
        }

        if (Auth::login($username, $password)) {
            redirect('/');
        } else {
            Auth::setFlash('error', 'Credenciales incorrectas.');
            redirect('/login');
        }
    }

    public function logout() {
        Auth::logout();
        redirect('/login');
    }

    public function changePassword() {
        Auth::requireLogin();
        $username = trim($_POST['username'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        if (empty($username) || empty($currentPassword)) {
            Auth::setFlash('error', 'El usuario y la contraseña actual son requeridos.');
            redirect('/settings');
        }

        $db = Database::getConnection();
        $user = Auth::user();
        
        // Fetch current user credentials to verify password
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $userData = $stmt->fetch();

        if (!password_verify($currentPassword, $userData['password'])) {
            Auth::setFlash('error', 'La contraseña actual es incorrecta.');
            redirect('/settings');
        }

        if (!empty($newPassword)) {
            // Update username and password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $hashedPassword, $user['id']]);
        } else {
            // Update username only
            $stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$username, $user['id']]);
        }

        // Update session
        $_SESSION['username'] = $username;

        Auth::setFlash('success', 'Credenciales actualizadas correctamente.');
        redirect('/settings');
    }
}
