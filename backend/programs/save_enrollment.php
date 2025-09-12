<?php 
require_once "../system_logs.php";
require_once "../config_normal.php";
session_start();

if(isset($_POST["attendance_rate"]) && isset($_POST["beneficiary_id"]) && isset($_POST["enrollment_date"]) && isset($_POST["pre_assessment_score"]) && isset($_POST["program_id"]) && isset($_POST["skills_acquired"]) && isset($_POST["status"]))
{
    $attendance_rate = (int)$_POST["attendance_rate"];
    $beneficiary_id = (int)$_POST["beneficiary_id"];
    date_default_timezone_set('Asia/Manila');
    $enrollment_date = $_POST["enrollment_date"];
    $pre_assessment_score = (int)$_POST["pre_assessment_score"];
    $program_id = (int)$_POST["program_id"];
    $skills_acquired = $_POST["skills_acquired"];
    $status = $_POST["status"];

    if(empty($attendance_rate) && $attendance_rate !== 0)
    {
        echo json_encode(["success" => false, "message" => "Attendance Rate is empty"]);
        exit();
    }

    if(empty($beneficiary_id) && $beneficiary_id !== 0)
    {
        echo json_encode(["success" => false, "message" => "Beneficiary ID is empty"]);
        exit();
    }

    if(empty($enrollment_date))
    {
        echo json_encode(["success" => false, "message" => "Enrollment Date is empty"]);
        exit();
    }

    if(empty($pre_assessment_score) && $pre_assessment_score !== 0)
    {
        echo json_encode(["success" => false, "message" => "Pre-assessment Score is empty"]);
        exit();
    }

    if(empty($program_id) && $program_id !== 0)
    {
        echo json_encode(["success" => false, "message" => "Program ID is empty"]);
        exit();
    }   


    if(empty($skills_acquired))
    {
        echo json_encode(["success" => false, "message" => "Skills Acquired is empty"]);
        exit();
    }   

    // Save the enrollment data to the database
    $sql = "INSERT INTO program_enrollments 
            (attendance_rate, beneficiary_id, enrollment_date, pre_assessment_score, program_id, skills_acquired, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iississ", $attendance_rate, $beneficiary_id, $enrollment_date, $pre_assessment_score, $program_id, $skills_acquired, $status);

    if($stmt->execute())
    {
        // Log the enrollment action
        $user_id = $_SESSION['user_id'] ?? 1; // fallback user id
        $description_log = "Saved enrollment for Beneficiary ID: $beneficiary_id in Program ID: $program_id";
        system_logs($conn, $user_id, 'Insert', $description_log);
        echo json_encode(["success" => true]);
    }
    else
    {
        echo json_encode([
            "success" => false, 
            "message" => "Failed to save enrollment: " . $stmt->error
        ]);
    }
}
else
{
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
