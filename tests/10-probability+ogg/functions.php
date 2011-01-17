<?php

require_once (__DIR__.'/class.probability.php');
require_once (__DIR__.'/class.midigen.php');

//Ten kawa³ek kodu to taki LUKIER SYNTAKTYCZNY :-D
$scales['major']['notes'] = array(0,2,4,5,7,9,11);
$scales['major']['length'] = 12;
$scales['major']['triad'] = array(0,3,4);

$scales['minor']['notes'] = array(0,2,3,5,7,8,10);
$scales['minor']['length'] = 12;
$scales['minor']['triad'] = array(0,3,4);

$scales['harmonic']['notes'] = array(0,2,3,5,7,8,11);
$scales['harmonic']['length'] = 12;
$scales['harmonic']['triad'] = array(0,3,4);

/*
function globals()
{
    $vars = array();
    foreach($GLOBALS as $k => $v){
        $vars[] = "$".$k;
    }
    return "global ".  join(",", $vars).";";
}
*/

// TODO:
// - reimplement note display in <canvas> (like http://mootools.net/blog/2010/05/18/a-magical-journey-into-the-base-fx-class/)

//	Probabilities of the following:

// - that the note is in scale, rather than being sharp or flat
$pNoteOnScale = new Probability;
$pNoteOnScale-> add(TRUE, 95);
$pNoteOnScale-> add(FALSE, 5);

// DEBUGGING: ALL TRUE
//$pNoteOnScale-> set(TRUE);

// - that the note after is close in pitch to the note before
$pNoteInProximity = new Probability;
$pNoteInProximity-> add(TRUE, 65);
$pNoteInProximity-> add(FALSE, 35);

// - that the last note is on a triad
$pLastNoteOnTriad = new Probability;
$pLastNoteOnTriad-> add(TRUE, 90);
$pLastNoteOnTriad-> add(FALSE, 10);

// DEBUGGING: ALL TRUE
//$pLastNoteOnTriad-> set(TRUE);

// - that the last note is a tonic
$pLastNoteIsTonic = new Probability;
$pLastNoteIsTonic-> add(TRUE, 70);
$pLastNoteIsTonic-> add(FALSE, 30);

// DEBUGGING: ALL TRUE
//$pLastNoteIsTonic-> set(TRUE);

// - that the beat notes are on triad
$pFirstPhraseNoteOnTriad = new Probability;
$pFirstPhraseNoteOnTriad-> add(TRUE, 60);
$pFirstPhraseNoteOnTriad-> add(FALSE, 40);

// - that the last note of the phrase is on triad (subdominant or dominant)
$pLastPhraseNoteOnTriad = new Probability;
$pLastPhraseNoteOnTriad-> add(TRUE, 70);
$pLastPhraseNoteOnTriad-> add(FALSE, 30);

// DEBUGGING: ALL TRUE
//$pLastPhraseNoteOnTriad-> set(TRUE);

// - that the second phrase is similar to second phrase
$pSecondPhraseSimilar = new Probability;
$pSecondPhraseSimilar-> add(TRUE, 65);
$pSecondPhraseSimilar-> add(FALSE, 35);

// other probabilities:
// - that the melody shifts into a parallel scale
$pParallelScale = new Probability;
$pParallelScale-> add(TRUE, 5);
$pParallelScale-> add(FALSE, 95);

// - that intervals repeat (like, 2nd up, 3rd down, 1st, 2nd up, 3rd down, 1st) minimum 2 intervals = 3 notes
$pIntervalRepeat = new Probability;
$pIntervalRepeat-> add(TRUE, 20);
$pIntervalRepeat-> add(FALSE, 80);

// - that a group of notes in close proximity will be based on one chord (also a 7th chord)
$pChordBased = new Probability;
$pChordBased-> add(TRUE, 15);
$pChordBased-> add(FALSE, 85);

// if so, how many notes together:
$pHowMany = new Probability;
$pHowMany-> add(2, 95);
$pHowMany-> add(3, 65);
$pHowMany-> add(4, 35);
$pHowMany-> add(5, 15);
$pHowMany-> add(6, 8);
$pHowMany-> add(7, 6);
$pHowMany-> add(8, 4);

