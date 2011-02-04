<?php
error_reporting(E_ALL);
/****************************************************************************
MIDI CLASS CODE
****************************************************************************/
//require_once('./classes/midi.class.php');
require_once('./class.MIDIfy.php');

/*
    Requires tiMIDIty to be installed in /usr/bin/timidity
*/

class AuralizeNumbers extends MIDIfy
{
    public $pitches;
    
    public function __construct($pitches = NULL, $saveDir = __DIR__, $referenceNote = 60)
    {
        $this->pitches = $pitches;
        $this->referenceNote = $referenceNote;
        $this->saveDir = $saveDir.'/';
        $this->auralizeThis();
    }
    
    public function getFilename()
    {
        return $this->filename; 
    }
    
    public function auralizeThis()
    {
        if (is_array($this->pitches))
        {
            $this->generateMIDI($this->pitches);
            $this->generateAudio($this->filepath);
            return $this->filename;
        }
        return false;
    }
    
    public function generateMIDI($pitches)
    {
        $this->initializeMIDI();
        $this->addSequence($this->noteSequenceCreateFromPitches($pitches), 480, $this->midiTrack);
        $this->finalizeMIDI();
        $this->saveMIDItoFile();
    }
    
    public function generateAudio($filepath)
    {
        //we could also make use of FIFOs:
        //http://stackoverflow.com/questions/60942/how-can-i-send-the-stdout-of-one-process-to-multiple-processes-using-preferably
    	$command =  '/usr/bin/timidity -A110 -Ow --verbose=-2 --reverb=f,100 --output-file=- '.$filepath.'.mid | tee >(lame --silent -V6 - '.$filepath.'.mp3) | oggenc -Q -q1 -o '.$filepath.'.ogg -';
        //file_put_contents('command', $command);
        if (defined('ASYNCHRONOUS_LAUNCH')) $this->launchBackgroundProcess('/bin/bash -c "'.$command.'"');
        else shell_exec('/bin/bash -c "'.$command.'"');
        return true;
    }
    
    private function launchBackgroundProcess($call)
    {   // http://robert.accettura.com/blog/2006/09/14/asynchronous-processing-with-php/
        pclose(popen($call.' /dev/null &', 'r'));
        return true;
    }
}