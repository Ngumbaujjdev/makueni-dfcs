<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayPaymentHistory'])):
    $app = new App;
    
    // Query to get payment history from all transaction tables
    $query = "SELECT 
                'farmer_payment' as payment_type,
                fat.id as transaction_id,
                fat.reference_id,
                fat.description,
                fat.amount,
                fat.created_at,
                CONCAT(u.first_name, ' ', u.last_name) as recipient_name,
                CONCAT('FARM', LPAD(f.id, 6, '0')) as recipient_ref,
                'Credit to Farmer' as transaction_details,
                'success' as status,
                fat.processed_by as processor_id,
                CONCAT(pu.first_name, ' ', pu.last_name) as processor_name
                FROM farmer_account_transactions fat
                JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                JOIN farmers f ON fa.farmer_id = f.id
                JOIN users u ON f.user_id = u.id
                LEFT JOIN users pu ON fat.processed_by = pu.id
                WHERE fat.transaction_type = 'credit'
                
                UNION ALL
                
                SELECT 
                'agrovet_payment' as payment_type,
                aat.id as transaction_id,
                aat.reference_id,
                aat.description,
                aat.amount,
                aat.created_at,
                a.name as recipient_name,
                CONCAT('AGV', LPAD(a.id, 4, '0')) as recipient_ref,
                'Credit to Agrovet' as transaction_details,
                'success' as status,
                aat.processed_by as processor_id,
                CONCAT(pu.first_name, ' ', pu.last_name) as processor_name
                FROM agrovet_account_transactions aat
                JOIN agrovet_accounts aa ON aat.agrovet_account_id = aa.id
                JOIN agrovets a ON aa.agrovet_id = a.id
                LEFT JOIN users pu ON aat.processed_by = pu.id
                WHERE aat.transaction_type = 'credit'
                
                UNION ALL
                
                SELECT 
                'bank_payment' as payment_type,
                bat.id as transaction_id,
                bat.reference_id,
                bat.description,
                bat.amount,
                bat.created_at,
                CASE 
                    WHEN bat.description LIKE '%to farmer%' THEN 'Farmer Payment'
                    WHEN bat.description LIKE '%to agrovet%' THEN 'Agrovet Payment'
                    WHEN bat.description LIKE '%input credit%' THEN 'Input Credit Payment'
                    WHEN bat.description LIKE '%loan%' THEN 'Loan Payment'
                    ELSE 'Other Payment'
                END as recipient_name,
                '' as recipient_ref,
                bat.description as transaction_details,
                'success' as status,
                bat.processed_by as processor_id,
                CONCAT(pu.first_name, ' ', pu.last_name) as processor_name
                FROM bank_account_transactions bat
                LEFT JOIN users pu ON bat.processed_by = pu.id
                WHERE bat.transaction_type = 'debit'
                
                ORDER BY created_at DESC";

    $payment_history = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between" style="background-color: #6AA32D; color: white;">
        <div class="card-title">
            <i class="fa-solid fa-money-bill-transfer me-2"></i> Bank Payment History
        </div>
        <div>
            <button class="btn btn-sm btn-light" onclick="loadPaymentHistory()">
                <i class="fa-solid fa-sync-alt me-1"></i> Refresh Data
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-payment-history" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Date</th>
                        <th>Recipient</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($payment_history && count($payment_history) > 0): ?>
                    <?php foreach ($payment_history as $payment): ?>
                    <?php
                    // Set icon and badge class based on payment type
                    switch($payment->payment_type) {
                        case 'farmer_payment':
                            $typeIcon = 'fa-tractor';
                            $badgeClass = 'bg-success';
                            $typeName = 'Farmer Payment';
                            break;
                        case 'agrovet_payment':
                            $typeIcon = 'fa-store';
                            $badgeClass = 'bg-primary';
                            $typeName = 'Agrovet Payment';
                            break;
                        case 'bank_payment':
                            if (strpos($payment->description, 'input credit') !== false) {
                                $typeIcon = 'fa-leaf';
                                $badgeClass = 'bg-danger';
                                $typeName = 'Input Credit';
                            } elseif (strpos($payment->description, 'loan') !== false) {
                                $typeIcon = 'fa-hand-holding-dollar';
                                $badgeClass = 'bg-info';
                                $typeName = 'Loan Payment';
                            } else {
                                $typeIcon = 'fa-money-bill-transfer';
                                $badgeClass = 'bg-secondary';
                                $typeName = 'Other Payment';
                            }
                            break;
                        default:
                            $typeIcon = 'fa-money-bill';
                            $badgeClass = 'bg-secondary';
                            $typeName = 'Other';
                    }
                    
                    // Format reference ID
                    $reference = '';
                    switch($payment->payment_type) {
                        case 'farmer_payment':
                            $reference = 'DLVR' . str_pad($payment->reference_id, 5, '0', STR_PAD_LEFT);
                            break;
                        case 'agrovet_payment':
                            $reference = 'AGVT' . str_pad($payment->reference_id, 5, '0', STR_PAD_LEFT);
                            break;
                        case 'bank_payment':
                            if (strpos($payment->description, 'produce sale') !== false) {
                                $reference = 'DLVR' . str_pad($payment->reference_id, 5, '0', STR_PAD_LEFT);
                            } elseif (strpos($payment->description, 'loan') !== false) {
                                $reference = 'LOAN' . str_pad($payment->reference_id, 5, '0', STR_PAD_LEFT);
                            } elseif (strpos($payment->description, 'input credit') !== false) {
                                $reference = 'INPT' . str_pad($payment->reference_id, 5, '0', STR_PAD_LEFT);
                            } else {
                                $reference = 'TXN' . str_pad($payment->transaction_id, 6, '0', STR_PAD_LEFT);
                            }
                            break;
                    }
                    
                    // Format date
                    $payment_date = date('d M Y, h:i A', strtotime($payment->created_at));
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reference); ?></td>
                        <td><?php echo $payment_date; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded me-2" style="background-color:#6AA32D;">
                                    <i class="fa-solid <?php echo $typeIcon ?> text-white"></i>
                                </span>
                                <div>
                                    <span class="fw-medium d-block">
                                        <?php echo htmlspecialchars($payment->recipient_name) ?>
                                    </span>
                                    <?php if (!empty($payment->recipient_ref)): ?>
                                    <small
                                        class="text-muted"><?php echo htmlspecialchars($payment->recipient_ref) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?php echo $badgeClass ?>">
                                <i class="fa-solid <?php echo $typeIcon ?> me-1"></i>
                                <?php echo $typeName ?>
                            </span>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo htmlspecialchars($payment->description) ?></small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-coins" style="color: #6AA32D;"></i>
                                </span>
                                <span class="fw-semibold">KES <?php echo number_format($payment->amount, 2) ?></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-transparent text-success">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-user" style="color: #6AA32D;"></i>
                                </span>
                                <?php echo htmlspecialchars($payment->processor_name) ?>
                            </div>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="d-flex flex-column align-items-center py-4">
                                <i class="fa-solid fa-receipt fa-3x mb-3" style="color: #6AA32D;"></i>
                                <h5>No payment history found</h5>
                                <p class="text-muted">There are no payment transactions recorded in the system.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#datatable-payment-history').DataTable({
        responsive: true,
        order: [
            [1, 'desc']
        ], // Sort by date descending
        language: {
            searchPlaceholder: 'Search payments...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: 'Bfrtip',
        buttons: [{
            extend: 'collection',
            text: '<i class="fa-solid fa-download me-1"></i> Export',
            buttons: [{
                    extend: 'excel',
                    text: '<i class="fa-solid fa-file-excel me-1"></i> Excel',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa-solid fa-file-pdf me-1"></i> PDF',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print me-1"></i> Print',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                }
            ],
            className: 'btn btn-outline-primary me-3'
        }]
    });

    // Add custom filter controls for payment types
    $("<div class='d-flex gap-2 mb-3 ms-3'></div>")
        .append(
            "<button class='btn btn-sm btn-primary filter-btn' data-filter='all'><i class='fa-solid fa-filter me-1'></i> All</button>"
        )
        .append(
            "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='Farmer Payment'><i class='fa-solid fa-tractor me-1'></i> Farmer Payments</button>"
        )
        .append(
            "<button class='btn btn-sm btn-outline-primary filter-btn' data-filter='Agrovet Payment'><i class='fa-solid fa-store me-1'></i> Agrovet Payments</button>"
        )
        .append(
            "<button class='btn btn-sm btn-outline-danger filter-btn' data-filter='Input Credit'><i class='fa-solid fa-leaf me-1'></i> Input Credits</button>"
        )
        .append(
            "<button class='btn btn-sm btn-outline-info filter-btn' data-filter='Loan Payment'><i class='fa-solid fa-hand-holding-dollar me-1'></i> Loan Payments</button>"
        )
        .insertBefore('#datatable-payment-history_wrapper .dataTables_filter');

    // Add padding between buttons and table
    $('#datatable-payment-history_wrapper .dt-buttons').addClass('me-3');

    // Add filter functionality
    $('.filter-btn').on('click', function() {
        let filterValue = $(this).data('filter');
        let table = $('#datatable-payment-history').DataTable();

        $('.filter-btn').removeClass('btn-primary btn-success btn-danger btn-info')
            .addClass('btn-outline-primary btn-outline-success btn-outline-danger btn-outline-info');

        $(this).removeClass(function(index, css) {
            return (css.match(/(^|\s)btn-outline-\S+/g) || []).join(' ');
        });

        if ($(this).data('filter') === 'all') {
            $(this).addClass('btn-primary');
        } else if ($(this).data('filter') === 'Farmer Payment') {
            $(this).addClass('btn-success');
        } else if ($(this).data('filter') === 'Agrovet Payment') {
            $(this).addClass('btn-primary');
        } else if ($(this).data('filter') === 'Input Credit') {
            $(this).addClass('btn-danger');
        } else if ($(this).data('filter') === 'Loan Payment') {
            $(this).addClass('btn-info');
        }

        if (filterValue === 'all') {
            table.column(3).search('').draw();
        } else {
            table.column(3).search(filterValue).draw();
        }
    });
});
</script>
<?php endif; ?>