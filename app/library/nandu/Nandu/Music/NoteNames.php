<?php
final class Nandu_Music_NoteNames
{
    public static function German() //Germany, Russia, Poland, Finland and Scandinavia
    {
        return array(
                'Notes' => array(
                    'C',
                    'D',
                    'E',
                    'F',
                    'G',
                    'A',
                    'H'
                ),
                'Accidentals' => array(
                    '+' => 'is',
                    '-' => 'es'
                )
        );
    }
    public static function American() //United States, Canada, the United Kingdom and the Republic of Ireland
    {
        return array(
                'Notes' => array(
                    'C',
                    'D',
                    'E',
                    'F',
                    'G',
                    'A',
                    'B'
                ),
                'Accidentals' => array(
                    '+' => '♯',
                    '-' => '♭'
                )
        );
    }
    public static function Romance() //solfège
    {
        return array(
                'Notes' => array(
                    'Do',
                    'Re',
                    'Mi',
                    'Fa',
                    'Sol',
                    'La',
                    'Si'
                ),
                'Accidentals' => array(
                    '+' => '♯',
                    '-' => '♭'
                )
        );
    }
    private function __construct(){}
}
