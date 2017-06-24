<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# cms_selflink (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A function for retrieving A url or link to an internal CMSMS Page.
#  This is a rewrite of the original cms_selflink plugin by Ted Kulk and various authors.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004-2013 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
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
#END_LICENSE

function smarty_function_cms_selflink($params, &$smarty)
{
    $gCms = \CmsApp::get_instance();
	$manager = $gCms->GetHierarchyManager();
	$url = '';
	$urlparam = '';
	$label_side = 'left';
	$label = '';
	$urlonly = 0;
	$node = null;
	$dir = null;
	$pageid = null;

	$rellink = (isset($params['rellink']) && $params['rellink'] == '1' ? true : false);
	if ( isset($params['urlparam']) && ( strlen($params['urlparam']) > 0 ) ) $urlparam = trim($params['urlparam']);

	if (isset($params['page']) or isset($params['href'])) {
		$page = null;
		if (isset($params['href'])) {
			$page = trim($params['href']);
			$urlonly = 1;
		}
		else {
			$page = trim($params['page']);
		}

		if( $page ) {
            if( (int)$page > 0 && is_numeric($page) ) {
                $pageid = (int)$page;
            }
            else {
                $page = cms_html_entity_decode($page); // decode entities (alias may be encoded if entered in WYSIWYG)
                $node = $manager->find_by_tag('alias',$page);
                if( $node ) $pageid = $node->get_tag('id');
            }
		}
	}

	else if( isset($params['dir']) ) {
		$startpage = null;
		if( $pageid ) $startpage = $pageid;
		if( !$startpage ) $startpage = $gCms->get_content_id();
		$dir = strtolower(trim($params['dir']));

		switch( $dir ) {
		case 'next':
			// next visible page.
			$flatcontent = $manager->getFlatList();
			$indexes = array_keys($flatcontent);
			$i = array_search($startpage,$indexes);
			if( $i < count($flatcontent) ) {
				for( $j = $i + 1; $j < count($flatcontent); $j++ ) {
					$k = $indexes[$j];
					$content = $flatcontent[$k]->getContent();
					if( !is_object($content) ) continue;
					if( !$content->Active() || !$content->HasUsableLink() || !$content->ShowInMenu() ) continue;
					$pageid = $content->Id();
					$label = CmsLangOperations::lang_from_realm('cms_selflink','next_label');
					break;
				}
			}
			break;

		case 'nextpeer':
		case 'nextsibling':
			// next valid peer page.
			$node = $manager->find_by_tag('id',$startpage);
			if( !$node ) return;
			$parent = $node->get_parent();
			if( !$parent ) $parent = $manager;
			$children = $parent->get_children();
			for( $i = 0; $i < count($children); $i++ ) {
				$id = $children[$i]->get_tag('id');
				if( $id == $startpage ) break;
			}
			if( $i < count($children) ) {
				for( $j = $i + 1; $j < count($children); $j++ ) {
					$content = $children[$j]->getContent();
					if( !is_object($content) ) continue;
					if( !$content->Active() || !$content->HasUsableLink() || !$content->ShowInMenu() ) continue;
					$pageid = $content->Id();
					$label = CmsLangOperations::lang_from_realm('cms_selflink','next_label');
					break;
				}
			}
			break;

		case 'prev':
		case 'previous':
			// previous visible page.
			$flatcontent = $manager->getFlatList();
			$indexes = array_keys($flatcontent);
			$i = array_search($startpage,$indexes);
			if( $i !== FALSE ) {
				for( $j = $i - 1; $j >= 0; $j-- ) {
					$k = $indexes[$j];
					$content = $flatcontent[$k]->getContent();
					if( !is_object($content) || !$content->Active() || !$content->HasUsableLink() || !$content->ShowInMenu() ) continue;
					$pageid = $content->Id();
					$label = CmsLangOperations::lang_from_realm('cms_selflink','prev_label');
					break;
				}
			}
			break;

		case 'prevpeer':
		case 'prevsibling':
			// previous valid peer page.
			$node = $manager->find_by_tag('id',$startpage);
			if( !$node ) return;
			$parent = $node->get_parent();
			if( !$parent ) $parent = $manager;
			$children = $parent->get_children();
			for( $i = 0; $i < count($children); $i++ ) {
				$id = $children[$i]->get_tag('id');
				if( $id == $startpage ) break;
			}
			if( $i < count($children) ) {
				for( $j = $i - 1; $j >= 0; $j-- ) {
					$content = $children[$j]->getContent();
					if( !is_object($content) || !$content->Active() || !$content->HasUsableLink() || !$content->ShowInMenu() ) continue;
					$pageid = $content->Id();
					$label = CmsLangOperations::lang_from_realm('cms_selflink','prev_label');
					break;
				}
			}
			break;

		case 'start':
			// default home page
            $contentops = ContentOperations::get_instance();
			$pageid = $contentops->GetDefaultPageId();
			break;

		case 'up':
			// parent page.
			$node = $manager->find_by_tag('id',$startpage);
			if( !$node ) return;
			$node = $node->get_parent();
			if( !$node ) return;
			$content = $node->GetContent();
			if( !$content ) return;
			$pageid = $content->Id();
			break;

		default:
			// unknown direction... prolly should do something here.
			return;
		}
	}

	if( $pageid == '' ) return;

	// one final check to see if this page exists.
	$node = $manager->find_by_tag('id',$pageid);
	if( !$node ) return;

	// get the content object.
	$content = $node->GetContent();
	if( !$content || !is_object($content) || !$content->Active() || !$content->HasUsableLink() ) return;

	// get our raw display data
	$alias = $content->Alias();
	$name = $content->Name();
	$url = $content->GetUrl();
	$menu_text = $content->MenuText();
	$titleattr = $content->TitleAttribute();
	if (isset($params['anchorlink'])) $url .= '#' . ltrim($params['anchorlink'], '#');
	if( $urlparam != '' ) $url .= $urlparam;

	if( empty($url) ) return; // no url to link to, therefore nothing to do.

	if( isset($params['urlonly']) ) $urlonly = cms_to_bool($params['urlonly']);

	if( $urlonly ) {
		if( isset($params['assign']) ) {
			$smarty->assign(trim($params['assign']),$url);
			return;
		}
		return $url;
	}

	// Now we build the output.
	$result = "";
	if (isset($params['label'])) {
		$label = $params['label'];
		$label = cms_htmlentities($label);
	}

	$title = (isset($name)) ? $name : '';
	if( isset($params['title']) ) {
		$title = $params['title'];
	}
	else if( !empty($titleattr) ) {
		$title = $titleattr;
	}
	$title = cms_htmlentities(strip_tags($title));

	if ($rellink && $dir != '' ) {
		// output a relative link.
		$result .= '<link rel="';
		switch($dir) {
		case 'prev':
		case 'previous':
			$result .= 'prev';
			break;

		case 'start':
		case 'anchor':
		case 'next':
		case 'up':
			$result .= $dir;
			break;
		}

		$result .= '" title="'.$title.'" ';
		$result .= 'href="'.$url.'" />';
	}
	else {
		if( isset($params['label_side']) ) $label_side = strtolower(trim($params['label_side']));
		if( $label_side == 'left' ) $result .= $label.' ';
		$result .= '<a href="'.$url.'"';
		$result .= ' title="'.$title.'" ';
		if (isset($params['target'])) $result .= ' target="'.$params['target'].'"';
		if (isset($params['id'])) $result .= ' id="'.$params['id'].'"';
		if (isset($params['class'])) $result .= ' class="'.$params['class'].'"';
		if (isset($params['tabindex'])) $result .= ' tabindex="'.$params['tabindex'].'"';
		if (isset($params['more'])) $result .= ' '.$params['more'];
		$result .= '>';

		$linktext = $name;
		if (isset($params['text'])) {
			$linktext = $params['text'];
		} elseif (isset($params['menu']) && $params['menu'] == "1") {
			$linktext = $menu_text;
		}

		if (isset($params['image']) && ! empty($params['image'])) {
			$width = (isset($params['width']) && !empty($params['width'])) ? (int)$params['width'] : '';
			$height = (isset($params['height']) && !empty($params['height'])) ? (int)$params['height'] : '';
			$alt = (isset($params['alt']) && ! empty($params['alt'])) ? $params['alt'] : '';
			$result .= "<img src=\"{$params['image']}\" alt=\"$alt\"";
			if( $width ) $width = max(1,$width);
			if( $width ) $result .= " width=\"$width\"";
			if( $height ) $height = max(1,$height);
			if( $height ) $result .= " height=\"$height\"";
			$result .= "/>";
			if (! (isset($params['imageonly']) && $params['imageonly'])) $result .= " $linktext";
		} else {
			$result .= $linktext;
		}

		$result .= '</a>';
		if( $label_side == 'right' ) $result .= ' '.$label;
	}

	$result = trim($result);
	if( isset($params['assign']) ){
		$smarty->assign(trim($params['assign']),$result);
		return;
	}
	return $result;
}

function smarty_cms_help_function_cms_selflink() {
    echo lang_by_realm('tags','help_function_cms_selflink');
}

?>
