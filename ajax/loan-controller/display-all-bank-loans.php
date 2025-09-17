<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayAllLoans'])):
    $app = new App;
    
    // Query to get all loan applications with related data
    $query = "SELECT 
    la.id,
    la.farmer_id,
    la.provider_type,
    la.loan_type_id,
    la.bank_id,
    la.amount_requested,
    la.term_requested,
    la.purpose,
    la.application_date,
    la.creditworthiness_score,
    la.status,
    lt.name as loan_type,
    lt.interest_rate,
    b.name as bank_name,
    CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
    fm.registration_number as farmer_reg,
    CASE 
        WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' OR la.status = 'defaulted' THEN 
            (SELECT al.approved_amount FROM approved_loans al WHERE al.loan_application_id = la.id)
        ELSE NULL
    END as approved_amount,
    CASE 
        WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' OR la.status = 'defaulted' THEN 
            (SELECT al.disbursement_date FROM approved_loans al WHERE al.loan_application_id = la.id)
        ELSE NULL
    END as disbursement_date
FROM loan_applications la
JOIN loan_types lt ON la.loan_type_id = lt.id
JOIN farmers fm ON la.farmer_id = fm.id
JOIN users u ON fm.user_id = u.id
LEFT JOIN banks b ON la.bank_id = b.id
WHERE la.provider_type = 'bank'
ORDER BY la.application_date DESC";
    
    $loanApplications = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-file-list-3-line me-2"></i> All Loan Applications
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
            <button class="btn btn-outline-success btn-sm" id="btnShowApproved">Approved</button>
            <button class="btn btn-outline-danger btn-sm" id="btnShowRejected">Rejected</button>
            <button class="btn btn-outline-warning btn-sm" id="btnShowPending">Pending</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-all-loans" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-hash-line me-1"></i>Reference</th>
                        <th><i class="ri-user-line me-1"></i>Farmer</th>
                        <th><i class="ri-file-list-line me-1"></i>Loan Type</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)</th>
                        <th><i class="ri-calendar-line me-1"></i>Term</th>
                        <th><i class="ri-bar-chart-line me-1"></i>Credit Score</th>
                        <th><i class="ri-time-line me-1"></i>Date</th>
                        <th><i class="ri-shield-check-line me-1"></i>Status</th>
                        <th><i class="ri-settings-3-line me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($loanApplications): ?>
                    <?php foreach ($loanApplications as $loan): ?>
                    <tr>
                        <td class="fw-semibold">LOAN<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-success me-2">
                                    <?php echo strtoupper(substr($loan->farmer_name, 0, 1)); ?>
                                </span>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($loan->farmer_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info-transparent text-info">
                                <?php echo htmlspecialchars($loan->loan_type) ?>
                            </span>
                            <small class="d-block text-muted"><?php echo $loan->interest_rate ?>% p.a.</small>
                        </td>
                        <td class="fw-semibold">
                            <?php if ($loan->approved_amount): ?>
                            <span class="text-success">KES <?php echo number_format($loan->approved_amount, 2) ?></span>
                            <?php if ($loan->approved_amount != $loan->amount_requested): ?>
                            <small class="d-block text-muted">Req: KES
                                <?php echo number_format($loan->amount_requested, 2) ?></small>
                            <?php endif; ?>
                            <?php else: ?>
                            KES <?php echo number_format($loan->amount_requested, 2) ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php echo $loan->term_requested ?> months</td>
                        <td>
                            <span class="badge <?php 
                                if ($loan->creditworthiness_score >= 80) echo 'bg-success'; 
                                elseif ($loan->creditworthiness_score >= 60) echo 'bg-warning';
                                else echo 'bg-danger';
                            ?>">
                                <?php echo number_format($loan->creditworthiness_score, 1) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($loan->disbursement_date): ?>
                            <span data-bs-toggle="tooltip" title="Disbursement Date">
                                <?php echo date('M d, Y', strtotime($loan->disbursement_date)) ?>
                            </span>
                            <small class="d-block text-muted">Applied:
                                <?php echo date('M d, Y', strtotime($loan->application_date)) ?></small>
                            <?php else: ?>
                            <?php echo date('M d, Y', strtotime($loan->application_date)) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php 
                                switch($loan->status) {
                                    case 'pending':
                                    case 'under_review':
                                        echo 'bg-warning';
                                        break;
                                    case 'approved':
                                    case 'disbursed':
                                    case 'completed':
                                        echo 'bg-success';
                                        break;
                                    case 'rejected':
                                        echo 'bg-danger';
                                        break;
                                    default:
                                        echo 'bg-secondary';
                                }
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $loan->status)); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary" title="View Details"
                                    onclick="viewLoanDetails(<?php echo $loan->id ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <?php if ($loan->status == 'under_review'): ?>
                                <button class="btn btn-sm btn-warning" title="Review Application"
                                    onclick="reviewLoanApplication(<?php echo $loan->id ?>)">
                                    <i class="ri-file-search-line"></i>
                                </button>
                                <?php endif; ?>
                                <?php if ($loan->status == 'approved' || $loan->status == 'disbursed' || $loan->status == 'completed'): ?>
                                <button class="btn btn-sm btn-success" title="Print Statement"
                                    onclick="printLoanStatement(<?php echo $loan->id ?>)">
                                    <i class="ri-printer-line"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2"></i>
                            <p>No loan applications found</p>
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
    var table = $('#datatable-all-loans').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by date
        language: {
            searchPlaceholder: 'Search loans...',
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
        table.search('Approved|Disbursed|Completed').regex(true).draw();
    });

    $('#btnShowRejected').click(function() {
        table.search('Rejected').draw();
    });

    $('#btnShowPending').click(function() {
        table.search('Pending|Under Review').regex(true).draw();
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Function to view loan details
function viewLoanDetails(loanId) {
    window.location.href = "loan-details?id=" + loanId;
}

// Function to review loan application
function reviewLoanApplication(loanId) {
    window.location.href = "review-loan?id=" + loanId;
}

// Function to print loan statement
function printLoanStatement(loanId) {
    // Show loading message with toastr
    toastr.info('Preparing your loan statement for download...', 'Please wait', {
        "positionClass": "toast-top-right",
        "progressBar": true,
        "timeOut": 0,
        "extendedTimeOut": 0,
        "closeButton": false,
        "hideMethod": "fadeOut"
    });

    // AJAX call to generate PDF
    $.ajax({
        url: "http://localhost/dfcs/ajax/loan-controller/generate-loan-statement-pdf.php",
        type: "POST",
        data: {
            loanId: loanId
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
                let filename = 'Loan_Statement_LOAN' + String(loanId).padStart(5, '0') + '.pdf';
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

                toastr.success('Loan statement downloaded successfully', 'Success', {
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
                        toastr.error(errorJson.error || 'Failed to generate loan statement',
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
            toastr.error('Failed to generate loan statement. Please try again.', 'Error', {
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