<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayActiveInputCredits'])):
    $app = new App;
    
    // Query to get all active input credits with farmer, agrovet, and item details
    $query = "SELECT 
                aic.id as credit_id,
                aic.credit_application_id,
                CONCAT('INPT', LPAD(ica.id, 5, '0')) as reference_number,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg,
                a.name as agrovet_name,
                a.location as agrovet_location,
                ici.input_type as primary_input_type,
                ici.input_name as primary_input_name,
                aic.approved_amount as original_amount,
                aic.credit_percentage as interest_rate,
                aic.repayment_percentage,
                aic.remaining_balance,
                aic.total_with_interest,
                aic.fulfillment_date,
                ica.application_date,
                ica.status as application_status,
                ROUND((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest * 100, 1) as repayment_percentage
              FROM approved_input_credits aic
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              JOIN farmers fm ON ica.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              JOIN input_credit_items ici ON ici.credit_application_id = ica.id
              WHERE aic.status = 'active'
              GROUP BY aic.id
              ORDER BY aic.remaining_balance DESC";

    $active_credits = $app->select_all($query);
?>
<div class="card custom-card shadow-sm">
    <div class="card-header d-flex justify-content-between" style="background-color: #6AA32D; color: white;">
        <div class="card-title d-flex align-items-center">
            <i class="fa-solid fa-credit-card me-2"></i> Active Input Credits
            <span class="badge bg-white text-success ms-2">
                <?php echo (is_array($active_credits)) ? count($active_credits) : 0; ?> credits
            </span>
            <span class="badge bg-white text-success ms-2">
                KES <?php 
                    $total_amount = 0;
                    if (is_array($active_credits)) {
                        foreach ($active_credits as $credit) {
                            $total_amount += $credit->remaining_balance;
                        }
                    }
                    echo number_format($total_amount, 2);
                ?>
            </span>
        </div>
        <div>
            <button class="btn btn-sm btn-light" onclick="loadActiveInputCredits()">
                <i class="fa-solid fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="active-input-credits-table" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Farmer</th>
                        <th>Agrovet</th>
                        <th>Input Type</th>
                        <th>Fulfillment Date</th>
                        <th>Original Amount</th>
                        <th>Remaining Balance</th>
                        <th>Repayment %</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($active_credits) && count($active_credits) > 0): ?>
                    <?php foreach ($active_credits as $credit): ?>
                    <?php
                    // Set icon based on input type
                    switch($credit->primary_input_type) {
                        case 'fertilizer':
                            $typeIcon = 'fa-fill-drip';
                            $badgeClass = 'bg-success';
                            $typeName = 'Fertilizer';
                            break;
                        case 'seeds':
                            $typeIcon = 'fa-seedling';
                            $badgeClass = 'bg-primary';
                            $typeName = 'Seeds';
                            break;
                        case 'pesticide':
                            $typeIcon = 'fa-spray-can';
                            $badgeClass = 'bg-warning';
                            $typeName = 'Pesticide';
                            break;
                        case 'tools':
                            $typeIcon = 'fa-tools';
                            $badgeClass = 'bg-info';
                            $typeName = 'Tools';
                            break;
                        default:
                            $typeIcon = 'fa-box';
                            $badgeClass = 'bg-secondary';
                            $typeName = 'Other';
                    }
                    
                    // Set repayment progress class
                    if ($credit->repayment_percentage >= 75) {
                        $progressClass = 'bg-success';
                    } elseif ($credit->repayment_percentage >= 50) {
                        $progressClass = 'bg-info';
                    } elseif ($credit->repayment_percentage >= 25) {
                        $progressClass = 'bg-warning';
                    } else {
                        $progressClass = 'bg-danger';
                    }
                    ?>
                    <tr class="parent-row" data-id="<?php echo $credit->credit_id; ?>">
                        <td>
                            <span class="badge bg-light text-dark">
                                <?php echo htmlspecialchars($credit->reference_number); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-user" style="color: #6AA32D;"></i>
                                </span>
                                <div>
                                    <span class="fw-medium d-block">
                                        <?php echo htmlspecialchars($credit->farmer_name) ?>
                                    </span>
                                    <small
                                        class="text-muted"><?php echo htmlspecialchars($credit->farmer_reg) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-store" style="color: #6AA32D;"></i>
                                </span>
                                <div>
                                    <span class="d-block"><?php echo htmlspecialchars($credit->agrovet_name) ?></span>
                                    <small
                                        class="text-muted"><?php echo htmlspecialchars($credit->agrovet_location) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?php echo $badgeClass ?>">
                                <i class="fa-solid <?php echo $typeIcon ?> me-1"></i>
                                <?php echo ucfirst($typeName) ?>:
                                <?php echo htmlspecialchars($credit->primary_input_name) ?>
                            </span>
                        </td>
                        <td>
                            <i class="fa-solid fa-calendar-day me-1 text-success"></i>
                            <?php echo date('M d, Y', strtotime($credit->fulfillment_date)) ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-tag" style="color: #6AA32D;"></i>
                                </span>
                                KES <?php echo number_format($credit->original_amount, 2) ?>
                                <span class="badge bg-light text-dark ms-2">
                                    <i class="fa-solid fa-percentage me-1"></i><?php echo $credit->interest_rate ?>%
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-hand-holding-dollar" style="color: #6AA32D;"></i>
                                </span>
                                <span class="fw-semibold">KES
                                    <?php echo number_format($credit->remaining_balance, 2) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-12"><?php echo $credit->repayment_percentage ?>%</span>
                                    <span class="fs-12 text-muted">Deduction:
                                        <?php echo $credit->repayment_percentage ?>%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar <?php echo $progressClass ?>"
                                        style="width: <?php echo $credit->repayment_percentage ?>%;" role="progressbar">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-success view-details"
                                data-id="<?php echo $credit->credit_id ?>"
                                onclick="toggleDetails(<?php echo $credit->credit_id ?>)">
                                <i class="fa-solid fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>
                    <tr class="detail-row" id="details-<?php echo $credit->credit_id; ?>"
                        style="display: none; background-color: rgba(106, 163, 45, 0.05);">
                        <td colspan="9" class="p-0">
                            <div class="card m-3 border-0 shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-clipboard-list me-2" style="color:#6AA32D;"></i>
                                        <span style="color:#6AA32D;" class="fw-medium">Application Details</span>
                                    </div>
                                    <button type="button" class="btn-close"
                                        onclick="toggleDetails(<?php echo $credit->credit_id ?>)"></button>
                                </div>
                                <div class="card-body pt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 40%;" class="fw-medium">Application Date:</td>
                                                        <td>
                                                            <i class="fas fa-calendar-alt me-1 text-success"></i>
                                                            <?php echo date('M d, Y', strtotime($credit->application_date)) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Application Status:</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-success-transparent text-success px-2">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                <?php echo ucfirst($credit->application_status) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Interest Rate:</td>
                                                        <td>
                                                            <i class="fas fa-percentage me-1 text-success"></i>
                                                            <?php echo $credit->interest_rate ?>%
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Repayment Method:</td>
                                                        <td>
                                                            <span class="badge bg-light text-dark px-2">
                                                                <i class="fas fa-percentage me-1"></i>Produce Deduction:
                                                                <?php echo $credit->repayment_percentage ?>%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 40%;" class="fw-medium">Original Amount:</td>
                                                        <td>
                                                            <i class="fas fa-money-bill me-1 text-success"></i>
                                                            KES <?php echo number_format($credit->original_amount, 2) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Total With Interest:</td>
                                                        <td>
                                                            <i class="fas fa-coins me-1 text-success"></i>
                                                            KES
                                                            <?php echo number_format($credit->total_with_interest, 2) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Amount Paid:</td>
                                                        <td>
                                                            <i class="fas fa-check-circle me-1 text-success"></i>
                                                            KES
                                                            <?php echo number_format($credit->total_with_interest - $credit->remaining_balance, 2) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Remaining Balance:</td>
                                                        <td>
                                                            <span class="fw-semibold" style="color:#6AA32D;">
                                                                <i class="fas fa-balance-scale me-1"></i>
                                                                KES
                                                                <?php echo number_format($credit->remaining_balance, 2) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3 p-2 bg-light rounded" style="color:#6AA32D;">
                                        <i class="fas fa-list me-2"></i>Input Items
                                    </h6>

                                    <?php
                                    // Get input credit items specifically for this application
                                    $query = "SELECT 
                                            input_type,
                                            input_name,
                                            quantity,
                                            unit,
                                            unit_price,
                                            total_price,
                                            description
                                            FROM input_credit_items
                                            WHERE credit_application_id = " . $credit->credit_application_id;
                                    $items = $app->select_all($query);
                                    ?>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered mb-0">
                                            <thead style="background-color: rgba(106, 163, 45, 0.1);">
                                                <tr>
                                                    <th class="text-success">Type</th>
                                                    <th class="text-success">Name</th>
                                                    <th class="text-success text-center">Quantity</th>
                                                    <th class="text-success">Unit</th>
                                                    <th class="text-success text-end">Unit Price</th>
                                                    <th class="text-success text-end">Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (is_array($items) && count($items) > 0): ?>
                                                <?php 
                                                    $items_total = 0;
                                                    foreach ($items as $item): 
                                                        $items_total += $item->total_price;
                                                    ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                                // Set icon based on input type
                                                                switch($item->input_type) {
                                                                    case 'fertilizer':
                                                                        echo '<i class="fas fa-fill-drip me-1 text-success"></i> ';
                                                                        break;
                                                                    case 'seeds':
                                                                        echo '<i class="fas fa-seedling me-1 text-primary"></i> ';
                                                                        break;
                                                                    case 'pesticide':
                                                                        echo '<i class="fas fa-spray-can me-1 text-warning"></i> ';
                                                                        break;
                                                                    case 'tools':
                                                                        echo '<i class="fas fa-tools me-1 text-info"></i> ';
                                                                        break;
                                                                    default:
                                                                        echo '<i class="fas fa-box me-1 text-secondary"></i> ';
                                                                }
                                                                echo ucfirst($item->input_type);
                                                                ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($item->input_name) ?></td>
                                                    <td class="text-center"><?php echo $item->quantity ?></td>
                                                    <td><?php echo ucfirst($item->unit) ?></td>
                                                    <td class="text-end">KES
                                                        <?php echo number_format($item->unit_price, 2) ?></td>
                                                    <td class="text-end">KES
                                                        <?php echo number_format($item->total_price, 2) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <tr class="fw-medium"
                                                    style="background-color: rgba(106, 163, 45, 0.1);">
                                                    <td colspan="5" class="text-end">Total:</td>
                                                    <td class="text-end">KES
                                                        <?php echo number_format($items_total, 2) ?></td>
                                                </tr>
                                                <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No items found for this
                                                        application</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="d-flex flex-column align-items-center py-4">
                                <i class="fa-solid fa-check-circle fa-3x mb-3" style="color: #6AA32D;"></i>
                                <h5>No active input credits found</h5>
                                <p class="text-muted">There are no active input credits to display at this time.</p>
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
// Global variable to store the DataTable instance
let activeCreditsTable;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
});

