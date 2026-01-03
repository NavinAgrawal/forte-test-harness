<html>
<?php
require_once __DIR__ . '/config/bootstrap.php';
      $APILoginID         = forte_config('api_login_id');
      $SecureTransKey     = 'L2iHpBwAthcglC';   
      $ordernumber        = 'COR-199287';
      $totalamount        = 10.95;
      $method             = 'sale';
      $version            = '1.0';
      $millitime          = microtime(true) * 1000;
      $utc                = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
      $data               = "$APILoginID|$method|$version|$totalamount|$utc|$ordernumber||";
      $hash               = hash_hmac('md5',$data,$SecureTransKey);
?>
<head>
      <script>function oncallback(e) { $('#message').html(e.data);} </script>
      <!--script type="text/javascript" src=https://checkout.forte.net/v1/js></script -->          <!-- production -->
      <script type="text/javascript" src=https://sandbox.forte.net/checkout/v1/js></script>    <!-- sandbox -->
      <script src=https://code.jquery.com/jquery-1.11.0.min.js></script>
</head>
<body>
<div id="message" style="background-color:#ffffff"></div>
      <center>    
      <button api_login_id="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"
      version_number="1.0"
      method="sale"
      callback="oncallback"
      total_amount=<?php echo $totalamount;?>
      utc_time=<?php echo $utc;?>
      signature=<?php echo $hash;?>
      order_number="COR-199287"
      consumer_id="123ABC"
      save_token="true"
      >Pay Now</button>
</body>
</html>
