<?php
include '../../includes/config/dbconn.php';

// Create a database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Fetch admin email
$adminResult = mysqli_query($dbConnection, "SELECT Email FROM admins LIMIT 1");
$adminInfo = mysqli_fetch_assoc($adminResult);
$adminEmail = isset($adminInfo['Email']) ? $adminInfo['Email'] : '';

$aboutResult = mysqli_query($dbConnection, "SELECT LandlordName, Location, DormName, ContactNumber FROM about");
$aboutInfo = mysqli_fetch_assoc($aboutResult);

// Use this information when generating the contract content
$landlordName = $aboutInfo['LandlordName'];
$location = $aboutInfo['Location'];
$dormname = $aboutInfo['DormName'];
$contactnumber = $aboutInfo['ContactNumber'];

// Fetch all tenants
$result = mysqli_query($dbConnection, "SELECT * FROM tenantprofile");
$tenants = mysqli_fetch_all($result, MYSQLI_ASSOC);


// Fetch all tenants with the computed column for the number of days
$result = mysqli_query($dbConnection, "SELECT *, DATEDIFF(check_out_date, check_in_date) AS daystay FROM tenantprofile");
$tenants = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Update daystay in the tenantprofile table
foreach ($tenants as $tenant) {
    $profileID = $tenant['ProfileID'];
    $daystay = $tenant['daystay'];
    
    mysqli_query($dbConnection, "UPDATE tenantprofile SET daystay = '$daystay' WHERE ProfileID = '$profileID'");
}


// Fetch room rates from room_management table
$roomRatesResult = mysqli_query($dbConnection, "SELECT * FROM room_management");
$roomRates = mysqli_fetch_all($roomRatesResult, MYSQLI_ASSOC);


?>


<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.js"></script>


