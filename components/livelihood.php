<!DOCTYPE html>
<html lang="en">
<?php
include "../backend/config.php";
session_start();

// Python Flask API endpoint
$python_api_url = 'http://localhost:8800';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


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

// Fetch livelihood programs from database
$sql = "SELECT 
    lp.id,
    lp.program_code,
    lp.program_name,
    lp.program_type,
    lp.description,
    lp.duration_months,
    lp.target_beneficiaries,
    lp.budget_allocated,
    lp.start_date,
    lp.end_date,
    lp.status,
    lp.success_criteria,
    lp.created_at,
    DATEDIFF(COALESCE(lp.end_date, CURDATE()), lp.start_date) as total_days,
    CASE 
        WHEN lp.status = 'completed' THEN 100
        WHEN lp.status = 'suspended' THEN 0
        ELSE GREATEST(0, LEAST(100, 
            DATEDIFF(CURDATE(), lp.start_date) * 100 / 
            NULLIF(DATEDIFF(COALESCE(lp.end_date, DATE_ADD(lp.start_date, INTERVAL lp.duration_months MONTH)), lp.start_date), 0)
        ))
    END as progress_percentage
FROM livelihood_programs lp 
ORDER BY lp.created_at DESC";

$result = $conn->query($sql);
$programs = $result->fetch_all(MYSQLI_ASSOC);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(*) as total_programs,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_programs,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_programs,
    COUNT(CASE WHEN status = 'planning' THEN 1 END) as planning_programs,
    COUNT(CASE WHEN status = 'suspended' THEN 1 END) as suspended_programs,
    SUM(target_beneficiaries) as total_target_beneficiaries,
    SUM(budget_allocated) as total_budget
