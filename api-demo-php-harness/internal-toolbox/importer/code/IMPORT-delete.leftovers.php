<?php
//this script deletes all the "leftover" files used by the various scripts

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

rename("../combined.tokens.CC.csv","../combined.tokens.CC.OLD.csv");
rename("../combined.tokens.ACH.csv","../combined.tokens.ACH.OLD.csv");
rename("../undo.import.CC.csv","../undo.import.CC.OLD.csv");
rename("../undo.import.ACH.csv","../undo.import.ACH.OLD.csv");
//rename("../code/dataset.csv","../code/dataset.OLD.csv");

$leftovers1 = 'CUSTOMER*';
$leftovers2 = 'AlarmBiller*';
$leftovers3 = 'failure*';
$leftovers4 = 'PAYMETHODS*';
$leftovers5 = 'CLIENT*';
$leftovers6 = 'Sedona*';
$leftovers7 = 'tokens*';
$leftovers8 = 'output*';
$leftovers9 = 'data*';
$leftovers10 = 'PAY*';
$leftovers11 = 'SCHEDULES*';

array_map("unlink", glob('../' . $leftovers1));
array_map("unlink", glob('../code/' . $leftovers1));
array_map("unlink", glob('../' . $leftovers2));
array_map("unlink", glob('../' . $leftovers3));
array_map("unlink", glob('../' . $leftovers4));
array_map("unlink", glob('../' . $leftovers5));
array_map("unlink", glob('../' . $leftovers6));
array_map("unlink", glob('../' . $leftovers7));
array_map("unlink", glob('../' . $leftovers8));
array_map("unlink", glob('../' . $leftovers9));
array_map("unlink", glob('../' . $leftovers10));
array_map("unlink", glob('../' . $leftovers11));

unlink('customers.delete.csv');
unlink('customer.list.csv');
unlink('paymethod.list.csv');
unlink('schedule.list.csv');
unlink('good.csv');
unlink('tokens.CC.csv');
unlink('tokens.ACH.csv');
unlink('output*.csv');
unlink('data*.csv');
//unlink('combined.CC.csv');
//unlink('combined.ACH.csv');

sleep(1);
$message = "All the leftover files have been successfully deleted:";
echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";

?>