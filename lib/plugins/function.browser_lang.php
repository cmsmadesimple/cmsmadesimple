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

function smarty_function_browser_lang($params, &$smarty)
{
  $default = 'en';
  $res = null;

  //
  // get the default language
  //
  if( isset($params['default']) ) $default = strtolower(substr($params['default'],0,2));

  // 
  // get the accepted languages
  //
  $accept_str = get_parameter_value( $params, 'accepted' );
  if( !$accept_str ) $accept_str = get_parameter_value( $params, 'accept' );
  if( $accept_str ) {
      $tmp = trim($accept_str);
      $tmp = trim($tmp,',');
      $tmp2 = explode(',',$tmp);
      if( is_array($tmp2) && count($tmp2) > 0 ) {
          $accepted = [];
          for( $i = 0; $i < count($tmp2); $i++ ) {
              if( strlen($tmp2[$i]) < 2 ) continue;
              $accepted[] = strtolower(substr($tmp2[$i],0,2));
          }

          //
          // process the accepted languages and the default
          // makes sure the array is unique, and that the default
          // is listed first
          //
          $accepted = array_merge(array($default),$accepted);
          $accepted = array_unique($accepted);
          
          // 
          // now process browser language
          //
          $res = $default;
          if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
              $alllang = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
              if (strpos($alllang, ";") !== FALSE) $alllang = substr($alllang,0,strpos($alllang, ";"));
              $langs = explode(",", $alllang);
              if( is_array($langs) && count($langs) ) {
                  foreach( $langs as $one ) {
                      $tmp = strtolower(substr($one,0,2));
                      if( in_array($tmp,$accepted) ) {
                          $res = $tmp;
                          break;
                      }
                  }
              }
          }
      }
  }

  if( isset($params['assign']) ) {
		$smarty->assign(trim($params['assign']),$res);
		return;
  }
  
  return $res;
}

function smarty_cms_about_function_browser_lang()
{
?>
	<p>Author: Robert Campbell &lt;calguy1000@cmsmadesimple.org&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>Written for CMSMS 1.9</li>
	</ul>
<?php
}
?>
