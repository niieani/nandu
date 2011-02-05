<?php

class Nandu_Music_Theory
{
    // TODO:
    // - reimplement note display in <canvas> (like http://mootools.net/blog/2010/05/18/a-magical-journey-into-the-base-fx-class/)
    public $pNoteOnScale;
    public $pNoteInProximity;
    public $pLastNoteOnTriad;
    public $pLastNoteIsTonic;
    public $pFirstPhraseNoteOnTriad;
    public $pLastPhraseNoteOnTriad;
    public $pSecondPhraseSimilar;
    public $pParallelScale;
    public $pIntervalRepeat;
    public $pChordBased;
    public $pHowMany;
    private $allowedDistancePrimary = 5;
    private $allowedDistanceSecondary = 12; //we don't really want intervals bigger than an octave
    
    function __construct()
    {
        //	Probabilities of the following:
        
        // - that the note is in scale, rather than being sharp or flat
        $this->pNoteOnScale = new Nandu_Utilities_Probability();
        $this->pNoteOnScale-> add(TRUE, 80);
        $this->pNoteOnScale-> add(FALSE, 20);
        
        // DEBUGGING: ALL TRUE
        //$this->pNoteOnScale-> set(TRUE);
        
        // - that the note after is close in pitch to the note before
        $this->pNoteInProximityPrimary = new Nandu_Utilities_Probability();
        $this->pNoteInProximityPrimary-> add(TRUE, 60);
        $this->pNoteInProximityPrimary-> add(FALSE, 40);
        
        $this->pNoteInProximitySecondary = new Nandu_Utilities_Probability();
        $this->pNoteInProximitySecondary-> add(TRUE, 85);
        $this->pNoteInProximitySecondary-> add(FALSE, 15);
        
        // - that the last note is on a triad
        $this->pLastNoteOnTriad = new Nandu_Utilities_Probability();
        $this->pLastNoteOnTriad-> add(TRUE, 80);
        $this->pLastNoteOnTriad-> add(FALSE, 20);
        
        // - that the last note is on the tonic chord
        $this->pLastNoteOnTonicChord = new Nandu_Utilities_Probability();
        $this->pLastNoteOnTonicChord-> add(TRUE, 55);
        $this->pLastNoteOnTonicChord-> add(FALSE, 45);
        
        // DEBUGGING: ALL TRUE
        //$this->pLastNoteOnTriad-> set(TRUE);
        
        // - that the last note is a tonic
        $this->pLastNoteIsTonic = new Nandu_Utilities_Probability();
        $this->pLastNoteIsTonic-> add(TRUE, 60);
        $this->pLastNoteIsTonic-> add(FALSE, 40);
        
        // DEBUGGING: ALL TRUE
        //$this->pLastNoteIsTonic-> set(TRUE);
        
        // - that the beat notes are on triad
        $this->pFirstPhraseNoteOnTriad = new Nandu_Utilities_Probability();
        $this->pFirstPhraseNoteOnTriad-> add(TRUE, 60);
        $this->pFirstPhraseNoteOnTriad-> add(FALSE, 40);
        
        // - that the last note of the phrase is on triad (subdominant or dominant)
        $this->pLastPhraseNoteOnTriad = new Nandu_Utilities_Probability();
        $this->pLastPhraseNoteOnTriad-> add(TRUE, 70);
        $this->pLastPhraseNoteOnTriad-> add(FALSE, 30);
        
        // DEBUGGING: ALL TRUE
        //$this->pLastPhraseNoteOnTriad-> set(TRUE);
        
        // - that the second phrase is similar to second phrase
        $this->pSecondPhraseSimilar = new Nandu_Utilities_Probability();
        $this->pSecondPhraseSimilar-> add(TRUE, 65);
        $this->pSecondPhraseSimilar-> add(FALSE, 35);
        
        // other probabilities:
        // - that the melody shifts into a parallel scale
        $this->pParallelScale = new Nandu_Utilities_Probability();
        $this->pParallelScale-> add(TRUE, 5);
        $this->pParallelScale-> add(FALSE, 95);
        
        // - that intervals repeat (like, 2nd up, 3rd down, 1st, 2nd up, 3rd down, 1st) minimum 2 intervals = 3 notes
        $this->pIntervalRepeat = new Nandu_Utilities_Probability();
        $this->pIntervalRepeat-> add(TRUE, 20);
        $this->pIntervalRepeat-> add(FALSE, 80);
        
        // - that a group of notes in close proximity will be based on one chord (also a 7th chord)
        $this->pChordBased = new Nandu_Utilities_Probability();
        $this->pChordBased-> add(TRUE, 15);
        $this->pChordBased-> add(FALSE, 85);
        
        // if so, how many notes together:
        $this->pHowMany = new Nandu_Utilities_Probability();
        $this->pHowMany-> add(2, 95);
        $this->pHowMany-> add(3, 65);
        $this->pHowMany-> add(4, 35);
        $this->pHowMany-> add(5, 15);
        $this->pHowMany-> add(6, 8);
        $this->pHowMany-> add(7, 6);
        $this->pHowMany-> add(8, 4);
    }
    
