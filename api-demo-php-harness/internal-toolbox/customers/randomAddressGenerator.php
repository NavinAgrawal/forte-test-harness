<?php
class State_info {

   public $zip_code;
   public $city_name;
   public $state_abbreviation;

   function __construct($city_name = null, $state_abbreviation = null, $zip_code = null)
   {
       $this->city_name = $city_name;
	   $this->state_abbreviation = $state_abbreviation;
	   $this->zip_code = $zip_code;
   }
   
};


class Address { 
	
	var $physical_address; 
	var $label;
    var $first_name;
    var $last_name;
    var $company_name;
    var $phone;
    var $email;
    var $shipping_address_type;
    var $address_type;
	var $address_token;
	var $location_id;
	var $links;
	
	function __construct($phone="555-555-0155", $email="dustin.thomas@forte.net", $physical_address = null, $label = null, 
						 $first_name = null, $last_name = null, $company_name = null, $shipping_address_type = null, $address_type = "none") {
		
		$this->phone =  $phone;
		$this->email = $email; 
		$this->physical_address = $physical_address;
		$this->label = $label; 
		$this->first_name = $first_name; 
		$this->last_name = $last_name; 
		$this->company_name = $company_name;
		$this->shipping_address_type = $shipping_address_type; 
		$this->address_type = $address_type;
		
   }
   
	public function hydrate($data) {

		$this->physical_address = new Physical_Address();
		//print_r($data);
		if (isset ($data->first_name)) {
			$this->first_name = $data->first_name;
		}
		if (isset ($data->last_name)) {
			$this->last_name = $data->last_name;
		}
		$this->address_token = $data->address_token;
		$this->location_id = $data->location_id;
		$this->phone = $data->phone;
		$this->email = $data->email;
		if (isset ($data->label)) {
			$this->label = $data->label;
		}
		$this->address_type = $data->address_type;
		if (isset ($data->shipping_address_type)) {
			$this->shipping_address_type = $data->shipping_address_type;
		}
		$this->physical_address->hydrate($data->physical_address);
		$this->links = array();
		foreach($data->links as $curr_link) {
			$this->links[] = $curr_link;
			}
        }
   
};

class Physical_Address {

   var $zip_code;
   var $city_name;
   var $state_abbreviation;
   var $address;
   var $phone;
   var $email;
   var $links;

	function __construct($state_obj = null, $address="1234 Test St") {
	   
		$this->address = $address;
		if (!(is_null($state_obj))) {
			$this->city_name = $state_obj->city_name;
			$this->state_abbreviation = $state_obj->state_abbreviation;
			$this->zip_code = $state_obj->zip_code;
		}
		else {
			$this->city_name = null;
			$this->state_abbreviation = null;
			$this->zip_code = null;
		}
	}
	
    public function hydrate($data) {
        $this->address = $data->street_line1;
        $this->city_name = $data->locality;
        $this->state_abbreviation = $data->region ;
        $this->zip_code = $data->postal_code;
    }
   
};


class Address_Generator {
	
	private $state_info_array;
	
	function __construct($state_file_location) {
		
		$this->fill_state($state_file_location);
		
	}
	
	private function fill_state($state_file_location) {
		$handle = fopen($state_file_location, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				
				$zip_code = strtok($line, ",");
				$city_name = strtok(",");
				$state_name = strtok(","); //not using currently
				$state_abbreviation = strtok(",");
				$new_state = new State_info($city_name, $state_abbreviation, $zip_code);
				$this->state_info_array[] = $new_state;
				//print_r($new_state);
			}
			//echo count($state_info_array);
			//var_dump($state_info_array);
		fclose($handle);
		}
		else {
			echo "Error opening state file";
			exit();
		}
	}
	
	private function get_random_state() {
		$random_key = array_rand($this->state_info_array);
		return $this->state_info_array[$random_key];
	}

	public function get_random_address() {
		$new_phys_address = new Physical_Address($this->get_random_state($this->state_info_array));
		$new_address = new Address();
		$new_address->physical_address = $new_phys_address;
		return $new_address;
	}
	
	public function test() {
		$ra = $this->get_random_address();
		var_dump($ra);
	}
	
}

//$company_name_array = array(); 
//$address_generator = new Address_Generator("us_postal_codes_short.csv");
//$address_generator->test();
?>