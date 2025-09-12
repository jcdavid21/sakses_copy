<?php 
require_once "../system_logs.php";
require_once "../config_normal.php";
session_start();

header('Content-Type: application/json');

if(isset($_POST["enrollment_id"]) && isset($_POST["attendance_rate"]) && isset($_POST["enrollment_date"]) && isset($_POST["pre_assessment_score"]) && isset($_POST["skills_acquired"]) && isset($_POST["status"]))
{
    date_default_timezone_set('Asia/Manila');

    // Sanitize + Cast
    $enrollment_id         = (int)$_POST["enrollment_id"];
    $attendance_rate       = (float)$_POST["attendance_rate"];
    $beneficiary_id        = (int)$_POST["beneficiary_id"];
    $enrollment_date       = trim($_POST["enrollment_date"]);
    $pre_assessment_score  = (float)$_POST["pre_assessment_score"];
    $post_assessment_score = $_POST["post_assessment_score"] !== "" ? (float)$_POST["post_assessment_score"] : null;
    $completion_date       = $_POST["completion_date"] !== "" ? trim($_POST["completion_date"]) : null;
    $certification_received= trim($_POST["certification_received"] ?? "");
    $dropout_reason        = trim($_POST["dropout_reason"] ?? "");
    $skills_acquired       = trim($_POST["skills_acquired"]);
    $status                = trim($_POST["status"]);

    // -------------------------------
    // VALIDATION RULES
    // -------------------------------
    $errors = [];

    if($enrollment_id <= 0) {
        $errors[] = "Invalid enrollment ID.";
    }

    if($attendance_rate < 0 || $attendance_rate > 100) {
        $errors[] = "Attendance rate must be between 0 and 100.";
    }

    if($pre_assessment_score < 0) {
        $errors[] = "Pre-assessment score cannot be negative.";
    }

    if($post_assessment_score !== null && $post_assessment_score < 0) {
        $errors[] = "Post-assessment score cannot be negative.";
    }


    if(strlen($skills_acquired) < 2) {
        $errors[] = "Skills acquired must have at least 2 characters.";
    }

    if(!empty($errors)) {
        $str = implode(", ", $errors);
        echo json_encode([
            "success" => false,
            "message" => "Validation failed: " . $str,
            "errors" => $errors
        ]);
        exit;
    }

    // -------------------------------
    // UPDATE DATABASE
    // -------------------------------
    $sql = "UPDATE program_enrollments SET
                attendance_rate = ?,
                enrollment_date = ?,
                pre_assessment_score = ?,
                post_assessment_score = ?,
                completion_date = ?,
                certification_received = ?,
                dropout_reason = ?,
                skills_acquired = ?,
                status = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "dssssssssi", 
        $attendance_rate, 
        $enrollment_date, 
        $pre_assessment_score, 
        $post_assessment_score, 
        $completion_date, 
        $certification_received, 
        $dropout_reason, 
        $skills_acquired, 
        $status,
        $enrollment_id
    );

    if($stmt->execute())
    {
        $user_id = $_SESSION['user_id'] ?? 1; 
        $description_log = "Updated enrollment ID: $enrollment_id for Beneficiary ID: $beneficiary_id in Program ID: $program_id";
        system_logs($conn, $user_id, 'Update', $description_log);

        echo json_encode(["success" => true]);
    }
    else
    {
        echo json_encode([
            "success" => false, 
            "message" => "Failed to update enrollment: " . $stmt->error
        ]);
    }
}
else
{
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
