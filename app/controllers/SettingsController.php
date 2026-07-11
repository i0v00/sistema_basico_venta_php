<?php
namespace App\Controllers;

use Core\Auth;

class SettingsController {
    public function __construct() {
        Auth::requireLogin();
    }

    public function index() {
        view('settings');
    }

    public function save() {
        $trackRawMaterials = isset($_POST['track_raw_materials']) ? '1' : '0';
        setSetting('track_raw_materials', $trackRawMaterials);

        Auth::setFlash('success', 'Configuración del sistema guardada.');
        redirect('/settings');
    }
}
