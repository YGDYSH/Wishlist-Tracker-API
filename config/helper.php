<?php
/**
 * CORS + JSON helper.
 * Handles OPTIONS preflight for Flutter development.
 * NOTE: "*" is for development only. Restrict in production.
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/**
 * Send a JSON response and stop.
 */
function respond($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

/**
 * Read and decode the JSON request body.
 */
function getJsonInput()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        respond([
            'success' => false,
            'message' => 'Invalid JSON input'
        ], 400);
    }

    return $data ?: [];
}