// Function to initialize the DataTable
function initializeDataTable() {
    // If table is already initialized, destroy it first
    if ($.fn.DataTable.isDataTable('#active-input-credits-table')) {
        $('#active-input-credits-table').DataTable().destroy();
    }

    // Initialize DataTable with advanced features
    activeCreditsTable = $('#active-input-credits-table').DataTable({
        responsive: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
        language: {
            search: "<i class='fa fa-search search-icon'></i>",
            lengthMenu: "_MENU_ records per page",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        },
        // Define column-specific sorting and filtering
        columnDefs: [{
                orderable: false,
                targets: [8]
            }, // Disable sorting on action column
            {
                targets: [5, 6], // For amount columns
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract numbers for sorting
                        return data.replace(/[^0-9.]/g, '');
                    }
                    return data;
                }
            },
            {
                targets: [7], // For percentage column
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract percentage value for sorting
                        const match = data.match(/(\d+(\.\d+)?)%/);
                        return match ? match[1] : 0;
                    }
                    return data;
                }
            }
        ],
        // Make table rows collapsible (for detail view)
        drawCallback: function() {
            // Re-initialize row click handlers after DataTable redraw
            $('.view-details').off('click').on('click', function(e) {
                e.stopPropagation(); // Prevent event bubbling
                const creditId = $(this).data('id');
                toggleDetails(creditId);
            });
        }
    });

    // Add search placeholder
    $('.dataTables_filter input').attr('placeholder', 'Search credits...');

    // Style the length menu
    $('.dataTables_length select').addClass('form-select form-select-sm');

    // Add custom class to search input
    $('.dataTables_filter input').addClass('form-control form-control-sm');
}

