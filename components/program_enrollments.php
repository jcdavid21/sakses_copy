<!DOCTYPE html>
<html lang="en">
<?php
include "../backend/config.php";
session_start();

// Fetch program enrollments with beneficiary and program details
$sql = "SELECT 
    pe.id,
    pe.enrollment_date,
    pe.completion_date,
    pe.status,
    pe.attendance_rate,
    pe.pre_assessment_score,
    pe.post_assessment_score,
    pe.skills_acquired,
    pe.certification_received,
    pe.dropout_reason,
    pe.created_at,
    pe.updated_at,
    b.beneficiary_id,
    CONCAT(b.first_name, ' ', COALESCE(b.middle_name, ''), ' ', b.last_name) as beneficiary_name,
    b.gender,
    b.contact_number,
    b.email,
    b.education_level,
    lp.program_code,
    lp.program_name,
    lp.program_type,
    lp.duration_months,
    lp.start_date as program_start_date,
    lp.end_date as program_end_date,
    CASE 
        WHEN pe.status = 'completed' THEN 100
        WHEN pe.status = 'dropped_out' THEN 0
        WHEN pe.status = 'transferred' THEN 0
        ELSE GREATEST(0, LEAST(100, 
            DATEDIFF(CURDATE(), pe.enrollment_date) * 100 / 
            NULLIF(DATEDIFF(COALESCE(lp.end_date, DATE_ADD(lp.start_date, INTERVAL lp.duration_months MONTH)), lp.start_date), 0)
        ))
    END as enrollment_progress
FROM program_enrollments pe
LEFT JOIN beneficiaries b ON pe.beneficiary_id = b.id
LEFT JOIN livelihood_programs lp ON pe.program_id = lp.id
ORDER BY pe.created_at DESC";

$result = $conn->query($sql);
$enrollments = $result->fetch_all(MYSQLI_ASSOC);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(*) as total_enrollments,
    COUNT(CASE WHEN pe.status = 'enrolled' THEN 1 END) as enrolled_count,
    COUNT(CASE WHEN pe.status = 'active' THEN 1 END) as active_count,
    COUNT(CASE WHEN pe.status = 'completed' THEN 1 END) as completed_count,
    COUNT(CASE WHEN pe.status = 'dropped_out' THEN 1 END) as dropped_out_count,
    COUNT(CASE WHEN pe.status = 'transferred' THEN 1 END) as transferred_count,
    AVG(CASE WHEN pe.attendance_rate > 0 THEN pe.attendance_rate END) as avg_attendance,
    AVG(CASE WHEN pe.post_assessment_score IS NOT NULL THEN pe.post_assessment_score END) as avg_post_score
FROM program_enrollments pe";

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();


$programs_sql = "SELECT id, program_name, program_code FROM livelihood_programs ORDER BY program_name";
$programs_result = $conn->query($programs_sql);
$programs = $programs_result->fetch_all(MYSQLI_ASSOC);

