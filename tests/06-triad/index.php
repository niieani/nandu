<?php

// probability of the following is higher:
// - that the note after is close in pitch to the note before
// - that the note is in scale, rather than being sharp or flat
// - that the beat notes are on triad

$scale['major']['notes'] = array(0,2,4,5,7,9,11);
$scale['major']['length'] = 12;
$scale['major']['triad'] = array(1,4,5);
//$scale['major']['I'] = array(1,3,5)

$genome = array(createGenome($scale['major'], 2), createGenome($scale['major']));

function createGenome($scale, $octaves = 1, $length = 16, $phraseLength = 8)
{
	//global $scale;
	
	$genome = array();
	$degrees = array(1,4,5);
	for($i = 0; $i < $length; $i++)
	{
		//$genome[] = getNoteFromDegrees($scale, $octaves, $degrees);
	
		//$genome[$i] = rand(1,16);
		if (!($i % $phraseLength)) //if beginning of phrase
		{	//get a nice note from the triad
			$genome[] = getNoteFromTriad($scale, $octaves);
			//getNoteOnScale($scale);
		}
		
		elseif (!(($i+($phraseLength+1)) % $phraseLength))
		{	//if this is the last note of the phrase
			$genome[] = getNoteOnNativeChord($scale, $octaves, 1);
		}
		
		else
		{	//get any note on scale
			$genome[] = getNoteOnScale($scale, $octaves);
		}
	
	}
	var_dump($genome);
	return $genome;
}

function getNoteFromTriad($scale, $octaves=1)
{
	$selectedDegree = $scale['triad'][array_rand($scale['triad'])];
	return getNoteOnNativeChord($scale, $octaves, $selectedDegree);
}

function getNoteFromDegrees($scale, $octaves=1, $degrees, $nonHuman)
// each element of $degrees has to be <= than count of notes in a scale, 
// this usually (western musical scales) means 7
{
	$notesAllowed = array();
	for($x = 0; $x < $octaves; $x++)
	{
		foreach($degrees as $degree)
		{
			//$degree is human musical input, counting from 1
			if(!($nonHuman)) $degree--;
			$notesAllowed[] = $scale['notes'][$degree] + ($scale['length'] * $x);
		}
	}
	return $notesAllowed[array_rand($notesAllowed)];
}