<body>
    <div class="container-scroller">
        <?php include '../topbar.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../sidebar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header"></div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin ">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <h1 class="MB-12">Tenant<ul class="list-arrow">
                                              <li>List of tenant Profile</li>
                                            </ul></h1>
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="example3" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                   
                                                    <th>Tenant Type</th>
                                                    <th>Email</th>
                                                    <th>Remaining Days</th> 
                                                     <th>Action</th>
                                                </tr>
                                            </thead>
                                          <tbody>
                                              <?php
                                                foreach ($tenants as $index => $tenant) {
                                                    echo "<tr>";
                                                    // Other existing columns
                                                    echo "<td>{$tenant['type_of_stay']}</td>";
                                                    echo "<td>{$tenant['email']}</td>";
                                                    echo "<td class='text-left' id='remainingTime-$index'>"; // Center-align the content

                                                    // Calculate remaining time using JavaScript
                                                    echo "</td>";
                                                    echo "<td><button type='button' class='btn btn-primary btn-sm view-btn' data-toggle='modal' data-target='#view-user-$index' data-tenant='" . htmlspecialchars(json_encode($tenant), ENT_QUOTES, 'UTF-8') . "'>MANAGE</button></td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
  foreach ($tenants as $index => $tenant) {
    // Update rate based on tenant type and room rate
    $newRate = 0;
    foreach ($roomRates as $roomRate) { 
        if ($roomRate['room_number'] == $tenant['room_number']) {
            if ($tenant['type_of_stay'] == 'Long-term') {
                $newRate = $roomRate['rate_per_month'];
            } elseif ($tenant['type_of_stay'] == 'Transient') {
                $newRate = $roomRate['rate_per_night'];
            }
            break; // Stop searching for room rates once found
        }
    }

    // Calculate total amount based on tenant type
    if ($tenant['type_of_stay'] == 'Long-term') {
        $totalMonths = floor($tenant['daystay'] / 30);
        $extraDays = $tenant['daystay'] % 30;
        $dailyrate = $newRate / 30;
        if ($extraDays != 0) {
            $totalAmount = ($newRate * $totalMonths) + ($dailyrate * $extraDays);
        } else {
            $totalAmount = $newRate * $totalMonths;
        }
     
    } elseif ($tenant['type_of_stay'] == 'Transient') {
        $totalAmount = $newRate * $tenant['daystay'];
    }

    // Update the rate and total amount in the tenant profile
    mysqli_query($dbConnection, "UPDATE tenantprofile SET rate = '$newRate', totalamount = '$totalAmount' WHERE ProfileID = '{$tenant['ProfileID']}'");

    // Fetch all payments with status 'Completed'
    $paymentsResult = mysqli_query($dbConnection, "SELECT * FROM payment WHERE Status = 'Completed' AND EmailID = '{$tenant['email']}'");
    $payments = mysqli_fetch_all($paymentsResult, MYSQLI_ASSOC);

    // Calculate total amount paid by the tenant
    $totalAmountPaid = 0;
    foreach ($payments as $payment) {
        $totalAmountPaid += $payment['Amount'];
    }

    // Calculate the balance for this tenant
    $balance = $tenant['totalamount'] - $totalAmountPaid;

    // Update the balance in the tenant profile
    mysqli_query($dbConnection, "UPDATE tenantprofile SET balance = '$balance' WHERE ProfileID = '{$tenant['ProfileID']}'");

        echo "<div class='modal fade' id='view-user-$index'>";
        echo "  <div class='modal-dialog modal-lg'>";
        echo "    <div class='modal-content'>";
        echo "      <div class='modal-header'>";
        echo "        <h4 class='modal-title'>Tenant Info</h4>";
        echo "        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
        echo "          <span aria-hidden='true'>&times;</span>";
        echo "        </button>";
        echo "      </div>";
        echo "      <div class='modal-body'>";
         echo "<form id='update-form' class='form-sample' method='post' action='../../includes/function/update_script.php' onsubmit='reloadPageOnce()'>";
        echo "  <p class='card-description'>Personal info</p>";
        echo "  <div class='row'>";
        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='fullName'>Name</label>";
        echo "        <input type='text' class='form-control' id='fullName' placeholder='Full Name' value='" . htmlspecialchars($tenant['Name'], ENT_QUOTES, 'UTF-8') . "' readonly />";
        echo "      </div>";
        echo "    </div>";
        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='tenantType'>Tenant Type</label>";
        echo "        <select class='form-control' id='type_of_stay' name='type_of_stay' required>";
        echo "          <option value='Long-term' " . (($tenant['type_of_stay'] === 'Long-term') ? 'selected' : '') . ">Long-term</option>";
        echo "          <option value='Transient' " . (($tenant['type_of_stay'] === 'Transient') ? 'selected' : '') . ">Transient</option>";
        echo "        </select>";
        echo "      </div>";
        echo "    </div>";

        // Add other form fields...
        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='roomNumber'>Room Number</label>";
        echo "        <input type='text' class='form-control' id='room_number' placeholder='Room Number' value='" . htmlspecialchars($tenant['room_number'], ENT_QUOTES, 'UTF-8') . "' readonly />";
        echo "      </div>";
        echo "    </div>";

        echo "    <div class='col-md-3'>";
        echo "      <div class='form-group'>";
        echo "        <label for='updateCheckInDate' style='color: black;'>Update Check-in Date</label>";
        echo "        <input type='date' class='form-control' name='update_check_in_date' id='updateCheckInDate' value='" . $tenant['check_in_date'] . "' required />";
        echo "      </div>";
        echo "    </div>";

        echo "    <div class='col-md-3'>";
        echo "      <div class='form-group'>";
        echo "        <label for='updateCheckOutDate' style='color: black;'>Update Check-out Date</label>";
        echo "        <input type='date' class='form-control' name='update_check_out_date' id='updateCheckOutDate' value='" . $tenant['check_out_date'] . "' required/>";
        echo "      </div>";
        echo "    </div>";

        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='gender'>Gender</label>";
        echo "        <select class='form-control' id='gender' readonly>";
        echo "          <option " . (($tenant['Gender'] === 'Male') ? 'selected' : '') . ">Male</option>";
        echo "          <option " . (($tenant['Gender'] === 'Female') ? 'selected' : '') . ">Female</option>";
        echo "        </select>";
        echo "      </div>";
        echo "    </div>";

        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='age'>Age</label>";
        echo "        <input type='text' class='form-control' id='age' placeholder='Age' value='" . $tenant['Age'] . "' readonly />";
        echo "      </div>";
        echo "    </div>";

        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='email'>Email</label>";
        echo "        <input type='text' class='form-control' id='email' name='email' placeholder='name@gmail.com' value='" . $tenant['email'] . "' readonly />";
        echo "      </div>";
        echo "    </div>";

        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='address'>Address</label>";
        echo "        <input type='text' class='form-control' id='address' placeholder='Address' value='" . $tenant['Address'] . "' readonly />";
        echo "      </div>";
        echo "    </div>";

        echo "    <div class='col-md-6'>";
        echo "      <div class='form-group'>";
        echo "        <label for='contactNumber'>Contact Number</label>";
        echo "        <input type='text' class='form-control' id='contactNumber' placeholder='Contact Number' value='" . $tenant['ContactNumber'] . "' readonly />";
        echo "      </div>";
        echo "    </div>";
      
        echo "    <div class='col-md-2'>";
        echo "      <div class='form-group'>";
        echo "        <label for='rate'>Rate Per Stay</label>";
        echo "        <input type='number' class='form-control' id='newRate' name='rate' value='" . $newRate . "' readonly />";
        echo "      </div>";
        echo "  </div>";
        echo "    <div class='col-md-2'>";
        echo "      <div class='form-group'>";
        echo "        <label for='totalamount'>Total Bills</label>";
        echo "        <input type='text' class='form-control' id='totalamount' name='totalamount' value='" . number_format($totalAmount, 2) . "' readonly />";
        echo "      </div>";
        echo "  </div>";
        echo "    <div class='col-md-2'>";
        echo "      <div class='form-group'>";
        echo "        <label for='balance'>Balance</label>";
        echo "        <input type='number' class='form-control' id='balance' name='balance' value='" . $balance . "' readonly />";
        echo "      </div>";
        echo "    </div>";
        echo "  </div>";

 // Fetch payment schedule only if tenant is long-term
    if ($tenant['type_of_stay'] === 'Long-term') {
        $tenantEmail = $tenant['email'];
        $paymentScheduleResult = mysqli_query($dbConnection, "SELECT * FROM paymentschedule WHERE EmailID = '$tenantEmail'");
        $paymentSchedule = mysqli_fetch_all($paymentScheduleResult, MYSQLI_ASSOC);

        // Display payment schedule
        echo "  <p class='card-description'>Payment Schedule</p>";
        echo "  <div class='table-responsive'>";
        echo "    <table class='table table-striped'>";
        echo "      <thead>";
        echo "        <tr>";
        echo "          <th>MonthYear</th>";
        echo "          <th>Amount</th>";
        echo "          <th>Status</th>";
        echo "          <th>Rent Due Date</th>";
        echo "        </tr>";
        echo "      </thead>";
        echo "      <tbody>";
        foreach ($paymentSchedule as $payment) {
            echo "<tr>";
            echo "<td>{$payment['MonthYear']}</td>";
            echo "<td>{$payment['Amount']}</td>";
            echo "<td>{$payment['Status']}</td>";
           echo "<td>" . date('F j, Y', strtotime($payment['rent_due_date'])) . "</td>";

            echo "</tr>";
        }
        echo "      </tbody>";
        echo "    </table>";
        echo "  </div>"; // Closing div for table-responsive
    }
        echo "  <br>";

        echo "  <p class='card-description'>Emergency Contact</p>";

        echo "  <div class='col-md-6'>";
        echo "    <div class='form-group'>";
        echo "      <label for='emergencyContactName'>Name</label>";
        echo "      <input type='text' class='form-control' id='emergencyContactName' placeholder='Full Name' value='" . $tenant['GuardianName'] . "' readonly />";
        echo "    </div>";
        echo "  </div>";

        echo "<div class='row'>";
        echo "  <div class='col-md-6'>";
        echo "    <div class='form-group'>";
        echo "      <label for='emergencyNumber'>Emergency Number</label>";
        echo "      <input type='text' class='form-control' id='emergencyNumber' placeholder='Emergency Number' value='" . $tenant['EmergencyNumber'] . "' readonly />";
        echo "    </div>";
        echo "  </div>";

            // Buttons on the left and Contract button on the right
        echo "<div class='col-md-12 col-6 d-flex flex-md-row flex-column justify-content-between'>";
        echo "  <div>";
        echo "    <button type='button' class='btn btn-success mb-2 mb-md-14' onclick='generateContract($index)'>Contract</button>";
        echo "    <button type='button' class='btn btn-danger mb-2 mb-md-14' onclick='removeTenant($index)'>Remove Tenant</button>";
        echo "    <input type='hidden' id='removeTenantEmail-$index' value='{$tenant['email']}' />";
        echo "  </div>";
        echo "  <div>";
        echo "    <button type='submit' name='update_submit' id='submit-btn' class='btn btn-primary mb-2 mb-md-14' onclick='showSuccessToast()'>Update</button>";

        echo "  </div>";
        echo "</div>";



        echo "  </div>";
        echo "  </div>";

        echo " </form>"; 

        echo "      </div>"; // Closing div for modal-body
        echo "    </div>"; // Closing div for modal-content
        echo "  </div>"; // Closing div for modal-dialog
        
      
    }
    ?>
           
                <?php include '../footer.php'; ?>
                <?php include '../scripts.php'; ?>
            </div>
        </div>
    </div>
    
 
<script>

            // Function to calculate remaining time and display
            function calculateRemainingTime(checkInDate, checkOutDate, index) {
                var currentDate = new Date();
                var checkInDateObj = new Date(checkInDate);
                var checkOutDateObj = new Date(checkOutDate);

                // If current date is before check-in date, set check-in date as the starting point
                var startDate = currentDate < checkInDateObj ? checkInDateObj : currentDate;

                var timeDiff = checkOutDateObj - startDate;
                var remainingDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                // Calculate remaining weeks and months
                var remainingWeeks = Math.floor(remainingDays / 7);
                var remainingMonths = Math.floor(remainingDays / 30);

                // Display remaining time in the corresponding table cell
                var element = document.getElementById('remainingTime-' + index);
                if (remainingDays > 30) {
                    element.innerHTML = remainingMonths + " Month(s)";
                } else if (remainingDays > 7) {
                    element.innerHTML = remainingWeeks + " Week(s)";
                } else {
                    element.innerHTML = remainingDays + " Day(s)";
                }

                // Apply color based on the remaining days
                if (remainingDays <= 7) {
                    element.style.color = 'red'; // If days are almost up, set color to red
                } else {
                    element.style.color = 'green'; // If days are long, set color to green
                }
            }

        // Loop through tenants and calculate remaining time for each
        <?php
        foreach ($tenants as $index => $tenant) {
            echo "calculateRemainingTime('{$tenant['check_in_date']}', '{$tenant['check_out_date']}', $index);";
        }
        ?>
        function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }
    const currentDate = formatDate(new Date().toISOString());
