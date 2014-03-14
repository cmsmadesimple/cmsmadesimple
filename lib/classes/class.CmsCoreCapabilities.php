<?php

/**
 * A class that identifies numerous core capabilities
 *
 * @package CMS
 * @license GPL
 */

/**
 * A class that identifies numerous standard core capabilities
 *
 * @package CMS
 * @license GPL
 * @see CMSModule::HasCapability
 */
final class CmsCoreCapabilities
{
  /**
   * @ignore
   */
  private function __construct() {}

  /**
   * A constant for the admin search capability
   */
  const ADMINSEARCH = 'AdminSearch'; // module supports admin search.

  /**
   * A constant for a capability indicating the module provides content block types
   */
  const CONTENT_BLOCKS = 'contentblocks'; 

  /**
   * A constant for a capability indicating that the module provides custom content types
   */
  const CONTENT_TYPES = 'content_types';

  /**
   * A constant indicating that the module handles events
   */
  const EVENTS = 'handles_events';

  /**
   * A constant indicating that the module is a plugin module
   */
  const PLUGIN_MODULE = 'plugin';

  /**
   * A capability indicating that the module provides frontend search functionality.
   */
  const SEARCH_MODULE = 'search';

  /**
   * A capability indicating that the module is a syntax editor module.
   */
  const SYNTAX_MODULE = 'syntaxhighlighting'; // string used pre-2.0

  /**
   * A capability indicating that the module provides pseudocron tasks
   */
  const TASKS = 'tasks';  // string used pre-2.0

  /**
   * A capability indicating that the module is a WYSIWYG module
   */
  const WYSIWYG_MODULE = 'wysiwyg'; // string used pre-2.0

} // end of class

?>