<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

require_once 'PHPUnit/Framework/TestCase.php';
include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class CmsApiTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		CmsCache::clear();
	}
	
	public function tearDown()
	{
		$this->setUp();
	}
	
	public function testSubstrMatchShouldWorkAsIntended()
	{
		$this->assertEquals('1234567890', substr_match('1234567890', '1234567890'));
		$this->assertEquals('12345', substr_match('1234567890', '1234542343'));
		$this->assertEquals('', substr_match('1234567890', '33423423411237890'));
		$this->assertEquals('1234567890', substr_match('1234567890', '1234567890', true));
		$this->assertEquals('67890', substr_match('1234567890', '23423423423423467890', true));
		$this->assertEquals('', substr_match('1234567890', '12345678901', true));
	}
	
}

# vim:ts=4 sw=4 noet
?>