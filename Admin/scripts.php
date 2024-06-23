  <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../../assets/js/off-canvas.js"></script>
    <script src="../../assets/js/hoverable-collapse.js"></script>
    <script src="../../assets/js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <!-- End custom js for this page -->
<script src="../../assets/js/file-upload.js"></script>
<script src="../../assets/js/chart.js"></script>
 <script src="../../assets/vendors/chart.js/Chart.min.js"></script>
      <!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
   <script src="../../assets/js/jquery.inputmask.bundle.js"></script>
 <script src="../../assets/js/inputmask.js"></script>
    <script src="../../assets/js/vendor.bundle.base1.js"></script>
    <script src="../../assets/js/jquery.inputmask.bundle.js"></script>
    <script src="../../assets/js/sweetalert.min.js"></script>
     <script src="../../assets/js/alerts.js"></script>
     <script src="../../assets/js/jquery.toast.min.js"></script>
     <script src="../../assets/js/toastDemo.js"></script>

    <script>
  $(function () {
    $("#example1").DataTable();
      $("#example4").DataTable();
      $("#example3").DataTable({
        "scrollX": true,
    });
    $('#example2').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": false,
      "autoWidth": false,
    });
      $('#example').DataTable( {
          dom: 'Bfrtip',
          buttons: [
            'print'
          ]
      } );
  }); 
</script>
<script>
var slideIndex = 1;
showDivs(slideIndex);

function plusDivs(n) {
  showDivs(slideIndex += n);
}

function currentDiv(n) {
  showDivs(slideIndex = n);
}

function showDivs(n) {
  var i;
  var x = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  if (n > x.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = x.length}
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" w3-red", "");
  }
  x[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " w3-red";
}
</script>
 <script>
        // Use the fetched data directly (no need for AJAX call)
        var incomeData = <?php echo json_encode($incomeData); ?>;

        // Function to update the chart with the fetched data
        function updateChart(incomeData) {
        console.log(incomeData);  // Log the incomeData for inspection

        var labels = incomeData.map(item => item.date);
        var years = incomeData.map(item => item.year); // New line to get years
        var amounts = incomeData.map(item => item.amount);

        var ctx = document.getElementById('yearCharts').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Income',
                    data: amounts,
                    backgroundColor: 'rgba(187, 77, 250)',
                    borderColor: 'rgba(80, 4, 122)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Add the year in the lower part of the chart
        var yearDiv = document.createElement('div');
            yearDiv.innerHTML = years.join('<br>'); // Display years with line breaks
            yearDiv.style.textAlign = 'center';
            yearDiv.style.fontWeight = 'bold';
            yearDiv.style.fontSize = '16px'; // You can adjust the font size
            yearDiv.style.marginBottom = '10px'; // Add margin between the years
        

            // Append the button below the chart
            var buttonContainer = document.createElement('div');
            buttonContainer.style.marginTop = '10px'; // Add margin between the year and the button
          
            ctx.canvas.parentNode.appendChild(buttonContainer);
    }


        // Call the function to update the chart with the fetched data
        updateChart(incomeData);
    </script>

<script>
    new Chart(document.getElementById("yearChart"), {
        type: 'bar',
        data: {
            labels: ['January','February','March','April','May','June','July','August','September','October','November','December'],
            datasets: [
                { 
                    data: [1040,1201,1201,1358,1201,1054,1257,1354,1254,1214,1047,1201],                                   
                    label: "Monthly Income Report",
                    backgroundColor: "purple",
                    fill: true
                }
            ]
        },
        options: {
            legend: { display: true },
            title: {
                display: true,
                text: 'Monthly Income Report'
            }
        }
    });
</script>


  <script>
   function Email() {
        window.location.href = 'change_email.php';
    }
    function togglePassword() {
        var passwordInput = document.getElementById('password');
        passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
    }
    function togglePassword1() {
        var passwordInput = document.getElementById('Conpassword');
        passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
    }
</script>



<script>
  // JavaScript to populate modal fields when the "MANAGE" button is clicked
  $('.view-btn').on('click', function() {
    var modal = $(this).closest('.modal'); // Find the closest parent modal

    var tenantData = JSON.parse($(this).data('tenant'));

    // Populate modal fields with data from the clicked tenant
    modal.find('#fullName').val(tenantData.Name);
    modal.find('#type_of_stay').val(tenantData.type_of_stay);
    modal.find('#updateCheckInDate').val(tenantData.check_in_date);
    modal.find('#updateCheckOutDate').val(tenantData.check_out_date);

    // Set the email as a data attribute on the form for submission
    modal.find('#update-form').data('email', tenantData.email);

    // Show the modal
    modal.modal('show');
  });

  // JavaScript to handle form submission for each modal
  $('.update-form').on('submit', function(event) {
    event.preventDefault();

    // Retrieve the email from the data attribute
    var email = $(this).data('email');

    // Perform an AJAX request to submit the form data to the server
    $.ajax({
      type: 'POST',
      url: 'update_script.php',
      data: {
        email: email,
        type_of_stay: $(this).find('#type_of_stay').val(),
        update_check_in_date: $(this).find('#updateCheckInDate').val(),
        update_check_out_date: $(this).find('#updateCheckOutDate').val()
      },
      success: function(response) {
        // Handle the server response if needed
        console.log(response);
        // Close the modal
        $(this).modal('hide');
      },
      error: function(error) {
        // Handle errors if needed
        console.error(error);
      }
    });
  });
