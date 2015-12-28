<?php

namespace __appbase;

function startswith($haystack,$needle)
{
  return (substr($haystack,0,strlen($needle)) == $needle);
}

function endswith($haystack,$needle)
{
  return (substr($haystack,-1*strlen($needle)) == $needle);
}

?>
