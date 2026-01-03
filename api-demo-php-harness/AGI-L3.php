<?php
require_once __DIR__ . '/config/bootstrap.php';
$pg_action_url = forte_pg_action_url();
$pg_merchant_id = forte_config('pg_merchant_id');
$pg_password = forte_config('pg_password');
$api_login_id = forte_config('api_login_id');
?>
<html>
<center>
<form method='Post' action='<?php echo htmlspecialchars($pg_action_url, ENT_QUOTES); ?>'>   <!-- production/sandbox via config -->
	<input type='hidden' name='pg_merchant_id' value='<?php echo htmlspecialchars($pg_merchant_id, ENT_QUOTES); ?>'/>
	<input type='hidden' name='pg_password' value='<?php echo htmlspecialchars($pg_password, ENT_QUOTES); ?>'/ >
	<!--input type='hidden' name='ecom_merchant_api_id' value='<?php echo htmlspecialchars($api_login_id, ENT_QUOTES); ?>'/ -->
	<input type='hidden' name='pg_transaction_type' value='10'/>  <!-- trans type 30 for onetime token -->
	<input type='hidden' name='pg_total_amount' value='0.06'/>
	<input type='hidden' name='pg_sales_tax_amount' value='0.00'/ -->
	<!--input type='hidden' name='pg_convenience_fee' value='1.95'/ -->
	<!--input type='hidden' name='pg_client_id' value='LEVEL-3_01'/ -->
	<!--input type='hidden' name='pg_customer_token' value=''/-->
	<!--input type='hidden' name='pg_payment_method_id' value=''/-->
	<!--input type='hidden' name='pg_payment_token' value=''/-->
	<!--input type='hidden' name='external_transaction_id' value='111111111111111111111'/-->	
	<!--input type='hidden' name='pg_onetime_token' value='ott_rWWPTaWKTxqam05RGqL6DQ'/-->
	<!--input type='hidden' name='pg_original_trace_number' value='a2f84a6f-61f4-4535-b2ec-9214c6a747c4'/>
	<input type='hidden' name='pg_original_authorization_code' value='072715'/ -->
	<!--input type='hidden' name='pg_schedule_quantity' value='2'/>
	<input type='hidden' name='pg_schedule_frequency' value='20'/>
	<input type='hidden' name='pg_schedule_recurring_amount' value='.01'/>
	<input type='hidden' name='pg_schedule_start_date' value='09/01/2021'/>
	<!--input type='hidden' name='pg_merchant_recurring' value='true'/ -->
	<!--input type='hidden' name='pg_procurement_card' value='true'/>
	<input type='hidden' name='pg_customer_acct_code' value='2236558'/>
	<!--input type='hidden' name='pg_cc_swipe_data' value='%B377936419194774^THANK YOU                 ^2706521180700833                ?;377936419194774=270652118070083300000?'/ -->
	<!--input type='hidden' name='pg_cc_enc_swipe_data' value='%B2223000050000014^TEST-VOID/TEST^251210100000000000000000000?;2223000050000014=2512101000000000?|0006|D56E030681668F298CD0C2C52D63687469D22E1AB5BD0F9199B2E13E5906F84648ACAF4780F06E385625C2181AEA7F48F596837E69BB830EF54BCBD7073AD65F|677AC56D46A1CF257D5287EC5D0E682B019BF56EE144BE52A12C6C6F7F9512EEE53B95741EA78CB7||61403000|B7EF9545E4D6ED2B5C55FE128EF97F955FE68FCFD9D36C8A41461F886831343908704E699C540156EBD45526587DCF1B1A28CD97711F80C9|B43A772071218AA|56FE894F6037A08A|9011880B43A772000250|||'/>
	<input type='hidden' name='pg_cc_enc_decryptor' value='21079802'/-->
	<input type='hidden' name='ecom_payment_card_type' value='mast'/>
	<input type='hidden' name='ecom_payment_card_name' value='James D Ivey'/>
	<input type='hidden' name='ecom_payment_card_number' value='**************'/>
	<input type='hidden' name='ecom_payment_card_expdate_month' value='01'/>
	<input type='hidden' name='ecom_payment_card_expdate_year' value='2030'/>
	<input type='hidden' name='ecom_payment_card_verification' value='157'/ -->
	<!--input type='hidden' name='ecom_payment_check_trn' value='111000614'/>  <!--091000019 -->
	<!--input type='hidden' name='ecom_payment_check_account' value='*****************'/>
	<input type='hidden' name='ecom_payment_check_account_type' value='C'/ -->
	<!--input type='hidden' name='pg_entry_class_code' value='WEB'/-->
	<input type='hidden' name='ecom_billto_postal_name_first' value='James'/>
	<input type='hidden' name='ecom_billto_postal_name_last' value='Ivey'/>
	<!--input type='hidden' name='ecom_billto_postal_street_line1' value='500 W Bethany Dr Suite 200'/>
	<input type='hidden' name='ecom_billto_postal_street_line2' value=''/>
	<input type='hidden' name='ecom_billto_postal_city' value='Allen'/>
	<input type='hidden' name='ecom_billto_postal_stateprov' value='TX'/ -->
	<input type='hidden' name='ecom_billto_postal_postalcode' value='75013'/>
	<!--input type='hidden' name='ecom_billto_postal_countrycode' value=''/ -->
	<!--input type='hidden' name='ecom_billto_telecom_phone_number' value=''/>
	<input type='hidden' name='ecom_billto_online_email' value='integration@forte.net' /-->
	<!--input type='hidden' name='pg_billto_postal_name_company' value='Widgets Inc'/>
	<!--input type='hidden' name='pg_shipto_postal_name_company' value=''/>
	<input type='hidden' name='pg_shipto_name' value=''/>
	<input type='hidden' name='pg_shipto_street_1' value=''/>
	<input type='hidden' name='pg_shipto_street_2' value=''/>
	<input type='hidden' name='pg_shipto_city' value=''/>
	<input type='hidden' name='pg_shipto_stateprov' value=''/>
	<input type='hidden' name='pg_shipto_postalcode' value=''/>
	<input type='hidden' name='ecom_shipto_postal_countrycode' value=''/>
	<input type='hidden' name='ecom_shipto_telecom_phone_number' value=''/>
	<input type='hidden' name='ecom_shipto_telecom_fax_number' value=''/>
	<input type='hidden' name='ecom_shipto_online_email' value=''/ -->
	<!--input type='hidden' name='ecom_consumerorderid' value='ACCTVERIFY'/>
	<!--input type='hidden' name='ecom_walletid' value=''/ -->
	<!--input type='hidden' name='pg_consumer_id' value='ACCT-1234'/>
	<!--input type='hidden' name='pg_merchant_data_1' value=''/>
	<input type='hidden' name='pg_merchant_data_2' value=''/>
	<input type='hidden' name='pg_merchant_data_3' value='custom data 3'/>
	<input type='hidden' name='pg_merchant_data_4' value='custom data 4'/-->
	<!--input type='hidden' name='pg_line_item_header' value='Total_Tax_Amount,Discount_Amount,Duty_Amount,Item_Commodity_Code,Item_Descriptor,Product_Code,Quantity,Unit_of_Measure,Unit_Cost,Discount_Per_Line_Item,Line_Item_Total,Ship_Date'/>
	<input type='hidden' name='pg_line_item_1' value='0.08,3.0,3.0,Iphone15,Iphone15plus,IPH15,1,USD,1.00,20,1.08,241218'/>
	<!--input type='hidden' name='pg_entered_by' value=''/>
	<input type='hidden' name='pg_billto_ssn' value=''/>
	<input type='hidden' name='pg_billto_dl_number' value=''/>
	<input type='hidden' name='pg_billto_dl_state' value=''/>
	<input type='hidden' name='pg_billto_date_of_birth' value=''/>
	<input type='hidden' name='pg_partial_auth_allowed_flag' value=''/>
	<input type='hidden' name='pg_preauth_no_decline_on_fail' value=''/>
	<input type='hidden' name='pg_preauth_decline_on_noanswer' value=''/ -->
	<!--input type='hidden' name='pg_avs_method' value='22000'/>
	<!--input type='hidden' name='pg_mail_or_phone_order' value=''/>
	<input type='hidden' name='pg_customer_ip_address' value=''/-->
	<!--input type='hidden' name='pg_biller_name' value='Biller Name Testing 123456789012345678901234567890'/-->
	<!--input type='hidden' name='pg_create_token' value='cst_'/ -->                <!-- cst_ creates both, mth_ creates paymethod only -->
	<!--input type='hidden' name='pg_cof_transaction_type' value='1'/ -->
	<input type='submit' value='Pay Now'/>
</form>
</html>
