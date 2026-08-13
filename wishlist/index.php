<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respond(['success' => false, 'message' => 'Method not allowed. Use GET.'], 405);
}

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($userId <= 0) {
    respond(['success' => false, 'message' => 'user_id is required'], 400);
}

try {
    $stmt = $pdo->prepare(
        'SELECT id, user_id, name, target_price, saved_amount, category, target_date, notes, created_at
         FROM wishlists
         WHERE user_id = ?
         ORDER BY created_at DESC'
    );
    $stmt->execute([$userId]);
    $wishlists = $stmt->fetchAll();

    respond([
        'success' => true,
        'message' => 'Wishlist retrieved successfully',
        'data' => $wishlists,
    ]);
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Failed to retrieve wishlist'], 500);
}