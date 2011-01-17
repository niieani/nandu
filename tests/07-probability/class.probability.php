<?php
/*
  // Display Found stats for each item
  header('Content-Type: text/plain');
  $testuje = new Probability;
  $testuje->add('jedna', 30);
  $testuje->add('druga', 70);
  echo $testuje->get();
*/

class Probability
{
	var
		$items = array();
	protected
		$total_weight = 0,
		$counted = false;
	
	public function add($item, $probability)
	{
		if ($this->counted == true) { $this->counted = false; $this->total_weight = 0; };
		$this->items[] = array($item, $probability);
	}
	
	private function getTotal()
	{
		// Find the total weight of all items
		foreach($this->items as $key=>$val)
		{
			$this->items[$key][2]=$this->total_weight;
			$this->total_weight=$this->items[$key][3]=$val[1]+$this->total_weight;
		}
		$this->counted = true;
	}
	
	public function get()
	{
		if ($this->counted == false) $this->getTotal();
		
		$chance=rand(0,$this->total_weight);

		foreach($this->items as $key=>$val)
			if($chance<=$val[3]) break;
			
		return $val[0]; //return $key;
	}
}

?>