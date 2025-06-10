<?php
header('Content-Type: application/json');

// Database credentials
$host = 'your_db_host';
$db   = 'your_db_name';
$user = 'your_db_user';
$pass = 'your_db_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $customerName = filter_var($_POST['customerName'], FILTER_SANITIZE_STRING);
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $paymentMethod = filter_var($_POST['paymentMethod'], FILTER_SANITIZE_STRING);
    $foodOrdered = filter_var($_POST['foodOrdered'], FILTER_SANITIZE_STRING);
    $roomNumber = filter_var($_POST['roomNumber'], FILTER_SANITIZE_NUMBER_INT);

    // Check if any of the fields are empty after sanitization
    if (empty($customerName) || empty($amount) || empty($paymentMethod) || empty($foodOrdered) || empty($roomNumber)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Prepare and execute the SQL query
    $sql = "INSERT INTO customer_orders (customer_name, amount, payment_method, food_ordered, room_number, order_date) VALUES (?, ?, ?, ?, ?, NOW())";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerName, $amount, $paymentMethod, $foodOrdered, $roomNumber]);

        echo json_encode(['status' => 'success', 'message' => 'Order added successfully.']);
    } catch (\PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error adding order: ' . $e->getMessage()]);
    }
} else {
    // If not a POST request
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
