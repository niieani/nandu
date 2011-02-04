<?php
/*
    // EXAMPLE:
  header('Content-Type: text/plain');
  $testuje = new Probability;
  $testuje->add('jedna', 30);
  $testuje->add('druga', 70);
  $testuje->set('trzecia');
  echo $testuje->get();
*/

/*
Useful information: http://www.phpfreaks.com/forums/php-coding-help/weighted-probability/15/
Similar class: http://snippets.dzone.com/posts/show/2451
class by Bazyli Brzóska
*/

/*

"The generation of random numbers is too important
to be left to chance."
Robert R. Coveyou, Oak Ridge National Laboratory

*/

class Probability
{
	public
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
		if (isset($this->set)) return $this->set;
		if ($this->counted == false) $this->getTotal();
		
		$chance=mt_rand(0,$this->total_weight);

		foreach($this->items as $key=>$val)
			if($chance<=$val[3]) break;
			
		return $val[0]; //return $key;
	}
	
	public function set($value)
	{
		//forces a get() value - useful for debugging
		$this->set = $value;
	}
}

?>