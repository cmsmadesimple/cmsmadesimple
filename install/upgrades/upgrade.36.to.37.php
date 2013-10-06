<?php
$gCms = cmsms();
$dbdict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

echo '<p>Deleting GCB Events...';
$tmp = array('AddGlobalContentPre','AddGlobalContentPost''EditGlobalContentPre','EditGlobalContentPost',
	     'DeleteGlobalContentPre','DeleteGlobalContentPost','GlobalContentPreCompile','GlobalContentPostCompile',
	     'ContentStylesheet');
$query = 'DELETE FROM '.cms_db_prefix().'events WHERE originator = \'Core\' AND 
          event_name IN ('.implode(',',$tmp).')';
$return = $db->Execute($query);

// create new tables

// read gcb's
//  convert to template
//  save
// drop gcb table
// drop gcb seq

// read stylesheets
//  convert to CmsLayoutStylesheet
//  save
//  save map of name,old_id,new_id
//  drop css table
// drop css assoc table
// drop css seq
// drop css assoc seq

// for each template
//  read template
//  create design
//  get attached stylesheets
//   convert map
//   add stylesheets to the design
//  create new template
//    attach template to design
//  save
//  save map of name,old_id,new_id
// drop templates table
// drop templates seq

// for each page
//   load content
//   lookup old template id
//   convert to new template id
//   set design id
//   save

?>