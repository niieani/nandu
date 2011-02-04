<?php
require_once (__DIR__.'/class.MusicTheory.php');
require_once (__DIR__.'/class.AuralizeNumbers.php');
//define('ASYNCHRONOUS_LAUNCH', true);
define('DEBUG', true);

// for well working async we need to implement a signalization method, to start loading when the file generation is completed
// otherwise the file sometimes gets downloaded only partially

//if using ASYNCHRONOUS_LAUNCH it's important to call the generate function very early in the script execution
//otherwise, it would be best to call after displying the HTML content

ini_set('display_errors', 1);

$music = new MusicTheory();
$files = array();

$genotype = $music->melodyGen(MusicScales::Major(), 2, 8, 16);
$audio = new AuralizeNumbers($genotype, __DIR__.'/tmp');
$files[] = $audio->getFilename();

foreach($genotype as $note)
{
    $degreeInfo = $music->getDegreeWithOctaveFromNote($note, MusicScales::Major(), NoteSearchMode::UP);
    echo $music->getHumanReadableNoteFromDegree($degreeInfo, 'American').', ';
}

//var_dump($genotype);

echo '<br />';
/*
$genotype = $music->melodyGen(MusicScales::Major(), 1, 8, 16);
$audio = new AuralizeNumbers($genotype, __DIR__.'/tmp');
$files[] = $audio->getFilename();
//echo $audio->getFilename().PHP_EOL;

foreach($genotype as $note)
{
    $degreeInfo = $music->getDegreeWithOctaveFromNote($note, MusicScales::Major(), NoteSearchMode::UP);
    echo $music->getHumanReadableNoteFromDegree($degreeInfo, 'American').', ';
}
echo '<br />';
echo PHP_EOL;
*/

/*$genotype[6] = NULL;
var_dump($genotype);

$genotype = $music->melodyFill($genotype, MusicScales::Major(), 1, 8);
var_dump($genotype);
*/

/*


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title>Genetic Music Evolution Interface</title>
</head>
<body>
<br/>
<?php
foreach ($files as $file)
{
    echo '<audio controls preload>
    <source src="./tmp/'.$file.'.ogg"></source>
	<source src="./tmp/'.$file.'.mp3"></source>
	</audio><br />';
}
?>
</body>
</html>

*/

?>
<?php
foreach ($files as $file)
{
    echo '<audio controls preload>
    <source src="./tmp/'.$file.'.ogg"></source>
    <source src="./tmp/'.$file.'.mp3"></source>
	</audio><br />
    ';
}
?>