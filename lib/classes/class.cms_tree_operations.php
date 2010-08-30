<?php

class cms_tree_operations
{
  private static $_keys;


  public static function add_key($key)
  {
    if( !is_array(self::$_keys) )
      {
	self::$_keys = array();
      }
    if( !in_array($key,self::$_keys) )
      {
	self::$_keys[] = $key;
      }
  }


  public static function load_from_list($data,$path_delim = '.',$alias_delim = ',')
  {
    $tree = new cms_content_tree();
    $count = 0;
    $nodelist = array();

    if( !is_array($data) ) return $count;

    for( $i = 0; $i < count($data); $i++ )
      {
	$line =& $data[$i];
	list($path,$alias) = explode($alias_delim,$line,2);
	$path_parts = explode($path_delim,$path);

	if( count($path_parts) == 1 )
	  {
	    if( isset($node_list[$path]) ) continue;
	    
	    $obj = new cms_content_tree(array('id'=>(int)$path_parts[0],'alias'=>$alias));
	    $node_list[$path] =& $obj;
	    $tree->add_node($obj);
	  }
	else
	  {
	    $parent = $tree;
	    for( $j = 0; $j < count($path_parts); $j++ )
	      {
		$cur_path = implode($path_delim, array_slice($path_parts, 0, $j+1));
		if( isset($node_list[$cur_path]) )
		  {
		    continue;
		  }
		$parent_id = (int)$path_parts[$j-1];
		$parent = $tree->find_by_tag('id',$parent_id);
		$obj = new cms_content_tree(array('id'=>(int)$path_parts[$j],'alias'=>$alias));
		$node_list[$cur_path] = $obj;
		$parent->add_node($obj);
	      }
	  }
      }

    return $tree;
  }

}

 // end of class
?>