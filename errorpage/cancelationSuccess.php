<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style2.css">
    <link rel="stylesheet" href="../assets/css/successfull.css">
  <link rel="shortcut icon" href="../../assets/images/favicon.png" />
</head>
<body>

<div class="wrapperAlert"   style="height: 550px;">

  <div class="contentAlert">

    <div class="topHalf">

   <p>
        <svg viewBox="0 0 512 512" width="100" title="check-circle" class="animated-check">
          <path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" />
        </svg>
      </p>
      <h1>Congratulations</h1>

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
          <p>Cancelation successful!</p>
          
          <p>Thank you for your cancellation. Your reservation has been successfully canceled.</p>
          
          

          <button id="alertMO">Done</button>
      </div>


  </div>        

</div>
<script>
    document.getElementById('alertMO').addEventListener('click', function() {
        window.location.href = '../index.php';
    });
</script>
</body>
</html>