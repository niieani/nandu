<?php
require_once (__DIR__.'/class.Timer.php');
$mainTimer = new Timer();
$mainTimer->start();

require_once (__DIR__.'/class.KLogger.php');
//define('DEBUG', './');
require_once (__DIR__.'/class.MusicTheory.php');
require_once (__DIR__.'/class.AuralizeNumbers.php');
define('ASYNCHRONOUS_LAUNCH', true);

ini_set('display_errors', 1);

$music = new MusicTheory();
$files = array();
$genotypes = array();

$genotypes[] = $music->melodyGen(MusicScales::Major(), 2, 8, 16);
$genotypes[] = $music->melodyGen(MusicScales::Major(), 2, 8, 16);

foreach ($genotypes as $genotype)
{
    $audio = new AuralizeNumbers($genotype, __DIR__.'/tmp');
    $files[] = $audio->getFilename();
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title>Genetic Music Evolution Interface</title>
<link rel="stylesheet" type="text/css" href="css/c-css.php"/>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.jplayer.min.js"></script>

<script type="text/javascript">
//<![CDATA[

//Popup dialog
function popup(message) {
         
    // get the screen height and width  
    var maskHeight = $(document).height();  
    var maskWidth = $(window).width();
     
    // calculate the values for center alignment
    var dialogTop =  (maskHeight/3) - ($('#dialog-box').height());  
    var dialogLeft = (maskWidth/2) - ($('#dialog-box').width()/2); 
     
    // assign values to the overlay and dialog box
    $('#dialog-overlay').css({height:maskHeight, width:maskWidth}).show();
    $('#dialog-box').css({top:dialogTop, left:dialogLeft}).show();
     
    // display the message
    $('#dialog-message').html(message);
             
}

$(document).ready(function(){
<?php
foreach ($files as $id => $file)
{
    $id++;
    echo <<< "EOT"
        window.setTimeout(jplayerSetup$id, 800); //FIX ME, this is a poor's man synchronization for the asynchronous generation :-D
        function jplayerSetup$id()
        {
            $("#jquery_jplayer_$id").jPlayer({
        	ready: function () {
        		$(this).jPlayer("setMedia", {
                    oga: "./tmp/$file.ogg",
                    mp3: "./tmp/$file.mp3"
        		});
        	},
        	ended: function (event) {
        		//$(this).jPlayer("play");
        	},
        	swfPath: "js",
        	supplied: "oga, mp3",
        	cssSelectorAncestor: "",
        	cssSelector: {
        	  play: "",
        	  pause: "",
        	  stop: "",
        	  videoPlay: "",
        	  seekBar: "",
        	  playBar: "",
        	  mute: "#mute",
        	  unmute: "#unmute",
        	  volumeBar: "",
        	  volumeBarValue: "",
        	  currentTime: "#currentTime",
        	  duration: ""
              }
            })
            .bind($.jPlayer.event.play, function() { // Using a jPlayer event to avoid both jPlayers playing together.
            		$(this).jPlayer("pauseOthers");
            });
        }

EOT;
}
?>
    window.setTimeout(showGenotypes, 1000); //generation of one genotype to mp3 and ogg takes about 500ms, so 1 second is plenty

    popup('<p>Please wait while the genotypes are being evolved</p>');
    $("body").css({ cursor:"wait" });
    function showGenotypes()
    {
        $('#dialog-overlay, #dialog-box').hide(); 
        $("body").css({ cursor:"auto" });
        $("#bar").fadeIn(500);
    }
    
    // if user resize the window, call the same function again
    // to make sure the overlay fills the screen and dialogbox aligned to center    
    $(window).resize(function () {
        //only do it if the dialog box is not hidden
        if (!$('#dialog-box').is(':hidden')) popup();       
    }); 
    
    
    $("div.track1").mouseenter( function() {
      $("#jquery_jplayer_1").jPlayer("play");
    }).mouseleave(function(){
      $("#jquery_jplayer_1").jPlayer("stop");
    });
    
    $("div.track2").mouseenter( function() {
      $("#jquery_jplayer_2").jPlayer("play");
    }).mouseleave(function(){
      $("#jquery_jplayer_2").jPlayer("stop");
    });
    
});


//]]>
</script>

</head>
<body>

<div id="wrapper">
	<h1>Ñandú<small>Genetic Music Evolution</small></h1>
	<div id="content">
		<h2>Hover your mouse over the track to hear it<br />Compare and pick the best one</h2>
        <div id="jquery_jplayer_1" class="jp-jplayer"></div>
        <div id="jquery_jplayer_2" class="jp-jplayer"></div>
        
        <div id="dialog-overlay"></div>
        <div id="dialog-box">
            <div class="dialog-content">
                <div id="dialog-message"></div>
            </div>
        </div>
        
		<div id="bar">
    		 <div id="first" class="element track1">
                
				<div class="bottom oneheight">
					<div class="infobox">
						<h3>Genotype one</h3>
					</div>
				</div>
				<div class="progressbar oneheight">
				</div>
				<div class="right oneheight">
				</div>
    			<div class="notes oneheight">
        		<?php
        		foreach ($genotypes[0] as $key => $value)
        		{
        		    $marginLeft = ($key)*17;
        		    $marginTop = 60-($value*2);
        			echo '<div class="note notetrack1" style="margin-left:'.$marginLeft.'px;margin-top:'.$marginTop.'px;"></div>';
        		}
        		?>
        		</div>
    			<a href="#" class="track1 oneheight"></a>
			</div>
			<div id="second" class="element track2">
				<div class="bottom oneheight">
					<div class="infobox">
						<h3>Genotype two</h3>
					</div>
				</div>
				<div class="progressbar oneheight">
				</div>
				<div class="right oneheight">
				</div>
    			<div class="notes oneheight">
    			<?php
    			foreach ($genotypes[1] as $key => $value)
    			{
    			    $marginLeft = ($key)*17;
    			    $marginTop = 60-($value*2);
    				echo '<div class="note notetrack2" style="margin-left:'.$marginLeft.'px;margin-top:'.$marginTop.'px;"></div>';
    			}
    			?>
    			</div>
        		<a href="#" class="track2 oneheight"></a>
			</div>
		</div>
    	<div class="debuginfo break">
        <p>
        <?php
        foreach($genotypes as $id => $genotype)
        {
            echo "<h3>Debug data for genotype nr $id: </h3><p>";
            foreach($genotype as $note)
            {
                $degreeInfo = $music->getDegreeWithOctaveFromNote($note, MusicScales::Major(), NoteSearchMode::UP);
                echo $music->getHumanReadableNoteFromDegree($degreeInfo, 'American').', ';
            }
            echo '</p>';
        }
        if (defined('DEBUG'))
        {
            echo '<div class="debuglog"><pre>';
            echo nl2br(file_get_contents(DEBUG.'/log.txt'), true);
            echo '</pre></div>';
            unlink(DEBUG.'/log.txt');
        }
        $mainTimer->stop();
        echo '<p>Page generation time: '.$mainTimer->get().'</p>';
        ?>
        </p>
        </div>
	</div>
</div>
</body>
</html>