function createGenome($scale, $octaves = 1, $length = 16, $phraseLength = 8)
{
	//eval(globals()); // get all the probability objects
	global $pFirstPhraseNoteOnTriad, $pNoteOnScale, $pLastPhraseNoteOnTriad, $pLastNoteOnTriad, $pLastNoteOnTonic;
	
	$genome = array();
	for($i = 0; $i < $length; $i++)
	{
		//$genome[$i] = rand(1,16);
		
		if (!($i % $phraseLength)) //if beginning of phrase
		{	//get a nice note from the triad
			if ($pFirstPhraseNoteOnTriad->get()) $genome[] = getNoteFromTriad($scale, $octaves);
			elseif ($pNoteOnScale->get()) $genome[] = getNoteOnScale($scale, $octaves);
			else $genome[] = getNoteRandom($scale, $octaves);
		}
		
		elseif (!(($i+($phraseLength+1)) % $phraseLength))
		{	//if this is the last note of the phrase
			//get a nice note from 'I'
			if ($pLastPhraseNoteOnTriad->get()) $genome[] = getNoteFromTriad($scale, $octaves);
			elseif ($pNoteOnScale->get()) $genome[] = getNoteOnScale($scale, $octaves);
			else $genome[] = getNoteRandom($scale, $octaves);
		}
		
		elseif ($i == ($length - 1))
		{	//if this is the last note of the melody
			if ($pLastNoteOnTriad->get())
			{	// need to change this to an actual tonic, not just a note from the tonic chord (or add another level)
				if ($pLastNoteOnTonic->get()) $genome[] = getNoteOnNativeChord($scale, $octaves, 1);
				else $genome[] = getNoteFromTriad($scale, $octaves);
			}
			elseif ($pNoteOnScale->get()) $genome[] = getNoteOnScale($scale, $octaves);
			else $genome[] = getNoteRandom($scale, $octaves);
		}
		
		else
		{	//get any note on scale
			if ($pNoteOnScale->get()) $genome[] = getNoteOnScale($scale, $octaves);
			else $genome[] = getNoteRandom($scale, $octaves);
			//echo getNoteOnScale($scale, $octaves).' - ';
		}
	
	}
	var_dump($genome);
	return $genome;
}

function checkInterval($note1, $note2)
{
	return $interval = ($note1 < $note2) ? ($note2 - $note1) : ($note1 - $note2);
	// commented is a slower method: (according to http://forums.whirlpool.net.au/archive/931915)
	// $interval = abs($note1 - $note2);
	
	// TODO
	switch($interval)
	{
		case 1:
		break;
	}
}

function getNoteInProximity($originalNote, $maxDistance=3)
{
	(rand(0,1) == 1) ? ($addsub=-1) : ($addsub=1);
	return abs($originalNote + (rand(0, $maxDistance) * $addsub));
}

function getNoteInProximityOnScale($scale, $originalNote, $maxDegreeDistance=3)
{
	$maxDegreeDistance--; //this is a maximal interval, so 2 is a 3rd, 4 is a 5th, and so on...
	$originalDegree = getDegreeWithOctaveFromNote($originalNote, $scale);
	(rand(0,1) == 1) ? ($addsub=-1) : ($addsub=1);
	
	//$degree = $originalDegree['degree'] + (rand(0, $maxDistance) * $addsub);
	
	$newDegree = getDegreeAndOctave($scale, ($originalDegree['degree'] + (rand(0, $maxDegreeDistance) * $addsub)), $originalDegree['octave']);
	return getNoteFromDegree($scale, $newDegree['octave'], $newDegree['degree']);
}

function getNoteIntervalOnScale($scale, $originalNote, $intervalInScale=2)
{
	($intervalInScale > 0) ? ($intervalInScale--) : ($intervalInScale++); // this is a interval, so 2 is a 3rd, 4 is a 5th, and so on... therefore correcting for human readability
	($intervalInScale > 0) ? ($way = 'UP') : ($way = 'DOWN');
	$originalDegree = getDegreeWithOctaveFromNote($originalNote, $scale, $way);
	
	//$degree = $originalDegree['degree'] + $intervalInScale;
	//$octave = $originalDegree['octave'];
	
	$newDegree = getDegreeAndOctave($scale, $originalDegree['degree'] + $intervalInScale, $originalDegree['octave']);
	/*
	while ($degree < 0)
	{
		$degree += count($scale['notes']);
		$octave--;
	}
	while ($degree >= count($scale['notes']))
	{
		$degree -= count($scale['notes']);
		$octave++;
	}
	*/
	return getNoteFromDegree($scale, $newDegree['octave'], $newDegree['degree']);
}

function getDegreeAndOctave($scale, $degree, $octave=0)
{
	while ($degree < 0)
	{
		$degree += count($scale['notes']);
		$octave--;
	}
	while ($degree >= count($scale['notes']))
	{
		$degree -= count($scale['notes']);
		$octave++;
	}
	return array('degree' => $degree, 'octave' => $octave);
}

function getNoteAndOctave($scale, $note, $octave=0)
{
	while ($note < 0)
	{
		$note += $scale['length'];
		$octave--;
	}
	while ($note >= $scale['length'])
	{
		$note -= $scale['length'];
		$octave++;
	}
	return array('note' => $note, 'octave' => $octave);
}

