<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
// The MIT License
// 
// Copyright (c) 2008 Ted Kulp
// 
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.

require_once(cms_join_path(ROOT_DIR, 'lib', 'syck', 'Yaml.php'));
require_once(cms_join_path(ROOT_DIR, 'lib', 'syck', 'Yaml', 'Dumper.php'));
require_once(cms_join_path(ROOT_DIR, 'lib', 'syck', 'Yaml', 'Exception.php'));
require_once(cms_join_path(ROOT_DIR, 'lib', 'syck', 'Yaml', 'Loader.php'));
require_once(cms_join_path(ROOT_DIR, 'lib', 'syck', 'Yaml', 'Node.php'));

/**
 * Simple wrapper around the syck yaml library.  Just loads
 * from a file and dumps back to an array again.
 *
 * @author Ted Kulp
 * @since 2.0
 **/
class CmsYaml extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Converts a string containg yaml data into a PHP array.
	 *
	 * @param string The data to decode or a file location containg yaml data
	 * @return Array The decoded yaml data
	 * @author Ted Kulp
	 **/
	function load($string)
	{
		return Horde_Yaml::load($string);
	}
	
	/**
	 * Loads a file containg yaml data yaml data into a PHP array.
	 *
	 * @param string The data to decode or a file location containg yaml data
	 * @return Array The decoded yaml data
	 * @author Ted Kulp
	 **/
	function load_file($file)
	{
		return Horde_Yaml::loadFile($file);
	}
	
	/**
	 * Dumps a PHP array into a string of yaml -- suitable for saving
	 * to a file.
	 *
	 * @param array The data to encode
	 * @param boolean Number of spaces to indent the dumped output.  Defaults to 2.
	 * @param boolean Number of characters to wordwrap.  If zero, there's no wordwrapping.  Default is 0.
	 * @return string The encoded yaml string
	 * @author Ted Kulp
	 **/
	function dump($array, $indent = 2, $wordwrap = 0)
	{
		return Horde_Yaml::dump($array, array('indent' => $indent, 'wordwrap' => $wordwrap));
	}
}

# vim:ts=4 sw=4 noet
?>