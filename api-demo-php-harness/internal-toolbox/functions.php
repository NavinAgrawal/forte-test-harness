<?php
	include_once('classes.php');
	
	function do_GET_with_CURL($url,$user):c
	{
		$returnClass = new c;
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_USERPWD, "$user");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$returnClass->server_output = curl_exec($ch);
		$returnClass->info = curl_getinfo($ch);
		$returnClass->header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		
		curl_close($ch);

		return $returnClass;		
	}	
	
	function do_POST_with_CURL($data,$header,$url,$user):c
	{
		$returnClass = new c;
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_USERPWD, "$user");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$returnClass->server_output = curl_exec($ch);
		$returnClass->info = curl_getinfo($ch);
		$returnClass->header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		
		curl_close($ch);
		
		return $returnClass;
	}

/*
$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
$data = json_decode($response);
$pretty = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

echo '<pre>';
print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
print_r($data);
echo '</pre>';
*/

/*	function scan_for_changes_cf_caller_email_requester($item, $accepted_time_of_last_update)
	{
		if ($item->name=='cf_caller_email_requester')
		{
			$choices='';
			$html_choices = "";
			
			foreach ($item->choices as $choice)
			{
				if(strlen($choices)>0) 
				{
					$choices=$choices.','.$choice;
					
					if ($choice=='Existing Merchant') $html_choices=$html_choices . '<option selected value="' .$choice. '">' .$choice. '</option>';
					else $html_choices=$html_choices . '<option value="' .$choice. '">' .$choice. '</option>';
				}
				else
				{
					$choices=$choice;				
					
					if ($choice=='Existing Merchant') $html_choices='<option selected value="' .$choice. '">' .$choice. '</option>';
					else $html_choices='<option value="' .$choice. '">' .$choice. '</option>';
				}	
			}				
			
			if ($item->updated_at!=$accepted_time_of_last_update)
			{
				echo "<h1>ALLEN TX, WE HAVE A PROBLEM. CONTACT BRITTNEY PETTIES!</h1>";
				echo 'The \'<i>cf_caller_email_requester</i>\', aka \'<b>' . $item->label . '</b>\', choices are: ' . $choices . '<br/>';
				echo 'The choices were last updated: ' . $item->updated_at . '<br/><br/>';
				echo 'The HTML of choices is: ' . $html_choices . '<br/><br/>View source of this page to get a complete list of choice options in HTML.';
			}		
		}
	}
	
	function scan_for_changes_default_ticket_type($item, $accepted_time_of_last_default_ticket_type_update)
	{
		if ($item->name=='ticket_type')
		{
			$choices='';
			$html_choices = "";
			
			foreach ($item->choices as $choice)
			{
				if(strlen($choices)>0) 
				{
					$choices=$choices.','.$choice;
					
					if ($choice=='Integration - Integration Questions') $html_choices=$html_choices . '<option selected value="' .$choice. '">' .$choice. '</option>';
					else $html_choices=$html_choices . '<option value="' .$choice. '">' .$choice. '</option>';
				}
				else
				{
					$choices=$choice;				
					
					if ($choice=='Integration - Integration Questions') $html_choices='<option selected value="' .$choice. '">' .$choice. '</option>';
					else $html_choices='<option value="' .$choice. '">' .$choice. '</option>';
				}	
			}				
			
			if ($item->updated_at!=$accepted_time_of_last_default_ticket_type_update)
			{
				echo "<h1>ALLEN TX, WE HAVE A PROBLEM. CONTACT BRITTNEY PETTIES!</h1>";
				echo 'The \'<i>default_ticket_type</i>\', aka \'<b>' . $item->label . '</b>\', choices are: ' . $choices . '<br/>';
				echo 'The choices were last updated: ' . $item->updated_at . '<br/><br/>';
				echo 'The HTML of choices is: ' . $html_choices . '<br/><br/>View source of this page to get a complete list of choice options in HTML.';
			}		
		}
	} */
?>