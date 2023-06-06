<?php

namespace __appbase;

function startswith($haystack,$needle)
{
  return (strncmp($haystack,$needle,strlen($needle)) == 0);
}

function endswith($haystack,$needle)
{
  $o = strlen($needle);
  if( $o > 0 ) {
    return substr_compare($haystack, $needle, -$o, $o) == 0;
  }
  return false;
}

?>
