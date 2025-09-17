<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    header("Location: http://localhost/dfcs/");
    exit();
}

$roleId = $_SESSION['role_id'];
$userId = $_SESSION['user_id'];
?>

<aside class="app-sidebar sticky" id="sidebar" style="background-color: #6AA32D!important; color: #fff!important;">
    <!-- Main sidebar header -->
    <div class="main-sidebar-header" style="background-color:#6AA32D!important;">
        <a href="http://localhost/dfcs/dashboard" class="header-logo">
            <img src="http://localhost/dfcs/assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
            <img src="http://localhost/dfcs/assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo">
            <img src="http://localhost/dfcs/assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
            <img src="http://localhost/dfcs/assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark">
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu" style="color: white!important;">

                <?php if($roleId == 5): // ADMIN MODULES ?>

                <!-- Admin Dashboard -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-dashboard side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/dashboard/system" class="side-menu__item">System
                                Overview</a>
                        </li>

                    </ul>
                </li>

                <!-- User Management -->
                <li class="slide__category"><span class="category-name">User Management</span></li>

                <!-- Farmers Management -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-tractor side-menu__icon"></i>
                        <span class="side-menu__label">Manage Farmers</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/farmers/view-data" class="side-menu__item">View All
                                Farmers</a>
                        </li>

                    </ul>
                </li>

                <!-- sacco  Management -->
                <li class="slide__category"><span class="category-name">Sacco Management</span></li>

                <!-- SACCO Staff Management -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-building side-menu__icon"></i>
                        <span class="side-menu__label">Manage SACCO Staff</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/sacco/view-data" class="side-menu__item">View Staff</a>
                        </li>
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/sacco/add-data" class="side-menu__item">Add Staff</a>
                        </li>
                    </ul>
                </li>

                <!-- sacco  Management -->
                <li class="slide__category"><span class="category-name">Bank Management</span></li>
                <!-- Bank Staff Management -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-building-bank side-menu__icon"></i>
                        <span class="side-menu__label">Manage Bank Staff</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/bank-staff/view-data" class="side-menu__item">View
                                Staff</a>
                        </li>
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/bank-staff/add-data" class="side-menu__item">Add
                                Staff</a>
                        </li>
                    </ul>
                </li>
                <!-- agrovet -->
                <li class="slide__category"><span class="category-name">Agrovet Management</span></li>

                <!-- Agrovet Staff Management -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-shopping-cart side-menu__icon"></i>
                        <span class="side-menu__label">Manage Agrovet Staff</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/agrovet-staff/view-data" class="side-menu__item">View
                                Staff</a>
                        </li>
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/agrovet-staff/add-data" class="side-menu__item">Add
                                Staff</a>
                        </li>
                    </ul>
                </li>
                <li class="slide__category"><span class="category-name">System Management</span></li>

                <!-- Bank Management -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-building-bank side-menu__icon"></i>
                        <span class="side-menu__label">Banks</span>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/admin/banks/view-data">View
                                Banks</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/admin/banks/add-data">Add
                                Bank</a></li>
                    </ul>
                </li>

                <!-- Agrovet Management -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-shopping-cart side-menu__icon"></i>
                        <span class="side-menu__label">Agrovets</span>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/admin/agrovets/view-data">View Agrovets</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/admin/agrovets/add-data">Add
                                Agrovet</a></li>
                    </ul>
                </li>

                <!-- Admin Profile -->
                <li class="slide__category"><span class="category-name">Account</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-user-circle side-menu__icon"></i>
                        <span class="side-menu__label">my Profile</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="http://localhost/dfcs/admin/profile/view-profile" class="side-menu__item">View
                                Profile</a>
                        </li>

                    </ul>
                </li>
                <!-- farmer  -->
                <?php elseif($roleId == 1): ?>
                <li class="slide__category"><span class="category-name">Main</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-dashboard side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="http://localhost/dfcs/farmers/dashboard/overview" class="side-menu__item">My
                                Overview</a>
                        </li>

                    </ul>
                </li>
                <li class="slide__category"><span class="category-name">Farm Management</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-tractor side-menu__icon"></i>
                        <span class="side-menu__label">My Farms</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/farms/view-data">View
                                Farms</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/farms/add-data">Add
                                Farm</a></li>
                    </ul>
                </li>

                <!-- Loan Management -->
                <!-- Financial Management -->
                <li class="slide__category"><span class="category-name">Financial</span></li>

                <!-- SACCO Loans (existing structure) -->
                <li class="slide has-sub open">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-building-bank side-menu__icon"></i>
                        <span class="side-menu__label">SACCO Loans</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/loans/apply">Apply for Loan</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/loans/applications">My Loan Applications</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/loans/active">Active Loans</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/loans/history">Loan History</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/loans/schedule">Repayment Schedule</a></li>
                    </ul>
                </li>

                <!-- Bank Loans (new structure with new URLs) -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-coin side-menu__icon"></i>
                        <span class="side-menu__label">Bank Loans</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/bank-loans/apply">Apply for Bank Loan</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/bank-loans/applications">My Bank Applications</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/bank-loans/active">Active Bank Loans</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/bank-loans/history">Bank Loan History</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/bank-loans/schedule">Bank Repayment Schedule</a>
                        </li>
                    </ul>
                </li>

                <!-- Input Credits -->
                <li class="slide__category"><span class="category-name">Inputs</span></li>
                <li class="slide has-sub open">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-shopping-cart side-menu__icon"></i>
                        <span class="side-menu__label">Input Credits</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/credits/apply">New Application</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/credits/applications">Applications</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/credits/active">Active Credits</a></li>
                    </ul>
                </li>

                <!-- Produce Management -->
                <li class="slide__category"><span class="category-name">Produce Management</span></li>
                <li class="slide has-sub open">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-truck-delivery side-menu__icon"></i>
                        <span class="side-menu__label">Produce</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">

                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/produce/history">Delivery History</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/produce/sales">Sales Records</a></li>
                    </ul>
                </li>
                <!-- Profile Management -->
                <li class="slide__category"><span class="category-name">Account</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-user-circle side-menu__icon"></i>
                        <span class="side-menu__label">Profile</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/farmers/profile/farmer-profile">View
                                Profile</a></li>

                    </ul>
                </li>
                <!-- // SACCO STAFF MODULES -->
                <?php elseif($roleId == 2):  ?>
                <!-- Dashboard -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-dashboard side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/dashboard/system">SACCO
                                Overview</a></li>
                    </ul>
                </li>

                <!-- Farmer Management -->
                <li class="slide__category"><span class="category-name">Farmers</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-users side-menu__icon"></i>
                        <span class="side-menu__label">Farmer Management</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/farmers/view">View All Farmers</a></li>

                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/farmers/reports">Farmer Reports</a></li>
                    </ul>
                </li>

                <!-- Produce Management -->
                <li class="slide__category"><span class="category-name">Produce</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-truck-delivery side-menu__icon"></i>
                        <span class="side-menu__label">Produce Management</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/produce/record">Record Deliveries</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/produce/produce-deliveries">Sold Produce</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/produce/process">Process Sales</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/produce/reports">Delivery Reports</a>
                        </li>
                    </ul>
                </li>

                <!-- Loan Processing -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-coin side-menu__icon"></i>
                        <span class="side-menu__label">Loan Processing</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/loans/pending">New Applications</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/loans/active">Active Loans</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/loans/history">Loan History</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/loans/payments">Payment Records</a></li>
                    </ul>
                </li>

                <!-- SACCO Reports -->
                <li class="slide__category"><span class="category-name">Reports</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-report side-menu__icon"></i>
                        <span class="side-menu__label">Reports</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/reports/financial">Financial Reports</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/reports/operations">Operation Reports</a>
                        </li>

                    </ul>
                </li>
                <!-- Accounts -->
                <li class="slide__category"><span class="category-name">Accounts</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-wallet side-menu__icon"></i>
                        <span class="side-menu__label">Accounts</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/sacco/account/overview">Account
                                Overview</a></li>

                    </ul>
                </li>

                <!-- SACCO Profile -->
                <li class="slide__category"><span class="category-name">Account</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-user-circle side-menu__icon"></i>
                        <span class="side-menu__label">Profile</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item" href="http://localhost/dfcs/sacco/profile">View
                                Profile</a></li>
                    </ul>
                </li>
                <?php elseif($roleId == 3): // BANK STAFF MODULES ?>
                <!-- Bank Dashboard -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-dashboard side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/dashboard/overview">
                                Overview</a></li>

                    </ul>
                </li>
                <!-- Farmer Management -->
                <li class="slide__category"><span class="category-name">Farmers</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-users side-menu__icon"></i>
                        <span class="side-menu__label">Farmer Management</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item" href="http://localhost/dfcs/bank/farmers/view">View
                                All Farmers</a></li>

                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/farmers/reports">Farmer Reports</a></li>
                    </ul>
                </li>

                <!-- Payment Management -->
                <li class="slide__category"><span class="category-name">Payments</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-cash side-menu__icon"></i>
                        <span class="side-menu__label">Payment Management</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">

                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/payments/process">Process Payments</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/payments/deductions">Manage Deductions</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/payments/history">Payment History</a></li>
                    </ul>
                </li>

                <!-- Loan Management -->
                <li class="slide__category"><span class="category-name">Loans</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-coin side-menu__icon"></i>
                        <span class="side-menu__label">Loan Management</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/loans/applications">New Applications</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/loans/active">Active Loans</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/loans/history">Loans History</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/loans/repayments">Loan Repayments</a></li>
                    </ul>
                </li>

                <!-- Input Credit Management -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-shopping-cart side-menu__icon"></i>
                        <span class="side-menu__label">Input Credits</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/credits/active">Active Credits</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/credits/deductions">Credit Deductions</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/credits/settlements">Agrovet
                                Settlements</a></li>
                    </ul>
                </li>

                <!-- Reports -->
                <li class="slide__category"><span class="category-name">Reports</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-report side-menu__icon"></i>
                        <span class="side-menu__label">Reports</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/reports/transactions">Transaction
                                Reports</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/reports/loans">Loan Reports</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/reports/credits">Credit Reports</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/bank/reports/reconciliation">Payment
                                Reconciliation</a></li>
                    </ul>
                </li>

                <!-- Bank Staff Profile -->
                <li class="slide__category"><span class="category-name">Account</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-user-circle side-menu__icon"></i>
                        <span class="side-menu__label">Profile</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item" href="http://localhost/dfcs/bank/profile">View
                                Profile</a></li>

                    </ul>
                </li>
                <?php elseif($roleId == 4): // AGROVET STAFF MODULES ?>
                <!-- Dashboard -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-dashboard side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/dashboard/overview">Perfomance</a></li>

                    </ul>
                </li>

                <!-- Input Credit Management -->
                <li class="slide__category"><span class="category-name">Credit Management</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-coin side-menu__icon"></i>
                        <span class="side-menu__label">Input Credits</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/credits/new">New Applications</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/credits/active">Active Credits</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/credits/history">Credit History</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/credits/payments">Payment Records</a>
                        </li>
                    </ul>
                </li>

                <!-- Input Catalog -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-plant side-menu__icon"></i>
                        <span class="side-menu__label">Input Catalog</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/catalog/browse">Browse Catalog</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/catalog/analysis">Input Movement Analysis</a></li>
                    </ul>
                </li>
                <!-- Farmer Records -->
                <li class="slide__category"><span class="category-name">Farmers</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-users side-menu__icon"></i>
                        <span class="side-menu__label">Farmer Records</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/farmers/view">View Farmers</a></li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/farmers/reports">Farmer reports</a></li>

                    </ul>
                </li>

                <!-- Reports -->
                <!-- Reports -->
                <li class="slide__category"><span class="category-name">Reports</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-report side-menu__icon"></i>
                        <span class="side-menu__label">Reports</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/reports/applications">Input Credit Applications</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/reports/repayments">Credit Repayment Tracking</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/reports/revenue">Commission & Interest Revenue</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/reports/farmers">Farmer Credit Analysis</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/reports/staff">Staff Performance Metrics</a>
                        </li>
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/reports/input-types">Input Type Distribution</a>
                        </li>
                    </ul>
                </li>
                <li class="slide__category"><span class="category-name">Accounts</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-wallet side-menu__icon"></i>
                        <span class="side-menu__label">Accounts</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item"
                                href="http://localhost/dfcs/agrovet/account/overview">Account
                                Overview</a></li>

                    </ul>
                </li>

                <!-- Profile -->
                <li class="slide__category"><span class="category-name">Account</span></li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-user-circle side-menu__icon"></i>
                        <span class="side-menu__label">Profile</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a class="side-menu__item" href="http://localhost/dfcs/agrovet/profile">View
                                Profile</a></li>

                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg>
            </div>
        </nav>
    </div>
</aside>