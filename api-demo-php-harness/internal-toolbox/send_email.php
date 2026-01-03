<?php 	

require_once __DIR__ . '/../config/bootstrap.php';
//FreshDesk API documentation is available here: https://developers.freshdesk.com/api/

include 'classes.php';
include 'functions.php';
	
	ini_set('max_execution_time', 0); // for infinite time of execution 
	$accepted_time_of_last_cf_caller_email_requester_update = '2019-08-07T15:32:48Z';
	$accepted_time_of_last_default_ticket_type_update = '2019-08-07T18:32:22Z';
	
	$FD_credentials_object = new FreshDesk_credentials;
	$FD_email_object=new email_template;
	
	$FD_credentials_object->api_key = forte_config('freshdesk_api_key');
	$FD_credentials_object->domain = forte_config('freshdesk_domain');
	$FD_credentials_object->password = forte_config('freshdesk_password');
	$FD_credentials_object->url = "https://$FD_credentials_object->domain.freshdesk.com/api/v2/tickets/outbound_email";
	$FD_credentials_object->user = $FD_credentials_object->api_key.':'.$FD_credentials_object->password;	
	
	$FD_email_object->agentName = "James Ivey";
	$FD_email_object->custom_fields = array("Incoming Call" => "Yes");
	$FD_email_object->email_config_id = "1000066820";
	$FD_email_object->group_id = "1000154603";
	$FD_email_object->priority = "1";
	$FD_email_object->signature="<table><tr><td><img src=\"https://www.forte.net/EmailSig/forteLogo-sm_csg.jpg\"></td> <td><p><strong>$FD_email_object->agentName</strong><br/>Technical Support Specialist II<br/>integration@forte.net<br/>866-290-5400 Option 5</p></td></tr></table>";
	//$FD_email_object->status = "3";
	$FD_email_object->subject = "Forte Phone Inquiry - Summary";
	
	if (isset($_POST["first_name"])) $FD_email_object->firstName
	= $_POST["first_name"];
	else $FD_email_object->firstName = "";
	
	if (isset($_POST["last_name"])) $FD_email_object->lastName = $_POST["last_name"];
	else $FD_email_object->lastName = "";

	if (isset($_POST["status"])) $FD_email_object->status = $_POST["status"];
	else $FD_email_object->status = "";
	
	if (isset($_POST["company_name"])) $FD_email_object->companyName = $_POST["company_name"];
	else $FD_email_object->companyName = "";
	
	if (isset($_POST["email_address"])) $FD_email_object->contactEmail = $_POST["email_address"];
	else $FD_email_object->contactEmail = "";
	
	if (isset($_POST["caller_category"])) $FD_email_object->callerCategory = $_POST["caller_category"];
	else $FD_email_object->callerCategory = "Account Holder";		
	
	if (isset($_POST["merchant_id"])) $FD_email_object->merchantID = $_POST["merchant_id"];
	else $FD_email_object->merchantID = "";

	if (isset($_POST["tags"])) $FD_email_object->tags = explode(',',$_POST["tags"]);
	else $FD_email_object->tags[] = null;	
		
	if (isset($_POST["issue_question"])) $FD_email_object->issueQuestion = $_POST["issue_question"];
	else $FD_email_object->issueQuestion = "";
	
	if (isset($_POST["type"])) $FD_email_object->type = $_POST["type"];
	else $FD_email_object->type = "";
	
	if (isset($_POST["resolution"])) $FD_email_object->resolution = $_POST["resolution"];
	else $FD_email_object->resolution = "";
		
	$FD_email_object->name = $FD_email_object->firstName . " " . $FD_email_object->lastName . " " . "(from " . $FD_email_object->companyName . ") email: " .$FD_email_object->contactEmail;
		
	$FD_email_object->description = "Hello Merchant,<br/><br/>This ticket was created in reference to the phone call we received from your business.<br/></br/><b>Contact Name: </b>$FD_email_object->name<br/><br/><b>Request/Issue: </b>$FD_email_object->issueQuestion<br/><br/><b>Resolution: </b>$FD_email_object->resolution<br/><br/><br/>Please note: If you have not previously created a user with our support ticket system, you may also receive an email for you to set up a user account which can be used for checking on the status of the tickets previously created. Creating a user is completely optional and not required.<br/><br/>$FD_email_object->signature";
		
	$data="";
	$eol = "\r\n";
	$mime_boundary = md5(time());	
		
	## Code starts here
		
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="email"' . $eol . $eol;
	$data .= "$FD_email_object->contactEmail" . $eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="name"' . $eol . $eol;
	$data .= "$FD_email_object->name" . $eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="subject"' . $eol . $eol;
	$data .= "$FD_email_object->subject" . $eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="priority"' . $eol . $eol;
	$data .= "$FD_email_object->priority" . $eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="status"' . $eol . $eol;
	$data .= "$FD_email_object->status" . $eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="group_id"' . $eol . $eol;
	$data .= "$FD_email_object->group_id" . $eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="email_config_id"' . $eol . $eol;
	$data .= "$FD_email_object->email_config_id" . $eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="description"' . $eol . $eol;
	$data .= "$FD_email_object->description".$eol;
	
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="type"' . $eol . $eol;
	$data .= "$FD_email_object->type" . $eol;

	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="custom_fields[cf_merchantiso_id]"' . $eol . $eol;
	$data .= $FD_email_object->merchantID . $eol;		

	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="custom_fields[cf_caller_email_requester]"' . $eol . $eol;
	$data .= $FD_email_object->callerCategory . $eol;		
	
	foreach ($FD_email_object->tags as $tag)
	{
		$data .= '--' . $mime_boundary . $eol;
		$data .= 'Content-Disposition: form-data; name="tags[]"' . $eol . $eol;
		$data .= $tag . $eol;		
	}
	
	//$data .= '--' . $mime_boundary . $eol;
	//$data .= 'Content-Disposition: form-data; name="attachments[]"; filename="' . $file2->name . '"' . $eol;
	//$data .= "Content-Type: $file2->contentType" . $eol . $eol;
	//$data .= file_get_contents($file2->path) . $eol;
	
	$data .= "--" . $mime_boundary . "--" . $eol . $eol;
	$header[] = "Content-type: multipart/form-data; boundary=" . $mime_boundary;
	
	$returnClass=do_POST_with_CURL($data,$header,$FD_credentials_object->url,$FD_credentials_object->user);
	
	$headers = substr($returnClass->server_output, 0, $returnClass->header_size);
	$response = substr($returnClass->server_output, $returnClass->header_size);
	
	$that = json_decode($response);
	
	if($returnClass->info['http_code'] == 201)
	{
		echo '<pre>';
		echo "Ticket created successfully, the email is given below. \n<br/><br/><br/>";
		echo "Response Headers:<br/><br/>";
		echo $headers."<br/>";
		echo "Response Body:<br/><br/>";
		print_r($FD_email_object->description);
	} 
	else
	{
		if($returnClass->info['http_code'] == 404) 
		{	
			echo "Error, Please check the end point \n" ."<br/><br/><br/>";
		} 
		else 
		{
			echo "Error, HTTP Status Code : " . $returnClass->info['http_code'] . "\n" . "<br/><br/><br/>";
		}

		echo "Headers are ".$headers."<br/><br/>";
		echo "Response are ".$response."<br/><br/><br/>";
	}
	
	//get caller_category
	$returnClass=do_GET_with_CURL('https://' . $FD_credentials_object->domain . '.freshdesk.com/api/v2/ticket_fields?type=custom_dropdown',$FD_credentials_object->user);

	$json_decoded_information = json_decode($returnClass->server_output);
	$counter = 0;
	
/*	foreach ($json_decoded_information as $junk)
	{		
		scan_for_changes_cf_caller_email_requester($junk,$accepted_time_of_last_cf_caller_email_requester_update);		
		$counter++;
	}  */
	
	//get default ticket type
	$returnClass=do_GET_with_CURL('https://' . $FD_credentials_object->domain . '.freshdesk.com/api/v2/ticket_fields?type=default_ticket_type',$FD_credentials_object->user);
	
	$json_decoded_information = json_decode($returnClass->server_output);
	$counter = 0;
	$junk = null;
	
/*	foreach ($json_decoded_information as $junk)
	{		
		scan_for_changes_default_ticket_type($junk, $accepted_time_of_last_default_ticket_type_update);		
		$counter++;
	} */
?>