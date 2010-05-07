<?php
if( !isset($gCms) ) exit;

$wordcount = 500;
if( isset($params['count']) )
  {
    $wordcount = (int)$params['count'];
  }

$pageid = $returnid;
if( isset($params['pageid']) )
  {
    $pageid = (int)$params['pageid'];
  }

$query = 'SELECT b.word 
            FROM '.cms_db_prefix().'module_search_items a, 
                 '.cms_db_prefix().'module_search_index b 
           WHERE a.content_id = \''.$pageid.'\' 
             AND a.module_name = \'search\' 
             AND a.extra_attr = \'content\' 
             AND a.id = b.item_id 
           ORDER BY b.count DESC';

$dbr = $db->SelectLimit( $query, $wordcount, 0 );

$wordlist = array();
while( $dbr && ($row = $dbr->FetchRow() ) )
  {
    $wordlist[] = $row['word'];
  }
echo implode(',',$wordlist);

?>
