<?php
namespace App\Controllers;

use Core\Auth;
use App\Models\User;

class UserController {
    public function __construct() {
        Auth::requireRole('admin');
    }

    /**
     * List all users
     */
    public function index() {
        $users = User::all();
        view('users/index', ['users' => $users]);
    }

    /**
     * Show create/edit form
     */
    public function form() {
        $editUser = null;
        if (isset($_GET['id'])) {
            $editUser = User::find((int)$_GET['id']);
            if (!$editUser) {
                Auth::setFlash('error', 'Usuario no encontrado.');
                redirect('/users');
            }
        }
        view('users/form', ['editUser' => $editUser]);
    }

    /**
     * Save (create or update) a user
     */
    public function save() {
        $id        = (int)($_POST['id'] ?? 0);
        $username  = trim($_POST['username'] ?? '');
        $fullName  = trim($_POST['full_name'] ?? '');
        $password  = $_POST['password'] ?? '';
        $role      = $_POST['role'] ?? 'caja';
        $active    = (int)($_POST['active'] ?? 1);

        // Validate
        if (empty($username) || empty($fullName)) {
            Auth::setFlash('error', 'El usuario y nombre completo son requeridos.');
            redirect($id ? "/users/edit?id={$id}" : '/users/create');
        }

        if (!in_array($role, ['admin', 'caja', 'cocinero'], true)) {
            Auth::setFlash('error', 'Rol inválido.');
            redirect('/users');
        }

        // Check username uniqueness
        if (User::usernameExists($username, $id)) {
            Auth::setFlash('error', 'El nombre de usuario ya está en uso.');
            redirect($id ? "/users/edit?id={$id}" : '/users/create');
        }

        if ($id > 0) {
            // Update — password optional
            if (empty($password) && $id === (int)(Auth::user()['id'] ?? 0)) {
                // Editing self without password → skip password field
            }
            User::update($id, [
                'username'  => $username,
                'full_name' => $fullName,
                'password'  => $password,
                'role'      => $role,
                'active'    => $active,
            ]);
            Auth::setFlash('success', 'Usuario actualizado correctamente.');
        } else {
            // Create — password required
            if (empty($password)) {
                Auth::setFlash('error', 'La contraseña es requerida para crear un usuario.');
                redirect('/users/create');
            }
            User::create([
                'username'  => $username,
                'full_name' => $fullName,
                'password'  => $password,
                'role'      => $role,
            ]);
            Auth::setFlash('success', 'Usuario creado correctamente.');
        }

        redirect('/users');
    }

    /**
     * Delete a user
     */
    public function delete() {
        $id = (int)($_POST['id'] ?? 0);
        $currentUser = Auth::user();

        if ($id === (int)$currentUser['id']) {
            Auth::setFlash('error', 'No puedes eliminar tu propio usuario.');
            redirect('/users');
        }

        if (User::delete($id, (int)$currentUser['id'])) {
            Auth::setFlash('success', 'Usuario eliminado.');
        } else {
            Auth::setFlash('error', 'No se pudo eliminar el usuario.');
        }
        redirect('/users');
    }
}
