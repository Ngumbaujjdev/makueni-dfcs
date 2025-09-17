<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayRecentTransactions'])):
    $app = new App;
    
    $query = "SELECT 
                sat.id,
                sat.transaction_type,
                sat.amount,
                sat.reference_id,
                sat.description,
                sat.processed_by,
                sat.created_at,
                CONCAT(u.first_name, ' ', u.last_name) as staff_name
              FROM sacco_account_transactions sat 
              LEFT JOIN users u ON sat.processed_by = u.id
              ORDER BY sat.created_at DESC
              LIMIT 10";
    
    $recentTransactions = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-exchange-dollar-line me-2"></i> Recent SACCO Account Transactions
        </div>
        <div>
            <a href="sacco-transactions" class="btn btn-sm btn-outline-primary">
                <i class="ri-eye-line me-1"></i> View All
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-recent-transactions" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-calendar-line me-1"></i>Date & Time</th>
                        <th><i class="ri-exchange-line me-1"></i>Type</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Amount</th>
                        <th><i class="ri-file-list-3-line me-1"></i>Reference</th>
                        <th><i class="ri-information-line me-1"></i>Description</th>
                        <th><i class="ri-user-line me-1"></i>Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentTransactions): ?>
                    <?php foreach ($recentTransactions as $transaction): ?>
                    <tr
                        class="<?php echo $transaction->transaction_type == 'credit' ? 'table-success-light' : 'table-danger-light'; ?>">
                        <td>
                            <div class="d-flex flex-column">
                                <span
                                    class="fw-semibold"><?php echo date('M d, Y', strtotime($transaction->created_at)) ?></span>
                                <small
                                    class="text-muted"><?php echo date('h:i A', strtotime($transaction->created_at)) ?></small>
                            </div>
                        </td>
                        <td>
                            <?php if($transaction->transaction_type == 'credit'): ?>
                            <span class="badge bg-success-transparent">
                                <i class="ri-arrow-down-line me-1"></i>Credit
                            </span>
                            <?php else: ?>
                            <span class="badge bg-danger-transparent">
                                <i class="ri-arrow-up-line me-1"></i>Debit
                            </span>
                            <?php endif; ?>
                        </td>
                        <td
                            class="fw-semibold <?php echo $transaction->transaction_type == 'credit' ? 'text-success' : 'text-danger'; ?>">
                            <?php echo $transaction->transaction_type == 'credit' ? '+' : '-'; ?>
                            KES <?php echo number_format($transaction->amount, 2) ?>
                        </td>
                        <td class="text-muted">
                            <?php if($transaction->reference_id): ?>
                            <a href="#" class="text-decoration-underline" data-bs-toggle="tooltip"
                                title="View Reference Details"
                                onclick="viewReferenceDetails(<?php echo $transaction->reference_id; ?>)">
                                REF<?php echo str_pad($transaction->reference_id, 6, '0', STR_PAD_LEFT); ?>
                            </a>
                            <?php else: ?>
                            -
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($transaction->description) ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 bg-primary">
                                    <?php echo strtoupper(substr($transaction->staff_name, 0, 1)); ?>
                                </span>
                                <?php echo htmlspecialchars($transaction->staff_name) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2 d-block"></i>
                            <p class="mb-0">No recent transactions found</p>
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
    $('#datatable-recent-transactions').DataTable({
        responsive: true,
        order: [
            [0, 'desc']
        ], // Sort by date descending
        language: {
            searchPlaceholder: 'Search transactions...',
            sSearch: '',
        },
        dom: 'Bfrtip', // Buttons for export
        buttons: [{
                extend: 'excel',
                text: '<i class="ri-file-excel-2-line me-1"></i> Excel',
                className: 'btn btn-sm btn-success me-1',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="ri-file-pdf-line me-1"></i> PDF',
                className: 'btn btn-sm btn-danger me-1',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'print',
                text: '<i class="ri-printer-line me-1"></i> Print',
                className: 'btn btn-sm btn-primary',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            }
        ],
        lengthMenu: [
            [5, 10, 25, -1],
            [5, 10, 25, "All"]
        ],
        pageLength: 5
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Function to view reference details (you can implement this based on reference type)
function viewReferenceDetails(referenceId) {
    // This is a placeholder - implement based on your requirements
    // For example, you might show details in a modal or redirect to a details page
    alert('Viewing reference ID: ' + referenceId);
    // Example: window.location.href = "transaction-reference?id=" + referenceId;
}
</script>
<?php endif; ?>