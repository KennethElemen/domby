<?php
// Include database connection file
include '../../includes/config/dbconn.php';

// Create a database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Fetch all contracts from the contracts table
$result = mysqli_query($dbConnection, "SELECT * FROM contracts");
$contracts = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close the database connection
$dbConnection->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.js"></script>

<!-- Add Font Awesome CDN link -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    .contract-icon-container {
        display: flex;
        align-items: center;
    }

    .contract-content {
        display: none; /* Hide the contract content by default */
    }

    .contract-icon {
        cursor: pointer;
        
    }
</style>

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
                                    <h4 class="card-title">All Contracts</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Date created</th>
                                                    <th>Tenant Name</th>
                                                    <th>Email</th>
                                                    <th>Download Contract</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($contracts as $contract) : ?>
                                                    <tr>
                                                        <td><?php echo date('F j, Y \a\t h:i A', strtotime(htmlspecialchars_decode($contract['contract_date']))); ?></td>
                                                        <td><?php echo htmlspecialchars($contract['tenant_name']); ?></td>
                                                         <td><?php echo htmlspecialchars($contract['EmailID']); ?></td>
                                                        <td>
                                                            <!-- Contract Icon Container -->
                                                            <div class="contract-icon-container">
                                                                <!-- Font Awesome icon for contract -->
                                                                <i class="fas fa-file-download contract-icon text-primary icon-lg" data-contract="<?php echo htmlspecialchars(json_encode($contract)); ?>"></i>
                                                                <!-- Contract Content (hidden by default) -->
                                                                <div class="contract-content">
                                                                    <?php echo htmlspecialchars_decode($contract['contract_content']); ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include '../footer.php'; ?>
            </div>
        </div>
    </div>
    <?php include '../scripts.php'; ?>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const icons = document.querySelectorAll('.contract-icon');

        icons.forEach(icon => {
            icon.addEventListener('click', function () {
                const contractData = JSON.parse(this.dataset.contract);
                const contractContent = contractData.contract_content;
                const tenantName = contractData.tenant_name;

                // Generate PDF and open preview window after contract is stored
                var element = document.createElement('div');
                element.innerHTML = contractContent;

                html2pdf()
                    .from(element)
                    .set({ filename: `Contract_${tenantName}.pdf`, margin: [10, 10] })
                    .save();
            });
        });
    });
</script>

</html>
