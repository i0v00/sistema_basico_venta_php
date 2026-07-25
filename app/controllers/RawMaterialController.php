<?php
namespace App\Controllers;

use Core\Auth;
use App\Models\RawMaterial;

class RawMaterialController {
    public function __construct() {
        Auth::requireLogin();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $rawMaterials = RawMaterial::all($search);
        
        view('raw_materials/index', [
            'rawMaterials' => $rawMaterials,
            'search' => $search
        ]);
    }

    public function form() {
        $id = $_GET['id'] ?? null;
        $rawMaterial = null;

        if ($id) {
            $rawMaterial = RawMaterial::find($id);
            if (!$rawMaterial) {
                Auth::setFlash('error', 'Materia prima no encontrada.');
                redirect('/raw-materials');
            }
        }

        view('raw_materials/form', [
            'rawMaterial' => $rawMaterial
        ]);
    }

    public function save() {
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $unit = trim($_POST['unit'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $currentStock = (float)($_POST['current_stock'] ?? 0);
        $minStock = (float)($_POST['min_stock'] ?? 0);

        if (empty($name) || empty($unit)) {
            Auth::setFlash('error', 'Nombre y unidad de medida son obligatorios.');
            redirect($id ? "/raw-materials/edit?id=$id" : '/raw-materials/create');
        }

        $data = [
            'name' => $name,
            'unit' => $unit,
            'price' => $price,
            'current_stock' => $currentStock,
            'min_stock' => $minStock
        ];

        if ($id) {
            RawMaterial::update($id, $data);
            Auth::setFlash('success', 'Materia prima actualizada correctamente.');
        } else {
            RawMaterial::create($data);
            Auth::setFlash('success', 'Materia prima registrada correctamente.');
        }

        redirect('/raw-materials');
    }

    public function delete() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            RawMaterial::delete($id);
            Auth::setFlash('success', 'Materia prima eliminada.');
        }
        redirect('/raw-materials');
    }
}