function generateContract(index) {
    var tenantData = <?php echo json_encode($tenants); ?>;
    var tenant = tenantData[index];
    var adminEmail = "<?php echo htmlspecialchars($adminEmail, ENT_QUOTES, 'UTF-8'); ?>";
     // Get the current date and time
     var currentDateTime = new Date().toLocaleString('en-US', {
        timeZone: 'Asia/Manila',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    });

    
    // Format for the contract
 var contractContent = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
           
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 20px;
            
            margin: auto;
        }

        h1 {
            text-align: center;
            color: #333;
             margin-bottom: 30px;
        }

        p {
             font-size:11px;
            margin-bottom: 15px;
            color: #555;
        }

        strong {
            font-weight: bold;
            color: #333;
        }

        hr {
            border: 0.5px solid #ddd;
            margin: 20px 0;
        }

        .signature {
            margin-top: 20px;
        }

        .signature strong {
            display: block;
            margin-top: 20px;
        }
    </style>
</head>
<body>


<h1>DORMITORY AGREEMENT</h1>
<h5 style="text-align:center;">Generated By: ${adminEmail}</h5>

<p style="text-align:center;">This Agreement is entered into on <strong>${currentDateTime}</strong>, between the landlord and the tenant.</p>

<p>
    <strong>Dormitory Name: <?php echo htmlspecialchars($dormname, ENT_QUOTES, 'UTF-8'); ?></strong><br>
    <strong>Contact No.: <?php echo htmlspecialchars($contactnumber, ENT_QUOTES, 'UTF-8'); ?></strong><br>