    //as input takes array $melody with a list of notes and NULLs where they should be filled
    //returns the melody with filled NULLs
    public function melodyFill(array $melody, $scale, $octaves = 1, $phraseLength = 8) //$length = 16
    {
        if (defined('DEBUG')) $log = Nandu_Utilities_KLogger::instance(dirname(DEBUG), Nandu_Utilities_KLogger::DEBUG);
        
        //if you want to extend or shorten the melody we should implmenet array_pad to full the melody if you specify length)
        $length = count($melody);
        
        foreach($melody as $i => $note)
        {
            if ($note === NULL)
            {
                $proximityPrimary = $this->pNoteInProximityPrimary->get();
                $proximitySecondary = $this->pNoteInProximitySecondary->get();
                do //not great implementation, but at least there's a higher possibility of getting a note in proximity to the one before
                {
                    if (!($i % $phraseLength)) //if beginning of phrase
                    {	//get a nice note from the triad and gently fallback to a onscale note and any note
                    	$melody[$i] = $this->getNoteHelper($scale, $octaves, array(NoteType::FirstPhraseNoteOnTriad, NoteType::OnScale, NoteType::Any));
                        if (defined('DEBUG')) $log->logDebug("$i: generating beginning phrase note - note $melody[$i]");
                    }
                    
                    elseif ($i == ($length - 1))
                    {    //if this is the last note of the melody
                        $melody[$i] = $this->getNoteHelper($scale, $octaves, array(NoteType::LastNoteIsTonic, NoteType::LastNoteOnTonicChord, NoteType::LastNoteOnTriad, NoteType::OnScale, NoteType::Any));
                        if (defined('DEBUG')) $log->logDebug("$i: generating last note - note ($melody[$i])");
                    }
                    
                    elseif (!(($i+($phraseLength+1)) % $phraseLength))
                    {	//if this is the last note of the phrase
                    	//get a nice note from 'I'
                    	$melody[$i] = $this->getNoteHelper($scale, $octaves, array(NoteType::LastPhraseNoteOnTriad, NoteType::OnScale, NoteType::Any));
                        if (defined('DEBUG')) $log->logDebug("$i: generating last phrase - note ($melody[$i])");
                    }
                    
                    else
                    {	//get any note on scale
                    	$melody[$i] = $this->getNoteHelper($scale, $octaves, array(NoteType::OnScale, NoteType::Any));
                        if (defined('DEBUG')) $log->logDebug("$i: generating normal - note ($melody[$i])");
                    }
                } while($i != 0 &&
                                (
                                    (
                                        $proximitySecondary === TRUE 
                                            && 
                                        (($melody[$i] = $this->findClosestEquivalent($melody[$i], $melody[$i-1], $this->allowedDistanceSecondary, $scale)) === FALSE)
                                    )
                                    ||
                                    (
                                        $proximityPrimary === TRUE 
                                            && 
                                        (($melody[$i] = $this->findClosestEquivalent($melody[$i], $melody[$i-1], $this->allowedDistancePrimary, $scale)) === FALSE)
                                    )
                                )
                        );
                /* that was one massive while, therefore I decided to break it up so it's nice and readable */
            }
        }
        return $melody;
    }
    