$beneficiaries_sql = "SELECT id, beneficiary_id, first_name, middle_name, last_name FROM beneficiaries ORDER BY first_name, last_name";
$beneficiaries_result = $conn->query($beneficiaries_sql);
$beneficiaries = $beneficiaries_result->fetch_all(MYSQLI_ASSOC);
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
    <title>Program Enrollments - SAKSES</title>

    <style>
        .enrollment-status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }

        .btn-delete{
            color: rgba(227, 26, 26, 1) ;
        }

        .status-enrolled {
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

        .status-dropped_out {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .status-transferred {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .program-type-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 0.2rem;
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

        .score-badge {
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border-radius: 0.3rem;
            font-weight: 600;
        }

        .score-high {
            background-color: #d4edda;
            color: #155724;
        }

        .score-medium {
            background-color: #fff3cd;
            color: #856404;
        }

        .score-low {
            background-color: #f8d7da;
            color: #721c24;
        }

        .attendance-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .attendance-high {
            color: #28a745;
        }

        .attendance-medium {
            color: #ffc107;
        }

        .attendance-low {
            color: #dc3545;
        }

        .beneficiary-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .beneficiary-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(45deg, #007bff, #6c757d);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .enrollment-details-modal .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .enrollment-details-modal .detail-item.full-width {
            grid-column: 1 / -1;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            color: #212529;
            font-size: 0.95rem;
        }

        .modern-modal {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .modal-title-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .modal-subtitle {
            margin: 0;
            opacity: 0.9;
            font-size: 0.85rem;
        }

        .modern-modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .form-floating>label {
            font-weight: 500;
            color: #6c757d;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }


        .btn-edit {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit:hover {
            background-color: #138496;
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
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content p-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container-fluid">
                <h1 class="page-title">
                    <i class="fas fa-user-graduate"></i>
                    Program Enrollments Management
                </h1>
                <p class="page-subtitle">Monitor and track beneficiary enrollment progress across programs</p>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_enrollments']); ?></div>
                <div class="stat-label">Total Enrollments</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['completed_count']); ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['active_count']); ?></div>
                <div class="stat-label">Active</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['avg_attendance'], 1); ?>%</div>
                <div class="stat-label">Avg Attendance</div>
            </div>
        </div>

        <!-- Main Enrollments Card -->
        <div class="beneficiaries-card">
            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-custom" onclick="showAddEnrollmentModal()">
                    <i class="fas fa-plus"></i> Enroll Beneficiary
                </button>
                <!-- <button class="btn btn-outline-secondary btn-custom" onclick="exportEnrollmentData()">
                    <i class="fas fa-download"></i> Export
                </button> -->
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchInput"
                                placeholder="Search enrollments...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="enrolled">Enrolled</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="dropped_out">Dropped Out</option>
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="programTypeFilter">
                            <option value="">All Program Types</option>
                            <option value="skills_training">Skills Training</option>
                            <option value="microenterprise">Microenterprise</option>
                            <option value="employment_facilitation">Employment Facilitation</option>
                            <option value="entrepreneurship">Entrepreneurship</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="attendanceFilter">
                            <option value="">All Attendance</option>
                            <option value="high">High (≥80%)</option>
                            <option value="medium">Medium (50-79%)</option>
                            <option value="low">Low (<50%)< /option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="genderFilter">
                            <option value="">All Genders</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100 btn-custom" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enrollments Table -->
            <div class="table-responsive">
                <table class="table beneficiaries-table">
                    <thead>
                        <tr>
                            <th>Beneficiary</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Attendance</th>
                            <th>Assessment Scores</th>
                            <th>Enrollment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="enrollmentsTableBody">
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr data-enrollment='<?php echo json_encode($enrollment); ?>'>
                                <td>
                                    <div class="beneficiary-info">
                                        <div class="beneficiary-avatar">
                                            <?php echo substr($enrollment['beneficiary_name'], 0, 2); ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($enrollment['beneficiary_name']); ?></div>
                                            <small class="text-muted"><?php echo $enrollment['beneficiary_id']; ?> • <?php echo $enrollment['gender']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($enrollment['program_name']); ?></div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="program-type-badge program-type-<?php echo $enrollment['program_type']; ?>">
                                                <?php echo ucwords(str_replace('_', ' ', $enrollment['program_type'])); ?>
                                            </span>
                                            <small class="text-muted"><?php echo $enrollment['program_code']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="enrollment-status-badge status-<?php echo $enrollment['status']; ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $enrollment['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill bg-primary" style="width: <?php echo $enrollment['enrollment_progress']; ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?php echo round($enrollment['enrollment_progress']); ?>%</small>
                                </td>
                                <td>
                                    <?php if ($enrollment['attendance_rate'] > 0): ?>
                                        <div class="attendance-indicator">
                                            <i class="fas fa-chart-line <?php
                                                                        echo $enrollment['attendance_rate'] >= 80 ? 'attendance-high' : ($enrollment['attendance_rate'] >= 50 ? 'attendance-medium' : 'attendance-low');
                                                                        ?>"></i>
                                            <span class="fw-semibold"><?php echo number_format($enrollment['attendance_rate'], 1); ?>%</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Not started</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <?php if ($enrollment['pre_assessment_score']): ?>
                                            <small class="text-muted">Pre: <?php echo $enrollment['pre_assessment_score']; ?>%</small>
                                        <?php endif; ?>
                                        <?php if ($enrollment['post_assessment_score']): ?>
                                            <span class="score-badge <?php
                                                                        echo $enrollment['post_assessment_score'] >= 80 ? 'score-high' : ($enrollment['post_assessment_score'] >= 60 ? 'score-medium' : 'score-low');
                                                                        ?>">
                                                Post: <?php echo $enrollment['post_assessment_score']; ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted"><small>Post: Pending</small></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium"><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></div>
                                    <?php if ($enrollment['completion_date']): ?>
                                        <small class="text-success">Completed: <?php echo date('M d, Y', strtotime($enrollment['completion_date'])); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="action-btn btn-view" onclick="viewEnrollment(<?php echo $enrollment['id']; ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn btn-edit" onclick="editEnrollment(<?php echo $enrollment['id']; ?>)" title="Edit Enrollment">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn btn-delete " onclick="deleteEnrollment(<?php echo $enrollment['id']; ?>)" title="Delete Enrollment">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading enrollments...</p>
            </div>
        </div>
    </div>

    <!-- Add Enrollment Modal -->
    <div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Enroll Beneficiary</h5>
                        <p class="modal-subtitle">Add a new program enrollment</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addEnrollmentForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="beneficiary_id" required>
                                        <option value="">Select Beneficiary</option>
                                    </select>
                                    <label>Beneficiary *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="program_id" required>
                                        <option value="">Select Program</option>
                                    </select>
                                    <label>Program *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="enrollment_date" required>
                                    <label>Enrollment Date *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="status" required>
                                        <option value="enrolled">Enrolled</option>
                                        <option value="active">Active</option>
                                    </select>
                                    <label>Initial Status *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" name="pre_assessment_score"
                                        min="0" max="100" step="0.1" placeholder="0">
                                    <label>Pre-Assessment Score (%)</label>
                                </div>
                                <small class="text-muted">Initial assessment score before program starts</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" name="attendance_rate"
                                        min="0" max="100" step="0.1" value="0">
                                    <label>Attendance Rate (%) - Leave 0 for new enrollments</label>
                                </div>
                                <small class="text-muted">This will be automatically calculated from class attendance records</small>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="skills_acquired"
                                        placeholder="Enter skills separated by commas" style="height: 80px"></textarea>
                                    <label>Expected Skills to Acquire</label>
                                </div>
                                <small class="text-muted">Example: Bread making, pastry decoration, food safety, customer service</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-custom-primary btn-primary" onclick="saveEnrollment()">
                        <i class="fas fa-save"></i> Enroll Beneficiary
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Enrollment Modal -->
    <div class="modal fade" id="editEnrollmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Edit Enrollment</h5>
                        <p class="modal-subtitle">Update enrollment information and progress</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editEnrollmentForm">
                        <input type="hidden" id="edit_enrollment_id" name="enrollment_id">

                        <div class="row g-4">
                            <!-- Basic Information -->
                            <div class="col-12">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input class="form-select" id="edit_beneficiary_name" name="beneficiary_name" disabled>
                                            <label>Beneficiary (Cannot be changed)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input class="form-select" id="edit_program_name" name="program_name" disabled>
                                            <label>Program (Cannot be changed)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="edit_enrollment_date"
                                                name="enrollment_date" readonly>
                                            <label>Enrollment Date (Read-only)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="edit_status" name="status" required>
                                                <option value="enrolled">Enrolled</option>
                                                <option value="active">Active</option>
                                                <option value="completed">Completed</option>
                                                <option value="dropped_out">Dropped Out</option>
                                                <option value="transferred">Transferred</option>
                                            </select>
                                            <label>Status *</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Tracking -->
                            <div class="col-12">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-chart-line me-2"></i>Progress Tracking
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="edit_attendance_rate"
                                                name="attendance_rate" min="0" max="100" step="0.1">
                                            <label>Attendance Rate (%)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="edit_pre_assessment_score"
                                                name="pre_assessment_score" min="0" max="100" step="0.1" readonly>
                                            <label>Pre-Assessment Score (%) - Read-only</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="edit_post_assessment_score"
                                                name="post_assessment_score" min="0" max="100" step="0.1">
                                            <label>Post-Assessment Score (%)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="edit_completion_date"
                                                name="completion_date">
                                            <label>Completion Date</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="edit_certification_received"
                                                name="certification_received" placeholder="Enter certification name">
                                            <label>Certification Received</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills and Additional Info -->
                            <div class="col-12">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-cogs me-2"></i>Skills & Additional Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="edit_skills_acquired"
                                                name="skills_acquired" placeholder="Enter skills separated by commas"
                                                style="height: 80px"></textarea>
                                            <label>Skills Acquired</label>
                                        </div>
                                    </div>
                                    <div class="col-12" id="dropoutReasonContainer" style="display: none;">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="edit_dropout_reason"
                                                name="dropout_reason" placeholder="Explain reason for dropout"
                                                style="height: 60px"></textarea>
                                            <label>Dropout Reason</label>
                                        </div>
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
                    <button type="button" class="btn btn-custom-primary btn-primary" onclick="updateEnrollment()">
                        <i class="fas fa-save"></i> Update Enrollment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let enrollments = <?php echo json_encode($enrollments); ?>;
        let filteredEnrollments = [...enrollments];

        // Initialize page
        $(document).ready(function() {
            initializeFilters();
            initializeSearch();
        });

        // Initialize search functionality
        function initializeSearch() {
            $('#searchInput').on('keyup', function() {
                filterEnrollments();
            });
        }

        // Initialize filter functionality
        function initializeFilters() {
            $('#statusFilter, #programTypeFilter, #attendanceFilter, #genderFilter').on('change', function() {
                filterEnrollments();
            });
        }

        // Filter enrollments based on search and filters
        function filterEnrollments() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const statusFilter = $('#statusFilter').val();
            const programTypeFilter = $('#programTypeFilter').val();
            const attendanceFilter = $('#attendanceFilter').val();
            const genderFilter = $('#genderFilter').val();

            filteredEnrollments = enrollments.filter(enrollment => {
                // Search filter
                const matchesSearch = !searchTerm ||
                    enrollment.beneficiary_name.toLowerCase().includes(searchTerm) ||
                    enrollment.beneficiary_id.toLowerCase().includes(searchTerm) ||
                    enrollment.program_name.toLowerCase().includes(searchTerm) ||
                    enrollment.program_code.toLowerCase().includes(searchTerm) ||
                    (enrollment.skills_acquired && enrollment.skills_acquired.toLowerCase().includes(searchTerm));

                // Status filter
                const matchesStatus = !statusFilter || enrollment.status === statusFilter;

                // Program type filter
                const matchesProgramType = !programTypeFilter || enrollment.program_type === programTypeFilter;

                // Attendance filter
                let matchesAttendance = true;
                if (attendanceFilter) {
                    const attendance = parseFloat(enrollment.attendance_rate);
                    switch (attendanceFilter) {
                        case 'high':
                            matchesAttendance = attendance >= 80;
                            break;
                        case 'medium':
                            matchesAttendance = attendance >= 50 && attendance < 80;
                            break;
                        case 'low':
                            matchesAttendance = attendance < 50;
                            break;
                    }
                }

                // Gender filter
                const matchesGender = !genderFilter || enrollment.gender === genderFilter;

                return matchesSearch && matchesStatus && matchesProgramType && matchesAttendance && matchesGender;
            });

            renderEnrollmentsTable();
        }

        // Clear all filters
        function clearFilters() {
            $('#searchInput').val('');
            $('#statusFilter').val('');
            $('#programTypeFilter').val('');
            $('#attendanceFilter').val('');
            $('#genderFilter').val('');
            filteredEnrollments = [...enrollments];
            renderEnrollmentsTable();
        }

        // Render enrollments table
        function renderEnrollmentsTable() {
            const tbody = $('#enrollmentsTableBody');
            tbody.empty();

            if (filteredEnrollments.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No enrollments found matching your criteria</p>
                        </td>
                    </tr>
                `);
                return;
            }

            filteredEnrollments.forEach(enrollment => {
                const row = createEnrollmentRow(enrollment);
                tbody.append(row);
            });
        }

        // Create enrollment table row
        function createEnrollmentRow(enrollment) {
            const initials = enrollment.beneficiary_name.substring(0, 2).toUpperCase();
            const attendanceClass = enrollment.attendance_rate >= 80 ? 'attendance-high' :
                (enrollment.attendance_rate >= 50 ? 'attendance-medium' : 'attendance-low');
            const scoreClass = enrollment.post_assessment_score >= 80 ? 'score-high' :
                (enrollment.post_assessment_score >= 60 ? 'score-medium' : 'score-low');

            return `
                <tr data-enrollment='${JSON.stringify(enrollment)}'>
                    <td>
                        <div class="beneficiary-info">
                            <div class="beneficiary-avatar">${initials}</div>
                            <div>
                                <div class="fw-semibold">${enrollment.beneficiary_name}</div>
                                <small class="text-muted">${enrollment.beneficiary_id} • ${enrollment.gender}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="fw-semibold">${enrollment.program_name}</div>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <span class="program-type-badge program-type-${enrollment.program_type}">
                                    ${enrollment.program_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                </span>
                                <small class="text-muted">${enrollment.program_code}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="enrollment-status-badge status-${enrollment.status}">
                            ${enrollment.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                        </span>
                    </td>
                    <td>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill bg-primary" style="width: ${enrollment.enrollment_progress}%"></div>
                        </div>
                        <small class="text-muted">${Math.round(enrollment.enrollment_progress)}%</small>
                    </td>
                    <td>
                        ${enrollment.attendance_rate > 0 ? 
                            `<div class="attendance-indicator">
                                <i class="fas fa-chart-line ${attendanceClass}"></i>
                                <span class="fw-semibold">${Number(enrollment.attendance_rate).toFixed(1)}%</span>
                            </div>` : 
                            '<span class="text-muted">Not started</span>'
                        }
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            ${enrollment.pre_assessment_score ? 
                                `<small class="text-muted">Pre: ${enrollment.pre_assessment_score}%</small>` : ''
                            }
                            ${enrollment.post_assessment_score ? 
                                `<span class="score-badge ${scoreClass}">Post: ${enrollment.post_assessment_score}%</span>` : 
                                '<span class="text-muted"><small>Post: Pending</small></span>'
                            }
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium">${new Date(enrollment.enrollment_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</div>
                        ${enrollment.completion_date ? 
                            `<small class="text-success">Completed: ${new Date(enrollment.completion_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</small>` : 
                            ''
                        }
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="action-btn btn-view" onclick="viewEnrollment(<?php echo $enrollment['id']; ?>)" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn btn-edit" onclick="editEnrollment(<?php echo $enrollment['id']; ?>)" title="Edit Enrollment">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete btn-danger" onclick="deleteEnrollment(<?php echo $enrollment['id']; ?>)" title="Delete Enrollment">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        // View enrollment details
        function viewEnrollment(enrollmentId) {
            const enrollment = enrollments.find(e => e.id == enrollmentId);
            if (!enrollment) return;

            const modalContent = `
                <div class="modal fade" id="viewEnrollmentModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content modern-modal">
                            <div class="modal-header modern-modal-header">
                                <div class="modal-title-icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title">Enrollment Details</h5>
                                    <p class="modal-subtitle">${enrollment.beneficiary_name} - ${enrollment.program_name}</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body enrollment-details-modal">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-user me-2"></i>Beneficiary Information</h6>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label class="detail-label">Full Name</label>
                                                <div class="detail-value fw-semibold">${enrollment.beneficiary_name}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">ID</label>
                                                <div class="detail-value">${enrollment.beneficiary_id}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Gender</label>
                                                <div class="detail-value">${enrollment.gender}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Education Level</label>
                                                <div class="detail-value">${enrollment.education_level}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Contact</label>
                                                <div class="detail-value">${enrollment.contact_number || 'Not provided'}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Email</label>
                                                <div class="detail-value">${enrollment.email || 'Not provided'}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-briefcase me-2"></i>Program Information</h6>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label class="detail-label">Program Name</label>
                                                <div class="detail-value fw-semibold">${enrollment.program_name}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Program Code</label>
                                                <div class="detail-value">${enrollment.program_code}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Program Type</label>
                                                <div class="detail-value">
                                                    <span class="program-type-badge program-type-${enrollment.program_type}">
                                                        ${enrollment.program_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Duration</label>
                                                <div class="detail-value">${enrollment.duration_months} months</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Program Period</label>
                                                <div class="detail-value">
                                                    ${new Date(enrollment.program_start_date).toLocaleDateString()} - 
                                                    ${enrollment.program_end_date ? new Date(enrollment.program_end_date).toLocaleDateString() : 'Ongoing'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2"></i>Enrollment Progress</h6>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label class="detail-label">Enrollment Status</label>
                                                <div class="detail-value">
                                                    <span class="enrollment-status-badge status-${enrollment.status}">
                                                        ${enrollment.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Enrollment Date</label>
                                                <div class="detail-value fw-semibold">${new Date(enrollment.enrollment_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
                                            </div>
                                            ${enrollment.completion_date ? `
                                                <div class="detail-item">
                                                    <label class="detail-label">Completion Date</label>
                                                    <div class="detail-value text-success fw-semibold">${new Date(enrollment.completion_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
                                                </div>
                                            ` : ''}
                                            <div class="detail-item">
                                                <label class="detail-label">Progress</label>
                                                <div class="detail-value">
                                                    <div class="progress-bar-container mb-2">
                                                        <div class="progress-bar-fill bg-primary" style="width: ${enrollment.enrollment_progress}%"></div>
                                                    </div>
                                                    <span class="fw-semibold">${Math.round(enrollment.enrollment_progress)}% Complete</span>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Attendance Rate</label>
                                                <div class="detail-value">
                                                    ${enrollment.attendance_rate > 0 ? 
                                                        `<div class="attendance-indicator">
                                                            <i class="fas fa-chart-line ${enrollment.attendance_rate >= 80 ? 'attendance-high' : 
                                                                (enrollment.attendance_rate >= 50 ? 'attendance-medium' : 'attendance-low')}"></i>
                                                            <span class="fw-semibold">${Number(enrollment.attendance_rate).toFixed(1)}%</span>
                                                        </div>` : 
                                                        '<span class="text-muted">Not started</span>'
                                                    }
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-clipboard-check me-2"></i>Assessment & Skills</h6>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label class="detail-label">Pre-Assessment Score</label>
                                                <div class="detail-value">
                                                    ${enrollment.pre_assessment_score ? 
                                                        `<span class="fw-semibold">${enrollment.pre_assessment_score}%</span>` : 
                                                        '<span class="text-muted">Not taken</span>'
                                                    }
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Post-Assessment Score</label>
                                                <div class="detail-value">
                                                    ${enrollment.post_assessment_score ? 
                                                        `<span class="score-badge ${enrollment.post_assessment_score >= 80 ? 'score-high' : 
                                                            (enrollment.post_assessment_score >= 60 ? 'score-medium' : 'score-low')}">${enrollment.post_assessment_score}%</span>` : 
                                                        '<span class="text-muted">Pending</span>'
                                                    }
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Improvement</label>
                                                <div class="detail-value">
                                                    ${(enrollment.pre_assessment_score && enrollment.post_assessment_score) ? 
                                                        `<span class="fw-semibold ${(enrollment.post_assessment_score - enrollment.pre_assessment_score) >= 0 ? 'text-success' : 'text-danger'}">
                                                            ${enrollment.post_assessment_score - enrollment.pre_assessment_score > 0 ? '+' : ''}${(enrollment.post_assessment_score - enrollment.pre_assessment_score).toFixed(1)}%
                                                        </span>` : 
                                                        '<span class="text-muted">N/A</span>'
                                                    }
                                                </div>
                                            </div>
                                            <div class="detail-item full-width">
                                                <label class="detail-label">Skills Acquired</label>
                                                <div class="detail-value">
                                                    ${enrollment.skills_acquired ? 
                                                        `<div class="d-flex flex-wrap gap-1">
                                                            ${enrollment.skills_acquired.split(',').map(skill => 
                                                                `<span class="badge bg-light text-dark">${skill.trim()}</span>`
                                                            ).join('')}
                                                        </div>` : 
                                                        '<span class="text-muted">No skills recorded yet</span>'
                                                    }
                                                </div>
                                            </div>
                                            <div class="detail-item full-width">
                                                <label class="detail-label">Certification Received</label>
                                                <div class="detail-value">
                                                    ${enrollment.certification_received ? 
                                                        `<span class="badge bg-success">${enrollment.certification_received}</span>` : 
                                                        '<span class="text-muted">No certification yet</span>'
                                                    }
                                                </div>
                                            </div>
                                            ${(enrollment.dropout_reason && enrollment.status === 'dropped_out') ? `
                                                <div class="detail-item full-width">
                                                    <label class="detail-label">Dropout Reason</label>
                                                    <div class="detail-value text-danger">${enrollment.dropout_reason}</div>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modern-modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if present
            $('#viewEnrollmentModal').remove();

            // Add modal to body and show
            $('body').append(modalContent);
            $('#viewEnrollmentModal').modal('show');
        }

        // Export enrollment data
        function exportEnrollmentData() {
            Swal.fire({
                icon: 'info',
                title: 'Export Started',
                text: 'Enrollment data export will begin shortly',
                timer: 2000,
                showConfirmButton: false
            });

            // Here you would implement the actual export functionality
            // For now, this is just a placeholder
            console.log('Export enrollment data functionality to be implemented');
        }

        // Show add enrollment modal
        function showAddEnrollmentModal() {
            // Reset form
            $('#addEnrollmentForm')[0].reset();

            // Set default enrollment date to today
            const today = new Date().toISOString().split('T')[0];
            $('input[name="enrollment_date"]').val(today);

            // Load beneficiaries and programs
            loadBeneficiaries('add');
            loadPrograms('add');

            // Show modal
            $('#addEnrollmentModal').modal('show');
        }

        // Load beneficiaries for dropdown
        function loadBeneficiaries(mode = 'add') {
            const selectElement = mode === 'add' ?
                $('select[name="beneficiary_id"]') :
                $('#edit_beneficiary_id');

            // Use the PHP-generated beneficiaries data
            const beneficiaries = <?php echo json_encode($beneficiaries); ?>;

            selectElement.empty().append('<option value="">Select Beneficiary</option>');
            beneficiaries.forEach(beneficiary => {
                const fullName = `${beneficiary.first_name} ${beneficiary.middle_name ? beneficiary.middle_name + ' ' : ''}${beneficiary.last_name}`;
                selectElement.append(`<option value="${beneficiary.id}">${fullName} (${beneficiary.beneficiary_id})</option>`);
            });
        }

        // Load programs for dropdown
        function loadPrograms(mode = 'add') {
            const selectElement = $('select[name="program_id"]');
            selectElement.empty().append('<option value="">Select Program</option>');
            // Use the PHP-generated programs data
            const programs = <?php echo json_encode($programs); ?>;

            programs.forEach(program => {
                selectElement.append(`<option value="${program.id}">${program.program_name} (${program.program_code})</option>`);
            });
        }

        // Save new enrollment
        async function saveEnrollment() {
            const form = $('#addEnrollmentForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const enrollmentData = Object.fromEntries(formData.entries());

            console.log('Saving enrollment data:', enrollmentData);

            // Show loading
            Swal.fire({
                title: 'Creating Enrollment...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try{
                const response = await $.ajax({
                    url: '../backend/programs/save_enrollment.php',
                    method: 'POST',
                    data: enrollmentData,
                    dataType: 'json'
                });


                if(response.success){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Enrollment created successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addEnrollmentModal').modal('hide');
                        // In real implementation, you would reload the data
                        location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Failed to create enrollment');
                }
            }catch(error){
                console.error('Error saving enrollment:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving the enrollment. Please try again.',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }

        $('#edit_status').on('change', function() {
            const status = $(this).val();
            const dropoutContainer = $('#dropoutReasonContainer');
            const dropoutReasonField = $('#edit_dropout_reason');

            if (status === 'dropped_out') {
                dropoutContainer.show();
                dropoutReasonField.prop('required', true);
            } else {
                dropoutContainer.hide();
                dropoutReasonField.prop('required', false);
                dropoutReasonField.val(''); // Clear the field when hiding
            }
        });

        function editEnrollment(enrollmentId) {
            const enrollment = enrollments.find(e => e.id == enrollmentId);
            if (!enrollment) return;

            // Load dropdowns first
            loadBeneficiaries('edit');
            loadPrograms('edit');

            // Populate form fields
            $('#edit_enrollment_id').val(enrollment.id);
            $('#edit_enrollment_date').val(enrollment.enrollment_date);
            $('#edit_status').val(enrollment.status);
            $('#edit_attendance_rate').val(enrollment.attendance_rate);
            $('#edit_pre_assessment_score').val(enrollment.pre_assessment_score);
            $('#edit_post_assessment_score').val(enrollment.post_assessment_score);
            $('#edit_completion_date').val(enrollment.completion_date);
            $('#edit_certification_received').val(enrollment.certification_received);
            $('#edit_skills_acquired').val(enrollment.skills_acquired);
            $('#edit_dropout_reason').val(enrollment.dropout_reason);
            $('#edit_beneficiary_name').val(enrollment.beneficiary_name);
            $('#edit_program_name').val(enrollment.program_name);

            // Handle dropout reason visibility
            const dropoutContainer = $('#dropoutReasonContainer');
            const dropoutReasonField = $('#edit_dropout_reason');

            if (enrollment.status === 'dropped_out') {
                dropoutContainer.show();
                dropoutReasonField.prop('required', true);
            } else {
                dropoutContainer.hide();
                dropoutReasonField.prop('required', false);
            }

            // Set selected values after loading dropdowns
            setTimeout(() => {
                $('#edit_beneficiary_name').val(enrollment.beneficiary_name);
                $('#edit_program_name').val(enrollment.program_name);
            }, 100);

            // Show modal
            $('#editEnrollmentModal').modal('show');
        }

        // Enhanced updateEnrollment function with validation for dropout reason
        async function updateEnrollment() {
            const form = $('#editEnrollmentForm')[0];
            const status = $('#edit_status').val();
            const dropoutReason = $('#edit_dropout_reason').val();

            // Custom validation for dropout reason
            if (status === 'dropped_out' && !dropoutReason.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please provide a reason for the dropout.',
                    confirmButtonText: 'OK'
                });
                $('#edit_dropout_reason').focus();
                return;
            }

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const enrollmentData = Object.fromEntries(formData.entries());
            console.log('Updating enrollment data:', enrollmentData);

            // Show loading
            Swal.fire({
                title: 'Updating Enrollment...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try{
                const response = await $.ajax({
                    url: '../backend/programs/update_enrollment.php',
                    method: 'POST',
                    data: enrollmentData,
                    dataType: 'json'
                });

                if(response.success){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Enrollment updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editEnrollmentModal').modal('hide');
                        // In real implementation, you would reload the data
                        location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Failed to update enrollment');
                }
            }catch(error){
                console.error('Error updating enrollment:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the enrollment. Please try again.',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }

        // Delete enrollment
        async function deleteEnrollment(enrollmentId) {
            const enrollment = enrollments.find(e => e.id == enrollmentId);
            if (!enrollment) return;

            const confirmed = await Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `This will permanently delete the enrollment for ${enrollment.beneficiary_name} in ${enrollment.program_name}`,
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Cancel'
            });

            if (!confirmed.isConfirmed) return;

            // Show loading
            Swal.fire({
                title: 'Deleting Enrollment...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try{
                const response = await $.ajax({
                    url: '../backend/programs/delete_enrollment.php',
                    method: 'POST',
                    data: { enrollment_id: enrollmentId },
                    dataType: 'json'
                });

                if(response.success){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Enrollment deleted successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // In real implementation, you would reload the data
                        location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Failed to delete enrollment');
                }
            }catch(error){
                console.error('Error deleting enrollment:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the enrollment. Please try again.',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }
    </script>

</body>

</html>