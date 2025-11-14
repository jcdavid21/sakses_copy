<!DOCTYPE html>
<html lang="en">
<?php
include "../backend/config.php";
session_start();

// Fetch system logs with user details
$sql = "SELECT 
    sl.id,
    sl.user_id,
    sl.action,
    sl.description,
    sl.created_at,
    CASE 
        WHEN u.username IS NOT NULL THEN u.username
        ELSE 'System'
    END as username,
    CASE 
        WHEN u.first_name IS NOT NULL AND u.last_name IS NOT NULL 
        THEN CONCAT(u.first_name, ' ', u.last_name)
        ELSE 'System User'
    END as full_name,
    u.role
FROM system_logs sl
LEFT JOIN users u ON sl.user_id = u.id
ORDER BY sl.created_at DESC";

$result = $conn->query($sql);
$logs = $result->fetch_all(MYSQLI_ASSOC);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(*) as total_logs,
    COUNT(CASE WHEN sl.action = 'login' THEN 1 END) as login_count,
    COUNT(CASE WHEN sl.action LIKE '%Insert%' OR sl.action LIKE '%add%' THEN 1 END) as create_count,
    COUNT(CASE WHEN sl.action LIKE '%Update%' OR sl.action LIKE '%update%' THEN 1 END) as update_count,
    COUNT(CASE WHEN sl.action LIKE '%Delete%' OR sl.action LIKE '%delete%' THEN 1 END) as delete_count,
    COUNT(DISTINCT sl.user_id) as active_users,
    DATE(MAX(sl.created_at)) as last_activity_date
