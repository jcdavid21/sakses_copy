<?php 
session_start();
require_once "../config_normal.php";
require_once "../system_logs.php";

header('Content-Type: application/json');

// Response array
$response = ["success" => false, "message" => ""];

// Required fields (no id since auto-increment)
$required_fields = [
    "add_civil_status", "complete_address", "contact_number",
    "add_date_of_birth", "education_level", "email", "employment_status_before",
    "family_size", "add_first_name", "add_gender",
    "has_disability", "household_head", "is_indigenous",
    "is_pantawid_beneficiary", "add_last_name", "monthly_income_before",
    "add_barangay"
];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === "") {
        $response["message"] = "Missing or empty field: $field";
        echo json_encode($response);
        exit;
    }
}

// Sanitize input
$civil_status            = trim($_POST["add_civil_status"]);
$complete_address        = trim($_POST["complete_address"]);
$contact_number          = trim($_POST["contact_number"]);
$date_of_birth           = trim($_POST["add_date_of_birth"]);
$education_level         = trim($_POST["education_level"]);
$email                   = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$employment_status_before= trim($_POST["employment_status_before"]);
$family_size             = intval($_POST["family_size"]);
$first_name              = trim($_POST["add_first_name"]);
$gender                  = trim($_POST["add_gender"]);
$has_disability          = intval($_POST["has_disability"]);
$household_head          = intval($_POST["household_head"]);
$is_indigenous           = intval($_POST["is_indigenous"]);
$is_pantawid_beneficiary = intval($_POST["is_pantawid_beneficiary"]);
$last_name               = trim($_POST["add_last_name"]);
$middle_name             = trim($_POST["add_middle_name"]);
$monthly_income_before   = floatval($_POST["monthly_income_before"]);
$barangay_id             = intval($_POST["add_barangay"]);

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

$max_id_query = "SELECT MAX(id) as last_id FROM beneficiaries";
$stmt = $conn->prepare($max_id_query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$next_beneficiary_id = $row['last_id'] + 1;

// Fixed INSERT query - removed extra placeholders
$sql = "INSERT INTO beneficiaries (
            first_name, middle_name, last_name, gender, date_of_birth, 
            civil_status, education_level, complete_address, contact_number, email, 
            family_size, household_head, has_disability, is_indigenous, is_pantawid_beneficiary, 
            employment_status_before, monthly_income_before, barangay_id, created_at, updated_at, beneficiary_id
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $response["message"] = "Database error: " . $conn->error;
    echo json_encode($response);
    exit;
}

date_default_timezone_set('Asia/Manila');
$created_at = date('Y-m-d H:i:s');
$updated_at = $created_at;

// Fixed bind_param - matching the number of columns exactly
$stmt->bind_param(
    "ssssssssssiiiiisdissi",
    $first_name,              // s
    $middle_name,             // s
    $last_name,               // s
    $gender,                  // s
    $date_of_birth,           // s
    $civil_status,            // s
    $education_level,         // s
    $complete_address,        // s
    $contact_number,          // s
    $email,                   // s
    $family_size,             // i
    $household_head,          // i
    $has_disability,          // i
    $is_indigenous,           // i
    $is_pantawid_beneficiary, // i
    $employment_status_before,// s
    $monthly_income_before,   // d
    $barangay_id,             // i
    $created_at,              // s
    $updated_at,               // s
    $next_beneficiary_id      // i
);

if ($stmt->execute()) {

    // Log the add action
    $user_id = $_SESSION['user_id'] ?? 1001;
    $description = "Added new beneficiary ID: $new_id, Name: $first_name $middle_name $last_name";
    system_logs($conn, $user_id, 'Insert', $description);

    $response["success"] = true;
    $response["message"] = "Beneficiary added successfully.";
    $response["id"] = $new_id;
} else {
    $response["message"] = "Insert failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>