<?php
error_reporting(E_ALL);

/****************************************************************************
MIDI CLASS CODE
****************************************************************************/
require_once('./classes/midi.class.php');

// generateOgg(array(50,60,70,60,70,60,70,40));
	
function generateOgg($pitches)
{
	$midi = new Midi();

	/*
	$instruments = $midi->getInstrumentList();
	$drumset     = $midi->getDrumset();
	$drumkit     = $midi->getDrumkitList();
	$notes       = $midi->getNoteList();
	*/

	$save_dir = __DIR__.'/tmp/';
//	srand((double)microtime()*1000000);
//	$filename = rand();
	$filename = time();
	$file = $save_dir.$filename;

	//DEFAULTS
	$rep = 1; //repetitions
	$bpm = 90; //BPM

	$midi->open(480); //timebase=480, quarter note=120
	$midi->setBpm($bpm);
		
	//channel
	$ch = 1;

	//$inst = $_POST["inst$k"];
	$inst = 0;

	// pitch
	//$pitches = array(50,60,70,60,70,60,70,40);

	// volume
	$v = 127;

	$ticksBetweenEvents = 480; // 120 = quarter note

	$t = 0;
	$ts = 0;
	$tn = $midi->newTrack() - 1;

	$midi->addMsg($tn, "0 PrCh ch=$ch p=$inst");
	for($r=0; $r<$rep; $r++)
	{
	foreach ($pitches as $n)
	{
		$n = $n + (12*5); // REMOVE THIS
		if ($ts == $t+$ticksBetweenEvents) $midi->addMsg($tn, "$ts Off ch=$ch n=$n v=127");
		$t = $ts;
		$midi->addMsg($tn, "$t On ch=$ch n=$n v=$v");
		$ts += $ticksBetweenEvents;
		if ($ts == $t+$ticksBetweenEvents) $midi->addMsg($tn, "$ts Off ch=$ch n=$n v=127");
	}
	}
	$midi->addMsg($tn, "$ts Meta TrkEnd");

	$midi->saveMidFile($file.'.mid', 0666);
//	system('/usr/bin/timidity -A100 -Ow --output-mono --verbose=-2 --reverb=g,100 --output-file=- '.$file.'.mid | oggenc -q2 -o '.$file.'.ogg -');
	system('/usr/bin/timidity -A110 -Ow --verbose=-2 --reverb=f,100 --output-file=- '.$file.'.mid | oggenc -q1 -o '.$file.'.ogg -');
	return ($filename.'.ogg');
	//$midi->playMidFile($file,$visible,$autostart,$loop,$player);
}

/*
<br /><br />
<input type="button" name="download" value="Save as SMF (*.mid)" onclick="self.location.href='download.php?f=<?=urlencode($file)?>'" />
*/
?>	