</script>

<script>
    $(document).ready(function() {
        $('.edit-room-btn').click(function() {
        var roomId = $(this).data('room-id');

        // Retrieve other fields' values based on the row and populate the modal
        var roomNumber = $(this).closest('tr').find('td:eq(0)').text();
        var roomName = $(this).closest('tr').find('td:eq(1)').text();
        var description = $(this).closest('tr').find('td:eq(2)').text();
        var ratePerNight = $(this).closest('tr').find('td:eq(4)').text();
        var ratePerMonth = $(this).closest('tr').find('td:eq(5)').text();
        var downPayment = $(this).closest('tr').find('td:eq(6)').text();
        var roomType = $(this).closest('tr').find('td:eq(7)').text();
        var numOfBeds = $(this).closest('tr').find('td:eq(8)').text();
        

        
        // Set the values in the modal form
        $('#room_number').val(roomNumber);
        $('#room_name').val(roomName);
        $('#description').val(description); 
        $('#rate_per_night').val(ratePerNight);
        $('#rate_per_month').val(ratePerMonth);
        $('#down_payment').val(downPayment);

        // Set the selected option for the Room Type dropdown
        $('#room_type').val(roomType);
        $('#num_of_beds').val(numOfBeds); 

        // Set the room ID as a hidden input for the update process
        $('#editRoomId').val(roomId);
    });
    // Edit Room Type Modal
    $('.edit-btn').click(function() {
        var roomTypeId = $(this).data('room-id');
        var roomTypeName = $(this).closest('tr').find('td:eq(0)').text();

        // Set the values in the modal form
        $('#editRoomTypeId').val(roomTypeId);
        $('#roomTypeName').val(roomTypeName);
    });
    });
    
    </script>
    
<script>
    $(document).ready(function() {
        $('[data-toggle="popover"]').popover({
            trigger: 'hover',
            html: true,
        });
    });
</script>



<script>
    function viewRoom(roomId, images, roomName) {
        // Clear previous content
        $('#view-modal-images .carousel-inner').empty();

        // Load images dynamically for the clicked row
        var imageArray = images.split(',');

        imageArray.forEach(function (image, index) {
            var activeClass = index === 0 ? 'active' : '';
            $('#view-modal-images .carousel-inner').append('<div class="carousel-item ' + activeClass + '"><img class="d-block mx-auto img-fluid custom-img" style="max-height: 300px;" src="../uploads/' + image + '" alt="Slide ' + (index + 1) + '"></div>');
        });

        // Open the modal
        $('#viewRoomModal').modal('show');
    }
</script>

 <!-- Include jQuery before Evo Calendar and FullCalendar scripts -->

    <!-- Include Evo Calendar scripts -->
    <script src="evo-calendar.min.js"></script>


   
    <script>
        $(document).ready(function () {
            // Define the evoCalendarEvents array
            var evoCalendarEvents = <?php echo json_encode($evoCalendarEvents); ?>;
            var formattedEvents = [];

            // Format the events array for Evo Calendar
            for (var i = 0; i < evoCalendarEvents.length; i++) {
                formattedEvents.push(evoCalendarEvents[i]);
            }

            // Initialize Evo Calendar
            $('#calendar').evoCalendar({
                // Your existing Evo Calendar configuration
                calendarEvents: formattedEvents,
            });

            // Update calendar events dynamically
            $('#calendar').evoCalendar('updateCalendarEvents', formattedEvents);
        });
    </script>
    
         <script>
        // Function to allow only numeric input for number type fields
        function allowOnlyNumericInput(inputField) {
            inputField.addEventListener('keypress', function(event) {
                const keyCode = event.keyCode;
                if (!(keyCode >= 48 && keyCode <= 57) && // Digits 0-9
                    !(keyCode >= 96 && keyCode <= 105) && // Numeric keypad
                    keyCode !== 8 && // Backspace
                    keyCode !== 9 && // Tab
                    keyCode !== 37 && // Left arrow
                    keyCode !== 39 && // Right arrow
                    keyCode !== 46 // Delete
                ) {
                    event.preventDefault();
                }
            });
        }

        // Call the function for each number type input field
        document.addEventListener('DOMContentLoaded', function () {
            const numberInputs = document.querySelectorAll('input[type="number"]');
            numberInputs.forEach(function(input) {
                allowOnlyNumericInput(input);
            });
        });
    </script>


