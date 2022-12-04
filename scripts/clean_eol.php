#!/usr/bin/env php
<?php

$srcdir = dirname(__DIR__);
echo "Migrating to *NIX-compatible EOL separators in files in and below '$srcdir'\n";

$verbose = true;

// folder names
$src_excludes1 = [
'~/\.git/~',
'~/\.svn/~',
'~/phar_installer/~',
'~/scripts/~',
'~/smarty/~',
'~/tests/~',
'~/images/~',
];

// file names
$file_excludes = [
'/index\.html?$/',
'/\.min\.\w+$/',
'/\.gif$/i',
'/\.png$/i',
'/\.svg$/i',
'/\.jpg$/i',
'/\.jpeg$/i',
'/\.ico$/i',
'/COPYING.*$/',
'/\.otf$/',
'/\.ttf$/',
'/\.woff2?$/',
'/\.eot$/',
'/\.gz$/',
'/\.zip$/',
'/\.git\w+/',
'/\.md$/i',
];

$iter = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($srcdir,
		FilesystemIterator::KEY_AS_FILENAME |
		FilesystemIterator::CURRENT_AS_PATHNAME |
		FilesystemIterator::SKIP_DOTS |
		FilesystemIterator::UNIX_PATHS
	),
	RecursiveIteratorIterator::SELF_FIRST);

foreach ($iter as $fn => $fp) {
	foreach ($src_excludes1 as $excl) {
		if (preg_match($excl, $fp)) {
			if ($verbose) { echo "  Ignore $fp\n"; }
			continue 2;
		}
	}

	foreach ($file_excludes as $excl) {
		if (preg_match($excl, $fn)) {
			if ($verbose) { echo "  Ignore $fp\n"; }
			continue 2;
		}
	}

	if (is_file($fp)) {
		$s = file_get_contents($fp);
		if ($s) {
			$c1 = strtr($s, ["\r\n"=>"\n", "\r"=>"\n"]);
			$c2 = trim($c1) . "\n";
			if (strlen($s) != strlen($c2)) {
				file_put_contents($fp, $c2);
				if ($verbose) { echo "Processed $fp\n"; }
			} else {
				if ($verbose) { echo "  Stet $fp\n"; }
			}
		}
	}
}