</p>
<p>
    <strong>Tenant Name: ${tenant['Name']}</strong><br>
    <strong>Address: ${tenant['Address']}</strong><br>
    <strong>Contact No. ${tenant['ContactNumber']}</strong>
</p>

<hr>

<p><strong>1. TERM OF OCCUPANCY:</strong><br>
    The term of this Agreement shall commence on <strong>${formatDate(tenant['check_in_date'])}</strong> and end on <strong>${formatDate(tenant['check_out_date'])}</strong>. The Tenant agrees to vacate the premises by <strong>${formatDate(tenant['check_out_date'])}</strong>, unless a new agreement is reached.
</p>

<p><strong>2. RENT PAYMENT:</strong><br>
    The monthly rent for the dormitory is <strong>${tenant['rate']}</strong>, payable on or before the Due date of each month. Payments are to be made to the landlord in [Cash or online payment].
</p>

<p><strong>3. UTILITIES:</strong><br>
    The rent includes/excludes utilities such as electricity, water, internet, etc.
</p>

<p><strong>4. USE OF PREMISES:</strong><br>
    The premises shall be used exclusively for residential purposes. The Tenant agrees not to sublet the premises or assign this Agreement without the written consent of the Landlord.
</p>

<p><strong>5. MAINTENANCE:</strong><br>
    The Tenant agrees to maintain the premises in good condition and promptly report any damages or necessary repairs to the Landlord.
