<?php

function createGenome()
{
	$genome = array();
	for($i = 0; $i < 16; $i++) {
		$genome[$i] = rand(1,16);
	}
	return $genome;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title>Genetic Music Evolution Interface</title>
<link rel="stylesheet" type="text/css" href="css/style2.css"/>
</head>
<body>

<div id="wrapper">
	<h1>Genetic Music Evolution Interface<small>by Bazyli &amp; Michał</small></h1>
	<div id="content">
		<h2>Play and compare</h2>
		<div id="bar">
			<div id="first" class="element">
				<div class="bottom oneheight">
					<div class="infobox">
						<h3>Version one</h3>
					</div>
				</div>
				<div class="progressbar oneheight">
				</div>
				<div class="right oneheight">
				</div>
				<div class="tracktext oneheight"><p>Playback&nbsp;implemented&nbsp;here</p></div>
                <div class="notes oneheight">
<?php
$genome = array(createGenome(), createGenome());
foreach ($genome[0] as $key => $value)
{
	$marginLeft = $key*15;
    $marginTop = ($value*5)-5;
	echo '<div class="note" style="margin-left:'.$marginLeft.'px;margin-top:'.$marginTop.'px;"></div>';
}
?>
                </div>
			</div>
			<div id="second" class="element">
				<div class="bottom oneheight">
					<div class="infobox">
						<h3>Version two</h3>
					</div>
				</div>
				<div class="progressbar oneheight">
				</div>
				<div class="right oneheight">
				</div>
				<div class="tracktext oneheight"><p>Playback&nbsp;implemented&nbsp;here</p></div>
                <div class="notes oneheight">
<?php
foreach ($genome[1] as $key => $value)
{
	$marginLeft = ($key*15)+3;
    $marginTop = ($value*5)-5;
	echo '<div class="note" style="margin-left:'.$marginLeft.'px;margin-top:'.$marginTop.'px;"></div>';
}
?>
                </div>
			</div>
		</div>
	</div>
</div>

</body>
</html>