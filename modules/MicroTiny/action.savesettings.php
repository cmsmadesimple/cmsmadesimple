<?php
if (!$this->VisibleToAdminUser()) $this->Redirect($id,"defaultadmin",$returnid);

$this->SetPreference('show_statusbar',(isset($params['show_statusbar']))?1:0);
$this->SetPreference('allow_resize',(isset($params['allow_resize']))?1:0);
$this->SetPreference('strip_background',(isset($params['strip_background']))?1:0);
$this->SetPreference('force_blackonwhite',(isset($params['force_blackonwhite']))?1:0);

if (isset($params['allowimages'])) $this->SetPreference('allowimages', 1 ); else $this->SetPreference('allowimages', 0 );

if (isset($params['css_styles'])) $this->SetPreference('css_styles',$params['css_styles']);

$this->Redirect($id,"defaultadmin",$returnid,array("module_message"=>$this->Lang("settingssaved"),"tab"=>"settings"));
?>