    public function melodyGen($scale, $octaves = 1, $phraseLength = 8, $length = 16)
    {
        return $this->melodyFill(array_fill(0, $length, NULL), $scale, $octaves, $phraseLength);
    }
    
    private function getNoteHelper($scale, $octaves, array $priorities)
    {
        foreach ($priorities as $type)
        {
            switch($type)
            {
                case NoteType::Tonic:
                    return $this->getNoteFromDegree($scale, $this->randomizeOctave($octaves), 0);
                    break;
                case NoteType::OnTonicChord:
                    return $this->getNoteOnNativeChord($scale, $octaves, 1);
                    break;
                case NoteType::OnTriad:
                    return $this->getNoteFromTriad($scale, $octaves);
                    break;
                case NoteType::OnScale:
                    if ($this->pNoteOnScale->get())
                        return $this->getNoteOnScale($scale, $octaves);
                    break;
                case NoteType::LastPhraseNoteOnTriad:
                    if ($this->pLastPhraseNoteOnTriad->get()) 
                        return $this->getNoteFromTriad($scale, $octaves);
                    break;
                case NoteType::FirstPhraseNoteOnTriad:
                    if ($this->pFirstPhraseNoteOnTriad->get()) 
                        return $this->getNoteFromTriad($scale, $octaves);
                    break;
                case NoteType::LastNoteOnTriad:
                    if ($this->pLastNoteOnTriad->get()) 
                        return $this->getNoteFromTriad($scale, $octaves);
                    break;
                case NoteType::LastNoteOnTonicChord:
                    if ($this->pLastNoteOnTonicChord->get()) 
                        return $this->getNoteOnNativeChord($scale, $octaves, 0);
                    break;
                case NoteType::LastNoteIsTonic:
                    if ($this->pLastNoteIsTonic->get()) 
                        return $this->getNoteFromDegree($scale, $this->randomizeOctave($octaves), 0);
                    break;
                case NoteType::Any:
                    return $this->getNoteRandom($scale, $octaves);
                    break;
            }
        }
    }
    
    function checkInterval($note1, $note2)
    {
    	return $interval = ($note1 < $note2) ? ($note2 - $note1) : ($note1 - $note2);
    	// commented is a slower method: (according to http://forums.whirlpool.net.au/archive/931915)
    	// $interval = abs($note1 - $note2);
    }
    
    function oneOctaveTransposeCloser($note1, $note2, $distance = 7, $scale)
    {
        if (defined('DEBUG')) $log = Nandu_Utilities_KLogger::instance(dirname(DEBUG), Nandu_Utilities_KLogger::DEBUG);

        if (defined('DEBUG')) $log->logDebug("trying to transpose octave for $note1");
        if ($this->checkInterval($note1-$scale['length'], $note2) <= $distance)
        {
            $closer = $note1-$scale['length'];
            if (defined('DEBUG')) $log->logDebug('closer = '."$closer is closer for $note2 (was $note1)");
            return $closer;
        }
        elseif ($this->checkInterval($note1+$scale['length'], $note2) <= $distance)
        {
            $closer = $note1+$scale['length'];
            if (defined('DEBUG')) $log->logDebug('closer = '."$closer is closer for $note2 (was $note1)");
            return $closer;
        }
        /*if($this->checkInterval($closer, $note2) < $distance)*/
        else return false;
    }
    
    function findClosestEquivalent($note1, $note2, $distance = 7, $scale)
    {
        if (defined('DEBUG')) $log = Nandu_Utilities_KLogger::instance(dirname(DEBUG), Nandu_Utilities_KLogger::DEBUG);
        
        if (defined('DEBUG')) $log->logDebug("finding closest equivalent for $note1 vs $note2 with distance $distance");
        if ($this->checkInterval($note1, $note2) > $distance)
        {
            return $this->oneOctaveTransposeCloser($note1, $note2, $distance, $scale);
        }
        else return $note1;
    }
    