function getNoteOnNativeChord($scale, $octaves=1, $degree=1, $nonHuman) 
{
	if(!($nonHuman)) $degree--;
	$degreesAllowed = array(0+$degree,2+$degree,4+$degree);
	return getNoteFromDegrees($scale, $octaves, $degreesAllowed, TRUE);
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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title>Genetic Music Evolution Interface</title>
<link rel="stylesheet" type="text/css" href="css/style2.css"/>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script> 
window.onload = function() {
	MIDI.connect();
};
 
window.onbeforeunload = function() { 
	if(MIDIPlugin) { 
		MIDIPlugin.closePlugin();
	}
};
 
pianoKeys = {
	'C': 0,
	'C#': 1,
	'Db': 1,
	'D': 2,
	'D#': 3,
	'Eb': 3,
	'E': 4,
	'F': 5,
	'F#': 6,
	'Gb': 6,
	'G': 7,
	'G#': 8,
	'Ab': 8,
	'A': 9,
	'A#': 10,
	'Bb': 10,
	'B': 11
};
 
var noteDuration = {
	q: 4,
	h: 2,
	w: 1
};

notes = new Array(<?php echo count($genome); ?>);

<?php 
foreach ($genome as $k => $v)
{
	echo "notes[$k] = [ ";
	foreach ($genome[$k] as $value)
	{
		echo $value.', ';
	}
	echo "];\n";
}
?>

flashNote = function() {
};
isPlaying = false;
notesWorking = new Array();
instance = 0;
track = 0;

MIDI = {
//	notes: [],
	archive: [],
	connect: function() {
		MIDIPlugin = document.MIDIPlugin;
		setTimeout(function() { // run on next event loop (once MIDIPlugin is loaded)
			try { // activate MIDIPlugin
				MIDIPlugin.openPlugin();
				var instruments = MIDIPlugin.getInstruments().split("|");
				for(var key in instruments) {
					if(instruments[key].toLowerCase().indexOf("guitar") != -1) {
//						MIDIPlugin.setInstrument(key);
						return;
					}
				}
			} catch(e) { // plugin not supported (download externals)
				alert("error - you don't have the soundbank installed");
				var a = document.createElement("a");
				a.href = "http://java.sun.com/products/java-media/sound/soundbanks.html";
				a.target = "_blank";
				a.appendChild(document.createTextNode("Download Soundbank"));
				var div = document.getElementById('bar');
				div.removeChild(div.firstChild);
				div.appendChild(a);
			}
		}, 0);
	},
	stop: function() {
		isPlaying = false;
		MIDI.reset();
	},
	reset: function() {
		MIDIPlugin.stopAllNotes(); 
		notesWorking.length = 0;
		//notesWorking = notes[track].slice(); 
	},
	start: function(thisInstance) {
		if(thisInstance==instance)
		{
			if (isPlaying == true) var note = notesWorking.shift()+(12*5);
			if (note > 0)
			{
				if(isPlaying == true)
				{
					setTimeout("MIDI.start("+thisInstance+")", 1200 / 2);
					if(MIDIPlugin)
					{
						MIDIPlugin.playNote(note);
					}
					flashNote();
				}
			}
			else 
			{
				isPlaying = false;
				MIDI.reset();
			}
		}
/*		var lengtho = [ 1 ];
		var key = [ "C/4" ];
		var keys = [ key, key, key, key ];
		var duration = [ 8,8,8,8 ];
		MIDI.archive.push({ keys: keys, duration: duration });
//		MIDI.archive = ({ keys: [1,4,7,20], duration: [ 120,120,120,120 ]});
		MIDI.notes = clone(MIDI.archive);
		MIDI.play();*/
	},
	play: function() {
		var note = MIDI.notes.shift();
		var keys = note.keys;
		var chord = [ ];
		for(var id in keys) {
			var key = keys[id].split("/");
			chord.push(pianoKeys[key[0]] + (12 * (parseInt(key[1]) - 1)));
		}
		var duration = noteDuration[note.duration] || parseInt(note.duration);
		setTimeout(MIDI.play, 1200 / duration);
		if(MIDIPlugin) {
			MIDIPlugin.playChord(chord);
			//MIDIPlugin.playNote(20);
		}
	}
}; 
 
clone = function (o) {
	if (typeof o != 'object') return (o);
	if (o == null) return (o);
	var z = (typeof o.length == 'number') ? [] : {};
	for (var i in o) z[i] = clone(o[i]);
	return z;
};
 
  </script> 
</head>
<body>

<div id="wrapper">
	<h1>Ñandú<small>Genetic Music Evolution</small></h1>
	<div id="content">
		<h2>Hover your mouse over the track to hear it<br />Compare and pick the best one</h2>
		<div id="bar">
			<div id="first" class="element track1">
				<!--<div class="tracktext oneheight"><p>Playback here</p></div>-->
                <div class="notes oneheight">
<?php
foreach ($genome[0] as $key => $value)
{
	$marginLeft = ($key)*17;
    $marginTop = 80-($value*5);
	echo '<div class="note" style="margin-left:'.$marginLeft.'px;margin-top:'.$marginTop.'px;"></div>';
}
?>
                </div>
				<div class="bottom oneheight">
					<div class="infobox">
						<h3>Version one</h3>
					</div>
				</div>
				<div class="progressbar oneheight">
				</div>
				<div class="right oneheight">
				</div>
			</div>
			<div id="second" class="element track2">
				<!--<div class="tracktext oneheight"><p>Playback here</p></div>-->
                <div class="notes oneheight">
<?php
foreach ($genome[1] as $key => $value)
{
	$marginLeft = ($key)*17;
    $marginTop = 80-($value*5);
	echo '<div class="note" style="margin-left:'.$marginLeft.'px;margin-top:'.$marginTop.'px;"></div>';
}
?>
                </div>
				<div class="bottom oneheight">
					<div class="infobox">
						<h3>Version two</h3>
					</div>
				</div>
				<div class="progressbar oneheight">
				</div>
				<div class="right oneheight">
				</div>
			</div>
		</div>
	</div>
</div>
<applet id="MIDIPlugin" code="MIDIPlugin.class" name="MIDIPlugin" archive="MIDIPlugin.jar" height="1" width="1" style="visibility:hidden;"></applet>
<script>

//var n = 0;
$("div.track1").mouseenter(function(){
	track = 0;
	notesWorking = notes[0].slice();
	isPlaying = true; 
	instance++;
	MIDI.start(instance);
}).mouseleave(function(){
	MIDI.stop();
});

$("div.track2").mouseenter(function(){
	track = 1;
	notesWorking = notes[1].slice();
	isPlaying = true; 
	instance++;
	MIDI.start(instance);
}).mouseleave(function(){
	MIDI.stop();
});
</script>
</body>
</html>