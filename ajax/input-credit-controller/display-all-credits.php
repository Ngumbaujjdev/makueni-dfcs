<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayAllCredits'])):
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
    
    // Query to get all input credit applications with related data for this agrovet
    $query = "SELECT 
                ica.id,
                ica.farmer_id,
                ica.agrovet_id,
                ica.total_amount,
                ica.credit_percentage,
                ica.total_with_interest,
                ica.repayment_percentage,
                ica.application_date,
                ica.creditworthiness_score,
                CASE 
                    WHEN ica.status = '' AND EXISTS (
                        SELECT 1 FROM approved_input_credits aic 
                        WHERE aic.credit_application_id = ica.id
                    ) THEN 
                        CASE 
                            WHEN (
                                SELECT aic.remaining_balance FROM approved_input_credits aic 
                                WHERE aic.credit_application_id = ica.id
                            ) = 0 THEN 'completed'
                            ELSE 'fulfilled'
                        END
                    WHEN ica.status = '' THEN 'pending'
                    ELSE ica.status
                END as status,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                f.registration_number as farmer_reg,
                a.name as agrovet_name,
                CASE 
                    WHEN ica.status = 'approved' OR ica.status = 'fulfilled' OR ica.status = 'completed' OR
                         (ica.status = '' AND EXISTS (SELECT 1 FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)) THEN 
                        (SELECT aic.approved_amount FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                    ELSE NULL
                END as approved_amount,
                CASE 
                    WHEN ica.status = 'approved' OR ica.status = 'fulfilled' OR ica.status = 'completed' OR
                         (ica.status = '' AND EXISTS (SELECT 1 FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)) THEN 
                        (SELECT aic.fulfillment_date FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                    ELSE NULL
                END as fulfillment_date,
                CASE 
                    WHEN ica.status = 'fulfilled' OR ica.status = 'completed' OR
                         (ica.status = '' AND EXISTS (SELECT 1 FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)) THEN 
                        (SELECT aic.remaining_balance FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                    ELSE NULL
                END as remaining_balance
              FROM input_credit_applications ica
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              WHERE ica.agrovet_id = '{$staff->agrovet_id}'
              ORDER BY ica.application_date DESC";
    
    $creditApplications = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-shopping-bag-line me-2"></i> All Input Credit Applications
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
            <button class="btn btn-outline-success btn-sm" id="btnShowApproved">Approved</button>
            <button class="btn btn-outline-danger btn-sm" id="btnShowRejected">Rejected</button>
            <button class="btn btn-outline-warning btn-sm" id="btnShowPending">Pending</button>
            <button class="btn btn-outline-info btn-sm" id="btnShowActive">Active</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-all-credits" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-hash-line me-1"></i>Reference</th>
                        <th><i class="ri-user-line me-1"></i>Farmer</th>
                        <th><i class="ri-store-line me-1"></i>Agrovet</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)</th>
                        <th><i class="ri-percent-line me-1"></i>Interest</th>
                        <th><i class="ri-bar-chart-line me-1"></i>Credit Score</th>
                        <th><i class="ri-time-line me-1"></i>Date</th>
                        <th><i class="ri-shield-check-line me-1"></i>Status</th>
                        <th><i class="ri-settings-3-line me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($creditApplications): ?>
                    <?php foreach ($creditApplications as $credit): ?>
                    <tr>
                        <td class="fw-semibold">ICRED<?php echo str_pad($credit->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-success me-2">
                                    <?php echo strtoupper(substr($credit->farmer_name, 0, 1)); ?>
                                </span>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($credit->farmer_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info-transparent text-info">
                                <?php echo htmlspecialchars($credit->agrovet_name) ?>
                            </span>
                        </td>
                        <td class="fw-semibold">
                            <?php if ($credit->approved_amount): ?>
                            <span class="text-success">KES
                                <?php echo number_format($credit->approved_amount, 2) ?></span>
                            <?php if ($credit->approved_amount != $credit->total_amount): ?>
                            <small class="d-block text-muted">Req: KES
                                <?php echo number_format($credit->total_amount, 2) ?></small>
                            <?php endif; ?>
                            <?php else: ?>
                            KES <?php echo number_format($credit->total_amount, 2) ?>
                            <?php endif; ?>
                            <?php if ($credit->remaining_balance !== null): ?>
                            <small
                                class="d-block text-<?php echo ($credit->remaining_balance > 0) ? 'danger' : 'success'; ?>">
                                Remaining: KES <?php echo number_format($credit->remaining_balance, 2) ?>
                            </small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $credit->credit_percentage ?>%
                            <small class="d-block text-muted">
                                <?php echo $credit->repayment_percentage ?>% repay
                            </small>
                        </td>
                        <td>
                            <span class="badge <?php 
                                if ($credit->creditworthiness_score >= 80) echo 'bg-success'; 
                                elseif ($credit->creditworthiness_score >= 60) echo 'bg-warning';
                                else echo 'bg-danger';
                            ?>">
                                <?php echo number_format($credit->creditworthiness_score, 1) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($credit->fulfillment_date): ?>
                            <span data-bs-toggle="tooltip" title="Fulfillment Date">
                                <?php echo date('M d, Y', strtotime($credit->fulfillment_date)) ?>
                            </span>
                            <small class="d-block text-muted">Applied:
                                <?php echo date('M d, Y', strtotime($credit->application_date)) ?></small>
                            <?php else: ?>
                            <?php echo date('M d, Y', strtotime($credit->application_date)) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php 
                                switch($credit->status) {
                                    case 'pending':
                                    case 'under_review':
                                        echo 'bg-warning';
                                        break;
                                    case 'approved':
                                    case 'fulfilled':
                                    case 'active':
                                        echo 'bg-success';
                                        break;
                                    case 'completed':
                                        echo 'bg-info';
                                        break;
                                    case 'rejected':
                                        echo 'bg-danger';
                                        break;
                                    default:
                                        echo 'bg-secondary';
                                }
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $credit->status)); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary" title="View Details"
                                    onclick=" viewCreditDetails(<?php echo $credit->id ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2"></i>
                            <p>No input credit applications found</p>
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
    var table = $('#datatable-all-credits').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by date
        language: {
            searchPlaceholder: 'Search credits...',
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
    $('#btnShowAll').click(function() {
        table.search('').draw();
    });

    $('#btnShowApproved').click(function() {
        table.search('Approved|Fulfilled|Completed').regex(true).draw();
    });

    $('#btnShowRejected').click(function() {
        table.search('Rejected').draw();
    });

    $('#btnShowPending').click(function() {
        table.search('Pending|Under Review').regex(true).draw();
    });

    $('#btnShowActive').click(function() {
        table.search('Fulfilled|Active').regex(true).draw();
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Function to view input credit details
function viewCreditDetails(creditId) {
    window.location.href = "input-credit-details?id=" + creditId;
}

// Function to print credit statement
function printCreditStatement(creditId) {
    // Show loading message with toastr
    toastr.info('Preparing your input credit statement for download...', 'Please wait', {
        "positionClass": "toast-top-right",
        "progressBar": true,
        "timeOut": 0,
        "extendedTimeOut": 0,
        "closeButton": false,
        "hideMethod": "fadeOut"
    });

    // AJAX call to generate PDF
    $.ajax({
        url: "http://localhost/dfcs/ajax/credit-controller/generate-credit-statement-pdf.php",
        type: "POST",
        data: {
            creditId: creditId
        },
        xhrFields: {
            responseType: 'blob' // Important for handling binary data like PDFs
        },
        success: function(response, status, xhr) {
            toastr.clear(); // Clear the loading message

            try {
                // Create a blob from the PDF data
                const blob = new Blob([response], {
                    type: 'application/pdf'
                });

                // Get filename from Content-Disposition header if available
                let filename = 'Input_Credit_Statement_ICRED' + String(creditId).padStart(5, '0') + '.pdf';
                const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(
                        /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (filenameMatch && filenameMatch[1]) {
                        filename = filenameMatch[1].replace(/['"]/g, '');
                    }
                }

                // Create a download link and trigger it
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();

                // Clean up
                window.URL.revokeObjectURL(url);
                document.body.removeChild(link);

                toastr.success('Input credit statement downloaded successfully', 'Success', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            } catch (e) {
                // If response isn't a PDF, it might be a JSON error message
                try {
                    const reader = new FileReader();
                    reader.onload = function() {
                        const errorJson = JSON.parse(reader.result);
                        toastr.error(errorJson.error || 'Failed to generate input credit statement',
                            'Error', {
                                "positionClass": "toast-top-right",
                                "progressBar": true,
                                "timeOut": 5000
                            });
                    };
                    reader.readAsText(response);
                } catch (readError) {
                    toastr.error('Failed to process server response', 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 5000
                    });
                }
            }
        },
        error: function(xhr, status, error) {
            toastr.clear();
            toastr.error('Failed to generate input credit statement. Please try again.', 'Error', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 5000
            });
            console.error('Error generating PDF:', error);
        }
    });
}
</script>
<?php endif; ?>