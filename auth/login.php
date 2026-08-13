<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed. Use POST.'], 405);
}

$data = getJsonInput();

$email    = isset($data['email']) ? trim((string)$data['email']) : '';
$password = isset($data['password']) ? (string)$data['password'] : '';

if ($email === '') {
    respond(['success' => false, 'message' => 'Email is required'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(['success' => false, 'message' => 'Invalid email format'], 400);
}

if ($password === '') {
    respond(['success' => false, 'message' => 'Password is required'], 400);
}

try {
    $stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        respond(['success' => false, 'message' => 'Invalid email or password'], 401);
    }

    if (!password_verify($password, $user['password'])) {
        respond(['success' => false, 'message' => 'Invalid email or password'], 401);
    }

    respond([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user' => [
                'id'    => (int)$user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
            ],
        ],
    ]);
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Login failed'], 500);
}