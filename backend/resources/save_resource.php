<?php
require_once "../system_logs.php";
require_once "../config_normal.php";
session_start();

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['program_id']) || empty($_POST['resource_type']) || 
        empty($_POST['resource_name']) || empty($_POST['quantity']) || empty($_POST['status'])) {
        throw new Exception('Please fill in all required fields');
    }

    $program_id = intval($_POST['program_id']);
    $resource_type = $_POST['resource_type'];
    $resource_name = trim($_POST['resource_name']);
    $quantity = intval($_POST['quantity']);
    $cost = !empty($_POST['cost']) ? floatval($_POST['cost']) : null;
    $supplier = !empty($_POST['supplier']) ? trim($_POST['supplier']) : null;
    $acquisition_date = !empty($_POST['acquisition_date']) ? $_POST['acquisition_date'] : null;
    $status = $_POST['status'];

    // Validate resource type
    $valid_types = ['equipment', 'materials', 'venue', 'instructor', 'budget'];
    if (!in_array($resource_type, $valid_types)) {
        throw new Exception('Invalid resource type');
    }

    // Validate status
    $valid_statuses = ['available', 'in_use', 'maintenance', 'damaged'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception('Invalid status');
    }

    // Verify program exists
    $program_check = $conn->prepare("SELECT id FROM livelihood_programs WHERE id = ?");
    $program_check->bind_param("i", $program_id);
    $program_check->execute();
    if ($program_check->get_result()->num_rows === 0) {
        throw new Exception('Selected program does not exist');
    }

    // Insert resource
    $stmt = $conn->prepare("
        INSERT INTO program_resources 
        (program_id, resource_type, resource_name, quantity, cost, supplier, acquisition_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issidsss",
        $program_id,
        $resource_type,
        $resource_name,
        $quantity,
        $cost,
        $supplier,
        $acquisition_date,
        $status
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to save resource: ' . $stmt->error);
    }

    $resource_id = $conn->insert_id;

    // Log the action
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $log_stmt = $conn->prepare("
            INSERT INTO system_logs (user_id, action, description) 
            VALUES (?, 'Insert', ?)
        ");
        $log_description = "Added new resource ID: $resource_id, Name: $resource_name";
        $log_stmt->bind_param("is", $user_id, $log_description);
        $log_stmt->execute();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Resource added successfully',
        'resource_id' => $resource_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>