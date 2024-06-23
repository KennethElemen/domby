<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/error.css">
    <link rel="stylesheet" href="../assets/css/unsuccessful.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" />
</head>
<body>
    
<div class="wrapperAlert error" style="height: 550px;">

  <div class="contentAlert">

    <div class="topHalf">

       <p>
        <svg viewBox="0 0 512 512" width="100" title="times-circle" class="animated-check">
          <rect width="100%" height="100%" fill="#FF6B6B"/> <!-- Red background -->
          <path d="M512 71.723L440.277 0 256 184.277 71.723 0 0 71.723 184.277 256 0 440.277 71.723 512 256 327.723 440.277 512 512 440.277 327.723 256z" />
        </svg>
      </p>
      <h1>Error</h1>

     <ul class="bg-bubbles">
       <li></li>
       <li></li>
       <li></li>
       <li></li>
       <li></li>
       <li></li>
       <li></li>
       <li></li>
       <li></li>
       <li></li>
     </ul>
    </div>

    <div class="bottomHalf">
      <p>Reservation unsuccessful. An error occurred.</p>
      
      <p>There might be an issue with your reservation. Please double-check the information provided.</p>
      <p>ensure that the email you used is valid and not already registered.</p>
    

      <button id="alertMO">Go Back</button>
    </div>


  </div>        

</div>
<script>
    document.getElementById('alertMO').addEventListener('click', function() {
        window.location.href = '../Guest/Reservation.php';
    });
</script>
</body>
</html>
