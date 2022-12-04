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

function smarty_function_anchor($params, $smarty)
{
	$to = (isset($params['anchor'])) ? trim($params['anchor']) : '';
	if( $to === '' ) return '<!-- anchor tag: no anchor provided -->';

    //current content useless for runtime-populated pages e.g. News details
    if( !empty($_SERVER['QUERY_STRING']) ) {
        //$_SERVER['QUERY_STRING'] like 'page=news/99/107/somename'
        $config = cmsms()->GetConfig();
        $tmp = $config['query_var'].'=';
        $path = str_replace($tmp,'',$_SERVER['QUERY_STRING']);
        $url = $config['root_url'].'/'.trim($path,' /');
    }
    else {
        $content = cms_utils::get_current_content();
        if( !is_object($content) ) return '';
        $url = $content->GetURL();
    }

    $class = "";
    $title = "";
    $tabindex = "";
    $accesskey = "";
    if( isset($params['class']) && $params['class'] !== '' ) $class = ' class="'.$params['class'].'"';
    if( isset($params['title']) && $params['title'] !== '' ) $title = ' title="'.$params['title'].'"';
    if( isset($params['tabindex']) && $params['tabindex'] !== '' ) $tabindex = ' tabindex="'.(int)$params['tabindex'].'"';
    if( isset($params['accesskey']) && $params['accesskey'] !== '' ) $accesskey = ' accesskey="'.$params['accesskey'].'"';

    $url = preg_replace('/&(?!amp;)/','&amp;',$url.'#'.rawurlencode($to));
    if( !empty($params['onlyhref']) && cms_to_bool($params['onlyhref']) ) {
        $tmp = $url;
    }
    else {
        $text = trim(get_parameter_value($params,'text'));
        if( $text === '' ) {
            $text = htmlentities($to).'<!-- anchor tag: no text provided -->';
        }
        $tmp = '<a href="'.$url.'"'.$class.$title.$tabindex.$accesskey.'>'.$text.'</a>';
    }

    if( isset($params['assign']) ) {
        $smarty->assign(trim($params['assign']),$tmp);
        return '';
    }
    return $tmp;
}
?>
