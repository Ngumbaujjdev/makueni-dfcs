<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayDeductions'])):
    $app = new App;
    
    // Query to get all active loans and input credits (all deductions)
    $query = "SELECT 
                'bank_loan' as deduction_type,
                al.id as deduction_id,
                CONCAT('LOAN', LPAD(la.id, 5, '0')) as reference_number,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.id as farmer_id,
                fm.registration_number as farmer_reg,
                lt.name as loan_type,
                b.name as bank_name,
                al.approved_amount as original_amount,
                al.remaining_balance as outstanding_amount,
                al.disbursement_date as start_date,
                al.expected_completion_date as end_date,
                al.total_repayment_amount as total_amount,
                ROUND((al.total_repayment_amount - al.remaining_balance) / al.total_repayment_amount * 100, 1) as repayment_percentage
              FROM approved_loans al
              JOIN loan_applications la ON al.loan_application_id = la.id
              JOIN farmers fm ON la.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              JOIN loan_types lt ON la.loan_type_id = lt.id
              LEFT JOIN banks b ON la.bank_id = b.id
              WHERE al.status = 'active'
              AND la.provider_type = 'bank'
              AND al.remaining_balance > 0
              
              UNION ALL
              
              SELECT 
                'sacco_loan' as deduction_type,
                al.id as deduction_id,
                CONCAT('LOAN', LPAD(la.id, 5, '0')) as reference_number,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.id as farmer_id,
                fm.registration_number as farmer_reg,
                lt.name as loan_type,
                'SACCO' as bank_name,
                al.approved_amount as original_amount,
                al.remaining_balance as outstanding_amount,
                al.disbursement_date as start_date,
                al.expected_completion_date as end_date,
                al.total_repayment_amount as total_amount,
                ROUND((al.total_repayment_amount - al.remaining_balance) / al.total_repayment_amount * 100, 1) as repayment_percentage
              FROM approved_loans al
              JOIN loan_applications la ON al.loan_application_id = la.id
              JOIN farmers fm ON la.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              JOIN loan_types lt ON la.loan_type_id = lt.id
              WHERE al.status = 'active'
              AND la.provider_type = 'sacco'
              AND al.remaining_balance > 0
              
              UNION ALL
              
              SELECT 
                'input_credit' as deduction_type,
                aic.id as deduction_id,
                CONCAT('INPT', LPAD(ica.id, 5, '0')) as reference_number,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.id as farmer_id,
                fm.registration_number as farmer_reg,
                'Input Credit' as loan_type,
                av.name as bank_name,
                aic.approved_amount as original_amount,
                aic.remaining_balance as outstanding_amount,
                aic.fulfillment_date as start_date,
                NULL as end_date,
                aic.total_with_interest as total_amount,
                ROUND((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest * 100, 1) as repayment_percentage
              FROM approved_input_credits aic
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              JOIN farmers fm ON ica.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              JOIN agrovets av ON ica.agrovet_id = av.id
              WHERE aic.status = 'active'
              AND aic.remaining_balance > 0
              
              ORDER BY deduction_type, outstanding_amount DESC";

    $deductions = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between" style="background-color: #6AA32D; color: white;">
        <div class="card-title">
            <i class="fa-solid fa-calculator me-2"></i> Active Deductions
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-deductions" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Farmer</th>
                        <th>Facility Type</th>
                        <th>Provider</th>
                        <th>Original Amount</th>
                        <th>Outstanding</th>
                        <th>Repaid %</th>
                        <th>Start Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($deductions && count($deductions) > 0): ?>
                    <?php foreach ($deductions as $deduction): ?>
                    <?php
                    // Set icon and badge class based on deduction type
                    switch($deduction->deduction_type) {
                        case 'bank_loan':
                            $typeIcon = 'fa-building-columns';
                            $badgeClass = 'bg-primary';
                            $typeName = 'Bank Loan';
                            break;
                        case 'sacco_loan':
                            $typeIcon = 'fa-landmark';
                            $badgeClass = 'bg-purple';
                            $typeName = 'SACCO Loan';
                            break;
                        case 'input_credit':
                            $typeIcon = 'fa-leaf';
                            $badgeClass = 'bg-danger';
                            $typeName = 'Input Credit';
                            break;
                        default:
                            $typeIcon = 'fa-money-bill';
                            $badgeClass = 'bg-secondary';
                            $typeName = 'Other';
                    }
                    
                    // Set repayment progress class
                    if ($deduction->repayment_percentage >= 75) {
                        $progressClass = 'bg-success';
                    } elseif ($deduction->repayment_percentage >= 50) {
                        $progressClass = 'bg-info';
                    } elseif ($deduction->repayment_percentage >= 25) {
                        $progressClass = 'bg-warning';
                    } else {
                        $progressClass = 'bg-danger';
                    }
                    ?>
                    <tr>
                        <td>
                            <span class="badge <?php echo $badgeClass ?>">
                                <i class="fa-solid <?php echo $typeIcon ?> me-1"></i>
                                <?php echo $typeName ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($deduction->reference_number); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-user" style="color: #6AA32D;"></i>
                                </span>
                                <div>
                                    <span class="fw-medium d-block">
                                        <?php echo htmlspecialchars($deduction->farmer_name) ?>
                                    </span>
                                    <small
                                        class="text-muted"><?php echo htmlspecialchars($deduction->farmer_reg) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-file-invoice-dollar" style="color: #6AA32D;"></i>
                                </span>
                                <?php echo htmlspecialchars($deduction->loan_type) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid <?php echo $typeIcon ?>" style="color: #6AA32D;"></i>
                                </span>
                                <?php echo htmlspecialchars($deduction->bank_name) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-money-bill" style="color: #6AA32D;"></i>
                                </span>
                                KES <?php echo number_format($deduction->original_amount, 2) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-hand-holding-dollar" style="color: #6AA32D;"></i>
                                </span>
                                KES <?php echo number_format($deduction->outstanding_amount, 2) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-12"><?php echo $deduction->repayment_percentage ?>%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar <?php echo $progressClass ?>"
                                        style="width: <?php echo $deduction->repayment_percentage ?>%;"
                                        role="progressbar"></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($deduction->start_date)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="d-flex flex-column align-items-center py-4">
                                <i class="fa-solid fa-check-circle fa-3x mb-3" style="color: #6AA32D;"></i>
                                <h5>No active deductions found</h5>
                                <p class="text-muted">There are no active loans or input credits to process.</p>
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
    $('#datatable-deductions').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by outstanding amount
        language: {
            searchPlaceholder: 'Search deductions...',
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa-solid fa-file-pdf me-1"></i> PDF',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print me-1"></i> Print',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                }
            ],
            className: 'btn btn-outline-primary me-3' // Added me-3 for right margin
        }]
    });

    // Add custom filter controls for deduction types with extra padding
    $("<div class='d-flex gap-2 mb-3 ms-3'></div>") // Added ms-3 for left margin
        .append(
            "<button class='btn btn-sm btn-primary filter-btn' data-filter='all'><i class='fa-solid fa-filter me-1'></i> All</button>"
            )
        .append(
            "<button class='btn btn-sm btn-outline-primary filter-btn' data-filter='Bank Loan'><i class='fa-solid fa-building-columns me-1'></i> Bank Loans</button>"
            )
        .append(
            "<button class='btn btn-sm btn-outline-primary filter-btn' data-filter='SACCO Loan'><i class='fa-solid fa-landmark me-1'></i> SACCO Loans</button>"
            )
        .append(
            "<button class='btn btn-sm btn-outline-primary filter-btn' data-filter='Input Credit'><i class='fa-solid fa-leaf me-1'></i> Input Credits</button>"
            )
        .insertBefore('#datatable-deductions_wrapper .dataTables_filter');

    // Add padding between buttons and table
    $('#datatable-deductions_wrapper .dt-buttons').addClass('me-3');

    // Add filter functionality
    $('.filter-btn').on('click', function() {
        let filterValue = $(this).data('filter');
        let table = $('#datatable-deductions').DataTable();

        $('.filter-btn').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');

        if (filterValue === 'all') {
            table.column(0).search('').draw();
        } else {
            table.column(0).search(filterValue).draw();
        }
    });
});
</script>
<?php endif; ?>