    function getNoteInProximity($originalNote, $maxDistance=3)
    {
    	(rand(0,1) == 1) ? ($addsub=-1) : ($addsub=1);
    	return abs($originalNote + (rand(0, $maxDistance) * $addsub));
    }
    
    function getNoteInProximityOnScale($scale, $originalNote, $maxDegreeDistance=3)
    {
    	$maxDegreeDistance--; //this is a maximal interval, so 2 is a 3rd, 4 is a 5th, and so on...
    	$originalDegree = $this->getDegreeWithOctaveFromNote($originalNote, $scale);
    	(rand(0,1) == 1) ? ($addsub=-1) : ($addsub=1);
    	
    	//$degree = $originalDegree['degree'] + (rand(0, $maxDistance) * $addsub);
    	
    	$newDegree = $this->getDegreeAndOctave($scale, ($originalDegree['degree'] + (rand(0, $maxDegreeDistance) * $addsub)), $originalDegree['octave']);
    	return $this->getNoteFromDegree($scale, $newDegree['octave'], $newDegree['degree']);
    }
    
    function getNoteIntervalOnScale($scale, $originalNote, $intervalInScale=2)
    {
    	($intervalInScale > 0) ? ($intervalInScale--) : ($intervalInScale++); // this is a interval, so 2 is a 3rd, 4 is a 5th, and so on... therefore correcting for human readability
    	($intervalInScale > 0) ? ($way = NoteSearchMode::UP) : ($way = NoteSearchMode::DOWN);
    	$originalDegree = $this->getDegreeWithOctaveFromNote($originalNote, $scale, $way);
    	
    	//$degree = $originalDegree['degree'] + $intervalInScale;
    	//$octave = $originalDegree['octave'];
    	
    	$newDegree = $this->getDegreeAndOctave($scale, $originalDegree['degree'] + $intervalInScale, $originalDegree['octave']);
    	return $this->getNoteFromDegree($scale, $newDegree['octave'], $newDegree['degree']);
    }
    
    function getDegreeAndOctave($scale, $degree, $octave=0)
    {
    	while ($degree < 0)
    	{
    		$degree += count($scale['notes']);
    		$octave--;
    	}
    	while ($degree >= count($scale['notes']))
    	{
    		$degree -= count($scale['notes']);
    		$octave++;
    	}
    	return array('degree' => $degree, 'octave' => $octave);
    }
    
    function getNoteAndOctave($scale, $note, $octave=0)
    {
    	while ($note < 0)
    	{
    		$note += $scale['length'];
    		$octave--;
    	}
    	while ($note >= $scale['length'])
    	{
    		$note -= $scale['length'];
    		$octave++;
    	}
    	return array('note' => $note, 'octave' => $octave);
    }
    