</p>

<p><strong>6. HOUSE RULES:</strong><br>
    The Tenant agrees to abide by any house rules provided by the Landlord, which may include noise restrictions, guest policies, and other reasonable regulations.
</p>

<p><strong>7. TERMINATION:</strong><br>
    Either party may terminate this Agreement with written notice of (7) days prior to the intended termination date.
</p>

<p><strong>8. INSPECTION:</strong><br>
    The Landlord reserves the right to inspect the premises with reasonable notice to ensure compliance with the terms of this Agreement.
</p>

<p><strong>9. GOVERNING LAW:</strong><br>
    This Agreement shall be governed by and construed in accordance with the laws of the Philippines.
</p>

<hr>

<div class="signature">
    <p><strong>IN WITNESS WHERE OF, the parties here to have executed this Dormitory Agreement as of the date first above written.</strong></p>

    <div style="display: flex; justify-content: space-between;">
        <div style="text-align: left;">
            <p><strong>Landlord/Owner:</strong></p>
            <p>________________________________________</p>
            <p style="margin-left: 40px; font-weight: bold;">Signature over Printed Name</p>
        </div>
        <div style="text-align: right;">
            <p style="margin-right: 210px;"><strong>Tenant:</strong></p>
            <p>________________________________________</p>
            <p style="margin-right: 40px; font-weight: bold;">Signature over Printed Name</p>
        </div>
    </div>
