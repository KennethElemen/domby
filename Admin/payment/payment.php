<!DOCTYPE html>
<html lang="en">

<?php include '../head.php'; ?>

<body>
    <div class="container-scroller">
        <?php include '../topbar.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../sidebar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header"></div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                  <div class="col-lg-12 mb-4">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <h1 class="mb-12">Completed Payment</h1>
                                    </div>
                                    <hr>
                                    </div>
                                    <div class="table-responsive">
                                    <table class="table table-striped" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Month Paid</th>
                                                    <th>Email</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Reference code</th>
                                                    <th>Proof of Payment</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        <?php
               
                                        include '../../includes/function/payment_status.php'; // Include the functions file
                                        
                                        // Call the function to process payments
                                        displayPaymentData($servername, $username, $password, $dbname);
                                    
                                       
                                        ?>
                                        </tbody>                                                                                                              
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="modal fade" id="view-proof-of-payment" tabindex="-1" role="dialog"
                        aria-labelledby="view-proof-of-payment-label" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="view-proof-of-payment-label">Proof of Payment</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <div class="modal-body">
                            <img id="proof-of-payment-img" class="d-block mx-auto img-fluid custom-img" src="path/to/your/image.jpg"
                                alt="Proof of Payment">
                        </div>


                        </div>
                    </div>
                </div>
                <?php include '../footer.php'; ?>
            </div>
        </div>
    </div>
    <?php include '../scripts.php'; ?>
     <script>
   $(document).ready(function () {
        $('#view-proof-of-payment').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var imageBase64 = button.data('image');
            var modal = $(this);
            modal.find('#proof-of-payment-img').attr('src', 'data:image/jpeg;base64,' + imageBase64);
        });
   });

    function redirectTo(url) {
        window.location.href = url;
    }

     $('.status-dropdown').change(function () {
        var paymentId = $(this).data('id');
        var newStatus = $(this).val();

        // Send an AJAX request to update the status
        $.ajax({
            url: '../../includes/function/update_status.php',
            method: 'POST',
            data: { payment_id: paymentId, new_status: newStatus },
            success: function (response) {
                // Handle the response if needed
                console.log(response);

                // Reload the page upon successful status update
                location.reload();
            },
            error: function (xhr, status, error) {
                // Handle errors if any
                console.error(xhr.responseText);
            }
        });
    });
</script>

</body>

</html>