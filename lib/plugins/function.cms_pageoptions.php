<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

function smarty_function_cms_pageoptions($params, $smarty)
{
  if( !isset($params['numpages']) ) return;
  $numpages = (int)$params['numpages'];
  if( $numpages < 1 ) return;
  $curpage = get_parameter_value($params,'curpage',1);
  $curpage = (int)$curpage;
  $curpage = max(1,min($numpages,$curpage));
  $surround = 3;
  if( isset($params['surround']) ) $surround = (int)$params['surround'];
  $surrund = max(1,min(20,$surround));
  $elipsis = get_parameter_value($params,'elipsis','');
  $bare = cms_to_bool(get_parameter_value($params,'bare',0));
  
  $list = array();
  for( $i = 1; $i <= min($surround,$numpages); $i++ ) {
    $list[] = (int)$i;
  }

  $x1 = max(1,(int)($curpage - $surround/2));
  $x2 = min($numpages,(int)($curpage + $surround/2));
  for( $i = $x1; $i <= $x2; $i++ ) {
    $list[] = (int)$i;
  }

  for( $i = max(1,$numpages - $surround); $i <= $numpages; $i++ ) {
    $list[] = (int)$i;
  }

  $list = array_unique($list);
  sort($list);

  if ( $bare ) {
    $out = $list;
    if( $elipsis ) {
      $out = array();
      for( $i = 1; $i < count($list); $i++ ) {
	if( $list[$i-1] != $list[$i] - 1 ) $out[] = $elipsis;
	$out[] = $list[$i];
      }
    }
  }
  else {
    $out = '';
    $fmt = '<option value="%d">%s</option>';
    $fmt2 = '<option value="%d" selected="selected">%s</option>';
    foreach( $list as $pagenum ) {
      if( $pagenum == $curpage ) {
	$out .= sprintf($fmt2,$pagenum,$pagenum);
      }
      else {
	$out .= sprintf($fmt,$pagenum,$pagenum);
      }
    }
  }

  if( isset($params['assign']) ) {
    $smarty->assign($params['assign'],$out);
    return;
  }
  return $out;
}