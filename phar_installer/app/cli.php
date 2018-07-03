<?php

// 1.  Get the arguments and fill out the config entry.
$opt = getopt( 'f:v', ['tmpdir:','dest:','nofiles','lang'] );
debug_display( $opt );