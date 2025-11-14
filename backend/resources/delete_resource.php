<?php
require_once "../system_logs.php";
require_once "../config_normal.php";
session_start();

header('Content-Type: application/json');

if(isset($_POST["resource_id"]))
{
    $resource_id = (int)$_POST["resource_id"];

    // Check if resource exists
    $sql = "SELECT * FROM program_resources WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resource_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0)
    {
        // Delete the resource
        $sql = "DELETE FROM program_resources WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $resource_id);

        if($stmt->execute())
        {
            // Log the deletion action
            $user_id = $_SESSION['user_id'] ?? 1; // fallback user id
            $description_log = "Deleted resource ID: $resource_id";
            system_logs($conn, $user_id, 'Delete', $description_log);

            echo json_encode(["success" => true, "message" => "Resource deleted successfully"]);
            exit();
        }
        else
        {
            echo json_encode(["success" => false, "message" => "Failed to delete resource"]);
            exit();
        }
    }
    else
    {
        echo json_encode(["success" => false, "message" => "Resource not found"]);
        exit();
    }
}
else
{
    echo json_encode(["success" => false, "message" => "Resource ID is required"]);
    exit();
}
?>