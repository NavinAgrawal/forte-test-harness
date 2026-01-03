<pre>
<?php
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('America/Chicago');

include 'randomAddressGenerator.php';
include 'randomCreditGenerator.php'; 
include 'randomCompanyGenerator.php';
include 'randomNameGenerator.php';
include 'paymethodGenerator.php';

class Customer {
	
	var $first_name;
	var $last_name;
	var $company_name;
	var $addresses;
	var $paymethods;
	var $customer_token;
	var $location_id;
	var $default_paymethod_type;
	var $default_paymethod_token;
	var $display_name;
	var $links;
	
	function __construct($first_name = null, $last_name = null, $company_name=null) {
		
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->company_name = $company_name;
		$this->addresses = null;
		$this->paymethods =  null;
	}
	
	
	function add_addresses($address_object) {
		$this->addresses[] = $address_object; 
	}
	
	
	function add_paymethods($card_object){
		$this->paymethods[] = $card_object;
	}
	
	
	//Export all values to an Array as Key-Value pairs
	public function export_to_array() {

		$customer_array = array( 
			"first_name"=> $this->first_name,
			"last_name"=> $this->last_name,
			"company_name"=> $this->company_name
		);
		
		$address_arrays = array();
		foreach ($this->addresses as $curr_address) {
			//var_dump($curr_address);
			$curr_address_array = array (
				"label" => $curr_address->label,
				"first_name"=> $curr_address->first_name,
				"last_name"=>  $curr_address->last_name,
				//"company_name"=> $curr_address->company_name,
				"phone"=> $curr_address->phone,
				"email"=> $curr_address->email,
				"shipping_address_type"=> $curr_address->shipping_address_type,
				"address_type"=> $curr_address->address_type,
				"physical_address"=> 
				array(
					"street_line1"=> $curr_address->physical_address->address,
					//"street_line2"=> $curr_address->physical_address->,
					"locality"=> $curr_address->physical_address->city_name,
					"region"=> $curr_address->physical_address->state_abbreviation,
					"postal_code"=> $curr_address->physical_address->zip_code,
				)
			);
			$address_arrays[] = $curr_address_array;
		}
		$customer_array["addresses"] = $address_arrays;
		
		//$paymethod_arrays = new array(); //Rest currently only supporting 1 paymethod during creation 
		foreach ($this->paymethods as $curr_paymethod) { 
			$paymethod_array = array ( 
			    "label" => $curr_paymethod->label,
				"notes" => $curr_paymethod->notes,
				"card" => array(
					 "account_number" => $curr_paymethod->card_data->account_number,
					 "expire_month" => $curr_paymethod->card_data->expire_month,
					 "expire_year" => $curr_paymethod->card_data->expire_year,
					 "card_verification_value"=> $curr_paymethod->card_data->card_verification_value,
					 "card_type" => $curr_paymethod->card_data->card_type,
					 "name_on_card" => $curr_paymethod->card_data->name_on_card,
					 "card_data" => $curr_paymethod->card_data->swipe_data,
					 "card_reader" => $curr_paymethod->card_data->card_reader
				 ) // end card array 
			); //end paymethod array
			$customer_array["paymethod"] = $paymethod_array;
		} // end for payment for loop 
		return $customer_array;
	} //end export 		
	
	function hydrate($data) {
		$this->customer_token = $data->customer_token;
		$this->location_id = $data->location_id;
		$this->default_paymethod_type = $data->default_paymethod_type;
		$this->default_paymethod_token = $data->default_paymethod_token;
		$this->default_billing_address_token = $data->default_billing_address_token;
		$this->first_name = $data->first_name;
		$this->last_name = $data->last_name;
		$this->display_name = $data->display_name;
		$this->addresses = array();
		foreach($data->addresses as $curr_std_obj) {
			$curr_address = new Address();
			$curr_address->hydrate($curr_std_obj);
			$this->addresses[] = $curr_address;
		}
		
		$this->paymethod = array();
		$curr_pmt_obj = $data->paymethod;
		//foreach($data->paymethod as $curr_pmt_obj) {
		$curr_paymethod = new Paymethod();
		$curr_paymethod->hydrate($curr_pmt_obj);
		$this->paymethod[] = $curr_paymethod;
		//}
		
		$this->links = array();
		foreach($data->links as $curr_link) {
			$this->links[] = $curr_link; 
		}
	}

	
	
}; // end class 

class Customer_Generator  {
	
	private $name_gen;
	private $address_gen;
	private $credit_gen;
	private $company_gen;
	
	
	function __construct() {
		
		$this->name_gen = new Name_Generator("names.txt");
		$this->address_gen = new Address_Generator("us_postal_codes_short.csv");
		$this->company_gen = new Company_Generator("companies.txt");
		$this->credit_gen = new Card_Generator();
	}

	
	function create_random_customer() {
		
		$shipping_options = array("Home","Business","Shipping","Billing");
		
		$name = $this->name_gen->get_random_name();
		$new_cust = new Customer($name[0], $name[1], null);
		//$new_cust->display_name = $name[0]." ".$name[1]; Not currently supported
		$paymethod_quantity = rand(1,1);
		
		//Create Paymethods and their label and name on card 
		//REST Can only handle 1 at this time
		//If adding ACH pay method generation change this area, TODO
		for($i = 0; $i < $paymethod_quantity; $i++) {
			$new_card_data = $this->credit_gen->get_random_paymethod();
			$new_paymethod = new paymethod($new_label, $new_notes, $type="Credit Card", $new_card_data);
			$new_cust->add_paymethods($new_paymethod);
			$new_cust->paymethods[$i]->card_data->name_on_card = $name[0]." ".$name[1];
		}
		
		
		//Create random addresses and their associated names, labels, address types 
		//REST Can only handle 2 at this time
		$address_quantity = rand(1,2);
		for($i = 0; $i < $address_quantity; $i++) {
			$new_address = $this->address_gen->get_random_address();
			$new_cust->add_addresses($new_address);
			if($i < 1) { //default case
				$new_cust->addresses[$i]->first_name = $name[0];
				$new_cust->addresses[$i]->last_name = $name[1];
				$new_cust->addresses[$i]->label = $name[1]." ".$shipping_options[0];
				$new_cust->addresses[$i]->shipping_address_type = "residential";
				$new_cust->addresses[$i]->address_type = "default_billing";
			}
			else {
				$shipping_index_choice = rand(0, (count($shipping_options)-1));
				$new_company = $this->company_gen->get_random_company();
				if($shipping_index_choice == 0) { //Other persons home 
					
				}
				else if($shipping_index_choice == 1) { //Customer Company 
					$new_cust->addresses[$i]->company_name = $new_company;
					$new_cust->addresses[$i]->label = $new_cust->company_name;
					$new_cust->addresses[$i]->shipping_address_type = "commercial";
				}
				else if($shipping_index_choice > 1) {
					$new_cust->addresses[$i]->company_name = $new_company;
					$new_cust->addresses[$i]->label = $new_cust->company_name." ".$shipping_options[$shipping_index_choice];
					$new_cust->addresses[$i]->shipping_address_type = "commercial";
				}
			}
				
		}
		return $new_cust;
	}
	
	public function test() {
		$test_customer = $this->create_random_customer(); 
		var_dump($test_customer);
	}
};

//$cg = new Customer_Generator();
//$cg->test();
//$test_cust = $cg->create_random_customer();
//var_dump($test_cust->addresses[0]);
//print_r($test_cust->export_to_array());
//print_r(get_random_address($state_info_array));
//get_random_name($first_name, $last_name);
//Todo: remove magic numbers/strings
?>
</pre>