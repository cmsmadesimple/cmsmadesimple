<?php

@error_reporting(E_ERROR);

?>
<form action="<?=$this->CreateLink($id, 'defaultadmin', $returnid, '', array('active_tab'=>'settings'), '', true, true, '');?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table>
    <tr>
      <td width="160"><?=$this->Lang('defwidth')?>:</td>
      <td width="400"><input type="text" name="<?=$id?>defwidth" id="defwidth" value="<?=$this->getPreference('defwidth')?>" /></td>
    </tr>
    <tr>
      <td valign="top"><?=$this->Lang('defheight')?> :</td>
      <td><input type="text" name="<?=$id?>defheight" id="defheight" value="<?=$this->getPreference('defheight')?>" /></td>
    </tr>
    <tr>
      <td valign="top"><?=$this->Lang('deffadetime')?> :</td>
      <td><input type="text" name="<?=$id?>deffadetime" id="deffadetime" value="<?=$this->getPreference('deffadetime')?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="<?=$id?>updatesettings" id="updatesettings" value="<?=$this->Lang('update')?>" /></td>
    </tr>
  </table>
</form>
