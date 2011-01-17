<?php

require_once (__DIR__.'/functions.php');
//$genome = array(createGenome($scales['major'], 2, 16, 8), createGenome($scales['major']));

//getNoteInProximityOnScale($scales['major'], 20, 3)

//var_dump (getDegreeWithOctaveFromNote(getNoteFromTriad($scales['major'], 1),$scales['major']));

var_dump (getDegreeWithOctaveFromNote(getNoteOnNativeChord($scales['major'], 1, 0, TRUE), $scales['major']));

//var_dump (getNoteIntervalOnScale($scales['major'], 2, -3));
?>