<!DOCTYPE html>
<html lang="en">
<?php
include "../backend/config.php";
session_start();

// Fetch program resources with program details
$sql = "SELECT 
    pr.id,
    pr.resource_type,
    pr.resource_name,
    pr.quantity,
    pr.cost,
    pr.supplier,
    pr.acquisition_date,
    pr.status,
    pr.created_at,
    lp.id as program_id,
    lp.program_code,
    lp.program_name,
    lp.program_type,
    lp.status as program_status
FROM program_resources pr
LEFT JOIN livelihood_programs lp ON pr.program_id = lp.id
ORDER BY pr.created_at DESC";

$result = $conn->query($sql);
$resources = $result->fetch_all(MYSQLI_ASSOC);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(*) as total_resources,
    COUNT(CASE WHEN pr.status = 'available' THEN 1 END) as available_count,
    COUNT(CASE WHEN pr.status = 'in_use' THEN 1 END) as in_use_count,
    COUNT(CASE WHEN pr.status = 'maintenance' THEN 1 END) as maintenance_count,
    COUNT(CASE WHEN pr.status = 'damaged' THEN 1 END) as damaged_count,
    SUM(pr.cost * pr.quantity) as total_investment,
    COUNT(DISTINCT pr.program_id) as programs_with_resources
FROM program_resources pr";

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Get programs for dropdown
$programs_sql = "SELECT id, program_name, program_code FROM livelihood_programs ORDER BY program_name";
$programs_result = $conn->query($programs_sql);
$programs = $programs_result->fetch_all(MYSQLI_ASSOC);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <link rel="stylesheet" href="../styles/beneficiaries.css">
    <title>Program Resources - SAKSES</title>

    <style>
        .resource-status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }

        .status-available {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .status-in_use {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .status-maintenance {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .status-damaged {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .resource-type-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 0.2rem;
            font-weight: 500;
        }

        .type-equipment {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .type-materials {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .type-venue {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .type-instructor {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .type-budget {
            background-color: #fce4ec;
            color: #c2185b;
        }

        .resource-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .icon-equipment {
            background: linear-gradient(45deg, #1565c0, #42a5f5);
        }

        .icon-materials {
            background: linear-gradient(45deg, #7b1fa2, #ba68c8);
        }

        .icon-venue {
            background: linear-gradient(45deg, #2e7d32, #66bb6a);
        }

        .icon-instructor {
            background: linear-gradient(45deg, #ef6c00, #ffa726);
        }

        .icon-budget {
            background: linear-gradient(45deg, #c2185b, #f06292);
        }

        .cost-display {
            font-weight: 600;
            color: #2e7d32;
        }

        .quantity-badge {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 0.2rem 0.5rem;
            border-radius: 0.3rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-delete {
            color: rgba(227, 26, 26, 1);
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

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .detail-item.full-width {
            grid-column: 1 / -1;
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
                    <i class="fas fa-boxes"></i>
                    Program Resources Management
                </h1>
                <p class="page-subtitle">Track and manage resources allocated to livelihood programs</p>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_resources']); ?></div>
                <div class="stat-label">Total Resources</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['available_count']); ?></div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['maintenance_count']); ?></div>
                <div class="stat-label">Under Maintenance</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['total_investment'], 2); ?></div>
                <div class="stat-label">Total Investment</div>
            </div>
        </div>

        <!-- Main Resources Card -->
        <div class="beneficiaries-card">
            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-custom" onclick="showAddResourceModal()">
                    <i class="fas fa-plus"></i> Add Resource
                </button>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchInput"
                                placeholder="Search resources...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="in_use">In Use</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="equipment">Equipment</option>
                            <option value="materials">Materials</option>
                            <option value="venue">Venue</option>
                            <option value="instructor">Instructor</option>
                            <option value="budget">Budget</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="programFilter">
                            <option value="">All Programs</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?php echo $program['id']; ?>">
                                    <?php echo htmlspecialchars($program['program_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="sortFilter">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="cost_high">Highest Cost</option>
                            <option value="cost_low">Lowest Cost</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100 btn-custom" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resources Table -->
            <div class="table-responsive">
                <table class="table beneficiaries-table">
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Quantity</th>
                            <th>Cost</th>
                            <th>Supplier</th>
                            <th>Acquisition Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="resourcesTableBody">
                        <!-- Table rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="pagination-container" id="paginationContainer">
                <div class="pagination-info">
                    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalRecords">0</span> entries
                </div>
                <nav aria-label="Resources pagination">
                    <ul class="pagination" id="paginationList"></ul>
                </nav>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading resources...</p>
            </div>
        </div>
    </div>

    <!-- Add Resource Modal -->
    <div class="modal fade" id="addResourceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Add New Resource</h5>
                        <p class="modal-subtitle">Add a resource to a program</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addResourceForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="program_id" required>
                                        <option value="">Select Program</option>
                                        <?php foreach ($programs as $program): ?>
                                            <option value="<?php echo $program['id']; ?>">
                                                <?php echo htmlspecialchars($program['program_name']); ?> (<?php echo $program['program_code']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label>Program *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="resource_type" required>
                                        <option value="">Select Type</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="materials">Materials</option>
                                        <option value="venue">Venue</option>
                                        <option value="instructor">Instructor</option>
                                        <option value="budget">Budget</option>
                                    </select>
                                    <label>Resource Type *</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="resource_name" required>
                                    <label>Resource Name *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                                    <label>Quantity *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" name="cost" min="0" step="0.01">
                                    <label>Cost (₱)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="status" required>
                                        <option value="available">Available</option>
                                        <option value="in_use">In Use</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="damaged">Damaged</option>
                                    </select>
                                    <label>Status *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="supplier">
                                    <label>Supplier</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="acquisition_date">
                                    <label>Acquisition Date</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-custom-primary btn-primary" onclick="saveResource()">
                        <i class="fas fa-save"></i> Add Resource
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Resource Modal -->
    <div class="modal fade" id="editResourceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Edit Resource</h5>
                        <p class="modal-subtitle">Update resource information</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editResourceForm">
                        <input type="hidden" id="edit_resource_id" name="resource_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="edit_program_id" name="program_id" required>
                                        <option value="">Select Program</option>
                                        <?php foreach ($programs as $program): ?>
                                            <option value="<?php echo $program['id']; ?>">
                                                <?php echo htmlspecialchars($program['program_name']); ?> (<?php echo $program['program_code']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label>Program *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="edit_resource_type" name="resource_type" required>
                                        <option value="">Select Type</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="materials">Materials</option>
                                        <option value="venue">Venue</option>
                                        <option value="instructor">Instructor</option>
                                        <option value="budget">Budget</option>
                                    </select>
                                    <label>Resource Type *</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit_resource_name" name="resource_name" required>
                                    <label>Resource Name *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="edit_quantity" name="quantity" min="1" required>
                                    <label>Quantity *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="edit_cost" name="cost" min="0" step="0.01">
                                    <label>Cost (₱)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="available">Available</option>
                                        <option value="in_use">In Use</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="damaged">Damaged</option>
                                    </select>
                                    <label>Status *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit_supplier" name="supplier">
                                    <label>Supplier</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="edit_acquisition_date" name="acquisition_date">
                                    <label>Acquisition Date</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-custom-primary btn-primary" onclick="updateResource()">
                        <i class="fas fa-save"></i> Update Resource
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let resources = <?php echo json_encode($resources); ?>;
        let filteredResources = [...resources];
        let currentPage = 1;
        const recordsPerPage = 10;

        // Initialize on document ready
        $(document).ready(function() {
            $('#resourcesTableBody').empty();
            initializeFilters();
            initializeSearch();
            filterResources();
        });

        // Initialize search functionality
        function initializeSearch() {
            $('#searchInput').on('keyup', function() {
                currentPage = 1;
                filterResources();
            });
        }

        // Initialize filter functionality
        function initializeFilters() {
            $('#statusFilter, #typeFilter, #programFilter, #sortFilter').on('change', function() {
                currentPage = 1;
                filterResources();
            });
        }

        // Filter resources based on criteria
        function filterResources() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const statusFilter = $('#statusFilter').val();
            const typeFilter = $('#typeFilter').val();
            const programFilter = $('#programFilter').val();
            const sortFilter = $('#sortFilter').val();

            filteredResources = resources.filter(resource => {
                const matchesSearch = !searchTerm ||
                    resource.resource_name.toLowerCase().includes(searchTerm) ||
                    (resource.supplier && resource.supplier.toLowerCase().includes(searchTerm)) ||
                    resource.program_name.toLowerCase().includes(searchTerm);

                const matchesStatus = !statusFilter || resource.status === statusFilter;
                const matchesType = !typeFilter || resource.resource_type === typeFilter;
                const matchesProgram = !programFilter || resource.program_id == programFilter;

                return matchesSearch && matchesStatus && matchesType && matchesProgram;
            });

            // Apply sorting
            switch(sortFilter) {
                case 'oldest':
                    filteredResources.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                case 'cost_high':
                    filteredResources.sort((a, b) => (b.cost * b.quantity) - (a.cost * a.quantity));
                    break;
                case 'cost_low':
                    filteredResources.sort((a, b) => (a.cost * a.quantity) - (b.cost * b.quantity));
                    break;
                default: // newest
                    filteredResources.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            }

            renderResourcesTable();
            renderPagination();
        }

        // Clear all filters
        function clearFilters() {
            $('#searchInput').val('');
            $('#statusFilter').val('');
            $('#typeFilter').val('');
            $('#programFilter').val('');
            $('#sortFilter').val('newest');
            currentPage = 1;
            filteredResources = [...resources];
            renderResourcesTable();
            renderPagination();
        }

        // Render resources table
        function renderResourcesTable() {
            const tbody = $('#resourcesTableBody');
            tbody.empty();

            if (filteredResources.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No resources found matching your criteria</p>
                        </td>
                    </tr>
                `);
                $('#paginationContainer').hide();
                return;
            }

            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = Math.min(startIndex + recordsPerPage, filteredResources.length);
            const currentPageData = filteredResources.slice(startIndex, endIndex);

            currentPageData.forEach(resource => {
                const row = createResourceRow(resource);
                tbody.append(row);
            });

            $('#paginationContainer').show();
            updatePaginationInfo();
        }

        // Create resource table row
        function createResourceRow(resource) {
            const iconClass = `icon-${resource.resource_type}`;
            const iconMap = {
                'equipment': 'fa-wrench',
                'materials': 'fa-cubes',
                'venue': 'fa-building',
                'instructor': 'fa-chalkboard-teacher',
                'budget': 'fa-money-bill-wave'
            };

            const totalCost = resource.cost ? (parseFloat(resource.cost) * parseInt(resource.quantity)).toFixed(2) : 'N/A';

            return `
                <tr data-resource='${JSON.stringify(resource).replace(/'/g, "&apos;")}'>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <div class="fw-semibold">${resource.resource_name}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium">${resource.program_name || 'N/A'}</div>
                        <small class="text-muted">${resource.program_code || ''}</small>
                    </td>
                    <td>
                        <span class="resource-status-badge status-${resource.status}">
                            ${resource.status.replace('_', ' ').charAt(0).toUpperCase() + resource.status.replace('_', ' ').slice(1)}
                        </span>
                    </td>
                    <td>
                        <span class="quantity-badge">${resource.quantity}</span>
                    </td>
                    <td>
                        <div class="cost-display">₱${totalCost}</div>
                        ${resource.cost ? `<small class="text-muted">₱${parseFloat(resource.cost).toFixed(2)} each</small>` : ''}
                    </td>
                    <td>
                        <div>${resource.supplier || '<span class="text-muted">Not specified</span>'}</div>
                    </td>
                    <td>
                        <div>${resource.acquisition_date ? new Date(resource.acquisition_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : '<span class="text-muted">N/A</span>'}</div>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="action-btn btn-view" onclick="viewResource(${resource.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn btn-edit" onclick="editResource(${resource.id})" title="Edit Resource">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteResource(${resource.id})" title="Delete Resource">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Render pagination
        function renderPagination() {
            const totalPages = Math.ceil(filteredResources.length / recordsPerPage);
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

            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                paginationList.append(`
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                    </li>
                `);
            }

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
            const totalPages = Math.ceil(filteredResources.length / recordsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderResourcesTable();
            renderPagination();
        }

        // Update pagination info
        function updatePaginationInfo() {
            if (filteredResources.length === 0) {
                $('#showingStart').text(0);
                $('#showingEnd').text(0);
                $('#totalRecords').text(0);
                return;
            }

            const startIndex = (currentPage - 1) * recordsPerPage + 1;
            const endIndex = Math.min(currentPage * recordsPerPage, filteredResources.length);

            $('#showingStart').text(startIndex);
            $('#showingEnd').text(endIndex);
            $('#totalRecords').text(filteredResources.length);
        }

        // Show add resource modal
        function showAddResourceModal() {
            $('#addResourceForm')[0].reset();
            const today = new Date().toISOString().split('T')[0];
            $('input[name="acquisition_date"]').val(today);
            $('#addResourceModal').modal('show');
        }

        // Save new resource
        async function saveResource() {
            const form = $('#addResourceForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const resourceData = Object.fromEntries(formData.entries());

            Swal.fire({
                title: 'Adding Resource...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await $.ajax({
                    url: '../backend/resources/save_resource.php',
                    method: 'POST',
                    data: resourceData,
                    dataType: 'json'
                });

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Resource added successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addResourceModal').modal('hide');
                        location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Failed to add resource');
                }
            } catch (error) {
                console.error('Error saving resource:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while saving the resource.',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Edit resource
        function editResource(resourceId) {
            const resource = resources.find(r => r.id == resourceId);
            if (!resource) return;

            $('#edit_resource_id').val(resource.id);
            $('#edit_program_id').val(resource.program_id);
            $('#edit_resource_type').val(resource.resource_type);
            $('#edit_resource_name').val(resource.resource_name);
            $('#edit_quantity').val(resource.quantity);
            $('#edit_cost').val(resource.cost);
            $('#edit_status').val(resource.status);
            $('#edit_supplier').val(resource.supplier);
            $('#edit_acquisition_date').val(resource.acquisition_date);

            $('#editResourceModal').modal('show');
        }

        // Update resource
        async function updateResource() {
            const form = $('#editResourceForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const resourceData = Object.fromEntries(formData.entries());

            Swal.fire({
                title: 'Updating Resource...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            console.log('Updating resource with data:', resourceData);

            try {
                const response = await $.ajax({
                    url: '../backend/resources/update_resource.php',
                    method: 'POST',
                    data: resourceData,
                    dataType: 'json'
                });

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Resource updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editResourceModal').modal('hide');
                        location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Failed to update resource');
                }
            } catch (error) {
                console.error('Error updating resource:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while updating the resource.',
                    confirmButtonText: 'OK'
                });
            }
        }

        // View resource details
        function viewResource(resourceId) {
            const resource = resources.find(r => r.id == resourceId);
            if (!resource) return;

            const iconMap = {
                'equipment': 'fa-wrench',
                'materials': 'fa-cubes',
                'venue': 'fa-building',
                'instructor': 'fa-chalkboard-teacher',
                'budget': 'fa-money-bill-wave'
            };

            const totalCost = resource.cost ? (parseFloat(resource.cost) * parseInt(resource.quantity)).toFixed(2) : 'N/A';

            const modalContent = `
                <div class="modal fade" id="viewResourceModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content modern-modal">
                            <div class="modal-header modern-modal-header">
                                <div class="modal-title-icon">
                                    <i class="fas ${iconMap[resource.resource_type]}"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title">Resource Details</h5>
                                    <p class="modal-subtitle">${resource.resource_name}</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label class="detail-label">Resource Name</label>
                                                <div class="detail-value fw-semibold">${resource.resource_name}</div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Resource Type</label>
                                                <div class="detail-value">
                                                    <span class="resource-type-badge type-${resource.resource_type}">
                                                        ${resource.resource_type.charAt(0).toUpperCase() + resource.resource_type.slice(1)}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Status</label>
                                                <div class="detail-value">
                                                    <span class="resource-status-badge status-${resource.status}">
                                                        ${resource.status.replace('_', ' ').charAt(0).toUpperCase() + resource.status.replace('_', ' ').slice(1)}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Program</label>
                                                <div class="detail-value">
                                                    <div class="fw-medium">${resource.program_name || 'Not assigned'}</div>
                                                    ${resource.program_code ? `<small class="text-muted">${resource.program_code}</small>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-dollar-sign me-2"></i>Cost & Quantity</h6>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label class="detail-label">Quantity</label>
                                                <div class="detail-value">
                                                    <span class="quantity-badge">${resource.quantity}</span>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Unit Cost</label>
                                                <div class="detail-value cost-display">
                                                    ${resource.cost ? '₱' + parseFloat(resource.cost).toFixed(2) : 'N/A'}
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Total Cost</label>
                                                <div class="detail-value cost-display fw-bold">
                                                    ${resource.cost ? '₱' + totalCost : 'N/A'}
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Supplier</label>
                                                <div class="detail-value">${resource.supplier || '<span class="text-muted">Not specified</span>'}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-calendar me-2"></i>Dates</h6>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label class="detail-label">Acquisition Date</label>
                                                <div class="detail-value">
                                                    ${resource.acquisition_date ? new Date(resource.acquisition_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) : '<span class="text-muted">Not specified</span>'}
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Record Created</label>
                                                <div class="detail-value">
                                                    ${new Date(resource.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}
                                                </div>
                                            </div>
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

            $('#viewResourceModal').remove();
            $('body').append(modalContent);
            $('#viewResourceModal').modal('show');
        }

        // Delete resource
        async function deleteResource(resourceId) {
            const resource = resources.find(r => r.id == resourceId);
            if (!resource) return;

            const confirmed = await Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `This will permanently delete ${resource.resource_name}`,
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Cancel'
            });

            if (!confirmed.isConfirmed) return;

            Swal.fire({
                title: 'Deleting Resource...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await $.ajax({
                    url: '../backend/resources/delete_resource.php',
                    method: 'POST',
                    data: { resource_id: resourceId },
                    dataType: 'json'
                });

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Resource deleted successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Failed to delete resource');
                }
            } catch (error) {
                console.error('Error deleting resource:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while deleting the resource.',
                    confirmButtonText: 'OK'
                });
            }
        }
    </script>

</body>
</html>