<?php
namespace App\Controllers;

use Core\Auth;
use App\Models\Category;

class CategoryController {
    public function __construct() {
        Auth::requireLogin();
    }

    public function index() {
        $categories = Category::all();
        view('categories/index', [
            'categories' => $categories
        ]);
    }

    public function save() {
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? '🍔');

        if (empty($name)) {
            Auth::setFlash('error', 'El nombre de la categoría es obligatorio.');
            redirect('/categories');
        }

        Category::create([
            'name' => $name,
            'icon' => $icon
        ]);

        Auth::setFlash('success', 'Categoría creada correctamente.');
        redirect('/categories');
    }

    public function update() {
        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? '🍔');

        if ($id <= 0 || empty($name)) {
            Auth::setFlash('error', 'Datos inválidos para actualizar la categoría.');
            redirect('/categories');
        }

        Category::update($id, ['name' => $name, 'icon' => $icon]);
        Auth::setFlash('success', 'Categoría actualizada correctamente.');
        redirect('/categories');
    }

    public function delete() {
        $id = $_POST['id'] ?? null;

        if ($id) {
            try {
                Category::delete($id);
                Auth::setFlash('success', 'Categoría eliminada.');
            } catch (\Exception $e) {
                Auth::setFlash('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
            }
        }

        redirect('/categories');
    }
}
