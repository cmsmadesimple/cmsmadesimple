<?php
    
	
	    /*
	    Copyright 2008, 2009, 2010, 2011 Patrik Hultgren
	    
	    YOUR PROJECT MUST ALSO BE OPEN SOURCE IN ORDER TO USE THIS VERSION OF PHP IMAGE EDITOR.
	    BUT YOU CAN USE PHP IMAGE EDITOR JOOMLA PRO IF YOUR CODE NOT IS OPEN SOURCE.
	    
	    This file is part of PHP Image Editor Joomla.
	
	    PHP Image Editor Joomla is free software: you can redistribute it and/or modify
	    it under the terms of the GNU General Public License as published by
	    the Free Software Foundation, either version 3 of the License, or
	    (at your option) any later version.
	
	    PHP Image Editor Joomla is distributed in the hope that it will be useful,
	    but WITHOUT ANY WARRANTY; without even the implied warranty of
	    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	    GNU General Public License for more details.
	
	    You should have received a copy of the GNU General Public License
	    along with PHP Image Editor Joomla. If not, see <http://www.gnu.org/licenses/>.
	    */

	if (!defined("PIE_PHP_VERSION_MINIMUM"))
	{
		define("PIE_PHP_VERSION_MINIMUM", "5");
		define("PIE_GD_VERSION_MINIMUM", "2.0.28");
		define("PIE_IMAGE_ORIGINAL_PATH", "_editimagesoriginal/");
		define("PIE_IMAGE_WORK_WITH_PATH", "_editimagesworkwith/");
		define("PIE_IMAGE_PNG_PATH", "_editimagespng/");
		define("PIE_MENU_RESIZE", "0");
		define("PIE_MENU_ROTATE", "1");
		define("PIE_MENU_CROP", "2");
		define("PIE_MENU_EFFECTS", "3");
	}

	if (!defined("PIE_IMAGE_MAX_WIDTH"))
	{
	    define("PIE_IMAGE_MAX_WIDTH", 900);
		define("PIE_IMAGE_MAX_HEIGHT", 1400);
		define("PIE_DEFAULT_LANGUAGE", "en-GB");
		define("PIE_RELOAD_PARENT_BROWSER_ON_SAVE", true);
		define("PIE_AJAX_POST_TIMEOUT_MS", 20000);
		
		define("PIE_RESIZE_ENABLED", true);
		define("PIE_ROTATE_ENABLED", true);
		define("PIE_CROP_ENABLED", true);
		define("PIE_EFFECTS_ENABLED", true);
		
		/*	
			PIE_START_PANEL can have any of these values.
			The panel must be enabled which is set above.
			
			PIE_MENU_RESIZE
			PIE_MENU_ROTATE
			PIE_MENU_CROP
			PIE_MENU_EFFECTS
		*/
		define("PIE_START_PANEL", PIE_MENU_RESIZE);
	}

	
?>