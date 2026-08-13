<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed. Use POST.'], 405);
}

$data = getJsonInput();

$wishlistId   = isset($data['id']) ? (int)$data['id'] : 0;
$userId       = isset($data['user_id']) ? (int)$data['user_id'] : 0;
$name         = isset($data['name']) ? trim((string)$data['name']) : '';
$targetPrice  = isset($data['target_price']) ? (float)$data['target_price'] : 0;
$savedAmount  = isset($data['saved_amount']) ? (float)$data['saved_amount'] : 0;
$category     = isset($data['category']) ? trim((string)$data['category']) : '';
$targetDate   = isset($data['target_date']) ? trim((string)$data['target_date']) : '';
$notes        = isset($data['notes']) ? trim((string)$data['notes']) : '';

if ($wishlistId <= 0) {
    respond(['success' => false, 'message' => 'id is required'], 400);
}

if ($userId <= 0) {
    respond(['success' => false, 'message' => 'user_id is required'], 400);
}

if ($name === '') {
    respond(['success' => false, 'message' => 'name is required'], 400);
}

if ($targetPrice <= 0) {
    respond(['success' => false, 'message' => 'target_price must be greater than 0'], 400);
}

if ($savedAmount < 0) {
    respond(['success' => false, 'message' => 'saved_amount must be greater than or equal to 0'], 400);
}

if ($savedAmount > $targetPrice) {
    respond(['success' => false, 'message' => 'saved_amount cannot be greater than target_price'], 400);
}

if ($targetDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate)) {
    respond(['success' => false, 'message' => 'target_date must be in YYYY-MM-DD format'], 400);
}

try {
    $stmt = $pdo->prepare('SELECT id FROM wishlists WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$wishlistId, $userId]);
    $existing = $stmt->fetch();

    if (!$existing) {
        respond(['success' => false, 'message' => 'Wishlist not found for this user'], 404);
    }

    $stmt = $pdo->prepare(
        'UPDATE wishlists
         SET name = ?, target_price = ?, saved_amount = ?, category = ?, target_date = ?, notes = ?
         WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$name, $targetPrice, $savedAmount, $category, $targetDate, $notes, $wishlistId, $userId]);

    respond([
        'success' => true,
        'message' => 'Wishlist updated successfully',
        'data' => [
            'id'           => $wishlistId,
            'user_id'      => $userId,
            'name'         => $name,
            'target_price' => $targetPrice,
            'saved_amount' => $savedAmount,
            'category'     => $category,
            'target_date'  => $targetDate,
            'notes'        => $notes,
        ],
    ]);
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Failed to update wishlist'], 500);
}