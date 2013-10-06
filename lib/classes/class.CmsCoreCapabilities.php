<?php

  // todo: document me.
final class CmsCoreCapabilities
{
  private function __construct() {}

  const ADMINSEARCH = 'AdminSearch'; // module supports admin search.
  const CONTENT_BLOCKS = 'contentblocks';  // string used pre-2.0
  const CONTENT_TYPES = 'content_types';
  const EVENTS = 'handles_events';
  const PLUGIN_MODULE = 'plugin';
  const SEARCH_MODULE = 'search';
  const SYNTAX_MODULE = 'syntaxhighlighting'; // string used pre-2.0
  const TASKS = 'tasks';  // string used pre-2.0
  const WYSIWYG_MODULE = 'wysiwyg'; // string used pre-2.0

} // end of class

?>