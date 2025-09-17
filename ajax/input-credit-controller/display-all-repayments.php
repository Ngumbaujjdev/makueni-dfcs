<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayAllRepayments'])):
    $app = new App;
    
    // Get session user_id to identify agrovet staff
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    
    // Get staff agrovet_id
    $staffQuery = "SELECT s.id as staff_id, s.agrovet_id 
                  FROM agrovet_staff s 
                  WHERE s.user_id = :user_id";
    
    $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
    
    if (!$staff) {
        echo json_encode(['success' => false, 'message' => 'Staff not found']);
        exit;
    }
    
    // Query to get all input credit repayments with related data for this agrovet
    $query = "SELECT 
                icr.id,
                icr.approved_credit_id,
                icr.produce_delivery_id,
                icr.amount,
                icr.deduction_date as payment_date,
                'produce_deduction' as payment_method,
                icr.notes,
                aic.credit_application_id,
                aic.approved_amount,
                aic.remaining_balance,
                ica.farmer_id,
                ica.agrovet_id,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg,
                CONCAT('ICRED', LPAD(ica.id, 5, '0')) as credit_reference,
                a.name as agrovet_name
              FROM input_credit_repayments icr
              JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              JOIN farmers fm ON ica.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              WHERE ica.agrovet_id = '{$staff->agrovet_id}'
              ORDER BY icr.deduction_date DESC";
    
    $repayments = $app->select_all($query);
?>
<div id="repaymentsSection">
    <div class="table-responsive">
        <table id="datatable-repayments" class="table table-bordered text-nowrap w-100">
            <thead>
                <tr>
                    <th><i class="ri-hash-line me-1"></i>ID</th>
                    <th><i class="ri-file-list-line me-1"></i>Credit Ref</th>
                    <th><i class="ri-user-line me-1"></i>Farmer</th>
                    <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)</th>
                    <th><i class="ri-calendar-line me-1"></i>Date</th>
                    <th><i class="ri-bank-line me-1"></i>Method</th>
                    <th><i class="ri-settings-3-line me-1"></i>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($repayments): ?>
                <?php foreach ($repayments as $repayment): ?>
                <tr>
                    <td class="fw-semibold">REP<?php echo str_pad($repayment->id, 5, '0', STR_PAD_LEFT); ?></td>
                    <td>
                        <span class="badge bg-primary-transparent text-primary">
                            <?php echo htmlspecialchars($repayment->credit_reference) ?>
                        </span>
                        <small
                            class="d-block text-muted"><?php echo htmlspecialchars($repayment->agrovet_name) ?></small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm bg-success me-2">
                                <?php echo strtoupper(substr($repayment->farmer_name, 0, 1)); ?>
                            </span>
                            <span class="fw-medium">
                                <?php echo htmlspecialchars($repayment->farmer_name) ?>
                            </span>
                        </div>
                    </td>
                    <td class="fw-semibold text-success">
                        KES <?php echo number_format($repayment->amount, 2) ?>
                    </td>
                    <td>
                        <?php echo date('M d, Y', strtotime($repayment->payment_date)) ?>
                    </td>
                    <td>
                        <?php
                        $badgeClass = '';
                        $methodName = '';
                        
                        switch($repayment->payment_method) {
                            case 'produce_deduction':
                                $badgeClass = 'bg-success-transparent text-success';
                                $methodName = 'Produce Deduction';
                                break;
                            case 'cash':
                                $badgeClass = 'bg-info-transparent text-info';
                                $methodName = 'Cash';
                                break;
                            case 'bank_transfer':
                                $badgeClass = 'bg-primary-transparent text-primary';
                                $methodName = 'Bank Transfer';
                                break;
                            case 'mobile_money':
                                $badgeClass = 'bg-warning-transparent text-warning';
                                $methodName = 'Mobile Money';
                                break;
                            default:
                                $badgeClass = 'bg-secondary-transparent text-secondary';
                                $methodName = ucfirst(str_replace('_', ' ', $repayment->payment_method));
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass ?>">
                            <?php echo $methodName ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary" title="View Details"
                                onclick="viewCreditRepaymentDetails(<?php echo $repayment->id ?>)">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn btn-sm btn-success" title="Print Receipt"
                                onclick="printCreditRepaymentReceipt(<?php echo $repayment->id ?>)">
                                <i class="ri-printer-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="ri-information-line fs-2 text-muted mb-2"></i>
                        <p>No input credit repayments found</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
    var table = $('#datatable-repayments').DataTable({
        responsive: true,
        order: [
            [4, 'desc']
        ], // Sort by date column
        language: {
            searchPlaceholder: 'Search repayments...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: 'Bfrtip',
        buttons: [{
            extend: 'collection',
            text: '<i class="ri-download-2-line me-1"></i> Export',
            buttons: [
                'copy',
                'excel',
                'csv',
                'pdf',
                'print'
            ]
        }]
    });

    // Filter buttons
    $('#btnShowAllRepayments').click(function() {
        table.search('').draw();
    });

    $('#btnShowThisMonth').click(function() {
        // Get current month name
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
            'Dec'
        ];
        const now = new Date();
        const monthName = months[now.getMonth()];

        // Apply search filter
        table.search(monthName + ' ' + now.getFullYear()).draw();
    });

    $('#btnShowLastMonth').click(function() {
        // Get last month name
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
            'Dec'
        ];
        const now = new Date();
        now.setMonth(now.getMonth() - 1);
        const monthName = months[now.getMonth()];

        // Apply search filter
        table.search(monthName + ' ' + now.getFullYear()).draw();
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
<?php endif; ?>