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
        <?php
        // Assuming you have the remaining time available in $remainingTime variable
        if (isset($remainingTime) && $remainingTime > 0) {
            echo '<p>Brute Force Protection Activated.</p>';
            echo '<p>Please try again after ' . $remainingTime . ' seconds.</p>';
        } else {
            echo '<p>Login Failed.</p>';
            echo '<p>It seems there was an issue with your login attempt. Please double-check your username and password. If the problem persists, you can request a password reset or contact our support team for assistance.</p>';
        }
        ?>

        <button id="alertMO" onclick="goBack()">Go Back</button>
    </div>

  </div>        

</div>
<script>
    document.getElementById('alertMO').addEventListener('click', function() {
        window.location.href = '../Guest/login.php';
    });

      function goBack() {
        window.history.back();
    }
</script>
</body>
</html>



