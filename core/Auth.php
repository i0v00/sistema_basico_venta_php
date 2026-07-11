<?php
namespace Core;

class Auth {
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure cookie options
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            session_start();
        }

        // Check timeout
        $lifetime = $_ENV['SESSION_LIFETIME'] ?? 1800;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $lifetime)) {
            self::logout();
            self::setFlash('error', 'Sesión expirada por inactividad.');
            redirect('/login');
        }
        $_SESSION['last_activity'] = time();
    }
 
    public static function login($username, $password) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
 
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
            $_SESSION['role']      = $user['role'] ?? 'caja';
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }
 
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
 
    public static function check() {
        self::initSession();
        return isset($_SESSION['user_id']);
    }
 
    public static function user() {
        if (self::check()) {
            return [
                'id'        => $_SESSION['user_id'],
                'username'  => $_SESSION['username'],
                'full_name' => $_SESSION['full_name'] ?? $_SESSION['username'],
                'role'      => $_SESSION['role'] ?? 'caja',
            ];
        }
        return null;
    }

    /**
     * Returns the current user's role or null if not logged in.
     */
    public static function role(): ?string {
        if (!self::check()) return null;
        return $_SESSION['role'] ?? 'caja';
    }

    /**
     * Checks if the current user has one of the allowed roles.
     * Admin always passes.
     *
     * @param string|array $roles  e.g. 'caja' or ['caja','cocinero']
     */
    public static function hasRole($roles): bool {
        $currentRole = self::role();
        if ($currentRole === null) return false;
        if ($currentRole === 'admin') return true; // admin tiene acceso total
        if (is_string($roles)) $roles = [$roles];
        return in_array($currentRole, $roles, true);
    }

    /**
     * Requires login AND one of the given roles. Redirects otherwise.
     *
     * @param string|array $roles
     */
    public static function requireRole($roles): void {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            self::setFlash('error', 'No tienes permiso para acceder a esa sección.');
            // Redirect to the appropriate home for the user's role
            $role = self::role();
            if ($role === 'cocinero') {
                redirect('/orders');
            } elseif ($role === 'caja') {
                redirect('/pos');
            } else {
                redirect('/');
            }
        }
    }
 
    public static function requireLogin() {
        if (!self::check()) {
            self::setFlash('error', 'Debes iniciar sesión para acceder.');
            redirect('/login');
        }
    }

    public static function setFlash($key, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash($key) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }
}
