<?php

error_reporting(E_ALL);

$deleteFlag = false;

/****************************************************************************
MIDI CLASS CODE
****************************************************************************/
require('./classes/midi.class.php');

$midi = new Midi();

$instruments = $midi->getInstrumentList();
$drumset     = $midi->getDrumset();
$drumkit     = $midi->getDrumkitList();
$notes       = $midi->getNoteList();
//---------------------------------------------

if (isset($_POST['player'])){
	$player = $_POST['player'];
	$autostart = isset($_POST['autostart']);
	$loop = isset($_POST['loop']);
	$visible = isset($_POST['visible']);
}else{
	$player = 'default';
	$autostart = true;
	$loop = false;
	$visible = true;
}


$aktiv=array();
$inst=array();
$note=array();
$vol=array();

/*for ($k=1;$k<=8;$k++){
	//$aktiv[$k] = isset($_POST["aktiv$k"])?1:0;
	//$inst[$k] = isset($_POST["inst$k"])?$_POST["inst$k"]:0;
	$note[$k] = isset($_POST["note$k"])?$_POST["note$k"]:35;
	$vol[$k] = isset($_POST["vol$k"])?$_POST["vol$k"]:127;
}*/


//if ($play){

	$save_dir = 'tmp/';
	srand((double)microtime()*1000000);
	$file = $save_dir.rand().'.mid';
	
	//DEFAULTS
	$rep = 1; //repetitions
	$bpm = 60; //BPM

	$midi->open(480); //timebase=480, quarter note=120
	$midi->setBpm($bpm);
	
	//for ($k=1;$k<=8;$k++) 
	{		
		//$ch = ($k<5) ? 10 : $k;
		
		//channel
		$ch = 1;
		
		//$inst = $_POST["inst$k"];
		$inst = 0;
		
		// pitch
		$n = 70; 
		//$n = $_POST["note$k"];
		
		// volume
		//$v = $_POST["vol$k"];
		$v = 127;
		
		$ticksBetweenEvents = 240; // 120 = quarter note
		
		$t = 0;
		$ts = 0;
		$tn = $midi->newTrack() - 1;
		
		$midi->addMsg($tn, "0 PrCh ch=$ch p=$inst");
		for ($r=0;$r<$rep;$r++){
			for ($i=0;$i<16;$i++)
			{
				if ($ts == $t+$ticksBetweenEvents) $midi->addMsg($tn, "$ts Off ch=$ch n=$n v=127");
				//if (isset($_POST["n$k$i"]))
				//if (1)
				{
					$t = $ts;
					$midi->addMsg($tn, "$t On ch=$ch n=$n v=$v");
				}
				$ts += $ticksBetweenEvents;
			}
			if ($ts == $t+$ticksBetweenEvents) $midi->addMsg($tn, "$ts Off ch=$ch n=$n v=127");
		}
		$midi->addMsg($tn, "$ts Meta TrkEnd");
	}	
	$midi->saveMidFile($file, 0666);
	$midi->playMidFile($file,$visible,$autostart,$loop,$player);
?>	
	<br /><br />
	<input type="button" name="download" value="Save as SMF (*.mid)" onclick="self.location.href='download.php?f=<?=urlencode($file)?>'" />

Player:
<select name="player">
<?
$players = array(
	'default'=>'Default',
	'quicktime'=>'Quicktime',
	'crescendo'=>'Crescendo',
	'bgsound'=>'IE Win BgSound',
	'windowsmedia'=>'Windows Media',
	'beatnik'=>'Beatnik',
	'mp3_flash'=>'MP3 Flash Player',
	'ogg_html5'=>'OGG HTML5 Player'
);
foreach ($players as$k=>$v){
	echo '<option value="'.$k.'"'.($player==$k?' selected="selected"':'').'>'.$v."</option>\n";
}
?>
</select>
<br /><br />
Settings:
<?
$settings = array(
	'autostart'=>'Autostart',
	'loop'=>'Loop',
	'visible'=>'Visible'
);
foreach ($settings as $k=>$v){
	echo '<input type="checkbox" name="'.$k.'"'.($$k?' checked="checked"':'').' />'.$v."\n";
}
?>
<!--
<br /><br />
<br />
<input type="checkbox" name="showTxt"<?=isset($_POST['showTxt'])?' checked="checked"':''?> />show MIDI result as Text<br />
<input type="checkbox" name="showXml"<?=isset($_POST['showXml'])?' checked="checked"':''?> />show MIDI result as XML
-->
<br /><br />
<input type="submit" name="play" value=" PLAY! " />&nbsp;&nbsp;
