<?php
/*
 HTTP API Router
 We handle all API routes as this is the entry point for the API.
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '//') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(sprintf('%s=%s', trim($key), trim($value)));
        }
    }
}
// SET HTTP Headers
header('Content-Type: application/json');
// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 3600');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/controllers/InstructorController.php';

// Get the request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

// '/api/ is the base path for the API (all end points should start with /api)
if ($uri[0] !== 'api') {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit();
}

// Handle all routes under /api
try {
    switch ($method) {
        case 'GET':
            if (count($uri) === 3 && $uri[1] === 'quiz') {
                // GET /api/quiz/{quiz_code}
                $studentController = new StudentController();
                $result = $studentController->getQuiz($uri[2]);
                http_response_code($result['code'] ?? 200);
                echo json_encode($result);
            } else if (count($uri) === 4 && $uri[1] === 'quiz' && $uri[3] === 'stats') {
                // GET /api/quiz/{quiz_code}/stats
                $instructorController = new InstructorController();
                $result = $instructorController->getQuizStats($uri[2]);
                http_response_code($result['code'] ?? 200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
            }
            break;

        case 'POST':
            if (count($uri) === 4 && $uri[1] === 'quiz' && $uri[3] === 'submit') {
                // POST /api/quiz/{quiz_code}/submit
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid request body']);
                    break;
                }

                $studentController = new StudentController();
                $result = $studentController->submitQuiz($uri[2], $data);
                http_response_code($result['code'] ?? 200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
