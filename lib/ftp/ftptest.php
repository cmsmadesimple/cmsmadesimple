<?
include "ftp_class.php";

$ftp = new ftp(TRUE);
$ftp->Verbose = TRUE;
$ftp->LocalEcho = TRUE;
if(!$ftp->SetServer("ftp.domain.com")) {
	$ftp->quit();
	die("Setiing server failed\n");
}

if (!$ftp->connect()) {
	die("Cannot connect\n");
}
if (!$ftp->login("login", "password")) {
	$ftp->quit();
	die("Login failed\n");
}

if(!$ftp->SetType(FTP_AUTOASCII)) echo "SetType FAILS!\n";
if(!$ftp->Passive(FALSE)) echo "Passive FAILS!\n";


$ftp->chdir("apache");
$ftp->cdup();

$ftp->nlist("-la");

$list=$ftp->rawlist(".", "-lA");
if($list===false) echo "LIST FAILS!";
else {
  foreach($list as $k=>$v) {
    $list[$k]=$ftp->parselisting($v);
  }
  print_r($list);
}


$filename  = "ftpweblog-102a.tar.gz";
if(FALSE !== $ftp->get($filename))
	echo $filename." has been downloaded.\n";
else {
	$ftp->quit();
	die("Error!!\n");
}
$ftp->nlist("-la");

if(FALSE !== $ftp->put($filename, "new-".$filename))
	echo $filename." has been uploaded as ".$filename.".bak\n";
else {
	$ftp->quit();
	die("Error!!\n");
}

$ftp->quit();
?>