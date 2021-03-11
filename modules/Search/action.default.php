<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
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
#---------------------------------------------------------------------------

if (!isset($gCms)) exit;

$template = null;
if (isset($params['formtemplate'])) {
    $template = trim($params['formtemplate']);
}
else {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('Search::searchform');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default summary template found');
        return;
    }
    $template = $tpl->get_name();
}

$cache_id = '|sr'.md5(serialize($params));
$compile_id = '';

$tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),$cache_id,$compile_id,$smarty);
if( !$tpl_ob->IsCached() ) {
    $inline = false;
    if( isset( $params['inline'] ) ) {
        $txt = strtolower(trim($params['inline']));
        if( $txt == 'true' || $txt == '1' || $txt == 'yes' ) $inline = true;
    }
    $origreturnid = $returnid;
    if( isset( $params['resultpage'] ) ) {
        $manager = $gCms->GetHierarchyManager();
        $node = $manager->sureGetNodeByAlias($params['resultpage']);
        if (isset($node)) {
            $returnid = $node->getID();
        }
        else {
            $node = $manager->sureGetNodeById($params['resultpage']);
            if (isset($node)) $returnid = $params['resultpage'];
        }
    }
    //Pretty Urls Compatibility
    $is_method = isset($params['search_method'])?'post':'get';

    // Variable named hogan in honor of moorezilla's Rhodesian Ridgeback :) https://forum.cmsmadesimple.org/index.php/topic,9580.0.html
    $submittext = (isset($params['submit'])) ? $params['submit'] : $this->Lang('searchsubmit');
    $searchtext = (isset($params['searchtext'])) ? $params['searchtext'] : $this->GetPreference('searchtext','');
    $tpl_ob->assign('search_actionid',$id);
    $tpl_ob->assign('searchtext',$searchtext);
    $tpl_ob->assign('destpage',$returnid);
    $tpl_ob->assign('form_method',$is_method);
    $tpl_ob->assign('inline',$inline);
    $tpl_ob->assign('startform', $this->CreateFormStart($id, 'dosearch', $returnid, $is_method, '', $inline ));
    $tpl_ob->assign('label', '<label for="'.$id.'searchinput">'.$this->Lang('search').'</label>');
    $tpl_ob->assign('searchprompt',$this->Lang('search'));
    //$tpl_ob->assign('inputbox', $this->CreateInputText($id, 'searchinput', $searchtext, 20, 50, $hogan));
    //$tpl_ob->assign('submitbutton', $this->CreateInputSubmit($id, 'submit', $submittext));
    $tpl_ob->assign('submittext', $submittext);

    // only here for backwards compatibility.
    $hogan = "onfocus=\"if(this.value==this.defaultValue) this.value='';\""." onblur=\"if(this.value=='') this.value=this.defaultValue;\"";
    $tpl_ob->assign('hogan',$hogan);

    $hidden = '';
    if( $origreturnid != $returnid ) $hidden .= $this->CreateInputHidden($id, 'origreturnid', $origreturnid);
    if( isset( $params['modules'] ) ) $hidden .= $this->CreateInputHidden( $id, 'modules', trim($params['modules']) );
    if( isset( $params['detailpage'] ) ) $hidden .= $this->CreateInputHidden( $id, 'detailpage', trim($params['detailpage']) );
    foreach( $params as $key => $value ) {
        if( preg_match( '/^passthru_/', $key ) > 0 ) $hidden .= $this->CreateInputHidden($id,$key,$value);
    }

    if( $hidden != '' ) $tpl_ob->assign('hidden',$hidden);
    $tpl_ob->assign('endform', $this->CreateFormEnd());
}

$tpl_ob->display();

#
# EOF
#