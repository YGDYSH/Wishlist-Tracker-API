<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed. Use POST.'], 405);
}

$data = getJsonInput();

$name     = isset($data['name']) ? trim((string)$data['name']) : '';
$email    = isset($data['email']) ? trim((string)$data['email']) : '';
$password = isset($data['password']) ? (string)$data['password'] : '';

if ($name === '') {
    respond(['success' => false, 'message' => 'Name is required'], 400);
}

if ($email === '') {
    respond(['success' => false, 'message' => 'Email is required'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(['success' => false, 'message' => 'Invalid email format'], 400);
}

if ($password === '') {
    respond(['success' => false, 'message' => 'Password is required'], 400);
}

if (strlen($password) < 6) {
    respond(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        respond(['success' => false, 'message' => 'Email already registered'], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())'
    );
    $stmt->execute([$name, $email, $hash]);

    $userId = (int)$pdo->lastInsertId();

    respond([
        'success' => true,
        'message' => 'Registration successful',
        'data' => [
            'user' => [
                'id'    => $userId,
                'name'  => $name,
                'email' => $email,
            ],
        ],
    ], 201);
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Failed to register user'], 500);
}