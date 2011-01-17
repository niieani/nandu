#!/usr/bin/env php
<?php
echo "Nandu project\n";

function getLine()
{
	$f = fopen('php://stdin', 'r');
	$string = fgets($f);
	fclose($f);
	return $string;
}

function createGenome()
{
	$genome = array();
	for($i = 0; $i < 8; $i++) {
		$genome[$i] = rand(1,10);
	}
	return $genome;
}

function initPopulation()
{
	$population = array();
	for($i = 0; $i < 4; $i++) {
		$population[$i] = createGenome();
	}
	return $population;
}

function dumpGenome($genome) {
	foreach ($genome as $gen) {
		printf("%d ", $gen);
	}
	echo PHP_EOL;
}

function dumpPopulation(array $population)
{
	$i = 0;
	foreach ($population as $genome) {
		printf("%d:\t", ++$i);
		if (is_array($genome)) 
			dumpGenome($genome);
		else 
			echo " NULL\n";
	}
	echo PHP_EOL;
}

function makeChooseForMe($a, $b) 
{
	$cA = $cB = 0;
	$v = 6;

	foreach ($a as $gen) {
		if ($gen == $v) $cA++;
	}
	
	foreach ($b as $gen) {
		if ($gen == $v) $cB++;
	}
	
	$choose = $cB > $cA ? 2 : 1;
	
	printf("Choice made: %d\n", $choose);

	sleep(1);
	
	return $choose;
}

function chooseGenome($a, $b)
{
	echo "Choose the better genome:\n";
	echo "1:\t";
	dumpGenome($a);
	echo "2:\t";
	dumpGenome($b);
//	$line = getLine();
//	if ($line[0] == '2')
//	return 2;
//	else
//	return 1;

	return makeChooseForMe($a, $b);
	
}

function merge($a, $b)
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

function mutate(&$genome)
{
	$index = rand(0, count($genome) - 1);
	$action = rand(0, 1);
	$genome[$index] += $action ? 1 : -1;
	$genome[$index] = abs($genome[$index] % 10);
}

function evolve(&$population)
{
	$indexA = rand(0, count($population) - 1);
	do {
		$indexB = rand(0, count($population) - 1);
	} while($indexB == $indexA);
	
		

	$a = $population[$indexA];
	$b = $population[$indexB];

	printf("%d : %d\n", $indexA, $indexB);
	
	$choose = chooseGenome($a, $b);

	switch ($choose) {
		case 1:
			array_splice($population, $indexB, 1);
			break;

		case 2:
			array_splice($population, $indexA, 1);
			break;
	}

	if(count($population) == 2) {
		printf("Merging.\n");
		$cd = merge($population[0], $population[1]);
		$population = array_merge($population, $cd);
	}

	if(rand(1,5) == 1) {
		$index = rand(0, count($population) - 1);
		printf("\t\t\tMutating %d\n", $index);
		mutate($population[$index]);
	}

}

$population = initPopulation();

dumpPopulation($population);

for ($i = 1; $i <= 10000; $i++) {
	printf("Iteration %d\n", $i);
	evolve($population);
	echo "\nPopulation:\n";
	dumpPopulation($population);
}
