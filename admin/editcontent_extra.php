<?php

function ajaxpreview($params)
{
  $gCms = cmsms();
  CmsContentOperations::load_content_types();

  $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
  $config = cms_config();
  
  $content_type = $params['content_type'];
  $contentobj = UnserializeObject($params["serialized_content"]);
  if (get_class($contentobj) != $content_type)
    {
      copycontentobj($contentobj, $content_type, $params);
    }

  $editortype = CmsContentOperations::get_content_editor_type($contentobj);
  $editor = new $editortype($contentobj);
  $editor->fill_from_form_data($params);


  $tmpfname = createtmpfname($contentobj);
  // str_replace is because of stupid windows machines.... when will they die.
  $_SESSION['cms_preview'] = str_replace('\\','/',$tmpfname);
  $tmpvar = substr(str_shuffle(md5($tmpfname)),-3);
  $url = $config["root_url"].'/index.php?'.$config['query_var']."=__CMS_PREVIEW_PAGE__&r=$tmpvar"; // temporary
	
  $objResponse = new CmsAjaxResponse();
  $objResponse->replace("#previewframe", "src", $url);
  $objResponse->replace("#serialized_content", "value", SerializeObject($contentobj));
  $count = 0;
//   foreach ($contentobj->TabNames() as $tabname)
//     {
//       $objResponse->script("jQuery('#editteb{$count}').removeClass('active');");
//       $objResponse->script("jQuery('#edittab{$count}_c').removeClass('active');");
//       $objResponse->script("jQuery('#edittab{$count}_c').hide()");
//       //$objResponse->script("Element.removeClassName('editab".$count."', 'active');Element.removeClassName('editab".$count."_c', 'active');$('editab".$count."_c').style.display = 'none';");
//       $count++;
//     }

  $objResponse->script("jQuery('#edittabpreview').addClass('active');");
  $objResponse->script("jQuery('#edittabpreview_c').addClass('active');");
  $objResponse->script("jQuery('#edittabpreview_c').show();");
  //$objResponse->script("Element.addClassName('edittabpreview', 'active');Element.addClassName('edittabpreview_c', 'active');$('edittabpreview_c').style.display = '';");
  return $objResponse->get_result();
}

function updatecontentobj(&$contentobj, $preview = false, $params = null)
{
	if ($params == null)
		$params = $_POST;

	$userid = get_userid();
	$adminaccess = check_ownership($userid, $contentobj->Id()) || check_permission($userid, 'Modify Any Page');
		
	#Fill contentobj with parameters
	$contentobj->FillParams($params);
	if ($preview)
	{
		$error = $contentobj->ValidateData();
	}

	if (isset($params["ownerid"]))
	{
		$contentobj->SetOwner($params["ownerid"]);
	}

	$contentobj->SetLastModifiedBy($userid);

// 	#Fill Additional Editors (kind of kludgy)
// 	if (isset($params["additional_editors"]))
// 	{
// 		$addtarray = array();
// 		foreach ($params["additional_editors"] as $addt_user_id)
// 		{
// 			$addtarray[] = $addt_user_id;
// 		}
// 		$contentobj->SetAdditionalEditors($addtarray);
// 	}
// 	else if ($adminaccess)
// 	{
// 		$contentobj->SetAdditionalEditors(array());
// 	}
}

function copycontentobj(&$contentobj, $content_type, $params = null)
{
	global $gCms;
	
	if ($params == null)
		$params = $_POST;

	$tmpobj = CmsContentOperations::clone_content_as($contentobj,$content_type);
	$contentobj = $tmpobj;
}

function createtmpfname(&$contentobj)
{
	global $gCms;
	$config =& $gCms->GetConfig();
	$templateops =& $gCms->GetTemplateOperations();

	$data["content_id"] = $contentobj->Id();
	$data['content_type'] = $contentobj->Type();
	$data["title"] = $contentobj->Name();
	$data["menutext"] = $contentobj->MenuText();
	$data["template_id"] = $contentobj->TemplateId();
	$data["hierarchy"] = $contentobj->Hierarchy();
	
	$templateobj = $templateops->LoadTemplateById($contentobj->TemplateId());
	$data['template'] = $templateobj->content;

	$stylesheetobj = get_stylesheet($contentobj->TemplateId());
	$data['encoding'] = $stylesheetobj['encoding'];
	$data['serialized_content'] = serialize($contentobj);

	$tmpfname = '';
	if (is_writable($config["previews_path"]))
	{
		$tmpfname = tempnam($config["previews_path"], "cmspreview");
	}
	else
	{
		$tmpfname = tempnam(TMP_CACHE_LOCATION, "cmspreview");
	}
	$handle = fopen($tmpfname, "w");
	fwrite($handle, serialize($data));
	fclose($handle);

	return $tmpfname;
}

?>