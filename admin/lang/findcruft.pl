#!/usr/bin/perl

$count = 0;

open FILE,"< en_US/admin.inc.php";
while (<FILE>)
{
	if ($_ =~ m/^\$lang\[\'admin\'\]\[\'(.*?)\'\]/)
	{
		$keyname = $1;
		$result = system("grep -q '$1' ../*") && system("grep -qr '$1' ../../lib/*");
		if ($result != 0)
		{
			print $keyname . "\n";
			$count++;
		}
	}
}
close(FILE);

print "Found $count crufty entries\n";
