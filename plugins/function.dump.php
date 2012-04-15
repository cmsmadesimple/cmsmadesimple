<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

function smarty_function_dump($params, &$template)
{
  $smarty = $template->smarty;
  $ignore = array('cms','smarty','db','config','params','param_map','langhash','xml_exclude_files','xmldtd');

  if( !function_exists('build_accessor') )
    {
      function build_accessor($parent_str,$parent_type,$childname)
      {
	$str = $parent_str;
	if( $parent_type == 'object' )
	  {
	    $str .= '-&gt;';
	  }
	else if( $parent_type == 'array' )
	  {
	    $str .= '.';
	  }
	$str .= $childname;
	return $str;
      }

  function dump_object($params,&$obj,$level=1,$ignore=array(),$accessor)
  {
    $maxlevel = 3;
    if( isset($params['maxlevel']) )
      {
	$maxlevel = (int)$params['maxlevel'];
	$maxlevel = max(1,$maxlevel);
	$maxlevel = min(10,$maxlevel);
      }
    if( $level > $maxlevel ) return;
    $objname = get_class($obj);
    $str = '';
    $str .= str_repeat('  ',$level).'Object Name: '.$objname.'<br/>';
    $str .= str_repeat('  ',$level).'Parent: '.get_parent_class($obj)."<br/>";

    if( !isset($params['nomethods']) )
      {
	$methods = get_class_methods($objname);
	if( count($methods) )
	  {
	    $str .= str_repeat('  ',$level).'Methods: <br/>';
	    foreach( $methods as $one )
	      {
		$str .= str_repeat('  ',$level).'- '.$one.'<br/>';
	      }
	  }
      }

    if( !isset($params['novars']) )
      {
	$vars = get_object_vars($obj);
	if( is_array($vars) && count($vars) )
	  {
	    $str .= str_repeat('  ',$level).'Properties: <br/>';
	    foreach( $vars as $name => $value )
	      {
		if( in_array($name,$ignore) ) continue;
		$acc = build_accessor($accessor,'object',$name);
		
		$type = gettype($value);
		if( $type == 'object' )
		  {
		    $str .= str_repeat('  ',$level).'- '.'<u>'.$name.': Object</u> <em>{$'.$acc.'}</em><br/>';
		    if( isset($params['recurse']) )
		      {
			$str .= dump_object($params,$value,$level+1,$ignore,$acc);
		      }
		  }
		else if( $type == 'array' )
		  {
		    $str .= str_repeat('  ',$level).'- '.'<u>'.$name.': Array ('.count($value).')</u> <em>{$'.$acc.'}</em><br/>';
		    if( isset($params['recurse']) )
		      {
			$str .= dump_array($params,$value,$level+1,$ignore,$acc);
		      }
		  }
		else if( $type == 'NULL' )
		  {
		    $str .= str_repeat('  ',$level).'- '.$name.': NULL <em>{$'.$acc.'}</em><br/>';
		  }
		else
		  {
		    $str .= str_repeat('  ',$level).'- '.$name.' = '.cms_htmlentities($value).' <em>{$'.$acc.'}</em><br/>';
		  }
	      }
	  }
      }
    return $str;
  }


  function dump_array($params,&$data,$level=1,$ignore=array(),$accessor)
  {
    $maxlevel = 3;
    if( isset($params['maxlevel']) )
      {
	$maxlevel = (int)$params['maxlevel'];
	$maxlevel = max(1,$maxlevel);
	$maxlevel = min(10,$maxlevel);
      }
    if( $level > $maxlevel ) return;
    $str = '';
    foreach( $data as $key => $value )
      {
	$acc = build_accessor($accessor,'array',$key);
	$type = gettype($value);
	if( is_object($value) )
	  {
	    $str .= str_repeat('  ',$level).'- <u>'.$key.' = Object</u> <em>{$'.$acc.'}</em><br/>';
	    if( isset($params['recurse']) )
	      {
		$str .= dump_object($params,$value,$level+1,$ignore,$acc);
	      }
	  }
	else if( is_array($value) )
	  {
	    $str .= str_repeat('  ',$level)."- <u>$key = Array (".count($value).')</u> <em>{$'.$acc.'}</em><br/>';
	    if( isset($params['recurse']) )
	      {
		$str .= dump_array($params,$value,$level+1,$ignore,$acc);
	      }
	  }
	else if( $type == 'NULL' )
	  {
	    $str .= str_repeat('  ',$level).'- '.$name.': NULL <em>{$'.$acc.'\}</em><br/>';
	  }
	else
	  {
	    $str .= str_repeat('  ',$level)."- $key = ".cms_htmlentities($value).' {$'.$acc.'}<br/>';
	  }
      }
    return $str;
  }
    }


  $gCms = cmsms();
  // get the item name (without any $)
  if( !isset($params['item']) )
    {
      return;
    }
  $item = trim($params['item']);
  if( startswith($item,'$') )
    {
      $item = substr($item,1);
    }

  // get the base object name.
  $pos1 = strpos($item,'->');
  $pos2 = strpos($item,'-');
  $pos = $pos1;
  $len = 2;
  if( $pos2 < $pos1 && $pos2 !== FALSE )
    {
      $pos = $pos2;
      $len = 1;
    }
  $str = substr($item,0,$pos);
  $work = substr($item,$pos+$len);

  // get the base object from smarty.
  $baseobj = $smarty->get_template_vars($str);
  $obj = $baseobj;

  $str = '$baseobj';
  $done = false;
  $tmpobj =& $baseobj->modules['Album'];
  $count = 0;
  while( $done == false )
    {
      $count++;
      $pos1 = strpos($work,'->');
      $pos2 = strpos($work,'.');
      if( $pos1 === FALSE ) $pos1 = 1000000;
      if( $pos2 === FALSE ) $pos2 = 1000000;
      $pos = $pos1;
      $len = 2;
      if( $pos2 < $pos1 )
	{
	  $pos = $pos2;
	  $len = 1;
	}
      $tmp = '';
      if( $pos1 == $pos2 && $pos1 == 1000000 )
	{
	  $tmp = $work;
	}
      else if( $pos !== FALSE && $pos < 100000 )
	{
	  $tmp = substr($work,0,$pos);
	}

      if( !empty($tmp) )
	{
	  if( is_object($obj) )
	    {
	      $str .= '->'.$tmp;
	    }
	  else if( is_array($obj) )
	    {
	      $str .= '[\''.$tmp.'\']';
	    }
	  $work = substr($work,$pos+$len);
	  $tmp2 = '$obj =& '.$str.';';
	  eval($tmp2);
	  $type = gettype($obj);
	  if( $count > 4 ) { print_r( $obj ); echo '<hr/>'; }
	}
      else
	{
	  $done = true;
	}
    }

  $parenttype = gettype($obj);
  $str = '<pre>';
  $str .= '<strong>Dump of: $'.$item;
  $str .= '</strong> ('.ucwords($parenttype).')<br/>';

  if( is_object($obj) )
    {
      $str .= dump_object($params,$obj,0,$ignore,$item);
    }
  else if( is_array($obj) )
    {
      $str .= dump_array($params,$obj,0,$ignore,$item);
    }
  else 
    {
      $str .= $obj.'<br/>';
    }
  $str.='</pre>';
	if( isset($params['assign']) ){
	    $smarty->assign(trim($params['assign']),$str);
	    return;
    }
	return $str;
}


function smarty_cms_help_function_dump() {
  echo lang('help_function_dump');
}


function smarty_cms_about_function_dump() {
	?>
	<p>Author: Robert Campbell&lt;calguy1000@hotmail.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
