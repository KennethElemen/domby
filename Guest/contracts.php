<!DOCTYPE html>
<html lang="en">
<head>
   <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Contracts</title>
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../assets/images/favicon.png"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include html2pdf.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.js"></script>
    <style>
        /* import inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        /* root */
        :root {
            --bg-light: #F5F5FC;
            --bg-dark: #35353C;
            --bg-darker: #15151C;
            --font-body: #55555C;
            --shadow: #C5C5CC;
        }

        /* demo related */
        body {
            background: #f2edf3;
            height: 100vh; /* Full viewport height */
            font-family: "Inter", sans-serif;
            color: var(--font-body);
            display: flex; /* Display flex */
            flex-direction: column; /* Stack flex items vertically */
            justify-content: center; /* Center vertically */
            align-items: center; /* Center horizontally */
        }
            
        .demo_title h1,
        h1 {
            color: var(--bg-darker);
            text-align: left; /* Center text */
            margin-bottom:70px;
            
        }

        /* Contract container */
        .contracts-container {
            display: flex; /* Display flex */
            flex-wrap: wrap; /* Allow wrapping */
            gap: 50px; /* Spacing between cards */
            justify-content: center; /* Center cards horizontally */
        }
        .demo_title h4{
            
            margin:20px;
        }

        /* tpn card style */
        .tpn_card {
            background: var(--bg-light);
            padding: 15px;
            border-radius: 15px;
            box-shadow: 1px 7px 13px var(--shadow);
            text-align: center; /* Center content */
            width: 300px; /* Fixed width for each card */
        }

        .tpn_card img {
            border-radius: 15px;
            box-shadow: 1px 7px 13px var(--shadow);
            width: 50%; /* Make image responsive */
            margin-bottom: 10px; /* Add space below image */
        }

        .tpn_card h5 {
            color: var(--bg-dark);
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .tpn_card p {
            color: var(--font-body);
            font-weight: 400;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .tpn_card .tpn_btn {
            background: var(--bg-dark);
            border: none;
            border-radius: 5px;
            color: #FFF;
            padding: 5px 10px;
            box-shadow: 1px 7px 13px var(--shadow);
            transition: all .7s ease;
            font-size: 1rem;
            text-decoration: none; /* Remove default underline */
            margin-top: 10px; /* Add space above button */
        }

        .tpn_card .tpn_btn:hover {
            background: var(--bg-darker);
            box-shadow: none;
        }
    </style>
</head>

<body>  
<?php
session_start(); // Start the session

// Check if the session variable is set and not empty
if (isset($_SESSION['submitted_email']) && !empty($_SESSION['submitted_email'])) {
    // Echo the submitted email
   

    $email = $_SESSION['submitted_email'];
    // Include database connection configuration
    include '../includes/config/dbconn.php';

    // Create a new database connection
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the database connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Prepare and execute SQL query to fetch all contracts for the submitted email
    $stmt = $dbConnection->prepare("SELECT id, contract_content, tenant_name, contract_date FROM contracts WHERE EmailID = ? ORDER BY contract_date DESC");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Close the statement
    $stmt->close();
    echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Contracts Found',
                    html: 'Contracts found for this email.',
                    timerProgressBar: true,
                    timer: 2000,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                }).then(() => {
                   
                });
             </script>";
    // Check if contracts are found
    if ($result->num_rows > 0) {
        // Initialize an array to store contracts
        $contracts = [];

        // Fetch all contracts
        while ($row = $result->fetch_assoc()) {
            $contracts[] = $row;
            
        }
        
        // Close the database connection
        $dbConnection->close();
    } else {
        // No contracts found for this email
        $contracts = [];
        echo "<p>No contracts found for the submitted email.</p>";
    }
} else {
    // If the session variable is not set or empty
    echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'error',
                    html: 'No Contracts found for this email.',
                    timerProgressBar: true,
                    timer: 2000,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                }).then(() => {
                    window.location.href = '../index.php';
                });
             </script>";
}
?>


<div class="container">
   <div class="row justify-content-center">
        <div class="col-12">
            <div class="demo_title text-center">
                <?php
                // Display contract name from the first contract if contracts are found
                if (!empty($contracts)) {
                    $firstContract = $contracts[0];
                    echo "<h1>{$firstContract['tenant_name']}'s Contract</h1>";
                } else {
                    echo "<h6>No contracts found</h6>";
                }
                ?>
            </div>
            <div class="contracts-container">
    <?php
    // Display contracts if found
    if (!empty($contracts)) {
        foreach ($contracts as $contract) {
            // Format the contract date
            $formattedDate = date('F j, Y', strtotime($contract['contract_date']));
            
            echo "<div class='tpn_card'>";
            echo "<img src='../assets/images/contract1.jpg' class='w-30 mb-4' />";
            echo "<i class='fas fa-file-download contract-icon text-primary icon-lg' data-contract=''></i>"; // Font Awesome icon
            echo "<h4>Date created: {$formattedDate}</h4>"; // Echo formatted contract date
            echo "<button class='btn btn-primary download-pdf-btn' data-contract='" . htmlspecialchars(json_encode($contract)) . "'>Download PDF</button>";
            echo "</div>"; 
        }
    } else {
        echo "<div class='tpn_card'>";
        echo "<div class='no-contracts'>No contracts found for this email.</div>";
        echo "</div>";
    }
    ?>
</div>
        </div>
        <div style="margin-top: 20px; text-align: center;">
    <button id="logoutBtn" class="btn btn-danger">Logout</button>
</div>
    </div>
</div>
<?php
// JavaScript code for PDF download and logout button
?>
<!-- JavaScript code for PDF download -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.download-pdf-btn');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const contractData = JSON.parse(this.getAttribute('data-contract'));
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
    
    // Logout button event listener
    document.getElementById('logoutBtn').addEventListener('click', function () {
        // Ask for confirmation using SweetAlert
        Swal.fire({
          title: 'Are you sure?',
          text: "You want to logout?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, logout!'
        }).then((result) => {
          if (result.isConfirmed) {
            // If confirmed, redirect to logout script
            window.location.href = '../index.php';
          }
        });
    });
</script>
</body>
</html>