FROM livelihood_programs";

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
    <title>Livelihood Programs - SAKSES</title>

    <style>
        .program-type-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }

        .program-type-skills_training {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .program-type-microenterprise {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .program-type-employment_facilitation {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .program-type-entrepreneurship {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .status-planning {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .status-active {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .status-completed {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .status-suspended {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .progress-bar-container {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .budget-amount {
            font-weight: 600;
            color: #2e7d32;
        }

        .program-details-modal .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .program-details-modal .detail-item.full-width {
            grid-column: 1 / -1;
        }

        .btn-delete {
            color: red;
        }

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
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content p-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container-fluid">
                <h1 class="page-title">
                    <i class="fas fa-briefcase"></i>
                    Livelihood Programs Management
                </h1>
                <p class="page-subtitle">Manage and monitor livelihood programs with comprehensive tracking</p>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_programs']); ?></div>
                <div class="stat-label">Total Programs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['active_programs']); ?></div>
                <div class="stat-label">Active Programs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_target_beneficiaries']); ?></div>
                <div class="stat-label">Target Beneficiaries</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['total_budget'], 0); ?></div>
                <div class="stat-label">Total Budget</div>
            </div>
        </div>

        <!-- Main Programs Card -->
        <div class="beneficiaries-card">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-list-ul"></i>
                    Programs Directory
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary-custom btn-custom" onclick="showAddProgramModal()">
                        <i class="fas fa-plus"></i> Add Program
                    </button>
                    <!-- <button class="btn btn-outline-secondary btn-custom" onclick="exportData()">
                        <i class="fas fa-download"></i> Export
                    </button> -->
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchInput"
                                placeholder="Search programs...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="skills_training">Skills Training</option>
                            <option value="microenterprise">Microenterprise</option>
                            <option value="employment_facilitation">Employment Facilitation</option>
                            <option value="entrepreneurship">Entrepreneurship</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="planning">Planning</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="durationFilter">
                            <option value="">All Duration</option>
                            <option value="short">Short (≤3 months)</option>
                            <option value="medium">Medium (4-6 months)</option>
                            <option value="long">Long (>6 months)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100 btn-custom" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Programs Table -->
            <div class="table-responsive">
                <table class="table beneficiaries-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Program Name</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Beneficiaries</th>
                            <th>Budget</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="programsTableBody">
                        <?php foreach ($programs as $program): ?>
                            <!-- Remove all PHP generated table rows -->
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="pagination-container" id="paginationContainer">
                <div class="pagination-info">
                    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalRecords">0</span> entries
                </div>
                <nav aria-label="Programs pagination">
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
                <p class="mt-2">Loading programs...</p>
            </div>
        </div>
    </div>

    <!-- Add Program Modal -->
    <div class="modal fade" id="addProgramModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Add New Program</h5>
                        <p class="modal-subtitle">Create a new livelihood program</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProgramForm">
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-info-circle"></i>
                                <span>Basic Information</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Program Name *</label>
                                    <input type="text" class="form-control" name="program_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Program Type *</label>
                                    <select class="form-select" name="program_type" required>
                                        <option value="">Select Type</option>
                                        <option value="skills_training">Skills Training</option>
                                        <option value="microenterprise">Microenterprise</option>
                                        <option value="employment_facilitation">Employment Facilitation</option>
                                        <option value="entrepreneurship">Entrepreneurship</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description *</label>
                                    <textarea class="form-control" name="description" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Timeline & Targets</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Duration (Months) *</label>
                                    <input type="number" class="form-control" style="background-color: rgba(103, 108, 113, 0.2); color: rgb(55, 55, 55);" name="duration_months" id="add_duration" min="1" required readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Target Beneficiaries *</label>
                                    <input type="number" class="form-control" name="target_beneficiaries" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Budget Allocated *</label>
                                    <input type="number" class="form-control" name="budget_allocated" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date *</label>
                                    <input type="date" class="form-control" name="start_date"
                                        id="add_start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="add_end_date"
                                        onchange="dateFunction(this.value)">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-target"></i>
                                <span>Success Criteria & Status</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" name="status" required>
                                        <option value="planning">Planning</option>
                                        <option value="active">Active</option>
                                        <option value="completed">Completed</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Success Criteria</label>
                                    <textarea class="form-control" name="success_criteria" rows="3" placeholder="Define success metrics for this program..."></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveProgram()">
                        <i class="fas fa-save"></i> Save Program
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Edit Program</h5>
                        <p class="modal-subtitle">Update program information</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProgramForm">
                        <input type="hidden" id="edit_program_id" name="id">

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-info-circle"></i>
                                <span>Basic Information</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Program Name *</label>
                                    <input type="text" class="form-control" id="edit_program_name" name="program_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Program Type *</label>
                                    <select class="form-select" id="edit_program_type" name="program_type" required>
                                        <option value="">Select Type</option>
                                        <option value="skills_training">Skills Training</option>
                                        <option value="microenterprise">Microenterprise</option>
                                        <option value="employment_facilitation">Employment Facilitation</option>
                                        <option value="entrepreneurship">Entrepreneurship</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description *</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Timeline & Targets</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Duration (Months) *</label>
                                    <input type="number" class="form-control" id="edit_duration_months" name="duration_months" min="1" required readonly style="background-color: rgba(103, 108, 113, 0.2); color: rgb(55, 55, 55);">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Target Beneficiaries *</label>
                                    <input type="number" class="form-control" id="edit_target_beneficiaries" name="target_beneficiaries" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Budget Allocated *</label>
                                    <input type="number" class="form-control" id="edit_budget_allocated" name="budget_allocated" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date *</label>
                                    <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="edit_end_date" name="end_date"
                                        onchange="editDateFunction(this.value)">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-target"></i>
                                <span>Success Criteria & Status</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="planning">Planning</option>
                                        <option value="active">Active</option>
                                        <option value="completed">Completed</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Success Criteria</label>
                                    <textarea class="form-control" id="edit_success_criteria" name="success_criteria" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateProgram()">
                        <i class="fas fa-save"></i> Update Program
                    </button>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let programs = <?php echo json_encode($programs); ?>;
        let filteredPrograms = [...programs];
        let currentPage = 1;
        const recordsPerPage = 10;

        // Initialize page
        $(document).ready(function() {
            // Clear PHP-generated table rows first
            $('#programsTableBody').empty();

            initializeFilters();
            initializeSearch();
            filterPrograms(); // This will render the table with pagination
        });

        // Initialize search functionality
        function initializeSearch() {
            $('#searchInput').on('keyup', function() {
                currentPage = 1; // Reset to first page when searching
                filterPrograms();
            });
        }

        // Initialize filter functionality
        function initializeFilters() {
            $('#typeFilter, #statusFilter, #durationFilter').on('change', function() {
                currentPage = 1; // Reset to first page when filtering
                filterPrograms();
            });
        }

        // Filter programs based on search and filters
        function filterPrograms() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const typeFilter = $('#typeFilter').val();
            const statusFilter = $('#statusFilter').val();
            const durationFilter = $('#durationFilter').val();

            filteredPrograms = programs.filter(program => {
                // Search filter
                const matchesSearch = !searchTerm ||
                    program.program_name.toLowerCase().includes(searchTerm) ||
                    program.program_code.toLowerCase().includes(searchTerm) ||
                    program.description.toLowerCase().includes(searchTerm);

                // Type filter
                const matchesType = !typeFilter || program.program_type === typeFilter;

                // Status filter
                const matchesStatus = !statusFilter || program.status === statusFilter;

                // Duration filter
                let matchesDuration = true;
                if (durationFilter) {
                    const duration = parseInt(program.duration_months);
                    switch (durationFilter) {
                        case 'short':
                            matchesDuration = duration <= 3;
                            break;
                        case 'medium':
                            matchesDuration = duration >= 4 && duration <= 6;
                            break;
                        case 'long':
                            matchesDuration = duration > 6;
                            break;
                    }
                }

                return matchesSearch && matchesType && matchesStatus && matchesDuration;
            });

            renderProgramsTable();
            renderPagination();
        }

        // Render programs table with pagination
        function renderProgramsTable() {
            const tbody = $('#programsTableBody');
            tbody.empty();

            if (filteredPrograms.length === 0) {
                tbody.append(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2 mb-0">No programs found matching your criteria</p>
                </td>
            </tr>
        `);
                $('#paginationContainer').hide();
                return;
            }

            // Calculate pagination
            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = Math.min(startIndex + recordsPerPage, filteredPrograms.length);
            const currentPageData = filteredPrograms.slice(startIndex, endIndex);

            // Render current page data
            currentPageData.forEach(program => {
                const row = createProgramRow(program);
                tbody.append(row);
            });

            $('#paginationContainer').show();
            updatePaginationInfo();
        }

        // Create program table row
        function createProgramRow(program) {
            return `
        <tr data-program='${JSON.stringify(program)}'>
            <td><span class="fw-bold">${program.program_code}</span></td>
            <td>
                <div>
                    <div class="fw-semibold">${program.program_name}</div>
                    <small class="text-muted">${program.description.substring(0, 50)}...</small>
                </div>
            </td>
            <td>
                <span class="program-type-badge program-type-${program.program_type}">
                    ${program.program_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                </span>
            </td>
            <td><span class="fw-medium">${program.duration_months} months</span></td>
            <td>
                <span class="status-badge status-${program.status}">
                    ${program.status.charAt(0).toUpperCase() + program.status.slice(1)}
                </span>
            </td>
            <td>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill bg-success" style="width: ${program.progress_percentage}%"></div>
                </div>
                <small class="text-muted">${Math.round(program.progress_percentage)}%</small>
            </td>
            <td><span class="fw-semibold">${Number(program.target_beneficiaries).toLocaleString()} <span style="font-size: 12px; color: #6c757d;">(target)</span></span></td>
            <td><span class="budget-amount">₱${Number(program.budget_allocated).toLocaleString()}</span></td>
            <td>
                <div class="d-flex">
                    <button class="action-btn btn-view" onclick="viewProgram(${program.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn btn-edit" onclick="editProgram(${program.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn btn-delete" onclick="deleteProgram(${program.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
        }

        // Render pagination controls
        function renderPagination() {
            const totalPages = Math.ceil(filteredPrograms.length / recordsPerPage);
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
            const totalPages = Math.ceil(filteredPrograms.length / recordsPerPage);

            if (page < 1 || page > totalPages) {
                return;
            }

            currentPage = page;
            renderProgramsTable();
            renderPagination();
        }

        // Update pagination info
        function updatePaginationInfo() {
            if (filteredPrograms.length === 0) {
                $('#showingStart').text(0);
                $('#showingEnd').text(0);
                $('#totalRecords').text(0);
                return;
            }

            const startIndex = (currentPage - 1) * recordsPerPage + 1;
            const endIndex = Math.min(currentPage * recordsPerPage, filteredPrograms.length);

            $('#showingStart').text(startIndex);
            $('#showingEnd').text(endIndex);
            $('#totalRecords').text(filteredPrograms.length);
        }

        // Clear all filters
        function clearFilters() {
            $('#searchInput').val('');
            $('#typeFilter').val('');
            $('#statusFilter').val('');
            $('#durationFilter').val('');
            currentPage = 1;
            filterPrograms();
        }

        // Show add program modal
        function showAddProgramModal() {
            // Reset form
            $('#addProgramForm')[0].reset();

            // Auto-generate program code
            const nextCode = generateNextProgramCode();
            $('input[name="program_code"]').val(nextCode);

            // Show modal
            $('#addProgramModal').modal('show');
        }

        // Generate next program code
        function generateNextProgramCode() {
            const currentYear = new Date().getFullYear();
            const existingCodes = programs
                .map(p => p.program_code)
                .filter(code => code.startsWith(`LP-${currentYear}`))
                .map(code => parseInt(code.split('-')[2]) || 0);

            const nextNumber = existingCodes.length > 0 ? Math.max(...existingCodes) + 1 : 1;
            return `LP-${currentYear}-${nextNumber.toString().padStart(3, '0')}`;
        }

        function dateFunction(endDate) {
            const startDateInput = document.getElementById('add_start_date');
            const endDateInput = document.getElementById('add_end_date');

            if (!startDateInput.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Start Date Required',
                    text: 'Please select a start date first.'
                });
                endDateInput.value = '';
                return;
            }

            if (startDateInput.value && endDateInput.value) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (endDate < startDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date',
                        text: 'End date cannot be earlier than start date.'
                    });
                    endDateInput.value = '';
                    return;
                }

                const durationMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());
                document.getElementById('add_duration').value = durationMonths > 0 ? durationMonths : 1;
            }
        }

        function editDateFunction(endDate) {
            const startDateInput = document.getElementById('edit_start_date');
            const endDateInput = document.getElementById('edit_end_date');

            if (!startDateInput.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Start Date Required',
                    text: 'Please select a start date first.'
                });
                endDateInput.value = '';
                return;
            }

            if (startDateInput.value && endDateInput.value) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (endDate < startDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date',
                        text: 'End date cannot be earlier than start date.'
                    });
                    endDateInput.value = '';
                    return;
                }

                const durationMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());
                document.getElementById('edit_duration_months').value = durationMonths > 0 ? durationMonths : 1;
            }
        }

        // Save new program
        function saveProgram() {
            const form = $('#addProgramForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const programData = Object.fromEntries(formData.entries());
            console.log(programData);

            // Add auto-generated program code
            programData.program_code = generateNextProgramCode();

            // Show loading
            Swal.fire({
                title: 'Creating Program...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '../backend/livelihood/save_program.php',
                method: 'POST',
                data: programData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Program created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then((result) => {
                            if (result) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to create program'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the program'
                    });
                }
            });
        }

        // Export analytics report
        function exportAnalytics() {
            const url = '../backend/export_analytics.php';
            const link = document.createElement('a');
            link.href = url;
            link.download = `program_analytics_${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            Swal.fire({
                icon: 'success',
                title: 'Export Started',
                text: 'Analytics report download should begin shortly',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Update createProgramRow to include checkbox
        function createProgramRow(program) {
            return `
                <tr data-program='${JSON.stringify(program)}'>
                    <td><span class="fw-bold">${program.program_code}</span></td>
                    <td>
                        <div>
                            <div class="fw-semibold">${program.program_name}</div>
                            <small class="text-muted">${program.description.substring(0, 50)}...</small>
                        </div>
                    </td>
                    <td>
                        <span class="program-type-badge program-type-${program.program_type}">
                            ${program.program_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                        </span>
                    </td>
                    <td><span class="fw-medium">${program.duration_months} months</span></td>
                    <td>
                        <span class="status-badge status-${program.status}">
                            ${program.status.charAt(0).toUpperCase() + program.status.slice(1)}
                        </span>
                    </td>
                    <td>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill bg-success" style="width: ${program.progress_percentage}%"></div>
                        </div>
                        <small class="text-muted">${Math.round(program.progress_percentage)}%</small>
                    </td>
                    <td><span class="fw-semibold">${Number(program.target_beneficiaries).toLocaleString()} <span style="font-size: 12px; color: #6c757d;">(target)</span></span></td>
                    <td><span class="budget-amount">₱${Number(program.budget_allocated).toLocaleString()}</span></td>
                    <td>
                        <div class="d-flex">
                            <button class="action-btn btn-view" onclick="viewProgram(${program.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn btn-edit" onclick="editProgram(${program.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteProgram(${program.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        async function deleteProgram(programId) {
            const confirmed = await Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            });

            if (!confirmed.isConfirmed) return;

            try {
                const response = await $.ajax({
                    url: '../backend/livelihood/delete_program.php',
                    method: 'POST',
                    data: {
                        id: programId
                    },
                    dataType: 'json'
                });

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Program has been deleted.'
                    }).then((result) => {
                        if (result) {
                            location.reload();
                        }
                    })
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        }

        // View program details
        function viewProgram(programId) {
            const program = programs.find(p => p.id == programId);
            if (!program) return;

            const modalContent = `
                <div class="modal fade" id="viewProgramModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content modern-modal">
                            <div class="modal-header modern-modal-header">
                                <div class="modal-title-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title">Program Details</h5>
                                    <p class="modal-subtitle">${program.program_name}</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body program-details-modal">
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <label class="detail-label">Program Code</label>
                                        <div class="detail-value fw-bold">${program.program_code}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label class="detail-label">Program Type</label>
                                        <div class="detail-value">
                                            <span class="program-type-badge program-type-${program.program_type}">
                                                ${program.program_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <label class="detail-label">Status</label>
                                        <div class="detail-value">
                                            <span class="status-badge status-${program.status}">
                                                ${program.status.charAt(0).toUpperCase() + program.status.slice(1)}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <label class="detail-label">Duration</label>
                                        <div class="detail-value">${program.duration_months} months</div>
                                    </div>
                                    <div class="detail-item">
                                        <label class="detail-label">Target Beneficiaries</label>
                                        <div class="detail-value fw-semibold">${Number(program.target_beneficiaries).toLocaleString()}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label class="detail-label">Budget Allocated</label>
                                        <div class="detail-value budget-amount">₱${Number(program.budget_allocated).toLocaleString()}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label class="detail-label">Start Date</label>
                                        <div class="detail-value">${new Date(program.start_date).toLocaleDateString()}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label class="detail-label">End Date</label>
                                        <div class="detail-value">${program.end_date ? new Date(program.end_date).toLocaleDateString() : 'Not set'}</div>
                                    </div>
                                    <div class="detail-item full-width">
                                        <label class="detail-label">Description</label>
                                        <div class="detail-value">${program.description}</div>
                                    </div>
                                    ${program.success_criteria ? `
                                        <div class="detail-item full-width">
                                            <label class="detail-label">Success Criteria</label>
                                            <div class="detail-value">${program.success_criteria}</div>
                                        </div>
                                    ` : ''}
                                    <div class="detail-item full-width">
                                        <label class="detail-label">Progress</label>
                                        <div class="detail-value">
                                            <div class="progress-bar-container mb-2">
                                                <div class="progress-bar-fill bg-success" style="width: ${program.progress_percentage}%"></div>
                                            </div>
                                            <span class="text-muted">${Math.round(program.progress_percentage)}% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modern-modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i> Close
                                </button>
                                <button type="button" class="btn btn-primary" onclick="editProgram(${program.id}); $('#viewProgramModal').modal('hide');">
                                    <i class="fas fa-edit"></i> Edit Program
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if present
            $('#viewProgramModal').remove();

            // Add modal to body and show
            $('body').append(modalContent);
            $('#viewProgramModal').modal('show');
        }

        function clearFilters() {
            $('#searchInput').val('');
            $('#typeFilter').val('');
            $('#statusFilter').val('');
            $('#durationFilter').val('');
            filteredPrograms = [...programs];
            renderProgramsTable();
            updateBulkOperationsBar();
        }

        function editProgram(program) {
            const programData = programs.find(p => p.id == program);
            if (!programData) return;

            // Populate form fields
            $('#edit_program_id').val(programData.id);
            $('#edit_program_name').val(programData.program_name);
            $('#edit_program_type').val(programData.program_type);
            $('#edit_description').val(programData.description);
            $('#edit_duration_months').val(programData.duration_months);
            $('#edit_target_beneficiaries').val(programData.target_beneficiaries);
            $('#edit_budget_allocated').val(programData.budget_allocated);
            $('#edit_start_date').val(programData.start_date);
            $('#edit_end_date').val(programData.end_date);
            $('#edit_status').val(programData.status);
            $('#edit_success_criteria').val(programData.success_criteria);

            // Show modal
            $('#editProgramModal').modal('show');
        }

        async function updateProgram() {
            const form = document.getElementById("editProgramForm");

            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const programData = Object.fromEntries(formData.entries());


            // Convert numeric fields to proper types
            programData.duration_months = parseInt(programData.duration_months);
            programData.target_beneficiaries = parseInt(programData.target_beneficiaries);
            programData.budget_allocated = parseFloat(programData.budget_allocated);

            // Show loading indicator
            Swal.fire({
                title: 'Updating Program...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await $.ajax({
                    url: '../backend/livelihood/update_program.php',
                    method: 'POST',
                    data: programData,
                    dataType: 'json'
                });

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Program updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then((result) => {
                        if (result) {
                            // Close modal and refresh page
                            $('#editProgramModal').modal('hide');
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update program'
                    });
                }
            } catch (error) {
                console.error('Update error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the program'
                });
            }
        }
    </script>

</body>

</html>