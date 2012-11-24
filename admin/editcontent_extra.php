<?php

function ajaxpreview($params)
{
  $gCms = cmsms();
	$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	$config = $gCms->GetConfig();
	$contentops = $gCms->GetContentOperations();

	$content_type = $params['content_type'];

	global $contentobj;
	if( !is_object($contentobj) ) {
	  return;
	}
// 	$contentobj = UnserializeObject($params["serialized_content"]);
// 	if (strtolower(get_class($contentobj)) != strtolower($content_type))
// 	{
// 		copycontentobj($contentobj, $content_type, $params);
// 	}
	updatecontentobj($contentobj, true, $params);
	$tmpfname = createtmpfname($contentobj);
	// str_replace is because of stupid windows machines.... when will they die.
	$_SESSION['cms_preview'] = str_replace('\\','/',$tmpfname);
	$tmpvar = substr(str_shuffle(md5($tmpfname)),-3);
	$url = $config["root_url"].'/index.php?'.$config['query_var']."=__CMS_PREVIEW_PAGE__&r=$tmpvar"; // temporary

	$objResponse = new xajaxResponse();
	$objResponse->assign("previewframe", "src", $url);
	$objResponse->assign("serialized_content", "value", SerializeObject($contentobj));
	$count = 0;
	foreach ($contentobj->TabNames() as $tabname)
	{
		$objResponse->script("Element.removeClassName('editab".$count."', 'active');Element.removeClassName('editab".$count."_c', 'active');$('editab".$count."_c').style.display = 'none';");
		$count++;
	}
	$objResponse->script("Element.addClassName('edittabpreview', 'active');Element.addClassName('edittabpreview_c', 'active');$('edittabpreview_c').style.display = '';");
	return $objResponse;
}

function updatecontentobj(&$contentobj, $preview = false, $params = null)
{
	if ($params == null)
		$params = $_POST;

	$userid = get_userid();
//	$adminaccess = check_ownership($userid, $contentobj->Id()) || check_permission($userid, 'Modify Any Page');
		
	#Fill contentobj with parameters
	$contentobj->FillParams($params,true);
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
  $contentops = cmsms()->GetContentOperations();
	
	if ($params == null)
		$params = $_POST;

	$newcontenttype = strtolower($content_type);
	$tmpobj = $contentops->CreateNewContent($newcontenttype);
	$tmpobj->SetId($contentobj->Id());
	$tmpobj->SetName($contentobj->Name());
	$tmpobj->SetMenuText($contentobj->MenuText());
	$tmpobj->SetTemplateId($contentobj->TemplateId());
	$tmpobj->SetParentId($contentobj->ParentId());
	$tmpobj->SetOldParentId($contentobj->OldParentId());
	$tmpobj->SetAlias($contentobj->Alias());
	$tmpobj->SetOwner($contentobj->Owner());
	$tmpobj->SetActive($contentobj->Active());
	$tmpobj->SetItemOrder($contentobj->ItemOrder());
	$tmpobj->SetOldItemOrder($contentobj->OldItemOrder());
	$tmpobj->SetShowInMenu($contentobj->ShowInMenu());
	$tmpobj->SetCachable($contentobj->Cachable());
	$tmpobj->SetHierarchy($contentobj->Hierarchy());
	$tmpobj->SetLastModifiedBy($contentobj->LastModifiedBy());
	$tmpobj->SetAdditionalEditors($contentobj->GetAdditionalEditors());
	$tmpobj->Properties();
	$contentobj = $tmpobj;
	$contentobj->FillParams($params);
}

function createtmpfname(&$contentobj)
{
  $gCms = cmsms();
	$config = $gCms->GetConfig();
	$templateops = $gCms->GetTemplateOperations();

	$data["content_id"] = $contentobj->Id();
	$data['content_type'] = $contentobj->Type();
	$data["title"] = $contentobj->Name();
	$data["menutext"] = $contentobj->MenuText();
	$data["content"] = $contentobj->Show();
	$data["template_id"] = $contentobj->TemplateId();
	$data["hierarchy"] = $contentobj->Hierarchy();
	
	$templateobj = $templateops->LoadTemplateById($contentobj->TemplateId());
	$data['template'] = $templateobj->content;

	$data['encoding'] = get_encoding();
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