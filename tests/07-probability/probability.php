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
		$items = array(),
		$total_weight = 0;
	
	function add($item, $probability)
	{
		$this->items[] = array($item, $probability);
	}
	
	function get()
	{
		// Find the total weight of all items
		$total_weight=0;
		foreach($this->items as $key=>$val)
		{
			$this->items[$key][2]=$this->total_weight;
			$this->total_weight=$this->items[$key][3]=$val[1]+$this->total_weight;
		}
		$chance=rand(0,$this->total_weight);

		foreach($this->items as $key=>$val)
			if($chance<=$val[3]) break;
			
		return $val[0]; //return $key;
	}
}

?>