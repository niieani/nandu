## Welcome to ñandú!

Nowadays popular and conventional ways of playback of music are very limited
in terms of adding variation to how it is played every time. Usually, we only
have the option to playback the piece exactly the way it was recorded and
mixed. We are only able to influence the environment in which we hear it. If
we want a “real”, “live” experience, our almost sole option is to see a live
performance. The project’s main idea is to create self-evolving music, which
will sound slightly different every time it is played. Read about it some more
below, or if you feel adventurous - get right to it and [start evolving](<!--?php
echo $this--->url(array('speciesId' =&gt; 1), 'evolve', true)) yourself!

## How does it work?

The software is based on user-interactive evolution, where the user can select
the parts of the track which he likes and ones which he does not like. It
imitates the behaviour of genetic evolution of species and biological natural
selection – in which you (the users) are the natural environment for the track
to evolve in. The parts tagged as “bad” become recessive and eventually change
or completely die out, and the “good” parts survive and create offsprings: the
new versions of the track. The ultimate goal for the software is to be able to
evolve entire musical pieces or songs, “mega-organisms”, created from smaller
parts that were also evolved at an earlier stage. This will of course take
some time, but the developement is going really fast! Until we get there,
enjoy the current state and be sure to check back in a couple of weeks!

## What is working now?

The process of creation started in the beginning of 2011 with most of the
ideas thought through in December 2010. Therefore, ñandú is still in its
infant stage of developement. As of version 1.0, released on the 6th February
2010, it's only possible to evolve 16-note melodies without rhythmic patterns.
However, this is prone to change with the next release (1.1) of the software,
adding many new features, mainly variable length and rhythmic evolution -
coming soon. There are two main genetic operators implemented: crossover in a
common point and mutation. Creative operators such as musical inversion,
augmentation, diminution and others (like in Bach's fuges) will be included in
the next release. The visual representation works best in modern browsers,
which support the used bits of HTML5 and CSS3 - therefore browsers such as
Firefox &gt;=3.6, Chrome, Safari &gt;=4. Opera unfortunately still has some
problems.

## How do I use it?

Head over to the [first organism's evolution page](<!--?php echo
$this--->url(array('speciesId' =&gt; 1), 'evolve', true)), or pick an organism
from the list in the select/create menu (click to open). More information is
available on the evolve page.

## Who's behind it?

Nandu is a little piece of software written by two students, namely Bazyli
Brzóska (studying BA Creative Sound and Music at University of Wales Newport)
and Michał Buczek (studying MA Computer Science at Cracow University of
Technology).

## Why is it called 'ñandú'?

Since we wanted the name of the project to connect with genetics, we thought
Charles Darwin was an obvious decision. Unfortunately, the name
[Darwin](http://en.wikipedia.org/wiki/Darwin_%28operating_system%29) was
already taken - we found that Darwin discovered certain species of birds,
known to native of South Americans as ñandú (now in Spanish), and in English
as [rhea](http://en.wikipedia.org/wiki/Rhea_%28bird%29). Rhea is also the name
of a goddess according to Greek mithology. Thus we decided that ñandú sounds
pretty awesome and is a great inspiration for the logo (which by the way has
not been made yet).

## Technical details

If you want technical details head over to our [ñandú
wiki](https://github.com/niieani/nandu/wiki/%C3%B1and%C3%BA).

## How to use?

When you are in the _evolve_ mode you are faced with two blobs of genetic
material. Hover your mouse over the blobs to hear them. Pick one that you like
best by clicking on it. The one you have chosen will become a parent for a new
generation and will also survive to the next one in an unchanged form as
'Genotype A' (the one on the left). This way, you can keep clicking on
Genotype A until the other one sounds better to you. You may stop this process
at any time. Visual representation may help you notice the evolutionary
process.

## Look-alike?

Like in real life, it sometimes happens that two members of species look very
similar - or in this case identical. If that happens you simply have to click
on either one in order to continue the evolution. Enough melody-organisms are
being generated, breed or mutated, so that the whole genetic pool will not be
influenced by one pair of look-alikes.

## MIDI

You can download the MIDI files for each Genotype from the slide menu (click
to open).

## OK. Any terms of service? What's the license like?

We like the Open Source community very much and have in fact used some parts
of code from many differnt projects available on the web. Therefore ñandú is
itself licensed with MIT License, so feel free to run it at home or use its
code, or parts of it in your own projects. No warranty of any kind that it
will work, though. The code is available on GitHub at
[https://github.com/niieani/nandu/](https://github.com/niieani/nandu/).

## Thanks!

Many thanks to Michał, the developer of the evolutionary core, the person
which I harassed every day to meet the deadline! Thanks to all the
participants of the beta testing, especially Tomek, who helped to find some
nasty bugs. And also thanks to you - the users - since you're here and reading
this text. :)