// Function to toggle details row visibility
function toggleDetails(creditId) {
    const detailsRow = document.getElementById('details-' + creditId);

    if (detailsRow.style.display === 'none') {
        // Close all other detail rows first
        document.querySelectorAll('.detail-row').forEach(row => {
            row.style.display = 'none';
        });

        // Toggle buttons visual state
        document.querySelectorAll('.view-details').forEach(btn => {
            btn.innerHTML = '<i class="fa-solid fa-eye me-1"></i> View';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-success');
        });

        // Show this detail row
        detailsRow.style.display = 'table-row';

        // Update button
        const button = document.querySelector(`.view-details[data-id="${creditId}"]`);
        button.innerHTML = '<i class="fa-solid fa-eye-slash me-1"></i> Hide';
        button.classList.remove('btn-outline-success');
        button.classList.add('btn-success');
    } else {
        // Hide detail row
        detailsRow.style.display = 'none';

        // Update button
        const button = document.querySelector(`.view-details[data-id="${creditId}"]`);
        button.innerHTML = '<i class="fa-solid fa-eye me-1"></i> View';
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-success');
    }

    // Adjust DataTable responsive features if table is initialized
    if ($.fn.DataTable.isDataTable('#active-input-credits-table')) {
        activeCreditsTable.responsive.recalc();
    }
}

// Function to reload the active input credits
function loadActiveInputCredits() {
    $.ajax({
        url: window.location.href,
        type: 'POST',
        data: {
            displayActiveInputCredits: true
        },
        success: function(response) {
            $('#active-credits-container').html(response);
            // Re-initialize DataTable after content update
            initializeDataTable();
        }
    });
}
</script>
<?php endif; ?>