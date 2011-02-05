<?php
class Nandu_Genetic_Operations
{
	/**
	 * @var Nandu_Music_Theory
	 */
	protected $_music;
	
	public function __construct()
	{
		$this->_music = new Nandu_Music_Theory();
	}
	
	public function crossOver($a, $b)
	{
		$c = array();
		$d = array();
		$pivot = rand(1, count($a) - 1);
	
		for($i = 0; $i < count($a); $i++) {
			if($i < $pivot) {
				$c[$i] = $a[$i];
				$d[$i] = $b[$i];
			} else {
				$c[$i] = $b[$i];
				$d[$i] = $a[$i];
			}
		}
		return array($c, $d);
	}

	public function mutate($genome)
	{
		$index = rand(0, count($genome) - 1);
		$action = rand(0, 1);
		$genome[$index] = null;
		$this->_music;
	}
}