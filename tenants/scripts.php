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
    <script>
  $(function () {
    $("#example1").DataTable();
      $("#example4").DataTable();
      $("#example3").DataTable({
        "scrollX": true,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
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
    function openPaymentPage() {
        // Specify the URL you want to open in a new tab
        var paymentPageUrl = '../../Guest/Monthly-payment.php';
        
        // Open the URL in a new tab
        window.open(paymentPageUrl, '_blank');
    }
</script>
<script>
    function openFullPaymentPage() {
        // Specify the URL you want to open in a new tab
        var paymentPageUrl = '../../Guest/Full-payment.php';
        
        // Open the URL in a new tab
        window.open(paymentPageUrl, '_blank');
    }
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