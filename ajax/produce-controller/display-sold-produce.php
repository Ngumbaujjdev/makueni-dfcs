<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displaySoldProduce'])):
    $app = new App;
    
    $query = "SELECT 
            pd.id,
            pd.quantity,
            pd.unit_price,
            pd.total_value,
            pd.quality_grade,
            pd.delivery_date,
            pd.status,
            pd.sale_date,
            pt.name as product_name,
            f.name as farm_name,
            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
            fm.registration_number
          FROM produce_deliveries pd
          JOIN farm_products fp ON pd.farm_product_id = fp.id
          JOIN product_types pt ON fp.product_type_id = pt.id
          JOIN farms f ON fp.farm_id = f.id
          JOIN farmers fm ON f.farmer_id = fm.id
          JOIN users u ON fm.user_id = u.id
          WHERE pd.status = 'sold' AND pd.is_sold = 1
          ORDER BY pd.sale_date DESC";
    
    $produces = $app->select_all($query);
?><div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-store-3-line me-2 text-success"></i>
            <span class="fw-bold">Sold Produce</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-sold-produce" class="table table-hover table-striped text-nowrap w-100">
                <thead>
                    <tr class="bg-light">
                        <th><i class="ri-hashtag me-2"></i>Reference</th>
                        <th><i class="ri-user-line me-2"></i>Farmer</th>
                        <th><i class="ri-plant-line me-2"></i>Product</th>
                        <th><i class="ri-scales-3-line me-2"></i>Quantity (KGs)</th>
                        <th><i class="ri-money-dollar-circle-line me-2"></i>Unit Price</th>
                        <th><i class="ri-funds-line me-2"></i>Total Value</th>
                        <th><i class="ri-calendar-line me-2"></i>Sale Date</th>
                        <th><i class="ri-settings-3-line me-2"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produces): ?>
                    <?php foreach ($produces as $produce): ?>
                    <tr>
                        <td class="align-middle">
                            <span class="badge bg-light-subtle text-dark border">
                                DLVR<?php echo str_pad($produce->id, 5, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-success-subtle rounded-circle">
                                    <span
                                        class="avatar-initials"><?php echo substr($produce->farmer_name, 0, 1); ?></span>
                                </div>
                                <div>
                                    <span class="fw-medium"><?php echo htmlspecialchars($produce->farmer_name) ?></span>
                                    <small
                                        class="d-block text-muted"><?php echo $produce->registration_number ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <span class="badge bg-success-subtle text-success">
                                <?php echo htmlspecialchars($produce->product_name) ?>
                            </span>
                        </td>
                        <td class="align-middle fw-medium"><?php echo number_format($produce->quantity, 2) ?></td>
                        <td class="align-middle fw-medium text-success">KES
                            <?php echo number_format($produce->unit_price, 2) ?></td>
                        <td class="align-middle fw-medium text-dark">KES
                            <?php echo number_format($produce->total_value, 2) ?></td>
                        <td class="align-middle">
                            <i class="ri-time-line me-1 text-muted"></i>
                            <?php echo date('M d, Y', strtotime($produce->sale_date)) ?>
                        </td>
                        <td class="align-middle">
                            <button class="btn btn-sm btn-success view-receipt"
                                data-produce-id="<?php echo $produce->id ?>"
                                data-reference="DLVR<?php echo str_pad($produce->id, 5, '0', STR_PAD_LEFT); ?>"
                                title="View Receipt">
                                <i class="ri-printer-line me-1"></i> Print Receipt
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="ri-inbox-line fs-2 text-muted d-block mb-2"></i>
                            <p class="text-muted">No sold produce found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initials {
    color: #6AA32D;
    font-weight: 600;
}

.table> :not(caption)>*>* {
    padding: 1rem 0.75rem;
}

.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
}
</style>
<script>
$(document).ready(function() {

    // Handle receipt viewing
    $('.view-receipt').on('click', function() {
        const produceId = $(this).data('produce-id');
        const reference = $(this).data('reference');

        // Show loading message
        toastr.info('Preparing receipt...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0
        });

        // Make AJAX request to generate receipt
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/generate-payment-receipt.php",
            type: "POST",
            data: {
                produceId: produceId
            },
            xhrFields: {
                responseType: 'blob' // Important for handling PDF response
            },
            success: function(response, status, xhr) {
                toastr.clear(); // Clear the loading message

                // Check if response is PDF
                if (response.type === 'application/pdf') {
                    // Create blob and download
                    const blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const filename = 'Payment_Receipt_' + reference + '.pdf';

                    // Create temporary link and trigger download
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();

                    // Cleanup
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(link);

                    toastr.success('Receipt downloaded successfully', 'Success', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000
                    });
                } else {
                    throw new Error('Invalid response type');
                }
            },
            error: function(xhr, status, error) {
                toastr.clear();

                // Try to parse error message if available
                let errorMessage = 'Failed to generate receipt';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    console.error('Error parsing error response:', e);
                }

                toastr.error(errorMessage, 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
            }
        });
    });
});
</script>


<script>
$(document).ready(function() {
    $('#datatable-sold-produce').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by sale date
        language: {
            searchPlaceholder: 'Search produce...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        // Custom styling for DataTables elements
        drawCallback: function() {
            $('.dataTables_wrapper .dataTables_length select').addClass(
                'form-select form-select-sm');
            $('.dataTables_wrapper .dataTables_filter input').addClass(
                'form-control form-control-sm');
        }
    });

    // receipt 

});
</script>
<?php endif; ?>