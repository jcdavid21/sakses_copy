<!DOCTYPE html>
<html lang="en">
<?php
include "../backend/config.php";
session_start();

// Python Flask API endpoint
$python_api_url = 'http://localhost:8800';

// Function to call Python API
function callPythonAPI($endpoint, $data = null)
{
    global $python_api_url;
    $url = $python_api_url . $endpoint;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        return json_decode($response, true);
    }
    return null;
}

// Fetch beneficiaries from database
$sql = "SELECT 
    b.id, 
    b.beneficiary_id, 
    b.first_name, b.middle_name, b.last_name,
    CONCAT(b.first_name, ' ', IFNULL(b.middle_name, ''), ' ', b.last_name) as full_name,
    b.date_of_birth,
    b.gender,
    b.civil_status,
    b.education_level,
    b.contact_number,
    b.email,
    b.complete_address,
    b.family_size,
    b.monthly_income_before,
    b.employment_status_before,
    b.is_pantawid_beneficiary,
    b.is_indigenous,
    b.has_disability,
    b.household_head,
    b.registration_date,
    TIMESTAMPDIFF(YEAR, b.date_of_birth, CURDATE()) as age,
    b.barangay_id
FROM beneficiaries b 
ORDER BY b.registration_date DESC";

$result = $conn->query($sql);
$beneficiaries = $result->fetch_all(MYSQLI_ASSOC);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(*) as total_beneficiaries,
    COUNT(CASE WHEN gender = 'Male' THEN 1 END) as male_count,
    COUNT(CASE WHEN gender = 'Female' THEN 1 END) as female_count,
    COUNT(CASE WHEN is_pantawid_beneficiary = 1 THEN 1 END) as pantawid_count,
    COUNT(CASE WHEN has_disability = 1 THEN 1 END) as pwd_count,
    COUNT(CASE WHEN is_indigenous = 1 THEN 1 END) as indigenous_count,
    AVG(monthly_income_before) as avg_income
