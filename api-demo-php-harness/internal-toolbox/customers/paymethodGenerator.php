 <?php
class paymethod {
	public $label;
	public $notes;
	public $type;
	public $card_data;
	public $paymethod_token;
	public $location_id;
	public $links;
   
   function __construct($label=NULL, $notes=NULL, $type=NULL, $card_data=NULL) {
	   $this->label = $label;
	   $this->notes = $notes;
	   $this->type = $type;
	   $this->card_data = $card_data;
	}
	
	function hydrate($data) {
		//print_r($data);
		$this->paymethod_token = $data->paymethod_token;
		$this->location_id = $data->location_id;
		$this->label = $data->label;
		$this->notes = $data->notes;
		$this->card_data = $data->card;
		$this->links = array();
		foreach ($data->links as $curr_link) {
			$this->links[] = $curr_link;
		}
	}
}
 ?>