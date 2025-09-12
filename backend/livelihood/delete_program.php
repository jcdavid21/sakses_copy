<?php 
require_once "../system_logs.php";
require_once "../config_normal.php";
session_start();


if(isset($_POST["id"]))
{
    $id = $_POST["id"];

    if(empty($id))
    {
        echo "ID is empty";
        exit();
    }

    // First, retrieve the program name for logging purposes
    $stmt_select = $conn->prepare("SELECT program_name FROM livelihood_programs WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if($result->num_rows === 0)
    {
        echo "No program found with the given ID";
        exit();
    }

    $row = $result->fetch_assoc();
    $program_name = $row['program_name'];

    // Now, proceed to delete the program
    $stmt_delete = $conn->prepare("DELETE FROM livelihood_programs WHERE id = ?");
    $stmt_delete->bind_param("i", $id);

    if($stmt_delete->execute())
    {
        // Log the deletion action
        $user_id = $_SESSION['user_id'] ?? 1; // Assuming user_id is stored in session
        $description_log = "Deleted livelihood program ID: $id, Name: $program_name";
        system_logs($conn, $user_id, 'Delete', $description_log);
        echo json_encode(["success" => true, "message" => "Program deleted successfully"]);
    }
    else
    {
        echo "Error: " . $stmt_delete->error;
        exit();
    }
}
else
{
    echo "ID not set";
    exit();
}
?>