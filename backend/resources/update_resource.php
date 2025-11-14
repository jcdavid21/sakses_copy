<?php 
require_once "../system_logs.php";
include "../config_normal.php";
session_start();

header('Content-Type: application/json');

if(!isset($_POST["resource_id"]) || !isset($_POST["program_id"]) || !isset($_POST["resource_type"]) || !isset($_POST["resource_name"]) || !isset($_POST["quantity"]) || !isset($_POST["status"]))
{
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

$resource_id = $_POST["resource_id"];
$program_id = $_POST["program_id"];
$resource_type = $_POST["resource_type"];
$resource_name = $_POST["resource_name"];
$quantity = $_POST["quantity"];
$status = $_POST["status"];
$acuquisition_date = isset($_POST["acquisition_date"]) ? $_POST["acquisition_date"] : null;
$cost = isset($_POST["cost"]) ? $_POST["cost"] : null;


// Check if resource exists
$sql = "SELECT * FROM program_resources WHERE id = ? AND program_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $resource_id, $program_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0)
{
    // Update the resource data
    $sql = "UPDATE program_resources 
            SET resource_type = ?, resource_name = ?, quantity = ?, cost = ?, acquisition_date = ?, status = ?
            WHERE id = ? AND program_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssidssii", $resource_type, $resource_name, $quantity, $cost, $acuquisition_date, $status, $resource_id, $program_id);

    if($stmt->execute())
    {
        // Log the enrollment action
        $user_id = $_SESSION['user_id'] ?? 1; // fallback user id
        $description_log = "Saved enrollment for Beneficiary ID: $beneficiary_id in Program ID: $program_id";
        system_logs($conn, $user_id, 'Insert', $description_log);

        echo json_encode(["success" => true, "message" => "Resource updated successfully"]);
        exit();
    }
    else
    {
        echo json_encode(["success" => false, "message" => "Failed to update resource"]);
        exit();
    }
}
else
{
    echo json_encode(["success" => false, "message" => "Resource not found"]);
    exit();
}



?>