<?php
final class Nandu_MIDIfy_NoteValue //for timebase 480
{
    const longa     = 7680;
    const double    = 3840;
    const whole     = 1920;
    const half      = 960;
    const quarter   = 480;
    const n4       = 480;
    const n8       = 240;
    const n16      = 120;
    const n32      = 60;
    const n64      = 30;
    const n128     = 15;
    
    // ensures that this class acts like an enum
    // and that it cannot be instantiated
    private function __construct(){}
}