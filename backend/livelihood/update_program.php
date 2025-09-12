<?php 
    require_once "../config_normal.php";
    require_once "../system_logs.php";
    session_start();


    if(isset($_POST["budget_allocated"]) && isset($_POST["description"]) && isset($_POST["duration_months"]) && isset($_POST["end_date"]) && isset($_POST["id"]) && isset($_POST["program_name"]) && isset($_POST["program_type"]) && isset($_POST["start_date"]) && isset($_POST["status"]) && isset($_POST["success_criteria"]) && isset($_POST["target_beneficiaries"]))
    {
        $budget_allocated = $_POST["budget_allocated"];
        $description = $_POST["description"];
        $duration_months = $_POST["duration_months"];
        $end_date = $_POST["end_date"];
        $id = $_POST["id"];
        $program_name = $_POST["program_name"];
        $program_type = $_POST["program_type"];
        $start_date = $_POST["start_date"];
        $status = $_POST["status"];
        $success_criteria = $_POST["success_criteria"];
        $target_beneficiaries = $_POST["target_beneficiaries"];

        $array_data = [
            "budget_allocated" => $budget_allocated,
            "description" => $description,
            "duration_months" => $duration_months,
            "end_date" => $end_date,
            "id" => $id,
            "program_name" => $program_name,
            "program_type" => $program_type,
            "start_date" => $start_date,
            "status" => $status,
            "success_criteria" => $success_criteria,
            "target_beneficiaries" => $target_beneficiaries
        ];

        foreach($array_data as $key => $value)
        {
            if(empty($value))
            {
                echo $key . " is empty";
                exit();
            }
        }

        $sql = "UPDATE livelihood_programs SET program_name = ?, program_type = ?, description = ?, target_beneficiaries = ?, success_criteria = ?, start_date = ?, end_date = ?, duration_months = ?, budget_allocated = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssi", $program_name, $program_type, $description, $target_beneficiaries, $success_criteria, $start_date, $end_date, $duration_months, $budget_allocated, $status, $id);
        
        if($stmt->execute())
        {
            // Log the update action
            $user_id = $_SESSION['user_id'] ?? 1; // Assuming user_id is stored in session
            $description_log = "Updated livelihood program ID: $id, Name: $program_name";
            system_logs($conn, $user_id, 'Update', $description_log);
            echo json_encode(["success" => true, "message" => "Program updated successfully"]);
        }
        else
        {
            echo "Error: " . $stmt->error;
            exit();
        }
    }

?>