<?php
namespace App\Models;

use Core\Database;

class User {
    /**
     * Returns all users.
     */
    public static function all(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, username, full_name, role, active, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Find a user by ID.
     */
    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, full_name, role, active FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a new user.
     */
    public static function create(array $data): int {
        $db = Database::getConnection();
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            "INSERT INTO users (username, full_name, password, role, active) VALUES (?, ?, ?, ?, 1)"
        );
        $stmt->execute([
            $data['username'],
            $data['full_name'] ?? $data['username'],
            $hashed,
            $data['role'],
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Update an existing user. Password is only updated if provided.
     */
    public static function update(int $id, array $data): void {
        $db = Database::getConnection();
        if (!empty($data['password'])) {
            $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $db->prepare(
                "UPDATE users SET username = ?, full_name = ?, password = ?, role = ?, active = ? WHERE id = ?"
            );
            $stmt->execute([
                $data['username'],
                $data['full_name'] ?? $data['username'],
                $hashed,
                $data['role'],
                $data['active'] ?? 1,
                $id,
            ]);
        } else {
            $stmt = $db->prepare(
                "UPDATE users SET username = ?, full_name = ?, role = ?, active = ? WHERE id = ?"
            );
            $stmt->execute([
                $data['username'],
                $data['full_name'] ?? $data['username'],
                $data['role'],
                $data['active'] ?? 1,
                $id,
            ]);
        }
    }

    /**
     * Delete a user (cannot delete yourself).
     */
    public static function delete(int $id, int $currentUserId): bool {
        if ($id === $currentUserId) return false;
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    }

    /**
     * Returns true if the username already exists (excluding the given ID).
     */
    public static function usernameExists(string $username, int $excludeId = 0): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $excludeId]);
        return (bool)$stmt->fetch();
    }
}