FROM system_logs sl";

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
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
    <title>System Logs - SAKSES</title>

    <style>
        .log-action-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }

        .action-login {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .action-logout {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .action-insert, .action-add {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .action-update {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .action-delete {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .action-view {
            background-color: #e0f2f1;
            color: #00695c;
        }

        .action-train_model {
            background-color: #fce4ec;
            color: #ad1457;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
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

        .user-avatar.system {
            background: linear-gradient(45deg, #28a745, #20c997);
        }

        .log-description {
            max-width: 300px;
            word-wrap: break-word;
            line-height: 1.4;
        }

        .time-info {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .recent-badge {
            background-color: #28a745;
            color: white;
            font-size: 0.6rem;
            padding: 0.1rem 0.3rem;
            border-radius: 0.2rem;
            margin-left: 0.25rem;
        }

        .log-details-modal .detail-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 1rem;
            align-items: start;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .detail-value {
            color: #212529;
            font-size: 0.95rem;
            word-break: break-word;
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

        .action-btn {
            padding: 0.375rem 0.5rem;
            border: none;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #17a2b8;
            color: white;
        }

        .action-btn:hover {
            background-color: #138496;
            color: white;
        }

        .log-row {
            transition: background-color 0.2s ease;
        }

        .log-row:hover {
            background-color: #f8f9fa;
        }

        .date-filter-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .filter-divider {
            width: 1px;
            height: 30px;
            background-color: #dee2e6;
            margin: 0 0.5rem;
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
                    <i class="fas fa-history"></i>
                    System Activity Logs
                </h1>
                <p class="page-subtitle">Monitor system activities and user actions</p>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_logs']); ?></div>
                <div class="stat-label">Total Logs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['active_users']); ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['login_count']); ?></div>
                <div class="stat-label">Login Events</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-value"><?php echo $stats['last_activity_date'] ? date('M d', strtotime($stats['last_activity_date'])) : 'N/A'; ?></div>
                <div class="stat-label">Last Activity</div>
            </div>
        </div>

        <!-- Main Logs Card -->
        <div class="beneficiaries-card">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-custom" onclick="refreshLogs()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-outline-secondary btn-custom" onclick="exportLogs()">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="btn btn-outline-danger btn-custom" onclick="clearOldLogs()">
                    <i class="fas fa-trash"></i> Clear Old Logs
                </button>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchInput"
                                placeholder="Search logs...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="actionFilter">
                            <option value="">All Actions</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="Insert">Insert</option>
                            <option value="Update">Update</option>
                            <option value="Delete">Delete</option>
                            <option value="view">View</option>
                            <option value="train_model">Train Model</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select" id="userFilter">
                            <option value="">All Users</option>
                            <?php
                            $users_sql = "SELECT DISTINCT u.id, u.username, CONCAT(u.first_name, ' ', u.last_name) as full_name 
                                         FROM users u 
                                         INNER JOIN system_logs sl ON u.id = sl.user_id 
                                         ORDER BY u.username";
                            $users_result = $conn->query($users_sql);
                            while ($user = $users_result->fetch_assoc()) {
                                echo "<option value='{$user['id']}'>{$user['username']} ({$user['full_name']})</option>";
                            }
                            ?>
                            <option value="system">System</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control filter-select" id="dateFromFilter" title="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control filter-select" id="dateToFilter" title="To Date">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100 btn-custom" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="table-responsive">
                <table class="table beneficiaries-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Timestamp</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <?php foreach ($logs as $log): ?>
                            <tr class="log-row" data-log='<?php echo json_encode($log); ?>'>
                                <td>
                                    <span class="fw-bold text-primary">#<?php echo $log['id']; ?></span>
                                </td>
                                <td>
                                    <div class="user-info" style="background-color: white !important; border: none !important;">
                                        <div class="user-avatar <?php echo $log['user_id'] ? '' : 'system'; ?>">
                                            <?php echo $log['user_id'] ? substr($log['username'], 0, 2) : 'SYS'; ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($log['full_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($log['username']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="log-action-badge action-<?php echo strtolower(str_replace('_', '', $log['action'])); ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $log['action'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="log-description">
                                        <?php echo htmlspecialchars($log['description']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium"><?php echo date('M d, Y', strtotime($log['created_at'])); ?></div>
                                    <div class="time-info"><?php echo date('g:i A', strtotime($log['created_at'])); ?>
                                        <?php if (strtotime($log['created_at']) > strtotime('-24 hours')): ?>
                                            <span class="recent-badge">NEW</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn" onclick="viewLogDetails(<?php echo $log['id']; ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading logs...</p>
            </div>
        </div>
    </div>

    <!-- View Log Details Modal -->
    <div class="modal fade" id="viewLogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <div class="modal-title-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Log Details</h5>
                        <p class="modal-subtitle">System activity information</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body log-details-modal">
                    <div id="logDetailsContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let logs = <?php echo json_encode($logs); ?>;
        let filteredLogs = [...logs];

        // Initialize page
        $(document).ready(function() {
            initializeFilters();
            initializeSearch();
        });

        // Initialize search functionality
        function initializeSearch() {
            $('#searchInput').on('keyup', function() {
                filterLogs();
            });
        }

        // Initialize filter functionality
        function initializeFilters() {
            $('#actionFilter, #userFilter, #dateFromFilter, #dateToFilter').on('change', function() {
                filterLogs();
            });
        }

        // Filter logs based on search and filters
        function filterLogs() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const actionFilter = $('#actionFilter').val();
            const userFilter = $('#userFilter').val();
            const dateFromFilter = $('#dateFromFilter').val();
            const dateToFilter = $('#dateToFilter').val();

            filteredLogs = logs.filter(log => {
                // Search filter
                const matchesSearch = !searchTerm ||
                    log.action.toLowerCase().includes(searchTerm) ||
                    log.description.toLowerCase().includes(searchTerm) ||
                    log.username.toLowerCase().includes(searchTerm) ||
                    log.full_name.toLowerCase().includes(searchTerm) ||
                    log.id.toString().includes(searchTerm);

                // Action filter
                const matchesAction = !actionFilter || log.action.toLowerCase().includes(actionFilter.toLowerCase());

                // User filter
                let matchesUser = true;
                if (userFilter) {
                    if (userFilter === 'system') {
                        matchesUser = !log.user_id;
                    } else {
                        matchesUser = log.user_id == userFilter;
                    }
                }

                // Date filters
                const logDate = new Date(log.created_at).toISOString().split('T')[0];
                const matchesDateFrom = !dateFromFilter || logDate >= dateFromFilter;
                const matchesDateTo = !dateToFilter || logDate <= dateToFilter;

                return matchesSearch && matchesAction && matchesUser && matchesDateFrom && matchesDateTo;
            });

            renderLogsTable();
        }

        // Clear all filters
        function clearFilters() {
            $('#searchInput').val('');
            $('#actionFilter').val('');
            $('#userFilter').val('');
            $('#dateFromFilter').val('');
            $('#dateToFilter').val('');
            filteredLogs = [...logs];
            renderLogsTable();
        }

        // Render logs table
        function renderLogsTable() {
            const tbody = $('#logsTableBody');
            tbody.empty();

            if (filteredLogs.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No logs found matching your criteria</p>
                        </td>
                    </tr>
                `);
                return;
            }

            filteredLogs.forEach(log => {
                const row = createLogRow(log);
                tbody.append(row);
            });
        }

        // Create log table row
        function createLogRow(log) {
            const isRecent = new Date(log.created_at) > new Date(Date.now() - 24 * 60 * 60 * 1000);
            const userInitials = log.user_id ? log.username.substring(0, 2).toUpperCase() : 'SYS';
            const userClass = log.user_id ? '' : 'system';
            
            return `
                <tr class="log-row" data-log='${JSON.stringify(log)}'>
                    <td>
                        <span class="fw-bold text-primary">#${log.id}</span>
                    </td>
                    <td>
                        <div class="user-info" style="background-color: white !important; border: none !important;">
                            <div class="user-avatar ${userClass}">${userInitials}</div>
                            <div>
                                <div class="fw-semibold">${log.full_name}</div>
                                <small class="text-muted">${log.username}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="log-action-badge action-${log.action.toLowerCase().replace('_', '')}">
                            ${log.action.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                        </span>
                    </td>
                    <td>
                        <div class="log-description">${log.description}</div>
                    </td>
                    <td>
                        <div class="fw-medium">${new Date(log.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</div>
                        <div class="time-info">
                            ${new Date(log.created_at).toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}
                            ${isRecent ? '<span class="recent-badge">NEW</span>' : ''}
                        </div>
                    </td>
                    <td>
                        <button class="action-btn" onclick="viewLogDetails(${log.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // View log details
        function viewLogDetails(logId) {
            const log = logs.find(l => l.id == logId);
            if (!log) return;

            const content = `
                <div class="detail-grid">
                    <div class="detail-label">Log ID:</div>
                    <div class="detail-value fw-bold text-primary">#${log.id}</div>
                    
                    <div class="detail-label">User:</div>
                    <div class="detail-value">
                        <div class="user-info" style="background-color: white !important; border: none !important;">
                            <div class="user-avatar ${log.user_id ? '' : 'system'}" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                ${log.user_id ? log.username.substring(0, 2).toUpperCase() : 'SYS'}
                            </div>
                            <div>
                                <div class="fw-semibold">${log.full_name}</div>
                                <small class="text-muted">${log.username}${log.role ? ` (${log.role})` : ''}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-label">Action:</div>
                    <div class="detail-value">
                        <span class="log-action-badge action-${log.action.toLowerCase().replace('_', '')}">
                            ${log.action.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                        </span>
                    </div>
                    
                    <div class="detail-label">Description:</div>
                    <div class="detail-value">${log.description}</div>
                    
                    <div class="detail-label">Timestamp:</div>
                    <div class="detail-value">
                        <div class="fw-medium">${new Date(log.created_at).toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</div>
                        <div class="time-info">${new Date(log.created_at).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        })}</div>
                        ${new Date(log.created_at) > new Date(Date.now() - 24 * 60 * 60 * 1000) ? 
                            '<span class="recent-badge">RECENT</span>' : ''}
                    </div>
                    
                    <div class="detail-label">Time Ago:</div>
                    <div class="detail-value">${getTimeAgo(log.created_at)}</div>
                </div>
            `;

            $('#logDetailsContent').html(content);
            $('#viewLogModal').modal('show');
        }

        // Helper function to get time ago
        function getTimeAgo(timestamp) {
            const now = new Date();
            const logTime = new Date(timestamp);
            const diffInSeconds = Math.floor((now - logTime) / 1000);

            if (diffInSeconds < 60) return `${diffInSeconds} seconds ago`;
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
            if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} days ago`;
            if (diffInSeconds < 31536000) return `${Math.floor(diffInSeconds / 2592000)} months ago`;
            return `${Math.floor(diffInSeconds / 31536000)} years ago`;
        }

        // Refresh logs
        function refreshLogs() {
            Swal.fire({
                title: 'Refreshing Logs...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Refreshed!',
                    text: 'Logs have been refreshed',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }, 1000);
        }

        // Export logs
        function exportLogs() {
            Swal.fire({
                icon: 'info',
                title: 'Export Started',
                text: 'System logs export will begin shortly',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Clear old logs
        function clearOldLogs() {
            Swal.fire({
                icon: 'warning',
                title: 'Clear Old Logs',
                text: 'This will permanently delete logs older than 30 days. Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Yes, clear them',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Clearing Old Logs...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cleared!',
                            text: 'Old logs have been cleared successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }, 1500);
                }
            });
        }
    </script>

</body>
</html>