FROM beneficiaries";

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../styles/beneficiaries.css">
    <style>
        .btn-edit {
            background-color: #17a2b8;
            color: white;
        }

        .action-btn {
            padding: 0.375rem 0.5rem;
            border: none;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-top: 1px solid #dee2e6;
            margin-top: 20px;
        }

        .pagination-info {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .pagination {
            margin: 0;
        }

        .pagination .page-link {
            color: #17a2b8;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            text-decoration: none;
        }

        .pagination .page-item.active .page-link {
            background-color: #446cadff;
            border-color: #446cadff;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
            border-color: #17a2b8;
            color: #17a2b8;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>
    <title>Beneficiaries - SAKSES</title>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content p-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container-fluid">
                <h1 class="page-title">
                    <i class="fas fa-users"></i>
                    Beneficiaries Management
                </h1>
                <p class="page-subtitle">Manage and monitor program beneficiaries with AI-powered insights</p>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_beneficiaries']); ?></div>
                <div class="stat-label">Total Beneficiaries</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['pantawid_count']); ?></div>
                <div class="stat-label">4Ps Recipients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-wheelchair"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['pwd_count']); ?></div>
                <div class="stat-label">PWD Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['indigenous_count']); ?></div>
                <div class="stat-label">Indigenous People</div>
            </div>
        </div>

        <!-- Main Beneficiaries Card -->
        <div class="beneficiaries-card">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-list-ul"></i>
                    Beneficiaries Directory
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary-custom btn-custom" onclick="showAddBeneficiaryModal()">
                        <i class="fas fa-plus"></i> Add Beneficiary
                    </button>
                    <button class="btn btn-outline-secondary btn-custom" onclick="exportData()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchInput"
                                placeholder="Search beneficiaries...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="genderFilter">
                            <option value="">All Genders</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="employmentFilter">
                            <option value="">All Employment</option>
                            <option value="employed">Employed</option>
                            <option value="unemployed">Unemployed</option>
                            <option value="underemployed">Underemployed</option>
                            <option value="self_employed">Self-Employed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pantawid">4Ps Recipients</option>
                            <option value="pwd">PWD</option>
                            <option value="indigenous">Indigenous</option>
                            <option value="household_head">Household Head</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100 btn-custom" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Beneficiaries Table -->
            <div class="table-responsive">
                <table class="table beneficiaries-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Employment</th>
                            <th>Income</th>
                            <th>Special</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="beneficiariesTableBody">
                        <!-- Table rows will be generated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="pagination-container" id="paginationContainer">
                <div class="pagination-info">
                    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalRecords">0</span> entries
                </div>
                <nav aria-label="Beneficiaries pagination">
                    <ul class="pagination" id="paginationList">
                        <!-- Pagination buttons will be generated by JavaScript -->
                    </ul>
                </nav>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading beneficiaries...</p>
            </div>
        </div>
    </div>

    <!-- Add Beneficiary Modal -->
    <div class="modal fade" id="addBeneficiaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Add New Beneficiary</h5>
                        <p class="modal-subtitle">Register a new program beneficiary</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addBeneficiaryForm">
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-user"></i>
                                <span>Personal Information</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" name="add_first_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" name="add_middle_name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" name="add_last_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" name="add_date_of_birth" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gender *</label>
                                    <select class="form-select" name="add_gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Civil Status *</label>
                                    <select class="form-select" name="add_civil_status" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Education & Employment</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Education Level *</label>
                                    <select class="form-select" name="education_level" id="add_education_level" required>
                                        <option value="">Select Education</option>
                                        <option value="Elementary">Elementary</option>
                                        <option value="High School">High School</option>
                                        <option value="Senior High">Senior High</option>
                                        <option value="College">College</option>
                                        <option value="Vocational">Vocational</option>
                                        <option value="Post Graduate">Post Graduate</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Employment Status *</label>
                                    <select class="form-select" name="employment_status_before" id="add_employment_status_before" required>
                                        <option value="">Select Status</option>
                                        <option value="employed">Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                        <option value="underemployed">Underemployed</option>
                                        <option value="self_employed">Self-Employed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Monthly Income (Before) *</label>
                                    <input type="number" class="form-control" name="monthly_income_before" id="add_monthly_income_before" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Family Size *</label>
                                    <input type="number" class="form-control" name="family_size" id="add_family_size" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-address-card"></i>
                                <span>Contact Information</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number" id="add_contact_number">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="add_email">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Barangay *</label>
                                    <select class="form-select" id="add_barangay" name="barangay_id" required>
                                        <option value="">Select Barangay</option>
                                        <?php
                                        // Fetch barangays from the database
                                        $barangays = $conn->query("SELECT id, name FROM barangays");
                                        while ($row = $barangays->fetch_assoc()) {
                                            echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Complete Address *</label>
                                    <textarea class="form-control" name="complete_address" id="add_complete_address" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-star"></i>
                                <span>Special Categories</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" name="is_pantawid_beneficiary" id="add_pantawid">
                                        <label class="form-check-label" for="add_pantawid">4Ps Beneficiary</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" name="is_indigenous" id="add_indigenous">
                                        <label class="form-check-label" for="add_indigenous">Indigenous People</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" name="has_disability" id="add_disability">
                                        <label class="form-check-label" for="add_disability">Person with Disability</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" name="household_head" id="add_household_head">
                                        <label class="form-check-label" for="add_household_head">Household Head</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveBeneficiary()">
                        <i class="fas fa-save"></i> Save Beneficiary
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated Edit Beneficiary Modal with Modern Design -->
    <div class="modal fade" id="editBeneficiaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Edit Beneficiary</h5>
                        <p class="modal-subtitle">Update beneficiary information</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBeneficiaryForm">
                        <input type="hidden" id="edit_beneficiary_id" name="id">

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-user"></i>
                                <span>Personal Information</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" id="edit_date_of_birth" name="date_of_birth" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gender *</label>
                                    <select class="form-select" id="edit_gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Civil Status *</label>
                                    <select class="form-select" id="edit_civil_status" name="civil_status" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Education & Employment</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Education Level *</label>
                                    <select class="form-select" id="edit_education_level" name="education_level" required>
                                        <option value="">Select Education</option>
                                        <option value="Elementary">Elementary</option>
                                        <option value="High School">High School</option>
                                        <option value="Senior High">Senior High</option>
                                        <option value="College">College</option>
                                        <option value="Vocational">Vocational</option>
                                        <option value="Graduate">Graduate</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Employment Status *</label>
                                    <select class="form-select" id="edit_employment_status_before" name="employment_status_before" required>
                                        <option value="">Select Status</option>
                                        <option value="employed">Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                        <option value="underemployed">Underemployed</option>
                                        <option value="self_employed">Self-Employed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Monthly Income (Before) *</label>
                                    <input type="number" class="form-control" id="edit_monthly_income_before" name="monthly_income_before" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Family Size *</label>
                                    <input type="number" class="form-control" id="edit_family_size" name="family_size" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-address-card"></i>
                                <span>Contact Information</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="edit_contact_number" name="contact_number">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="edit_email" name="email">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Barangay *</label>
                                    <select class="form-select" id="edit_barangay" name="barangay_id" required>
                                        <option value="">Select Barangay</option>
                                        <?php
                                        // Fetch barangays from the database
                                        $barangays = $conn->query("SELECT id, name FROM barangays");
                                        while ($row = $barangays->fetch_assoc()) {
                                            echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Complete Address *</label>
                                    <textarea class="form-control" id="edit_complete_address" name="complete_address" rows="3" required></textarea>
                                </div>

                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-star"></i>
                                <span>Special Categories</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" id="edit_pantawid" name="is_pantawid_beneficiary" value="1">
                                        <label class="form-check-label" for="edit_pantawid">4Ps Beneficiary</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" id="edit_indigenous" name="is_indigenous" value="1">
                                        <label class="form-check-label" for="edit_indigenous">Indigenous People</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" id="edit_disability" name="has_disability" value="1">
                                        <label class="form-check-label" for="edit_disability">Person with Disability</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" id="edit_household_head" name="household_head" value="1">
                                        <label class="form-check-label" for="edit_household_head">Household Head</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateBeneficiary()">
                        <i class="fas fa-save"></i> Update Beneficiary
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Global variables
        let allBeneficiaries = <?php echo json_encode($beneficiaries); ?>;
        let filteredBeneficiaries = [...allBeneficiaries];
        let currentPage = 1;
        const recordsPerPage = 10;

        // Initialize page
        $(document).ready(function() {
            // Clear PHP-generated table rows first
            $('#beneficiariesTableBody').empty();

            initializeFilters();
            initializeSearch();
            filterBeneficiaries(); // This will render the table with pagination
        });

        // Initialize search functionality
        function initializeSearch() {
            $('#searchInput').on('keyup', function() {
                currentPage = 1; // Reset to first page when searching
                filterBeneficiaries();
            });
        }

        // Initialize filter functionality
        function initializeFilters() {
            $('#genderFilter, #employmentFilter, #statusFilter').on('change', function() {
                currentPage = 1; // Reset to first page when filtering
                filterBeneficiaries();
            });
        }

        // Filter beneficiaries based on search and filters
        function filterBeneficiaries() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const genderFilter = $('#genderFilter').val();
            const employmentFilter = $('#employmentFilter').val();
            const statusFilter = $('#statusFilter').val();

            filteredBeneficiaries = allBeneficiaries.filter(beneficiary => {
                // Search filter
                const matchesSearch = !searchTerm ||
                    beneficiary.full_name.toLowerCase().includes(searchTerm) ||
                    beneficiary.beneficiary_id.toLowerCase().includes(searchTerm) ||
                    (beneficiary.email && beneficiary.email.toLowerCase().includes(searchTerm)) ||
                    beneficiary.complete_address.toLowerCase().includes(searchTerm);

                // Gender filter
                const matchesGender = !genderFilter || beneficiary.gender === genderFilter;

                // Employment filter
                const matchesEmployment = !employmentFilter || beneficiary.employment_status_before === employmentFilter;

                // Status filter
                let matchesStatus = true;
                if (statusFilter) {
                    switch (statusFilter) {
                        case 'pantawid':
                            matchesStatus = beneficiary.is_pantawid_beneficiary == 1;
                            break;
                        case 'pwd':
                            matchesStatus = beneficiary.has_disability == 1;
                            break;
                        case 'indigenous':
                            matchesStatus = beneficiary.is_indigenous == 1;
                            break;
                        case 'household_head':
                            matchesStatus = beneficiary.household_head == 1;
                            break;
                    }
                }

                return matchesSearch && matchesGender && matchesEmployment && matchesStatus;
            });

            renderBeneficiariesTable();
            renderPagination();
        }

        // Render beneficiaries table with pagination
        function renderBeneficiariesTable() {
            const tbody = $('#beneficiariesTableBody');
            tbody.empty();

            if (filteredBeneficiaries.length === 0) {
                tbody.append(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2 mb-0">No beneficiaries found matching your criteria</p>
                </td>
            </tr>
        `);
                $('#paginationContainer').hide();
                return;
            }

            // Calculate pagination
            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = Math.min(startIndex + recordsPerPage, filteredBeneficiaries.length);
            const currentPageData = filteredBeneficiaries.slice(startIndex, endIndex);

            // Render current page data
            currentPageData.forEach(beneficiary => {
                const row = createBeneficiaryRow(beneficiary);
                tbody.append(row);
            });

            $('#paginationContainer').show();
            updatePaginationInfo();
        }

        // Create beneficiary table row
        function createBeneficiaryRow(beneficiary) {
            const specialIndicators = [];
            if (beneficiary.is_pantawid_beneficiary == 1) {
                specialIndicators.push('<span class="indicator indicator-pantawid" title="4Ps Beneficiary">4P</span>');
            }
            if (beneficiary.is_indigenous == 1) {
                specialIndicators.push('<span class="indicator indicator-indigenous" title="Indigenous People">IP</span>');
            }
            if (beneficiary.has_disability == 1) {
                specialIndicators.push('<span class="indicator indicator-pwd" title="Person with Disability">PWD</span>');
            }
            if (beneficiary.household_head == 1) {
                specialIndicators.push('<span class="indicator indicator-head" title="Household Head">HH</span>');
            }

            return `
        <tr data-beneficiary='${JSON.stringify(beneficiary)}'>
            <td><span class="fw-bold">${beneficiary.beneficiary_id}</span></td>
            <td>
                <div>
                    <div class="fw-semibold">${beneficiary.full_name}</div>
                    <small class="text-muted">${beneficiary.email || 'No email'}</small>
                </div>
            </td>
            <td><span class="fw-medium">${beneficiary.age} years</span></td>
            <td>
                <span class="status-badge ${beneficiary.gender === 'Male' ? 'gender-male' : 'gender-female'}">
                    ${beneficiary.gender}
                </span>
            </td>
            <td>
                <span class="status-badge">
                    ${beneficiary.civil_status.replace('_', ' ')}
                </span>
            </td>
            <td>
                <span class="status-badge status-${beneficiary.employment_status_before}">
                    ${beneficiary.employment_status_before.replace('_', ' ')}
                </span>
            </td>
            <td><span class="fw-semibold">₱${parseFloat(beneficiary.monthly_income_before).toLocaleString()}</span></td>
            <td>
                <div class="special-indicators">
                    ${specialIndicators.join('')}
                </div>
            </td>
            <td>
                <div class="d-flex">
                    <button class="action-btn btn-view" onclick="viewBeneficiary(${beneficiary.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn btn-edit" onclick="editBeneficiary(${beneficiary.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
        }

        // Render pagination controls
        function renderPagination() {
            const totalPages = Math.ceil(filteredBeneficiaries.length / recordsPerPage);
            const paginationList = $('#paginationList');
            paginationList.empty();

            if (totalPages <= 1) {
                $('#paginationContainer').hide();
                return;
            }

            $('#paginationContainer').show();

            // Previous button
            const prevDisabled = currentPage === 1 ? 'disabled' : '';
            paginationList.append(`
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Previous</a>
        </li>
    `);

            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            // First page
            if (startPage > 1) {
                paginationList.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(1); return false;">1</a>
            </li>
        `);
                if (startPage > 2) {
                    paginationList.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
                }
            }

            // Page numbers around current page
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                paginationList.append(`
            <li class="page-item ${activeClass}">
                <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            </li>
        `);
            }

            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationList.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
                }
                paginationList.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a>
            </li>
        `);
            }

            // Next button
            const nextDisabled = currentPage === totalPages ? 'disabled' : '';
            paginationList.append(`
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next</a>
        </li>
    `);
        }

        // Change page
        function changePage(page) {
            const totalPages = Math.ceil(filteredBeneficiaries.length / recordsPerPage);

            if (page < 1 || page > totalPages) {
                return;
            }

            currentPage = page;
            renderBeneficiariesTable();
            renderPagination();
        }

        // Update pagination info
        function updatePaginationInfo() {
            if (filteredBeneficiaries.length === 0) {
                $('#showingStart').text(0);
                $('#showingEnd').text(0);
                $('#totalRecords').text(0);
                return;
            }

            const startIndex = (currentPage - 1) * recordsPerPage + 1;
            const endIndex = Math.min(currentPage * recordsPerPage, filteredBeneficiaries.length);

            $('#showingStart').text(startIndex);
            $('#showingEnd').text(endIndex);
            $('#totalRecords').text(filteredBeneficiaries.length);
        }

        // Clear all filters
        function clearFilters() {
            $('#searchInput').val('');
            $('#genderFilter').val('');
            $('#employmentFilter').val('');
            $('#statusFilter').val('');
            currentPage = 1;
            filterBeneficiaries();
        }


        async function updateBeneficiary() {
            const form = document.getElementById('editBeneficiaryForm');
            const formData = new FormData(form);

            const checkboxMap = {
                is_pantawid_beneficiary: "edit_pantawid",
                is_indigenous: "edit_indigenous",
                has_disability: "edit_disability",
                household_head: "edit_household_head"
            };

            Object.entries(checkboxMap).forEach(([field, id]) => {
                const el = document.getElementById(id);
                formData.set(field, el && el.checked ? 1 : 0);
            });


            // Generate full name
            formData.set("full_name",
                `${formData.get("first_name")} ${formData.get("middle_name") || ""} ${formData.get("last_name")}`.trim()
            );

            // Calculate age (if needed)
            if (formData.get("date_of_birth")) {
                const birthDate = new Date(formData.get("date_of_birth"));
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                formData.set("age", age);
            }


            $.ajax({
                url: '../backend/beneficiaries/update_beneficiary.php',
                type: 'POST',
                data: Object.fromEntries(formData), // convert to plain object
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Beneficiary updated successfully',
                            confirmButtonColor: '#27ae60'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update beneficiary',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error || 'Failed to update beneficiary',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            });
        }


        function viewBeneficiary(beneficiaryId) {
            const beneficiary = allBeneficiaries.find(b => b.id == beneficiaryId);
            if (!beneficiary) return;

            Swal.fire({
                title: `
                    <div class="modal-title-custom">
                        <div class="modal-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="modal-name-id">
                            <h3>${beneficiary.full_name}</h3>
                        </div>
                    </div>
                `,
                html: `
                    <div class="beneficiary-details-modal">
                        <!-- Personal Information Card -->
                        <div class="detail-card">
                            <div class="detail-card-header">
                                <i class="fas fa-user"></i>
                                <span>Personal Information</span>
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <label>Full Name</label>
                                        <value>${beneficiary.full_name}</value>
                                    </div>
                                    <div class="detail-item">
                                        <label>Age</label>
                                        <value>${beneficiary.age} years old</value>
                                    </div>
                                    <div class="detail-item">
                                        <label>Gender</label>
                                        <value>
                                            <span class="status-badge ${beneficiary.gender === 'Male' ? 'gender-male' : 'gender-female'}">
                                                ${beneficiary.gender}
                                            </span>
                                        </value>
                                    </div>
                                    <div class="detail-item">
                                        <label>Civil Status</label>
                                        <value>${beneficiary.civil_status.replace('_', ' ')}</value>
                                    </div>
                                    <div class="detail-item">
                                        <label>Education Level</label>
                                        <value>${beneficiary.education_level}</value>
                                    </div>
                                    <div class="detail-item">
                                        <label>Family Size</label>
                                        <value>${beneficiary.family_size} members</value>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Card -->
                        <div class="detail-card">
                            <div class="detail-card-header">
                                <i class="fas fa-address-book"></i>
                                <span>Contact Information</span>
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <label>Phone Number</label>
                                        <value>${beneficiary.contact_number || 'Not provided'}</value>
                                    </div>
                                    <div class="detail-item">
                                        <label>Email Address</label>
                                        <value>${beneficiary.email || 'Not provided'}</value>
                                    </div>
                                    <div class="detail-item full-width">
                                        <label>Complete Address</label>
                                        <value>${beneficiary.complete_address}</value>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Economic Information Card -->
                        <div class="detail-card">
                            <div class="detail-card-header">
                                <i class="fas fa-chart-line"></i>
                                <span>Economic Information</span>
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <label>Employment Status</label>
                                        <value>
                                            <span class="status-badge status-${beneficiary.employment_status_before}">
                                                ${beneficiary.employment_status_before.replace('_', ' ')}
                                            </span>
                                        </value>
                                    </div>
                                    <div class="detail-item">
                                        <label>Monthly Income</label>
                                        <value class="income-value">₱${parseFloat(beneficiary.monthly_income_before).toLocaleString()}</value>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Categories Card -->
                        <div class="detail-card">
                            <div class="detail-card-header">
                                <i class="fas fa-star"></i>
                                <span>Special Categories</span>
                            </div>
                            <div class="detail-card-body">
                                <div class="special-categories">
                                    ${beneficiary.is_pantawid_beneficiary == 1 ? '<div class="category-badge pantawid"><i class="fas fa-hand-holding-heart"></i> 4Ps Beneficiary</div>' : ''}
                                    ${beneficiary.is_indigenous == 1 ? '<div class="category-badge indigenous"><i class="fas fa-seedling"></i> Indigenous People</div>' : ''}
                                    ${beneficiary.has_disability == 1 ? '<div class="category-badge pwd"><i class="fas fa-wheelchair"></i> Person with Disability</div>' : ''}
                                    ${beneficiary.household_head == 1 ? '<div class="category-badge household-head"><i class="fas fa-home"></i> Household Head</div>' : ''}
                                    ${beneficiary.is_pantawid_beneficiary != 1 && beneficiary.is_indigenous != 1 && beneficiary.has_disability != 1 && beneficiary.household_head != 1 ? '<div class="no-special-categories">No special categories</div>' : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Registration Info -->
                        <div class="registration-info">
                            <i class="fas fa-calendar"></i>
                            <span>Registered on ${new Date(beneficiary.registration_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                        </div>
                    </div>
                `,
                width: '900px',
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times"></i> Close',
                confirmButtonColor: '#8e44ad',
                cancelButtonColor: '#6c757d',
                customClass: {
                    popup: 'beneficiary-modal',
                    title: 'beneficiary-modal-title',
                    htmlContainer: 'beneficiary-modal-content'
                }
            })
        }

        function editBeneficiary(beneficiaryId) {
            const beneficiary = allBeneficiaries.find(b => b.id == beneficiaryId);
            if (!beneficiary) return;
            console.log(beneficiary);

            // Populate all form fields
            $('#edit_beneficiary_id').val(beneficiary.id);
            $('#edit_first_name').val(beneficiary.first_name);
            $('#edit_middle_name').val(beneficiary.middle_name);
            $('#edit_last_name').val(beneficiary.last_name);
            $('#edit_date_of_birth').val(beneficiary.date_of_birth);
            $('#edit_gender').val(beneficiary.gender);
            $('#edit_civil_status').val(beneficiary.civil_status);
            $('#edit_education_level').val(beneficiary.education_level);
            $('#edit_employment_status_before').val(beneficiary.employment_status_before);
            $('#edit_monthly_income_before').val(beneficiary.monthly_income_before);
            $('#edit_family_size').val(beneficiary.family_size);
            $('#edit_contact_number').val(beneficiary.contact_number);
            $('#edit_email').val(beneficiary.email);
            $('#edit_complete_address').val(beneficiary.complete_address);
            $('#edit_barangay').val(beneficiary.barangay_id);

            // Set checkboxes
            $('#edit_pantawid').prop('checked', beneficiary.is_pantawid_beneficiary == 1);
            $('#edit_indigenous').prop('checked', beneficiary.is_indigenous == 1);
            $('#edit_disability').prop('checked', beneficiary.has_disability == 1);
            $('#edit_household_head').prop('checked', beneficiary.household_head == 1);

            // Show modal
            $('#editBeneficiaryModal').modal('show');
        }

        // Fixed function to save beneficiary
        async function saveBeneficiary() {
            const form = document.getElementById('addBeneficiaryForm');
            const formData = new FormData(form);

            // Handle checkbox fields BEFORE converting to data object
            const checkboxMap = {
                is_pantawid_beneficiary: "add_pantawid",
                is_indigenous: "add_indigenous",
                has_disability: "add_disability",
                household_head: "add_household_head"
            };

            // Set checkbox values in formData
            Object.entries(checkboxMap).forEach(([field, id]) => {
                const el = document.getElementById(id);
                formData.set(field, el && el.checked ? 1 : 0);
            });

            // Fix the barangay field name mismatch
            // The backend expects 'add_barangay' but the form has 'barangay_id'
            const barangayValue = formData.get('barangay_id');
            if (barangayValue) {
                formData.set('add_barangay', barangayValue);
                formData.delete('barangay_id'); // Remove the old field name
            }

            // Debug: Log all form data
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            try {
                $.ajax({
                    url: '../backend/beneficiaries/add_beneficiary.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log("Server response:", response);

                        // Handle both JSON and string responses
                        let result = response;
                        if (typeof response === 'string') {
                            try {
                                result = JSON.parse(response);
                            } catch (e) {
                                console.error("Failed to parse JSON:", response);
                                result = {
                                    success: false,
                                    message: "Invalid server response"
                                };
                            }
                        }

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Beneficiary added successfully',
                                confirmButtonColor: '#27ae60'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#addBeneficiaryModal').modal('hide');
                                    form.reset(); // Reset the form
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Failed to add beneficiary',
                                confirmButtonColor: '#e74c3c'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Network error: ' + (xhr.responseText || error),
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                });
            } catch (error) {
                console.error("Exception:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to add beneficiary',
                    confirmButtonColor: '#e74c3c'
                });
            }
        }

        // Function to retrain models - REPLACE EXISTING
        async function retrain_models() {
            Swal.fire({
                title: 'Retrain AI Models',
                text: 'This will retrain the machine learning models with the latest data. Continue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Retrain',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#8e44ad'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Retraining Models...',
                        html: `
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p>Please wait while the AI models are being retrained with the latest data.</p>
                        <small class="text-muted">This may take a few minutes...</small>
                    </div>
                `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    try {
                        const response = await fetch('http://localhost:8800/model/retrain', {
                            method: 'POST'
                        });
                        const result = await response.json();

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Models Retrained Successfully!',
                                html: `
                            <div class="alert alert-success">
                                <h6>Training Results:</h6>
                                <ul class="text-start mb-0">
                                    <li>Success Prediction Model: ${result.results.success_model ? 'Trained' : 'Failed'}</li>
                                    <li>Income Prediction Model: ${result.results.income_model ? 'Trained' : 'Failed'}</li>
                                </ul>
                            </div>
                            <p class="text-muted">AI models have been updated with the latest beneficiary data.</p>
                        `,
                                confirmButtonColor: '#27ae60'
                            });
                        } else {
                            throw new Error('Retraining failed');
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Retraining Failed',
                            text: 'Failed to retrain models. Please check the Python service and try again.',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                }
            });
        }



        // Display prediction results
        function displayPredictionResults(beneficiary, successPrediction, incomePrediction) {
            let successHtml = '<div class="alert alert-warning">Success prediction not available</div>';
            let incomeHtml = '<div class="alert alert-warning">Income prediction not available</div>';

            if (successPrediction && !successPrediction.error) {
                const riskColor = successPrediction.risk_level === 'Low' ? 'success' :
                    successPrediction.risk_level === 'Medium' ? 'warning' : 'danger';

                successHtml = `
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-chart-line"></i> Program Success Prediction</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="h4 text-primary">${(successPrediction.success_probability * 100).toFixed(1)}%</div>
                                    <small class="text-muted">Success Rate</small>
                                </div>
                                <div class="col-4">
                                    <div class="h4 text-info">${(successPrediction.confidence * 100).toFixed(1)}%</div>
                                    <small class="text-muted">Confidence</small>
                                </div>
                                <div class="col-4">
                                    <span class="badge bg-${riskColor} fs-6">${successPrediction.risk_level} Risk</span><br>
                                    <small class="text-muted">Risk Level</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            if (incomePrediction && !incomePrediction.error) {
                const currentIncome = parseFloat(beneficiary.monthly_income_before);
                const predictedIncome = incomePrediction.predicted_income;
                const increase = ((predictedIncome - currentIncome) / currentIncome * 100);
                const increaseColor = increase > 50 ? 'success' : increase > 20 ? 'warning' : 'info';

                incomeHtml = `
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Income Prediction</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="h5 text-success">₱${predictedIncome.toLocaleString()}</div>
                                    <small class="text-muted">Predicted Income</small>
                                </div>
                                <div class="col-4">
                                    <div class="h5 text-${increaseColor}">+${increase.toFixed(1)}%</div>
                                    <small class="text-muted">Income Increase</small>
                                </div>
                                <div class="col-4">
                                    <span class="badge bg-secondary fs-6">${incomePrediction.income_category}</span><br>
                                    <small class="text-muted">Income Category</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            Swal.fire({
                title: `AI Predictions for ${beneficiary.full_name}`,
                html: successHtml + incomeHtml,
                width: '800px',
                confirmButtonText: 'Close',
                confirmButtonColor: '#6c757d'
            });
        }

        // Update the showAddBeneficiaryModal function
        function showAddBeneficiaryModal() {
            $('#addBeneficiaryModal').modal('show');
        }

        // Export data
        function exportData() {
            // Convert filtered data to CSV
            const headers = ['ID', 'Name', 'Age', 'Gender', 'Civil Status', 'Education', 'Employment', 'Income', 'Address'];
            const csvData = [headers];

            filteredBeneficiaries.forEach(beneficiary => {
                csvData.push([
                    beneficiary.beneficiary_id,
                    beneficiary.full_name,
                    beneficiary.age,
                    beneficiary.gender,
                    beneficiary.civil_status,
                    beneficiary.education_level,
                    beneficiary.employment_status_before,
                    beneficiary.monthly_income_before,
                    beneficiary.complete_address
                ]);
            });

            const csvString = csvData.map(row => row.map(field => `"${field}"`).join(',')).join('\n');
            const blob = new Blob([csvString], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = 'beneficiaries_export.csv';
            a.click();

            window.URL.revokeObjectURL(url);
        }

        // Helper function for API calls to Python Flask service
        async function callPythonAPI(endpoint, data = null) {
            const pythonApiUrl = 'http://localhost:8800';

            try {
                const options = {
                    method: data ? 'POST' : 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                };

                if (data) {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(pythonApiUrl + endpoint, options);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('API call failed:', error);
                throw error;
            }
        }

        // PHP endpoints for predictions (fallback if direct Python API calls don't work)
        async function predictSuccess(beneficiaryData) {
            try {
                const response = await fetch('/predict_success_php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(beneficiaryData)
                });
                return await response.json();
            } catch (error) {
                console.error('Success prediction error:', error);
                return null;
            }
        }

        async function predictIncome(beneficiaryData) {
            try {
                const response = await fetch('/predict_income_php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(beneficiaryData)
                });
                return await response.json();
            } catch (error) {
                console.error('Income prediction error:', error);
                return null;
            }
        }


        // Export data
        function exportData() {
            // Convert filtered data to CSV
            const headers = ['ID', 'Name', 'Age', 'Gender', 'Civil Status', 'Education', 'Employment', 'Income', 'Address'];
            const csvData = [headers];

            filteredBeneficiaries.forEach(beneficiary => {
                csvData.push([
                    beneficiary.beneficiary_id,
                    beneficiary.full_name,
                    beneficiary.age,
                    beneficiary.gender,
                    beneficiary.civil_status,
                    beneficiary.education_level,
                    beneficiary.employment_status_before,
                    beneficiary.monthly_income_before,
                    beneficiary.complete_address
                ]);
            });

            const csvString = csvData.map(row => row.map(field => `"${field}"`).join(',')).join('\n');
            const blob = new Blob([csvString], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = 'beneficiaries_export.csv';
            a.click();

            window.URL.revokeObjectURL(url);
        }
    </script>

</body>

</html>