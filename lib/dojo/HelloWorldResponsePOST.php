<?php
  /*
  * HelloWorldResponsePOST.php
  * --------
  *
  * Print the name that is passed in the
  * 'name' $_POST parameter in a sentence
  */

  header('Content-type: text/plain');
  print "Hello {$_POST['name']}, welcome to the world of Dojo!\n";
?>
