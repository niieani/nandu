<?php

final class Nandu_Music_MusicScales //constant arrays, kind of
{
    public static function GetScaleById($id)
    {
        switch($id)
        {
            case 0:
                return self::Major();
            case 1:
                return self::Minor();
            case 2:
                return self::MinorHarmonic();
        }
        return false;
    }
    //Ten kawałek kodu to taki LUKIER SYNTAKTYCZNY :-D
    public static function Major()
    {
        return array(
            'notes' => array(0,2,4,5,7,9,11),   //the scale in defined in semitones counting from 0 (0 is 1st degree)
            'length' => 12,                     //how many semitones does the scale have?
            'triad' => array(0,3,4)             //what are the triad notes
        );
    }
    public static function Minor() //natural
    {
        return array(
            'notes' => array(0,2,3,5,7,8,10),
            'length' => 12,
            'triad' => array(0,3,4)
        );
    }
    public static function MinorNatural()
    {
        return self::Minor();
    }
    public static function MinorHarmonic()
    {
        return array(
            'notes' => array(0,2,3,5,7,8,11),
            'length' => 12,
            'triad' => array(0,3,4)
        );
    }
    
    private function __construct(){}
}