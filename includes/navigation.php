<header class="app-header">
    <div class="main-header-container container-fluid">
        <!-- Left Header Content -->
        <div class="header-content-left">
            <div class="header-element">
                <div class="horizontal-logo pl-4">
                    <a href="http://localhost/dfcs/dashboard" class="header-logo">
                        <img src="http://localhost/dfcs/assets/images/brand-logos/desktop-logo.png" alt="logo"
                            class="desktop-logo" />
                        <img src="http://localhost/dfcs/assets/images/brand-logos/toggle-logo.png" alt="logo"
                            class="toggle-logo" />
                        <img src="http://localhost/dfcs/assets/images/brand-logos/desktop-dark.png" alt="logo"
                            class="desktop-dark" />
                        <img src="http://localhost/dfcs/assets/images/brand-logos/toggle-dark.png" alt="logo"
                            class="toggle-dark" />
                    </a>
                </div>
            </div>
            <div class="header-element">
                <a aria-label="Hide Sidebar"
                    class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                    data-bs-toggle="sidebar" href="javascript:void(0);">
                    <span style="color: #6AA32D!important;"></span>
                </a>
            </div>
        </div>

        <!-- Right Header Content -->
        <div class="header-content-right">
            <!-- Location/Country -->
            <div class="header-element country-selector">
                <a href="javascript:void(0);" class="header-link">
                    <img src="http://localhost/dfcs/assets/images/flags/kenya.png" alt="img"
                        class="rounded-circle header-link-icon" />
                </a>
            </div>

            <!-- Notifications -->
            <div class="header-element notifications-dropdown">
                <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" id="notificationDropdown">
                    <i class="bx bx-bell header-link-icon"></i>
                    <?php
        $app = new App;
        $userId = $_SESSION['user_id'];
        $roleId = $_SESSION['role_id'];
        
        // Initialize notification count
        $totalNotifications = 0;
        $notifications = [];
        
        switch($roleId) {
            case 1: // Farmer
                // 1. Active loan applications
                $loanQuery = "SELECT COUNT(*) as count FROM loan_applications la 
                             INNER JOIN farmers f ON la.farmer_id = f.id 
                             WHERE f.user_id = $userId AND la.status IN ('pending', 'under_review')";
                $loanResult = $app->select_one($loanQuery);
                if($loanResult->count > 0) {
                    $notifications[] = ['icon' => 'bx-money', 'text' => "{$loanResult->count} loan application(s) in progress", 'color' => 'text-primary'];
                }
                
                // 2. Input credit applications
                $creditQuery = "SELECT COUNT(*) as count FROM input_credit_applications ica
                               INNER JOIN farmers f ON ica.farmer_id = f.id
                               WHERE f.user_id = $userId AND ica.status IN ('pending', 'under_review')";
                $creditResult = $app->select_one($creditQuery);
                if($creditResult->count > 0) {
                    $notifications[] = ['icon' => 'bx-package', 'text' => "{$creditResult->count} input credit application(s) pending", 'color' => 'text-warning'];
                }
                
                // 3. Expected produce deliveries
                $produceQuery = "SELECT COUNT(*) as count FROM expected_produce ep
                                INNER JOIN farm_products fp ON ep.farm_product_id = fp.id
                                INNER JOIN farms f ON fp.farm_id = f.id
                                INNER JOIN farmers fr ON f.farmer_id = fr.id
                                WHERE fr.user_id = $userId AND ep.status = 'pending' 
                                AND ep.expected_delivery_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
                $produceResult = $app->select_one($produceQuery);
                if($produceResult->count > 0) {
                    $notifications[] = ['icon' => 'bx-leaf', 'text' => "{$produceResult->count} produce delivery(ies) due soon", 'color' => 'text-success'];
                }
                
                // 4. Account balance updates
                $balanceQuery = "SELECT balance FROM farmer_accounts fa
                                INNER JOIN farmers f ON fa.farmer_id = f.id
                                WHERE f.user_id = $userId";
                $balanceResult = $app->select_one($balanceQuery);
                if($balanceResult && $balanceResult->balance > 50000) {
                    $notifications[] = ['icon' => 'bx-wallet', 'text' => "Account balance: KES " . number_format($balanceResult->balance), 'color' => 'text-info'];
                }
                
                // 5. Farm management reminders
                $farmQuery = "SELECT COUNT(*) as count FROM farms f
                             INNER JOIN farmers fr ON f.farmer_id = fr.id
                             WHERE fr.user_id = $userId AND f.is_active = 1";
                $farmResult = $app->select_one($farmQuery);
                if($farmResult->count > 0) {
                    $notifications[] = ['icon' => 'bx-home', 'text' => "Managing {$farmResult->count} active farm(s)", 'color' => 'text-secondary'];
                }
                
                // 6. Recent transactions
                $transQuery = "SELECT COUNT(*) as count FROM farmer_account_transactions fat
                              INNER JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                              INNER JOIN farmers f ON fa.farmer_id = f.id
                              WHERE f.user_id = $userId AND DATE(fat.created_at) = CURDATE()";
                $transResult = $app->select_one($transQuery);
                if($transResult->count > 0) {
                    $notifications[] = ['icon' => 'bx-transfer', 'text' => "{$transResult->count} transaction(s) today", 'color' => 'text-primary'];
                }
                break;
                
            case 2: // SACCO Staff
                // 1. Pending loan applications
                $pendingLoans = $app->select_one("SELECT COUNT(*) as count FROM loan_applications WHERE status = 'pending' AND provider_type = 'sacco'");
                if($pendingLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-file', 'text' => "{$pendingLoans->count} loan application(s) awaiting review", 'color' => 'text-danger'];
                }
                
                // 2. Under review loans
                $reviewLoans = $app->select_one("SELECT COUNT(*) as count FROM loan_applications WHERE status = 'under_review' AND provider_type = 'sacco'");
                if($reviewLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-search', 'text' => "{$reviewLoans->count} loan(s) under review", 'color' => 'text-warning'];
                }
                
                // 3. Recently approved loans
                $approvedLoans = $app->select_one("SELECT COUNT(*) as count FROM approved_loans al
                                                 INNER JOIN loan_applications la ON al.loan_application_id = la.id
                                                 WHERE la.provider_type = 'sacco' AND DATE(al.approval_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
                if($approvedLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-check-circle', 'text' => "{$approvedLoans->count} loan(s) approved this week", 'color' => 'text-success'];
                }
                
                // 4. Active loans requiring attention
                $activeLoans = $app->select_one("SELECT COUNT(*) as count FROM approved_loans al
                                               INNER JOIN loan_applications la ON al.loan_application_id = la.id
                                               WHERE la.provider_type = 'sacco' AND al.status = 'active'");
                if($activeLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-time', 'text' => "{$activeLoans->count} active loan(s) being serviced", 'color' => 'text-info'];
                }
                
                // 5. Recent repayments
                $recentRepayments = $app->select_one("SELECT COUNT(*) as count FROM loan_repayments lr
                                                    INNER JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                    INNER JOIN loan_applications la ON al.loan_application_id = la.id
                                                    WHERE la.provider_type = 'sacco' AND DATE(lr.payment_date) = CURDATE()");
                if($recentRepayments->count > 0) {
                    $notifications[] = ['icon' => 'bx-money', 'text' => "{$recentRepayments->count} repayment(s) received today", 'color' => 'text-success'];
                }
                
                // 6. Member farmers
                $totalFarmers = $app->select_one("SELECT COUNT(*) as count FROM farmers WHERE is_verified = 1");
                if($totalFarmers->count > 0) {
                    $notifications[] = ['icon' => 'bx-group', 'text' => "{$totalFarmers->count} verified farmer members", 'color' => 'text-secondary'];
                }
                break;
                
            case 3: // Bank Staff
                // 1. Pending bank loan applications
                $bankPendingLoans = $app->select_one("SELECT COUNT(*) as count FROM loan_applications WHERE status = 'pending' AND provider_type = 'bank'");
                if($bankPendingLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-file-blank', 'text' => "{$bankPendingLoans->count} bank loan application(s) pending", 'color' => 'text-danger'];
                }
                
                // 2. High-value loan applications
                $highValueLoans = $app->select_one("SELECT COUNT(*) as count FROM loan_applications WHERE amount_requested > 200000 AND provider_type = 'bank' AND status IN ('pending', 'under_review')");
                if($highValueLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-diamond', 'text' => "{$highValueLoans->count} high-value loan(s) require attention", 'color' => 'text-warning'];
                }
                
                // 3. Recently disbursed loans
                $disbursedLoans = $app->select_one("SELECT COUNT(*) as count FROM approved_loans al
                                                  INNER JOIN loan_applications la ON al.loan_application_id = la.id
                                                  WHERE la.provider_type = 'bank' AND al.status = 'active' 
                                                  AND DATE(al.disbursement_date) >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)");
                if($disbursedLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-transfer-alt', 'text' => "{$disbursedLoans->count} loan(s) disbursed recently", 'color' => 'text-info'];
                }
                
                // 4. Loan repayments due
                $repaymentsDue = $app->select_one("SELECT COUNT(*) as count FROM approved_loans al
                                                 INNER JOIN loan_applications la ON al.loan_application_id = la.id
                                                 WHERE la.provider_type = 'bank' AND al.status = 'active' 
                                                 AND al.remaining_balance > 0");
                if($repaymentsDue->count > 0) {
                    $notifications[] = ['icon' => 'bx-calendar', 'text' => "{$repaymentsDue->count} active loan(s) with outstanding balance", 'color' => 'text-primary'];
                }
                
                // 5. Account transactions
                $bankTransactions = $app->select_one("SELECT COUNT(*) as count FROM bank_account_transactions WHERE DATE(created_at) = CURDATE()");
                if($bankTransactions->count > 0) {
                    $notifications[] = ['icon' => 'bx-receipt', 'text' => "{$bankTransactions->count} bank transaction(s) today", 'color' => 'text-success'];
                }
                break;
                
            case 4: // Agrovet Staff
                // 1. Pending input credit applications
                $pendingCredits = $app->select_one("SELECT COUNT(*) as count FROM input_credit_applications WHERE status = 'pending'");
                if($pendingCredits->count > 0) {
                    $notifications[] = ['icon' => 'bx-package', 'text' => "{$pendingCredits->count} input credit application(s) pending", 'color' => 'text-danger'];
                }
                
                // 2. Under review applications
                $reviewCredits = $app->select_one("SELECT COUNT(*) as count FROM input_credit_applications WHERE status = 'under_review'");
                if($reviewCredits->count > 0) {
                    $notifications[] = ['icon' => 'bx-search-alt', 'text' => "{$reviewCredits->count} application(s) under review", 'color' => 'text-warning'];
                }
                
                // 3. Approved credits awaiting fulfillment
                $approvedCredits = $app->select_one("SELECT COUNT(*) as count FROM approved_input_credits WHERE status = 'pending_fulfillment'");
                if($approvedCredits->count > 0) {
                    $notifications[] = ['icon' => 'bx-check-double', 'text' => "{$approvedCredits->count} approved credit(s) awaiting fulfillment", 'color' => 'text-info'];
                }
                
                // 4. Active input credits
                $activeCredits = $app->select_one("SELECT COUNT(*) as count FROM approved_input_credits WHERE status = 'active'");
                if($activeCredits->count > 0) {
                    $notifications[] = ['icon' => 'bx-time-five', 'text' => "{$activeCredits->count} active input credit(s)", 'color' => 'text-primary'];
                }
                
                // 5. Recent repayments
                $creditRepayments = $app->select_one("SELECT COUNT(*) as count FROM input_credit_repayments WHERE DATE(deduction_date) = CURDATE()");
                if($creditRepayments->count > 0) {
                    $notifications[] = ['icon' => 'bx-wallet', 'text' => "{$creditRepayments->count} credit repayment(s) received today", 'color' => 'text-success'];
                }
                
                // 6. Inventory status
                $inventoryItems = $app->select_one("SELECT COUNT(*) as count FROM input_catalog WHERE is_active = 1");
                if($inventoryItems->count > 0) {
                    $notifications[] = ['icon' => 'bx-store', 'text' => "{$inventoryItems->count} active inventory item(s)", 'color' => 'text-secondary'];
                }
                break;
                
            case 5: // Admin
                // 1. Total system users
                $totalUsers = $app->select_one("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
                if($totalUsers->count > 0) {
                    $notifications[] = ['icon' => 'bx-users', 'text' => "{$totalUsers->count} active system users", 'color' => 'text-primary'];
                }
                
                // 2. Recent user registrations
                $newUsers = $app->select_one("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
                if($newUsers->count > 0) {
                    $notifications[] = ['icon' => 'bx-user-plus', 'text' => "{$newUsers->count} new user(s) this week", 'color' => 'text-success'];
                }
                
                // 3. System activity today
                $todayActivity = $app->select_one("SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) = CURDATE()");
                if($todayActivity->count > 0) {
                    $notifications[] = ['icon' => 'bx-line-chart', 'text' => "{$todayActivity->count} system activity(ies) today", 'color' => 'text-info'];
                }
                
                // 4. Pending verifications
                $pendingVerifications = $app->select_one("SELECT COUNT(*) as count FROM farmers WHERE is_verified = 0");
                if($pendingVerifications->count > 0) {
                    $notifications[] = ['icon' => 'bx-shield-quarter', 'text' => "{$pendingVerifications->count} farmer(s) awaiting verification", 'color' => 'text-warning'];
                }
                
                // 5. Total loan portfolio
                $totalLoans = $app->select_one("SELECT COUNT(*) as count FROM approved_loans WHERE status IN ('active', 'completed')");
                if($totalLoans->count > 0) {
                    $notifications[] = ['icon' => 'bx-money', 'text' => "{$totalLoans->count} total loan(s) in portfolio", 'color' => 'text-secondary'];
                }
                
                // 6. System health
                $recentErrors = $app->select_one("SELECT COUNT(*) as count FROM audit_logs WHERE DATE(created_at) = CURDATE()");
                if($recentErrors->count > 0) {
                    $notifications[] = ['icon' => 'bx-pulse', 'text' => "{$recentErrors->count} audit log(s) today", 'color' => 'text-dark'];
                }
                break;
        }
        
                 // Limit to 7 notifications maximum
                 $notifications = array_slice($notifications, 0, 7);
                 $totalNotifications = count($notifications);
                 ?>
                    <span class="badge rounded-pill header-icon-badge" style="background-color: #BA8448!important;">
                        <?php echo $totalNotifications; ?>
                    </span>
                </a>

                <!-- Notification Dropdown -->
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                    style="width: 320px;">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Notifications</h6>
                            <span class="badge bg-secondary"><?php echo $totalNotifications; ?> Active</span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                        <?php if(empty($notifications)): ?>
                        <div class="dropdown-item p-3 text-center">
                            <i class="bx bx-bell-off fs-24 text-muted mb-2"></i>
                            <p class="mb-0 text-muted">No new notifications</p>
                        </div>
                        <?php else: ?>
                        <?php foreach($notifications as $notification): ?>
                        <div class="dropdown-item p-3 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i
                                        class="bx <?php echo $notification['icon']; ?> fs-18 <?php echo $notification['color']; ?>"></i>
                                </div>
                                <div class="flex-fill">
                                    <p class="mb-0 fs-13 <?php echo $notification['color']; ?>">
                                        <?php echo $notification['text']; ?></p>
                                    <small class="text-muted">Just now</small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($notifications)): ?>
                    <div class="dropdown-divider"></div>
                    <div class="p-2 text-center">
                        <small class="text-muted">All notifications are informational only</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Section -->
            <div class="header-element">
                <a href="javascript:void(0);" class="header-link dropdown-toggle" id="profileDropdown"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="me-sm-2 me-0">
                            <img src="http://localhost/dfcs/assets/images/faces/face-image-1.jpg" alt="profile"
                                class="rounded-circle" width="32" height="32" />
                        </div>
                        <div class="d-sm-block d-none">
                            <?php
                            $userId = $_SESSION['user_id'];
                            
                            // Get user name directly from users table since that's where first_name and last_name are stored
                            $query = "SELECT first_name, last_name FROM users WHERE id = $userId";
                            $user = $app->select_one($query);
                            
                            if($user) {
                                echo "<p class='fw-semibold mb-0 lh-1'>{$user->first_name} {$user->last_name}</p>";
                            }
                            
                            // Role display
                            $roleText = match($roleId) {
                                1 => "Farmer",
                                2 => "SACCO Staff", 
                                3 => "Bank Staff",
                                4 => "Agrovet Staff",
                                5 => "Admin",
                                default => "User"
                            };
                            echo "<span class='op-7 fw-normal d-block fs-11'>$roleText</span>";
                            ?>
                        </div>
                    </div>
                </a>

                <!-- Profile Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li>
                        <?php
                              $roleId = $_SESSION['role_id'];
                              
                              // Define profile URLs based on role
                              $profileUrls = [
                                  1 => 'http://localhost/dfcs/farmers/profile/farmer-profile', 
                                  2 => 'http://localhost/dfcs/sacco/profile/',  
                                  3 => 'http://localhost/dfcs/bank-profile',    
                                  4 => 'http://localhost/dfcs/agrovet-profile', 
                                  5 => 'http://localhost/dfcs/admin/profile/view-profile'          
                              ];
                      
                              // Get correct profile URL based on role
                              $profileUrl = $profileUrls[$roleId] ?? 'http://localhost/dfcs/profile';
                              
                              // Define role-specific profile icons to ge the icons 
                              $profileIcons = [
                                  1 => 'ti ti-tractor',     
                                  2 => 'ti ti-building',    
                                  3 => 'ti ti-building-bank', 
                                  4 => 'ti ti-shopping-cart', 
                                  5 => 'ti ti-user-circle'    
                              ];
                      
                              // Get correct icon based on role
                              $icon = $profileIcons[$roleId] ?? 'ti ti-user-circle';
                              ?>

                        <a class="dropdown-item d-flex" href="<?php echo $profileUrl; ?>">
                            <i class="<?php echo $icon; ?> fs-18 me-2 op-7"></i>
                            <?php
                                  // Display role-specific profile text
                                  $profileText = match($roleId) {
                                      1 => "Farmer Profile",
                                      2 => "SACCO Profile",
                                      3 => "Bank Staff Profile",
                                      4 => "Agrovet Profile",
                                      5 => "Admin Profile",
                                      default => "Profile"
                                  };
                                  echo $profileText;
                                  ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex" href="http://localhost/dfcs/authentication/logout">
                            <i class="ti ti-logout fs-18 me-2 op-7"></i>Log Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>