    public function getDegreeWithOctaveFromNote($note, $scale, $way = NoteSearchMode::DOWN)
    {
    	$sharp = 0;
    	$flat = 0;
    	
    	$noteInfo = $this->getNoteAndOctave($scale, $note);
    	$note = &$noteInfo['note'];
    	$octave = &$noteInfo['octave'];
    	
    	if (in_array($note, $scale['notes'])) 
    	{
    		$degree = array_search($note, $scale['notes']);
    		return array('degree' => $degree, 'octave' => $octave, 'accidental' => 0);
    	}
    	else
    	{
    		if ($way === NoteSearchMode::DOWN) // TO DO: Simplify this.
    		{
    			$note_flat_search = $note;
    			while (!(in_array($note_flat_search, $scale['notes'])))
    			{
    				$note_flat_search++;
    				$flat++;
    				$temp = $this->getNoteAndOctave($scale, $note_flat_search);
    				$note_flat_search = $temp['note'];
    			}
    			$accidental = -($flat);
    			return array('degree' => array_search($note_flat_search, $scale['notes']), 'octave' => $octave, 'accidental' => $accidental);
    		}
    		else
    		{
    			$note_sharp_search = $note;
    			while (!(in_array($note_sharp_search, $scale['notes'])) && $sharp >= 0)
    			{
    				$note_sharp_search--;
    				$sharp++;
    				$temp = $this->getNoteAndOctave($scale, $note_sharp_search);
    				$note_sharp_search = $temp['note'];
    			}
    			$accidental = $sharp;
    			return array('degree' => array_search($note_sharp_search, $scale['notes']), 'octave' => $octave, 'accidental' => $accidental);
    		}
    	}
    }
    
    
    public function getHumanReadableNoteFromDegree(array $degreeInfo, $namingStyle = 'American') //$referencePoint
    {
        $names = NoteNames::$namingStyle();
        $noteName = $names['Notes'][$degreeInfo['degree']];
        while($degreeInfo['accidental'] > 0)
        {
            $noteName .= $names['Accidentals']['+'];
            $degreeInfo['accidental']--;
        }
        while($degreeInfo['accidental'] < 0)
        {
            $noteName .= $names['Accidentals']['-'];
            $degreeInfo['accidental']++;
        }
        $noteName .= '('.$degreeInfo['octave'].')';
        
        return $noteName;
    }
    
    function getNoteFromTriad($scale, $octaves=1)
    {
    	$selectedDegree = $scale['triad'][array_rand($scale['triad'])];
    	//return getNoteOnNativeChord($scale, $octaves, $selectedDegree);
    	return $this->getNoteFromDegree($scale, $this->randomizeOctave($octaves), $selectedDegree);
    }
    
    function getNoteFromDegrees($scale, $octaves=1, $degrees)
    // each element of $degrees has to be <= than count of notes in a scale, 
    // this usually (western musical scales) means 7
    {
    	$notesAllowed = array();
    	$scaleLength = count($scale['notes']);
    	foreach($degrees as $degree)
    	{
    		$value = $degree['degree'];
    		if($value > $scaleLength)
    		{
    			// normalization of octaves is required:
    			$degreeInfo = $this->getDegreeAndOctave($scale, $value);
    			$value = $degreeInfo['degree'];
    		}
    		for($x = 0; $x < $octaves; $x++)
    		{
    			$notesAllowed[] = $scale['notes'][$value] + $degree['accidental'] + ($scale['length'] * $x);
    		}
    	}
    	return $notesAllowed[array_rand($notesAllowed)];
    }
    
    function getNoteFromDegree($scale, $octave=0, $degree)
    // each element of $degree has to be <= than count of notes in a scale, 
    // this usually (western musical scales) means 7
    {
    	return $scale['notes'][$degree] + ($scale['length'] * $octave);
    }
    
    /*
    function getNoteOnNativeChord($scale, $octaves=1, $degree=0) 
    {
    	$degreesAllowed = array(0+$degree,2+$degree,4+$degree);
    	return getNoteFromDegrees($scale, $octaves, $degreesAllowed);
    }
    */
    function getNoteOnNativeChord($scale, $octaves=1, $degree=0, $seventh=FALSE) 
    {
    	$output = array(
    		array('degree' => 0+$degree, 'accidental' => 0), 
    		array('degree' => 2+$degree, 'accidental' => 0), 
    		array('degree' => 4+$degree, 'accidental' => 0)
    	);
    	
    	if ($seventh == TRUE) 
    	$output[] = array('degree' => 6+$degree, 'accidental' => -1);
    	
    	return $this->getNoteFromDegrees($scale, $octaves, $output);
    }
    
    
    private function randomizeOctave($octaves)
    {
        return rand(1, $octaves) - 1;
    }
    
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
    
    function getNoteRandom($scale, $octaves=1)
    {
    	$fullLength = ($scale['length'] * $octaves) - 1;
    	return rand(0, $fullLength);
    }

}