function getDegreeWithOctaveFromNote($note, $scale, $way='DOWN')
{
	$sharp = 0;
	$flat = 0;
	
	$noteInfo = getNoteAndOctave($scale, $note);
	$note = $noteInfo['note'];
	$octave = $noteInfo['octave'];
	
	if (in_array($note, $scale['notes'])) 
	{
		$degree = array_search($note, $scale['notes']);
		return array('degree' => $degree, 'octave' => $octave, 'accidental' => 0);
	}
	else
	{
		if ($way == 'DOWN') // TO DO: Simplify this.
		{
			$note_flat_search = $note;
			while (!(in_array($note_flat_search, $scale['notes'])))
			{
				$note_flat_search++;
				$flat++;
				$temp = getNoteAndOctave($scale, $note_flat_search);
				$note_flat_search = $temp['note'];
			}
			$accidental = -($flat);
			return array('degree' => array_search($note_flat_search, $scale['notes']), 'octave' => $octave, 'accidental' => $accidental);
		}
		else
		{
			$note_sharp_search = $note;
			while (!(in_array($note_sharp_search, $scale['notes'])) && $sharp >= 0)
			{
				$note_sharp_search--;
				$sharp++;
				$temp = getNoteAndOctave($scale, $note_sharp_search);
				$note_sharp_search = $temp['note'];
			}
			$accidental = $sharp;
			return array('degree' => array_search($note_sharp_search, $scale['notes']), 'octave' => $octave, 'accidental' => $accidental);
		}
	}
}

function getNoteFromTriad($scale, $octaves=1)
{
	$selectedDegree = $scale['triad'][array_rand($scale['triad'])];
	//return getNoteOnNativeChord($scale, $octaves, $selectedDegree);
	$octave = rand(1, $octaves) - 1;
	return getNoteFromDegree($scale, $octave, $selectedDegree);
}
/*
function getNoteFromDegrees($scale, $octaves=1, $degrees)
// each element of $degrees has to be <= than count of notes in a scale, 
// this usually (western musical scales) means 7
{
	$notesAllowed = array();
	$scaleLength = count($scale['notes']);
	for($x = 0; $x < $octaves; $x++)
	{
		foreach($degrees as $degree)
		{
			if($degree > $scaleLength)
			{
				// normalization of octaves is required:
				$degreeInfo = getDegreeAndOctave($scale, $degree);
				$degree = $degreeInfo['degree'];
			}
			$notesAllowed[] = $scale['notes'][$degree] + ($scale['length'] * $x);
		}
	}
	return $notesAllowed[array_rand($notesAllowed)];
}
*/
function getNoteFromDegrees($scale, $octaves=1, $degrees)
// each element of $degrees has to be <= than count of notes in a scale, 
// this usually (western musical scales) means 7
{
	$notesAllowed = array();
	$scaleLength = count($scale['notes']);
	foreach($degrees as $degree)
	{
		$value = $degree['degree'];
		if($value > $scaleLength)
		{
			// normalization of octaves is required:
			$degreeInfo = getDegreeAndOctave($scale, $value);
			$value = $degreeInfo['degree'];
		}
		for($x = 0; $x < $octaves; $x++)
		{
			$notesAllowed[] = $scale['notes'][$value] + $degree['accidental'] + ($scale['length'] * $x);
		}
	}
	return $notesAllowed[array_rand($notesAllowed)];
}

function getNoteFromDegree($scale, $octave=0, $degree)
// each element of $degree has to be <= than count of notes in a scale, 
// this usually (western musical scales) means 7
{
	return $scale['notes'][$degree] + ($scale['length'] * $octave);
}

/*
function getNoteOnNativeChord($scale, $octaves=1, $degree=0) 
{
	$degreesAllowed = array(0+$degree,2+$degree,4+$degree);
	return getNoteFromDegrees($scale, $octaves, $degreesAllowed);
}
*/
function getNoteOnNativeChord($scale, $octaves=1, $degree=0, $seventh=FALSE) 
{
	$output = array(
		array('degree' => 0+$degree, 'accidental' => 0), 
		array('degree' => 2+$degree, 'accidental' => 0), 
		array('degree' => 4+$degree, 'accidental' => 0)
	);
	
	if ($seventh == TRUE) 
	$output[] = array('degree' => 5+$degree, 'accidental' => 1);
	
	return getNoteFromDegrees($scale, $octaves, $output);
}

//echo getNoteOnScale($scale['major']);
function getNoteOnScale($scale, $octaves=1)
{
	$pitchesAllowed = array();
	foreach($scale['notes'] as $pitch)
	{
			for($x = 0; $x < $octaves; $x++)
			{
					$pitchesAllowed[] = $pitch + ($scale['length'] * $x);
			}
	}
	return $pitchesAllowed[array_rand($pitchesAllowed)];
}

function getNoteRandom($scale, $octaves=1)
{
	$fullLength = ($scale['length'] * $octaves) - 1;
	return rand(0, $fullLength);
}

?>