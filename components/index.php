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

// Function to get individual beneficiary predictions
function getBeneficiaryPrediction($beneficiary_id)
{
    return callPythonAPI('/predict/beneficiary', ['beneficiary_id' => $beneficiary_id]);
}

// Function to get program analytics
function getProgramAnalytics($program_id)
{
    return callPythonAPI('/analytics/program/' . $program_id);
}

// Function to get model status
function getModelStatus()
{
    return callPythonAPI('/model/status');
}

// Get dashboard data from Python API
$dashboard_data = callPythonAPI('/dashboard_data');
$stats = $dashboard_data['statistics'] ?? [];
$programs = $dashboard_data['programs'] ?? [];
$districts = $dashboard_data['districts'] ?? [];
$trends = $dashboard_data['trends'] ?? [];

// Get additional ML insights
$model_status = getModelStatus();

// Get program predictions from Python API
$program_predictions_response = callPythonAPI('/predict/programs');
$program_predictions = $program_predictions_response['program_predictions'] ?? [];
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

    <style>
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            background: #f9fafb;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding-top: 80px;
            }
        }

        /* Dashboard Header */
        .dashboard-header {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            color: #1f2937;
            font-weight: 500;
            margin-bottom: 0;
        }

        .page-subtitle {
            color: #6b7280;
            margin-bottom: 1rem;
        }

        /* Statistic Cards */
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin-bottom: 0.75rem;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        /* Chart Containers */
        .chart-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        /* Program Cards */
        .program-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .program-card:hover {
            transform: translateY(-2px);
        }

        .success-high {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #11998e;
        }

        .success-medium {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #ffc107;
        }

        .success-low {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #dc2626;
        }

        /* Buttons */
        .btn-gradient {
            background: #6b7280;
            border: none;
            color: white;
            border-radius: 25px;
            padding: 8px 24px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .btn-gradient:hover {
            background: #1f2937;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(74, 108, 247, 0.25);
            color: white;
        }

        /* Alerts */
        .alert-ml {
            background: #f1f5ff;
            color: #1f2937;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            font-weight: 500;
        }

        /* Prediction Card */
        .prediction-card {
            background: #ffffff;
            color: #1f2937;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease;
        }

        .prediction-card:hover {
            transform: translateY(-2px);
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 1.5rem;
        }

        /* Prediction table enhancements */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Model status indicators */
        .model-status-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1rem;
        }

        /* Enhanced badges */
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Loading states */
        .table-loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Program prediction specific styles */
        .prediction-metric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .metric-value {
            font-weight: 600;
            font-size: 0.9rem;
        }

    </style>
    <title>SAKSES - Program Analytics Dashboard</title>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Dashboard Header -->
            <div class="dashboard-header text-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-chart-line me-3"></i>SAKSES Program Analytics Dashboard
                    </h1>
                    <p class="page-subtitle">
                        Smart Program Analytics for Knowledge-driven Success Evaluation System
                    </p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <button class="btn btn-gradient me-2" onclick="retrainModels()">
                            <i class="fas fa-robot me-2"></i>Retrain ML Models
                        </button>
                        <button class="btn btn-gradient" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dataset Upload Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="chart-container">
                        <h4 class="mb-3">
                            <i class="fas fa-upload me-2 text-primary"></i>Upload Training Dataset
                        </h4>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="datasetFile" class="form-label">Select CSV Dataset File</label>
                                    <input type="file" class="form-control" id="datasetFile" accept=".csv" onchange="previewDataset()" />
                                    <small class="text-muted">Upload CSV file with beneficiary training data</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button class="btn-gradient" onclick="uploadDataset()" id="uploadBtn" disabled>
                                        <i class="fas fa-upload me-2"></i>Upload & Train
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="downloadSampleDataset()">
                                        <i class="fas fa-download me-2"></i>Sample CSV
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dataset Preview Section -->
                        <div class="mt-4" id="datasetPreview" style="display: none;">
                            <h5 class="mb-3">
                                <i class="fas fa-eye me-2 text-success"></i>Dataset Preview
                            </h5>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center p-2">
                                            <small class="text-muted">Total Records</small>
                                            <div class="fw-bold" id="totalRecords">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center p-2">
                                            <small class="text-muted">Columns</small>
                                            <div class="fw-bold" id="totalColumns">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center p-2">
                                            <small class="text-muted">Valid Records</small>
                                            <div class="fw-bold text-success" id="validRecords">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center p-2">
                                            <small class="text-muted">Issues Found</small>
                                            <div class="fw-bold text-warning" id="issuesFound">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Quality Issues -->
                            <div id="dataIssues" style="display: none;" class="alert alert-warning mb-3">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Data Quality Issues</h6>
                                <ul id="issuesList" class="mb-0"></ul>
                            </div>

                            <!-- Preview Table -->
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="table-dark sticky-top">
                                        <tr id="previewHeaders"></tr>
                                    </thead>
                                    <tbody id="previewData"></tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <small class="text-muted">Showing first 10 rows of your dataset</small>
                                <div>
                                    <button class="btn btn-sm btn-outline-danger me-2" onclick="clearPreview()">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="proceedWithUpload()" id="proceedBtn">
                                        <i class="fas fa-check me-1"></i>Proceed with Upload
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div class="mt-3" id="uploadProgress" style="display: none;">
                            <div class="progress mb-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                    id="uploadProgressBar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="uploadStatus">Uploading...</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-light mt-2">Processing ML predictions...</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?= number_format($stats['total_beneficiaries'] ?? 0) ?></div>
                        <div class="text-muted">Total Beneficiaries</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?= number_format($stats['completed_programs'] ?? 0) ?></div>
                        <div class="text-muted">Completed Programs</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #fd746c 0%, #ff9068 100%);">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="stat-number"><?= number_format($stats['active_enrollments'] ?? 0) ?></div>
                        <div class="text-muted">Active Enrollments</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ffc107 0%, #ff8906 100%);">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-number"><?= number_format($stats['avg_success_score'] ?? 0, 1) ?>%</div>
                        <div class="text-muted">Avg Success Score</div>
                    </div>
                </div>
            </div>

            <!-- ML Prediction Alert -->
            <div class="alert alert-ml" role="alert">
                <i class="fas fa-brain me-2"></i>
                <strong>Machine Learning Insights:</strong>
                Our AI models predict program completion, employment outcomes, and skill development success to optimize training interventions.
            </div>

            <!-- ML Insights and Recent Predictions -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h4 class="mb-3">
                            <i class="fas fa-brain me-2 text-primary"></i>Recent Program Predictions
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Program</th>
                                        <th>Completion Rate</th>
                                        <th>Employment Rate</th>
                                        <th>Skill Development</th>
                                        <th>Overall Success</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="predictionsTable">
                                    <?php foreach (array_slice($program_predictions, 0, 5) as $pred): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($pred['program_name'] ?? 'Unknown Program') ?></strong><br>
                                                <small class="text-muted"><?= ucfirst(str_replace('_', ' ', $pred['program_type'] ?? '')) ?> | <?= $pred['total_enrollments'] ?? 0 ?> enrollments</small>
                                            </td>
                                            <td>
                                                <?php
                                                $completion_rate = $pred['predictions']['completion_prediction']['predicted_rate'] ?? 0;
                                                $color = $completion_rate >= 70 ? 'success' : ($completion_rate >= 50 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= $completion_rate ?>%</span><br>
                                                <small class="text-muted"><?= $pred['predictions']['completion_prediction']['trend'] ?? 'Unknown' ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $employment_rate = $pred['predictions']['employment_prediction']['predicted_rate'] ?? 0;
                                                $emp_color = $employment_rate >= 70 ? 'success' : ($employment_rate >= 50 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?= $emp_color ?>"><?= $employment_rate ?>%</span><br>
                                                <small class="text-muted"><?= $pred['predictions']['employment_prediction']['trend'] ?? 'Unknown' ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $skill_improvement = $pred['predictions']['skill_development_prediction']['predicted_improvement'] ?? 0;
                                                $skill_color = $skill_improvement >= 70 ? 'success' : ($skill_improvement >= 50 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?= $skill_color ?>"><?= $skill_improvement ?>%</span><br>
                                                <small class="text-muted"><?= $pred['predictions']['skill_development_prediction']['trend'] ?? 'Unknown' ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $overall = $pred['predictions']['overall_success']['predicted_rate'] ?? 0;
                                                $overall_color = $pred['predictions']['overall_success']['badge_color'] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $overall_color ?>"><?= $overall ?>%</span><br>
                                                <small class="text-muted"><?= $pred['predictions']['overall_success']['category'] ?? 'Unknown' ?> Success</small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewProgramDetails(<?= $pred['program_id'] ?>)">
                                                    View Analytics
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h4 class="mb-3">
                            <i class="fas fa-cogs me-2 text-primary"></i>Model Status
                        </h4>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Models Loaded</span>
                                <span class="badge bg-success"><?= count($model_status['models_loaded'] ?? []) ?></span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" style="width: <?= (count($model_status['models_loaded'] ?? []) / 3) * 100 ?>%"></div>
                            </div>
                        </div>

                        <?php if (!empty($model_status['model_metrics'])): ?>
                            <div class="mb-3">
                                <h6>Completion Prediction Model</h6>
                                <small class="text-muted">
                                    Accuracy: <?= number_format(($model_status['model_metrics']['completion_prediction']['accuracy'] ?? 0) * 100, 1) ?>%<br>
                                    Training Samples: <?= number_format($model_status['model_metrics']['completion_prediction']['training_samples'] ?? 0) ?>
                                </small>
                            </div>

                            <div class="mb-3">
                                <h6>Employment Prediction Model</h6>
                                <small class="text-muted">
                                    Accuracy: <?= number_format(($model_status['model_metrics']['employment_prediction']['accuracy'] ?? 0) * 100, 1) ?>%<br>
                                    Training Samples: <?= number_format($model_status['model_metrics']['employment_prediction']['training_samples'] ?? 0) ?>
                                </small>
                            </div>

                            <div class="mb-3">
                                <h6>Skill Development Model</h6>
                                <small class="text-muted">
                                    Accuracy: <?= number_format(($model_status['model_metrics']['skill_development_prediction']['accuracy'] ?? 0) * 100, 1) ?>%<br>
                                    Training Samples: <?= number_format($model_status['model_metrics']['skill_development_prediction']['training_samples'] ?? 0) ?>
                                </small>
                            </div>
                        <?php endif; ?>

                        <button class="btn btn-sm btn-gradient w-100" onclick="refreshPredictions()">
                            <i class="fas fa-sync me-2"></i>Refresh Predictions
                        </button>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Program Performance Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h4 class="mb-3">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>Program Performance Analysis
                        </h4>
                        <canvas id="programChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Success Rate by District -->
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h4 class="mb-3">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>Success by District
                        </h4>
                        <canvas id="districtChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Enrollment Trends -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="chart-container">
                        <h4 class="mb-3">
                            <i class="fas fa-trending-up me-2 text-primary"></i>Enrollment Trends (Last 12 Months)
                        </h4>
                        <canvas id="trendsChart" height="60"></canvas>
                    </div>
                </div>
            </div>

            <!-- Program Cards -->
            <div class="row">
                <div class="col-12">
                    <div class="chart-container">
                        <h4 class="mb-3">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>Program Performance Summary
                        </h4>
                        <div class="row" id="programCards">
                            <?php foreach ($programs as $program): ?>
                                <?php
                                $success_rate = $program['avg_success_score'] ?? 0;
                                $success_class = $success_rate > 70 ? 'success-high' : ($success_rate > 40 ? 'success-medium' : 'success-low');
                                ?>
                                <div class="col-lg-6 col-md-12 mb-3">
                                    <div class="program-card <?= $success_class ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0"><?= htmlspecialchars($program['program_name'] ?? 'Unknown Program') ?></h6>
                                            <span class="badge bg-primary"><?= ucfirst(str_replace('_', ' ', $program['program_type'] ?? '')) ?></span>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <small class="text-muted">Enrollments</small>
                                                <div class="fw-bold"><?= number_format($program['total_enrollments'] ?? 0) ?></div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Success Rate</small>
                                                <div class="fw-bold"><?= number_format($success_rate, 1) ?>%</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" style="width: <?= $success_rate ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prediction Panel -->
            <div class="row">
                <div class="col-12">
                    <div class="prediction-card">
                        <h4 class="mb-3 text-center">
                            <i class="fas fa-crystal-ball me-2"></i>ML-Powered Program Predictions
                        </h4>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                <h5>Program Completion</h5>
                                <p>Predicts likelihood of beneficiaries completing their assigned programs</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-briefcase fa-3x mb-3 text-primary"></i>
                                <h5>Employment Outcomes</h5>
                                <p>Forecasts post-program employment success and job placement likelihood</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-chart-line fa-3x mb-3 text-warning"></i>
                                <h5>Skill Development</h5>
                                <p>Measures expected skill improvement and learning progress outcomes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Dashboard data from PHP
        const dashboardData = <?= json_encode($dashboard_data ?? []) ?>;
        const programs = <?= json_encode($programs ?? []) ?>;
        const districts = <?= json_encode($districts ?? []) ?>;
        const trends = <?= json_encode($trends ?? []) ?>;

        // Program Performance Chart
        const programCtx = document.getElementById('programChart').getContext('2d');
        const programChart = new Chart(programCtx, {
            type: 'bar',
            data: {
                labels: programs.map(p => p.program_name?.substring(0, 20) + '...' || 'Unknown'),
                datasets: [{
                    label: 'Success Rate (%)',
                    data: programs.map(p => p.avg_success_score || 0),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }, {
                    label: 'Enrollments',
                    data: programs.map(p => p.total_enrollments || 0),
                    backgroundColor: 'rgba(56, 239, 125, 0.8)',
                    borderColor: 'rgba(56, 239, 125, 1)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Success Rate (%)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Number of Enrollments'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // District Success Chart
        const districtCtx = document.getElementById('districtChart').getContext('2d');
        const districtChart = new Chart(districtCtx, {
            type: 'doughnut',
            data: {
                labels: districts.map(d => `District ${d.district}`),
                datasets: [{
                    data: districts.map(d => d.avg_success_score || 0),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Trends Chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        const trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trends.map(t => `${t.year}-${String(t.month).padStart(2, '0')}`),
                datasets: [{
                    label: 'New Enrollments',
                    data: trends.map(t => t.enrollments || 0),
                    borderColor: 'rgba(102, 126, 234, 1)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Completions',
                    data: trends.map(t => t.completions || 0),
                    borderColor: 'rgba(56, 239, 125, 1)',
                    backgroundColor: 'rgba(56, 239, 125, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        let trainingStatusInterval = null;

        function retrainModels() {
            Swal.fire({
                title: 'Retrain ML Models?',
                html: `
            <p>This will retrain the models with the latest data:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>Program Completion Prediction</li>
                <li>Employment Outcome Prediction</li>
                <li>Skill Development Prediction</li>
            </ul>
            <p><small class="text-muted">Process will take approximately 3-4 minutes</small></p>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Retrain!',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Start training
                    fetch('http://localhost:8800/model/retrain', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showTrainingProgress();
                            } else {
                                throw new Error(data.error || 'Unknown error');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to start training: ' + error.message,
                                icon: 'error'
                            });
                        });
                }
            });
        }

        function showTrainingProgress() {
            Swal.fire({
                title: 'Training Program ML Models...',
                html: `
                    <div class="mb-3">
                        <div class="progress mb-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                style="width: 0%" id="trainingProgress"></div>
                        </div>
                        <small class="text-muted" id="progressPercentage">0%</small>
                    </div>
                    <div class="mb-2">
                        <strong id="trainingPhase">Initializing...</strong>
                    </div>
                    <p id="trainingStatus">Preparing training environment...</p>
                    <div id="trainingLog" style="max-height: 250px; overflow-y: auto; text-align: left; font-family: monospace; font-size: 11px; background: #1a1a1a; color: #00ff00; padding: 15px; border-radius: 5px; margin-top: 15px; border: 1px solid #333;">
                        <div class="log-entry">[INFO] Initializing SAKSES Program ML Training...</div>
                        <div class="log-entry">[INFO] Loading program completion data...</div>
                        <div class="log-entry">[INFO] Loading employment outcome data...</div>
                        <div class="log-entry">[INFO] Loading skill development metrics...</div>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                width: '600px',
                didOpen: () => {
                    // Start polling for progress immediately
                    trainingStatusInterval = setInterval(updateTrainingProgress, 800);
                }
            });
        }

        function updateTrainingProgress() {
            fetch('http://localhost:8800/model/training-status')
                .then(response => response.json())
                .then(data => {
                    const progressBar = document.getElementById('trainingProgress');
                    const statusText = document.getElementById('trainingStatus');
                    const phaseText = document.getElementById('trainingPhase');
                    const percentageText = document.getElementById('progressPercentage');
                    const logDiv = document.getElementById('trainingLog');

                    if (progressBar && statusText && phaseText && percentageText) {
                        progressBar.style.width = (data.progress || 0) + '%';
                        percentageText.textContent = (data.progress || 0) + '%';
                        statusText.textContent = data.message || 'Training in progress...';
                        phaseText.textContent = data.phase || 'Processing...';

                        // Add to log with timestamp
                        if (logDiv && data.log_entry) {
                            const logEntry = document.createElement('div');
                            logEntry.className = 'log-entry';
                            const timestamp = new Date().toLocaleTimeString();
                            logEntry.innerHTML = `<span style="color: #888">[${timestamp}]</span> ${data.log_entry}`;
                            logDiv.appendChild(logEntry);
                            logDiv.scrollTop = logDiv.scrollHeight;
                        }

                        // Update progress bar color based on progress
                        if (data.progress >= 80) {
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-success';
                        } else if (data.progress >= 40) {
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';
                        } else {
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
                        }
                    }

                    // Check if training is complete
                    if (data.status === 'completed') {
                        clearInterval(trainingStatusInterval);

                        // Safely handle models_trained array
                        const modelsTrainedList = (data.models_trained && Array.isArray(data.models_trained)) ?
                            data.models_trained.map(model =>
                                `<li><i class="fas fa-check text-success"></i> ${model.replace('_', ' ').toUpperCase().replace('PREDICTION', 'MODEL')}</li>`
                            ).join('') :
                            '<li><i class="fas fa-check text-success"></i> Program models trained successfully</li>';

                        const duration = data.training_duration || 'Unknown';

                        Swal.fire({
                            title: 'Training Complete!',
                            html: `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success" style="font-size: 48px; margin-bottom: 15px;"></i>
                            <p class="mb-3">Program prediction models have been retrained successfully!</p>
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Training Results:</h6>
                                    <ul style="text-align: left; display: inline-block; list-style: none; padding: 0;">
                                        ${modelsTrainedList}
                                    </ul>
                                    <hr>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> Training Duration: ${duration}<br>
                                        <i class="fas fa-calendar"></i> Completed: ${new Date().toLocaleString()}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `,
                            icon: 'success',
                            confirmButtonText: '<i class="fas fa-sync"></i> Refresh Dashboard',
                            confirmButtonClass: 'btn btn-success'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (data.status === 'error') {
                        clearInterval(trainingStatusInterval);
                        Swal.fire({
                            title: 'Training Failed!',
                            html: `
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 48px; margin-bottom: 15px;"></i>
                            <p>An error occurred during training:</p>
                            <div class="alert alert-danger">
                                ${data.message || 'Unknown error occurred'}
                            </div>
                        </div>
                    `,
                            icon: 'error',
                            confirmButtonText: 'Try Again',
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                retrainModels();
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching training status:', error);
                    // Don't clear interval immediately, might be temporary network issue
                });
        }

        function refreshDashboard() {
            window.location.reload();
        }

        function viewProgramDetails(programId) {
            fetch(`http://localhost:8800/analytics/program/${programId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-graduation-cap text-primary me-2"></i>Program Overview</h6>
                        <div class="prediction-metric">
                            <span class="metric-label">Total Enrollments:</span>
                            <span class="metric-value">${data.total_enrollments || 0}</span>
                        </div>
                        <div class="prediction-metric">
                            <span class="metric-label">Completions:</span>
                            <span class="metric-value">${data.completions || 0}</span>
                        </div>
                        <div class="prediction-metric">
                            <span class="metric-label">Average Attendance:</span>
                            <span class="metric-value">${(data.avg_attendance || 0).toFixed(1)}%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line text-success me-2"></i>Success Metrics</h6>
                        <div class="prediction-metric">
                            <span class="metric-label">Success Score:</span>
                            <span class="metric-value">${(data.avg_success_score || 0).toFixed(1)}%</span>
                        </div>
                        <div class="prediction-metric">
                            <span class="metric-label">Completion Rate:</span>
                            <span class="metric-value">${((data.completions || 0) / (data.total_enrollments || 1) * 100).toFixed(1)}%</span>
                        </div>
                    </div>
                </div>
            `;

                    Swal.fire({
                        title: `${data.program_name} - Program Analytics`,
                        html: content,
                        icon: 'info',
                        width: 700,
                        confirmButtonText: 'Close'
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to get program analytics: ' + error.message,
                        icon: 'error'
                    });
                });
        }

        function refreshPredictions() {
            Swal.fire({
                title: 'Refreshing...',
                text: 'Getting latest program predictions',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulate refresh - in production, you'd reload the page or fetch new data
            setTimeout(() => {
                Swal.close();
                window.location.reload();
            }, 2000);
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        function uploadDataset() {
            const fileInput = document.getElementById('datasetFile');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire({
                    title: 'No File Selected',
                    text: 'Please select a CSV file to upload',
                    icon: 'warning'
                });
                return;
            }

            if (!file.name.toLowerCase().endsWith('.csv')) {
                Swal.fire({
                    title: 'Invalid File Type',
                    text: 'Please select a CSV file',
                    icon: 'error'
                });
                return;
            }

            const formData = new FormData();
            formData.append('dataset', file);

            // Show upload progress
            document.getElementById('uploadProgress').style.display = 'block';
            document.getElementById('uploadProgressBar').style.width = '10%';
            document.getElementById('uploadStatus').textContent = 'Uploading dataset...';

            fetch('http://localhost:8800/upload_dataset', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('uploadProgressBar').style.width = '50%';
                        document.getElementById('uploadStatus').textContent = 'Dataset uploaded. Processing...';

                        // Start training automatically
                        setTimeout(() => {
                            retrainModels();
                            document.getElementById('uploadProgress').style.display = 'none';
                        }, 1000);

                        Swal.fire({
                            title: 'Dataset Uploaded Successfully!',
                            html: `
                    <p>Records processed: <strong>${data.records_processed}</strong></p>
                    <p>Starting model training...</p>
                `,
                            icon: 'success',
                            timer: 3000
                        });
                    } else {
                        throw new Error(data.error || 'Upload failed');
                    }
                })
                .catch(error => {
                    document.getElementById('uploadProgress').style.display = 'none';
                    Swal.fire({
                        title: 'Upload Failed',
                        text: error.message,
                        icon: 'error'
                    });
                });
        }

        function downloadSampleDataset() {
            // Create sample CSV content
            const csvContent = `beneficiary_id,first_name,last_name,date_of_birth,gender,civil_status,education_level,family_size,monthly_income_before,employment_status_before,is_pantawid_beneficiary,is_indigenous,has_disability,household_head,barangay_name,district,program_name,program_type,duration_months,enrollment_date,completion_date,status,attendance_rate,pre_assessment_score,post_assessment_score,employment_outcome,monthly_income_after,success_score,completion_rate,employment_rate,skill_improvement
B200,Maria,Santos,1990-05-15,Female,Married,High School,4,8000,unemployed,1,0,0,1,Bagong Pag-asa,4,Bakery Skills Training,skills_training,3,2025-01-15,2025-04-15,completed,95.5,65,88,employed,15000,85.5,95,90,23
B201,Juan,Dela Cruz,1985-08-22,Male,Single,College,2,12000,underemployed,0,0,0,0,San Antonio,3,Call Center Job Readiness,employment_facilitation,2,2025-02-01,2025-04-01,completed,88.0,70,92,employed,18000,90.2,88,95,22
B202,Ana,Rodriguez,1992-12-10,Female,Widowed,Vocational,3,6000,unemployed,1,1,0,1,Payatas,2,Sari-Sari Store Start-up,microenterprise,6,2025-01-20,,active,82.5,55,75,self_employed,11000,78.3,82,85,20
B203,Pedro,Garcia,1988-03-18,Male,Married,Elementary,5,5000,unemployed,1,0,1,1,Culiat,4,Food Cart Business,entrepreneurship,4,2025-03-01,,active,75.0,45,68,business_started,13000,72.1,75,80,23
B204,Rosa,Mendoza,1995-11-25,Female,Single,Senior High,1,9000,employed,0,0,0,0,Holy Spirit,2,Dressmaking Skills,skills_training,5,2025-04-10,,enrolled,0,60,,unemployed,9000,65.0,0,0,0`;

            // Create and download file
            const blob = new Blob([csvContent], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sakses_sample_dataset.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            Swal.fire({
                title: 'Sample Dataset Downloaded',
                text: 'Use this format for your training data',
                icon: 'info',
                timer: 2000
            });
        }

        let csvData = null;
        let csvHeaders = [];

        function previewDataset() {
            const fileInput = document.getElementById('datasetFile');
            const file = fileInput.files[0];

            if (!file) {
                clearPreview();
                return;
            }

            if (!file.name.toLowerCase().endsWith('.csv')) {
                Swal.fire({
                    title: 'Invalid File Type',
                    text: 'Please select a CSV file',
                    icon: 'error'
                });
                clearPreview();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    parseCSV(e.target.result);
                } catch (error) {
                    Swal.fire({
                        title: 'Error Reading File',
                        text: 'Unable to parse CSV file: ' + error.message,
                        icon: 'error'
                    });
                }
            };
            reader.readAsText(file);
        }

        function parseCSV(csvText) {
            const lines = csvText.split('\n').filter(line => line.trim());
            if (lines.length < 2) {
                Swal.fire({
                    title: 'Invalid CSV',
                    text: 'CSV file must have at least a header and one data row',
                    icon: 'error'
                });
                return;
            }

            // Parse headers
            csvHeaders = lines[0].split(',').map(header => header.trim().replace(/"/g, ''));

            // Parse data
            csvData = [];
            const issues = [];
            let validRecords = 0;

            for (let i = 1; i < lines.length && i < 50; i++) { // Preview first 50 rows max
                const values = parseCSVRow(lines[i]);
                if (values.length !== csvHeaders.length) {
                    issues.push(`Row ${i}: Column count mismatch (expected ${csvHeaders.length}, got ${values.length})`);
                    continue;
                }

                const row = {};
                let hasRequiredFields = true;

                for (let j = 0; j < csvHeaders.length; j++) {
                    row[csvHeaders[j]] = values[j];
                }

                // Check for required fields
                const requiredFields = ['beneficiary_id', 'first_name', 'last_name', 'program_name'];
                for (const field of requiredFields) {
                    if (!row[field] || row[field].trim() === '') {
                        issues.push(`Row ${i}: Missing required field '${field}'`);
                        hasRequiredFields = false;
                    }
                }

                if (hasRequiredFields) {
                    validRecords++;
                }

                csvData.push(row);
            }

            displayPreview(csvData.slice(0, 10), issues, validRecords, lines.length - 1);
        }

        function parseCSVRow(row) {
            const values = [];
            let current = '';
            let inQuotes = false;

            for (let i = 0; i < row.length; i++) {
                const char = row[i];
                if (char === '"') {
                    inQuotes = !inQuotes;
                } else if (char === ',' && !inQuotes) {
                    values.push(current.trim());
                    current = '';
                } else {
                    current += char;
                }
            }
            values.push(current.trim());

            return values.map(val => val.replace(/"/g, ''));
        }

        function displayPreview(data, issues, validRecords, totalRecords) {
            // Update statistics
            document.getElementById('totalRecords').textContent = totalRecords;
            document.getElementById('totalColumns').textContent = csvHeaders.length;
            document.getElementById('validRecords').textContent = validRecords;
            document.getElementById('issuesFound').textContent = issues.length;

            // Show issues if any
            if (issues.length > 0) {
                const issuesDiv = document.getElementById('dataIssues');
                const issuesList = document.getElementById('issuesList');
                issuesList.innerHTML = issues.slice(0, 10).map(issue => `<li>${issue}</li>`).join('');
                issuesDiv.style.display = 'block';
            } else {
                document.getElementById('dataIssues').style.display = 'none';
            }

            // Create table headers
            const headersRow = document.getElementById('previewHeaders');
            headersRow.innerHTML = csvHeaders.map(header => `<th>${header}</th>`).join('');

            // Create table data
            const dataBody = document.getElementById('previewData');
            dataBody.innerHTML = data.map((row, index) => {
                const cells = csvHeaders.map(header => {
                    let value = row[header] || '';
                    // Truncate long values
                    if (value.length > 30) {
                        value = value.substring(0, 30) + '...';
                    }
                    return `<td>${value}</td>`;
                }).join('');
                return `<tr>${cells}</tr>`;
            }).join('');

            // Show preview section
            document.getElementById('datasetPreview').style.display = 'block';
            document.getElementById('uploadBtn').disabled = false;
        }

        function clearPreview() {
            document.getElementById('datasetPreview').style.display = 'none';
            document.getElementById('uploadBtn').disabled = true;
            csvData = null;
            csvHeaders = [];
        }

        function proceedWithUpload() {
            if (csvData && csvData.length > 0) {
                uploadDataset();
            }
        }

        // Update the existing uploadDataset function
        function uploadDataset() {
            const fileInput = document.getElementById('datasetFile');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire({
                    title: 'No File Selected',
                    text: 'Please select a CSV file to upload',
                    icon: 'warning'
                });
                return;
            }

            if (!csvData || csvData.length === 0) {
                Swal.fire({
                    title: 'No Preview Data',
                    text: 'Please preview the dataset first',
                    icon: 'warning'
                });
                return;
            }

            // Show confirmation with preview summary
            const validRecords = document.getElementById('validRecords').textContent;
            const issues = document.getElementById('issuesFound').textContent;

            Swal.fire({
                title: 'Confirm Dataset Upload',
                html: `
            <div class="text-start">
                <p><strong>Dataset Summary:</strong></p>
                <ul>
                    <li>Total Records: ${document.getElementById('totalRecords').textContent}</li>
                    <li>Valid Records: ${validRecords}</li>
                    <li>Columns: ${document.getElementById('totalColumns').textContent}</li>
                    <li>Issues Found: ${issues}</li>
                </ul>
                ${issues > 0 ? '<p class="text-warning"><small><i class="fas fa-exclamation-triangle"></i> Records with issues will be skipped</small></p>' : ''}
            </div>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Upload & Process',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    performUpload(file);
                }
            });
        }

        function performUpload(file) {
            const formData = new FormData();
            formData.append('dataset', file);

            // Show upload progress
            document.getElementById('uploadProgress').style.display = 'block';
            document.getElementById('uploadProgressBar').style.width = '10%';
            document.getElementById('uploadStatus').textContent = 'Uploading dataset...';

            fetch('http://localhost:8800/upload_dataset', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('uploadProgressBar').style.width = '50%';
                        document.getElementById('uploadStatus').textContent = 'Dataset uploaded. Processing...';

                        // Start training automatically
                        setTimeout(() => {
                            retrainModels();
                            document.getElementById('uploadProgress').style.display = 'none';
                            clearPreview();
                        }, 1000);

                        Swal.fire({
                            title: 'Dataset Uploaded Successfully!',
                            html: `
                        <p>Records processed: <strong>${data.records_processed}</strong></p>
                        <p>Starting model training...</p>
                    `,
                            icon: 'success',
                            timer: 3000
                        });
                    } else {
                        throw new Error(data.error || 'Upload failed');
                    }
                })
                .catch(error => {
                    document.getElementById('uploadProgress').style.display = 'none';
                    Swal.fire({
                        title: 'Upload Failed',
                        text: error.message,
                        icon: 'error'
                    });
                });
        }
    </script>

</body>

</html>

<?php $conn->close(); ?>