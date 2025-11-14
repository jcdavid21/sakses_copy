<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- Desktop Sidebar -->
    <div class="desktop-sidebar d-none d-md-block">
        <div class="sidebar-header">
            <div class="logo">SAKSES</div>
            <div class="logo-subtitle">Analytics System</div>
        </div>

        <ul class="nav-menu">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="index.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- Data Management -->
            <div class="nav-section">Data Management</div>
            <li class="nav-item">
                <a href="beneficiaries.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'beneficiaries.php' ? 'active' : '' ?>">
                    <i class="fas fa-users nav-icon"></i>
                    <span class="nav-text">Beneficiaries</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="./livelihood.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'livelihood.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-hand-holding-heart nav-icon"></i>
                    <span class="nav-text">Livelihood Programs</span>
                </a>
            </li>
            <div class="nav-section">Program Management</div>
            <li class="nav-item">
                <a href="./program_enrollments.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'program_enrollments.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-plus nav-icon"></i>
                    <span class="nav-text">Program Enrollments</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="./program_resources.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'enrollments.php' ? 'active' : '' ?>">
                    <i class="fas fa-book nav-icon"></i>
                    <span class="nav-text">Program Resources</span>
                </a>
            </li>
            <!-- <li class="nav-item">
                <a href="outcomes.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'outcomes.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span class="nav-text">Outcomes</span>
                </a>
            </li> -->

            <!-- Analytics & Predictions -->
            <!-- <div class="nav-section">Analytics & Predictions</div>
            <li class="nav-item">
                <a href="predictions.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'predictions.php' ? 'active' : '' ?>">
                    <i class="fas fa-brain nav-icon"></i>
                    <span class="nav-text">Predictions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="analytics.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    <span class="nav-text">Analytics</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="success-factors.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'success-factors.php' ? 'active' : '' ?>">
                    <i class="fas fa-bullseye nav-icon"></i>
                    <span class="nav-text">Success Factors</span>
                </a>
            </li> -->

            <!-- Reports -->
            <div class="nav-section">Reports</div>
            <!-- <li class="nav-item">
                <a href="reports.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt nav-icon"></i>
                    <span class="nav-text">Generate Reports</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a href="./systemlogs.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'systemlogs.php' ? 'active' : '' ?>">
                    <i class="fas fa-history nav-icon"></i>
                    <span class="nav-text">System Logs</span>
                </a>
            </li>
        </ul>


        <!-- User Info -->
        <div class="user-info">
            <div class="user-profile">
                <div class="user-details">
                    <div class="user-name">Log out</div>
                </div>
                <i class="fas fa-sign-out-alt logout-btn" onclick="logout()"></i>
            </div>
        </div>
    </div>

    <!-- Mobile Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top mobile-navbar d-md-none">
        <div class="container-fluid">
            <a class="navbar-brand navbar-brand-mobile" href="#">
                <i class="fas fa-chart-line me-2"></i>SAKSES
            </a>

            <div class="d-flex align-items-center">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="mobileNavbar">
                <ul class="navbar-nav navbar-nav-mobile w-100">

                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
                            <i class="fas fa-tachometer-alt nav-icon"></i>Dashboard
                        </a>
                    </li>

                    <!-- Data Management -->
                    <li>
                        <h6 class="dropdown-header dropdown-header-mobile">Data Management</h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'beneficiaries.php' ? 'active' : '' ?>" href="beneficiaries.php">
                            <i class="fas fa-users nav-icon"></i>Beneficiaries
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'livelihood.php' ? 'active' : '' ?>" href="livelihood.php">
                            <i class="fa-solid fa-hand-holding-heart nav-icon"></i>Livelihood Programs
                        </a>
                    </li>

                    <!-- Program Management -->
                    <li>
                        <h6 class="dropdown-header dropdown-header-mobile">Program Management</h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'program_enrollments.php' ? 'active' : '' ?>" href="program_enrollments.php">
                            <i class="fas fa-user-plus nav-icon"></i>Program Enrollments
                        </a>
                    </li>

                    <!-- Reports -->
                    <li>
                        <h6 class="dropdown-header dropdown-header-mobile">Reports</h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'systemlogs.php' ? 'active' : '' ?>" href="systemlogs.php">
                            <i class="fas fa-history nav-icon"></i>System Logs
                        </a>
                    </li>

                    <!-- Logout -->
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#" onclick="logout()">
                            <i class="fas fa-sign-out-alt nav-icon"></i>Logout
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>





    <script>
        $(document).ready(function() {
            $(window).on('resize', function() {
                if ($(window).width() > 768) {
                    $('.navbar-collapse').collapse('hide');
                }
            });

            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        function logout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of the system",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Logging out...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        window.location.href = './logout.php';
                    }, 1000);
                }
            });
        }

        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                timerProgressBar: true
            });
        }

        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message
            });
        }
    </script>
</body>

</html>