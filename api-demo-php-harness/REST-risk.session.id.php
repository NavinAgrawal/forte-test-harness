<html>
<body>
<?php
require_once __DIR__ . '/config/bootstrap.php';
$a = uniqid();
$org = rawurlencode(forte_config('organization_id'));
echo $a;
?>
<script type="text/javascript" src="https://img3.forte.net/fp/tags.js?<?php echo $org; ?>=xdzpgyj7&session_id=<?php echo $a;?>&pageid=1">
</script>
<noscript>
<iframe style="width: 100px; height: 100px; border: 0;position: absolute; top: -5000px;" src="https://img3.forte.net/tags?<?php echo $org; ?>=xdzpgyj7&session_id=<?php echo $a;?>&pageid=1">
</iframe>
</noscript>
</body>
</html>
