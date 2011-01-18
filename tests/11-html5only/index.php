<?php

require_once (__DIR__.'/functions.php');
ini_set(display_errors, 1);

$genome = array(createGenome($scales['major'], 2, 16, 8), createGenome($scales['major']));

$files = array();
foreach ($genome as $k => $pitches)
{
	$files[$k] = generateOgg($pitches);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title>Genetic Music Evolution Interface</title>
</head>
<body>
<?php
foreach ($files as $file)
{
	echo '<audio src="./tmp/'.$file.'" controls></audio><br />';
}
?>
</body>
</html>