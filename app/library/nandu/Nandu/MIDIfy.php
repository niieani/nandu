<?php
require_once 'MIDI/midi.class.php';
class Nandu_MIDIfy
{
    private $midi;
    private $midiInitialized = false;
    private $midiTimeSequence = 0;
    public $midiTrack;
    public $filename;
    public $filepath;
    public $saveDir;
    public $referenceNote;
    
    public function initializeMIDI($timebase = 480, $bpm = 97)
    {
        /*
        $instruments = $midi->getInstrumentList();
        $drumset     = $midi->getDrumset();
        $drumkit     = $midi->getDrumkitList();
        $notes       = $midi->getNoteList();
        */
        
        $this->midi = new Midi();
        
        $this->midi->open($timebase); //sets timebase to 480
        $this->midi->setBpm($bpm);
        
        //channel
        $channel = 1;
        
        //$inst
        $instrument = 12; // nice 9
        
        $this->midiTrack = $this->addTrack();
        
        $this->midi->addMsg($this->midiTrack, "0 PrCh ch=$channel p=$instrument");
        
        $this->midiInitialized = true;
        
        return true;
    }
    
    public function finalizeMIDI()
    {
        $time = $this->midiTimeSequence;
        $this->midi->addMsg($this->midiTrack, "$time Meta TrkEnd");
    }
    
    public function saveMIDItoFile()
    {
        $this->filename = base_convert(mt_rand(), 10, 36);
        $this->filepath = $this->saveDir.$this->filename;
        $this->midi->saveMidFile($this->filepath.'.mid', 0666);
        if (defined('DEBUG')) $log = KLogger::instance(dirname(DEBUG), KLogger::DEBUG);
        if (defined('DEBUG')) $log->logInfo("Saved MIDI to file: $this->filepath");
        return true;
    }
    
    public function addTrack()
    {   // correction for the stupid return in the MIDI class. WTF was the programmer thinking!
        return $this->midi->newTrack() - 1;
    }
    
    public function addNote($time, $track, $pitch, $length, $channel=1, $volume=127)
    {
        if ($this->midiInitialized === true)
        {
            // implementing this with addMsg would be faster, but a lot more work!
            // therefore, this is something TODO in the future
            $pitch = $pitch + $this->referenceNote;
            $this->midiTimeSequence = $offTime = $time + $length;
            $this->midi->insertMsg($track, "$time On ch=$channel n=$pitch v=$volume");
            $this->midi->insertMsg($track, "$offTime Off ch=$channel n=$pitch v=127");
            return true;
        }
        else
        {
            throw new Exception("MIDI was not initialized");
            return false;
        }
    }
    
    public function addNoteAtEnd($track, $pitch, $length, $channel = 1, $volume=127)
    {
        return $this->addNote($this->midiTimeSequence, $track, $pitch, $length, $channel, $volume);
    }
    
    public function addSequence(array $notes, $time = null, $track, $channel = 1)
    {
        if ($time === null) $time = $this->midiTimeSequence;
        foreach($notes as $note)
        {
            $this->addNote($time, $track, $note['pitch'], $note['length'], $note['channel'], $note['volume']);
            $time = $this->midiTimeSequence;
        }
    }
    
    public function noteSequenceCreateFromPitches(array $pitches, $length = Nandu_MIDIfy_NoteValue::quarter, $channel = 1, $volume = 127)
    {
        $noteSequence = array();
        foreach ($pitches as $i => $pitch)
        {
            $noteSequence[$i]['pitch'] = $pitch;
            $noteSequence[$i]['length'] = $length;
            $noteSequence[$i]['channel'] = $channel; // do we really need to use such a horrible, depracated thing as channels in MIDI?
            $noteSequence[$i]['volume'] = $volume;
        }
        return $noteSequence;
    }
}