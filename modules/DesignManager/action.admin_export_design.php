<?php
#-------------------------------------------------------------------------
# Module: DesignManager - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Manage Designs') ) return;

$this->SetCurrentTab('designs');

if( !isset($params['design']) || $params['design'] == '' ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}


$ref_map = array();
function _ref_map_get_sig($fn,$type = 'URL')
{
  global $ref_map;
  if( count($ref_map) > 0 ) {
    foreach( $ref_map as $key => $val ) {
      if( $fn == $val ) return $key;
    }
  }
  $sig = '__'.$type.'::'.md5($fn).'__';
  $ref_map[$sig] = $fn;
  return $sig;
}

function _get_css_urls($css_content)
{
  $content = $css_content;
  $regex='/url\s*\(\"*(.*)\"*\)/i';
  $content = preg_replace_callback($regex,
				   function($matches) {
				     $config = cmsms()->GetConfig();
				     $url = $matches[1];
				     if( !startswith($url,'http') || startswith($url,$config['root_url']) || startswith($url,'[[root_url]]') ) {
				       $sig = _ref_map_get_sig($url);
				       $sig = "url(".$sig.")";
				       return $sig;
				     }
				     return $matches[0];
				   },
				   $content);

  return $content;
}

function _get_sub_templates( $template )
{
  $t_matches_a = array();

  $replace_fn = function($matches) {
    $out = preg_replace_callback("/template\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
				 function($matches){
				   $type = 'TPL';
				   if( endswith($matches[1],'.tpl') ) $type = 'MM';
				   $sig = _ref_map_get_sig($matches[1],$type);
				   return str_replace($matches[1],$sig,$matches[0]);
				 },$matches[0]);
    return $out;
  };

  $replace_fn2 = function($matches) {
    $out = preg_replace_callback("/name\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
				 function($matches){
				   $sig = _ref_map_get_sig($matches[1],'TPL');
				   return str_replace($matches[1],$sig,$matches[0]);
				 },$matches[0]);
    return $out;
  };

  $replace_fn3 = function($matches) {
    $out = preg_replace_callback("/file\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i",
				 function($matches){
				   $bad = array('string:','startswith','module_db_tpl','module_file_tpl',
						'tpl_top','tpl_body','tpl_head','file:http');
				   foreach( $bad as $badone ) {
				     if( endswith($matches[1],$badone) ) return $matches[0];
				   }

				   $sig = _ref_map_get_sig($matches[1],'TPL');
				   return str_replace($matches[1],$sig,$matches[0]);
				 },$matches[0]);
    return $out;
  };

  $regex='/\{menu.*\}/';
  $template = preg_replace_callback( $regex, $replace_fn, $template );

  $regex='/\{.*MenuManager.*\}/';
  $template = preg_replace_callback( $regex, $replace_fn, $template );

  $regex='/\{global_content.*\}/';
  $template = preg_replace_callback( $regex, $replace_fn2, $template );

  $regex='/\{include.*\}/';
  $template = preg_replace_callback( $regex, $replace_fn3, $template );

  return $template;
}


function _get_tpl_urls($tpl_content)
{
  $content = $tpl_content;
  $types = array("href", "src", "url");
  while(list(,$type) = each($types)) {
    $innerT = '[a-z0-9:?=&@/._-]+?';
    $content = preg_replace_callback("|$type\=([\"'`])(".$innerT.")\\1|i",
				     function($matches) {
				       $config = cmsms()->GetConfig();
				       $url = $matches[2];
				       if( !startswith($url,'http') || startswith($url,$config['root_url']) || startswith($url,'{root_url}') ) {
					 $sig = _ref_map_get_sig($url);
					 return $sig;
				       }
				       return $matches[0];
				     },
				     $content);
  }
  return $content;
}

try {
	// and the work...
    $the_design = CmsLayoutCollection::load($params['design']);
    $exporter = new dm_design_exporter($the_design);
	$xml = $exporter->get_xml();

	// clear any output buffers.
	$handlers = ob_list_handlers();
	for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

	// headers
	header('Content-Description: File Transfer');
	header('Content-Type: application/force-download');
	header('Content-Disposition: attachment; filename='.munge_string_to_url($the_design->get_name()).'.xml');

	// output
	echo $xml;
    exit();
}
catch( \Exception $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

#
# EOF
#
?>
