#!/usr/bin/perl

%original;

open FILE,"< en_US/admin.inc.php";
while (<FILE>)
{
	if ($_ =~ m/(.+?) = (.*)/)
	{
		$keyname = $1;
		$value = $2;
		if ($value =~ m/(.*?)\ \/\/needs translation/)
		{
			$value = $1;
		}
		$original{$keyname} = $value;
	}
}
close(FILE);

print "total count: " . scalar(keys %original) . "\n";

@langs = ();
opendir(DH, ".") or die "Couldn't open $dir for reading: $!";
while( defined ($file = readdir(DH)) )
{
	if ($file =~ /(.*?)\.nls\.php$/ && $1 ne "en_US")
	{
		push(@langs, $1);
	}
}

foreach $curlang(@langs)
{
	my %current;
	open FILE,"< ".$curlang."/admin.inc.php";
	while (<FILE>)
	{
		if ($_ =~ m/(.+?) = (.+)/)
		{
			$keyname = $1;
			$value = $2;
			#if ($value =~ m/(.*?)\ \/\/needs translation/)
			#{
			#	$value = $1;
			#}
			$current{$keyname} = $value;
		}
	}
	close(FILE);

	print "total count in ".$curlang.": " . scalar(keys %current) . "\n";

	my @missing;
	foreach $onekey(keys %original)
	{
		if (!(exists $current{$onekey}))
		{
			push(@missing, $onekey);
		}
	}

	if (scalar(@missing) > 0)
	{
		print scalar(@missing) . " keys missing in: " . $curlang . "\n";
		open FILE,"> ".$curlang."/admin.inc.php";
		print FILE "<?php\n";
		foreach $existingkey(keys %current)
		{
			print FILE $existingkey . " = " . $current{$existingkey} . "\n";
		}
		foreach $missingkey(@missing)
		{
			print FILE $missingkey . " = " . $original{$missingkey} . " //needs translation\n";
		}
		print FILE "?>\n";
		close(FILE);
	}
}