</div>




</body>
</html>
`;

 // Store the contract in the contracts table
 var xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_contracts.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Contract stored successfully
            console.log('Contract stored in the contracts table.');

            // Generate PDF and open preview window after contract is stored
            var element = document.createElement('div');
            element.innerHTML = contractContent;

            html2pdf(element, {
                margin: 10,
                filename: `Dormitory_Contract_${tenant['Name']}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            });

            // Open a new window for contract preview
            var previewWindow = window.open('', '_blank');
            previewWindow.document.write(`
                <html lang="en">
                <head>
                    <style>
                        /* Add any additional styling here if needed */
                    </style>
                </head>
                <body>
                    ${contractContent}
                </body>
                </html>
            `);
        }
    };
    xhr.send('tenant_email=' + encodeURIComponent(tenant['email']) +
             '&contract_content=' + encodeURIComponent(contractContent) +
             '&dorm_name=' + encodeURIComponent("<?php echo htmlspecialchars($dormname, ENT_QUOTES, 'UTF-8'); ?>") +
             '&contact_number=' + encodeURIComponent("<?php echo htmlspecialchars($contactnumber, ENT_QUOTES, 'UTF-8'); ?>") +
             '&check_in_date=' + encodeURIComponent(formatDate(tenant['check_in_date'])) +
             '&check_out_date=' + encodeURIComponent(formatDate(tenant['check_out_date'])) +
             '&rate=' + encodeURIComponent(tenant['rate'])+
             '&Name=' + encodeURIComponent(tenant['Name'])
             );
}
 
document.addEventListener('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
            event.preventDefault();

            // Get the currently open modal's index
            var openModal = document.querySelector('.modal.show');
            if (openModal) {
                var index = parseInt(openModal.id.split('-')[2]);
                if (!isNaN(index)) {
                    generateContract(index);
                }
            }
        }
    });

function removeTenant(index) {
    var tenantEmail = document.getElementById('removeTenantEmail-' + index).value;

    // Show SweetAlert to prompt user for confirmation
    swal({
        title: 'Are you sure you want to remove this tenant?',
        icon: 'warning',
        buttons: {
            cancel: {
                text: 'Cancel',
                value: false,
                visible: true,
                className: '',
                closeModal: true,
            },
            confirm: {
                text: 'Remove',
                value: true,
                visible: true,
                className: '',
                closeModal: false,
            }
        },
    }).then((willRemove) => {
        if (willRemove) { // If user confirmed removal
            // Prompt user for removal reason
            swal({
                title: 'This action canoont be undone',

                inputPlaceholder: 'Select reason',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Submit',
                inputValidator: function (value) {
                    return new Promise(function (resolve, reject) {
                        if (value !== '') {
                            resolve();
                        } else {
                            reject('You need to select a reason');
                        }
                    });
                }
            }).then(function (result) {
                // User selected a reason, proceed with removal
                var reason = result.value; // Get the selected reason
                // Perform AJAX request to remove the tenant with the selected reason
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'remove_tenant.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Reload the page after removal
                        location.reload();
                    }
                };
                xhr.send('remove_tenant_submit=true&tenant_email=' + encodeURIComponent(tenantEmail) + '&removal_reason=' + encodeURIComponent(reason));
            }).catch(function (error) {
                // User either canceled or didn't select a reason, do nothing
            });
        }
    });
}



   // Function to handle success message display
   function showSuccessToast(message) {
        swal({
            title: 'Success!',
            text: message || 'Operation completed successfully.',
            icon: 'success',
            timer: 20000, // Display the message for 5 seconds
        });
    }

    </script>
    
   

</body>

</html>