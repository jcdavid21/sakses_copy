<?php 
session_start();
require_once "../config_normal.php";
require_once "../system_logs.php";

header('Content-Type: application/json');

// Response array
$response = ["success" => false, "message" => ""];

// Validate required fields
$required_fields = [
    "civil_status", "complete_address", "contact_number",
    "date_of_birth", "education_level", "email", "employment_status_before",
    "family_size", "first_name", "gender",
    "has_disability", "household_head", "id", "is_indigenous",
    "is_pantawid_beneficiary", "last_name", "monthly_income_before",
    "barangay_id"
];


foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === "") {
        $response["message"] = "Missing or empty field: $field";
        echo json_encode($response);
        exit;
    }
}


// Sanitize input
$civil_status            = trim($_POST["civil_status"]);
$complete_address        = trim($_POST["complete_address"]);
$contact_number          = trim($_POST["contact_number"]);
$date_of_birth           = trim($_POST["date_of_birth"]);
$education_level         = trim($_POST["education_level"]);
$email                   = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$employment_status_before= trim($_POST["employment_status_before"]);
$family_size             = intval($_POST["family_size"]);
$first_name              = trim($_POST["first_name"]);
$gender                  = trim($_POST["gender"]);
$has_disability          = intval($_POST["has_disability"]);
$household_head          = intval($_POST["household_head"]);
$id                      = intval($_POST["id"]);
$is_indigenous           = intval($_POST["is_indigenous"]);
$is_pantawid_beneficiary = intval($_POST["is_pantawid_beneficiary"]);
$last_name               = trim($_POST["last_name"]);
$middle_name             = trim($_POST["middle_name"]);
$monthly_income_before   = floatval($_POST["monthly_income_before"]);

// Extra validations
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response["message"] = "Invalid email format.";
    echo json_encode($response);
    exit;
}

if (!preg_match('/^[0-9]{11}$/', $contact_number)) {
    $response["message"] = "Invalid contact number. Must be 11 digits.";
    echo json_encode($response);
    exit;
}

// Update query
$sql = "UPDATE beneficiaries SET 
            first_name = ?, 
            middle_name = ?, 
            last_name = ?, 
            gender = ?, 
            date_of_birth = ?, 
            civil_status = ?, 
            education_level = ?, 
            complete_address = ?, 
            contact_number = ?, 
            email = ?, 
            family_size = ?, 
            household_head = ?, 
            has_disability = ?, 
            is_indigenous = ?, 
            is_pantawid_beneficiary = ?, 
            employment_status_before = ?, 
            monthly_income_before = ?,
            updated_at = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $response["message"] = "Database error: " . $conn->error;
    echo json_encode($response);
    exit;
}

date_default_timezone_set('Asia/Manila');
$updated_at = date('Y-m-d H:i:s');

$stmt->bind_param(
    "ssssssssssiiiiisdsi",
    $first_name,
    $middle_name,
    $last_name,
    $gender,
    $date_of_birth,
    $civil_status,
    $education_level,
    $complete_address,
    $contact_number,
    $email,
    $family_size,             // i
    $household_head,          // i
    $has_disability,          // i
    $is_indigenous,           // i
    $is_pantawid_beneficiary, // i
    $employment_status_before,// s
    $monthly_income_before,   // d
    $updated_at,              // s
    $id                       // i
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // Log the update action
        $user_id = $_SESSION['user_id'] ?? 1001;
        $description = "Updated beneficiary ID: $id, Name: $first_name $middle_name $last_name";
        system_logs($conn, $user_id, 'Update', $description);

        $response["success"] = true;
        $response["message"] = "Beneficiary updated successfully.";
    } else {
        $response["message"] = "No changes made or record not found.";
    }
} else {
    $response["message"] = "Update failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
