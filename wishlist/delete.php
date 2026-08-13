<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed. Use POST.'], 405);
}

$data = getJsonInput();

$wishlistId = isset($data['id']) ? (int)$data['id'] : 0;
$userId     = isset($data['user_id']) ? (int)$data['user_id'] : 0;

if ($wishlistId <= 0) {
    respond(['success' => false, 'message' => 'id is required'], 400);
}

if ($userId <= 0) {
    respond(['success' => false, 'message' => 'user_id is required'], 400);
}

try {
    $stmt = $pdo->prepare('SELECT id FROM wishlists WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$wishlistId, $userId]);
    $existing = $stmt->fetch();

    if (!$existing) {
        respond(['success' => false, 'message' => 'Wishlist not found for this user'], 404);
    }

    $stmt = $pdo->prepare('DELETE FROM wishlists WHERE id = ? AND user_id = ?');
    $stmt->execute([$wishlistId, $userId]);

    respond([
        'success' => true,
        'message' => 'Wishlist deleted successfully',
        'data' => [
            'id' => $wishlistId,
        ],
    ]);
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Failed to delete wishlist'], 500);
}