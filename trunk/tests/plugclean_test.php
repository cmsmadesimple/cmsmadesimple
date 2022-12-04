<?php

function join_path(...$args) : string
{
	if (is_array($args[0])) {
		$args = $args[0];
	}
	$path = implode(DIRECTORY_SEPARATOR, $args);
	return str_replace(['\\', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR],
		[DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);
}

function endswith( string $str, string $sub ) : bool
{
	$o = strlen( $sub );
	if( $o > 0 && $o <= strlen($str) ) {
		return strpos($str, $sub, -$o) !== false;
	}
	return false;
}

// 1. Clean plugins API where if necessary

$root = '/var/www/html/cmsms-23DEV/tester';

$dirs = [
['lib', 'plugins'],
['admin', 'plugins'],
['assets', 'plugins'],
['plugins'], //deprecated
];

foreach ($dirs as $segs) {
	$path = join_path($root, ...$segs);
	if (is_dir($path)) {
		$files = scandir($path, SCANDIR_SORT_NONE);
		if ($files) {
			foreach ($files as $one) {
				if (endswith($one, '.php')) {
					$fp = $path.DIRECTORY_SEPARATOR.$one;
					$content = file_get_contents($fp);
					if ($content) {
						$parts = explode('.',$one);
						$patn = '/function\\s+smarty(_cms)?_'.$parts[0].'_'.$parts[1].'\\s?\\([^,]+,[^,]*(&\\s?)(\\$\\S+)\\s?\\)\\s?[\r\n]/';
						if (preg_match($patn, $content, $matches)) {
							$content = str_replace($matches[2].$matches[3], $matches[3], $content);
							file_put_contents($fp.'-NEW', $content);
						}
					}
				}
			}
		}
	}
}
