<?php
$inclfilename = '../include.php';

while(!@file_exists($inclfilename)){
            $inclfilename = "../".$inclfilename;
}
@require_once($inclfilename);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>CMS Link - Insert CMS Made Simple Link</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta content="noindex, nofollow" name="robots">
		<script type="text/javascript" src="fckcmscontent.js"></script>
		<script type="text/javascript">
		<!--
			var oEditor			= window.parent.InnerDialogLoaded(); 
			var FCK				= oEditor.FCK; 
			var FCKLang			= oEditor.FCKLang ;
			var FCKConfig		= oEditor.FCKConfig ;
			var FCKCMSContent	= oEditor.FCKCMSContent; 
			 
			// oLink: The actual selected link in the editor.
			var oLink = FCK.Selection.MoveToAncestorNode( 'A' ) ;
			if ( oLink )
				FCK.Selection.SelectNode( oLink ) ;
	
			window.onload = function ()	{ 
				// First of all, translates the dialog box texts.
				oEditor.FCKLanguageManager.TranslatePage(document);
				
				LoadSelected();							//See function below 
				window.parent.SetOkButton( true );		//Show the "Ok" button. 
				
			} 
			 
			//If an anchor (A) object is currently selected, load the properties into the dialog 
			function LoadSelected()	{
				var sSelected;
				
				if ( oEditor.FCKBrowserInfo.IsGecko ) {
					sSelected = FCK.EditorWindow.getSelection();
				} else {
					sSelected = FCK.EditorDocument.selection.createRange().text;
				}

				if ( sSelected == "" ) {
					alert( 'Please select a text in order to create a (internal) link' );
				}

			}

			//Code that runs after the OK button is clicked 
			function Ok() {
				//Validate is option is selected
				var oPageList = document.getElementById( 'cmbPages' ) ;
				if(oPageList.selectedIndex == -1) {
					alert('Please select a page in order to create a link');
					return false;
				}
				
				var oTagLink = document.getElementById( 'chkTaglink' );
				
				if (oTagLink.checked) {
					//Create special CMS tag
					var sSelected;
					
					if ( oEditor.FCKBrowserInfo.IsGecko ) {
						sSelected = FCK.EditorWindow.getSelection();
					} else {
						sSelected = FCK.EditorDocument.selection.createRange().text;
					}
				
					var sPageId = oPageList[oPageList.selectedIndex].value;
					var sTagOutput = "{cms_selflink page='" + sPageId + "' text='" + sSelected + "'}";
					
					oEditor.FCK.InsertHtml( sTagOutput );
								
				} else {
					var sURL = document.getElementById( 'PageURL' ) ; 
					var sPageId = oPageList[oPageList.selectedIndex].value;
					oLink = oEditor.FCK.CreateLink( sURL.value + sPageId ) ;
					SetAttribute( oLink, 'title'	, document.getElementById( 'txtTitle' ).value ) ;
				}
				return true;
			} 
			
		//-->
		</script>
	</head>
			
	<body scroll="no" style="overflow:hidden;">
	
		 <input type="hidden" id="PageURL" value="<?php echo $config["root_url"]."/index.php?".$config['query_var']."=" ?>" />
		 <table height="100%" cellSpacing="0" cellPadding="0" width="100%" border="0"> 
		 	<tr> 
				<td>
					<table width="100%">
						<tr>
							<td colspan="2"><span fckLang="DlgCMSCommentPageSelection">Select a CMS Page to link to:</span>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2">
								<select id="cmbPages" style="WIDTH: 100%" size="8" name="cmbPages">
<?php
								$content_array = ContentManager::GetAllContent(false);
								
								foreach ($content_array as $one) {
									if ($one->FriendlyName() == 'Separator') {
										continue;
									}
									echo "<option value=\"".$one->Id()."\">".$one->Hierarchy()." ".$one->Name()."</value>";	
								}
?>
								</select>
							</td>
						</tr>
						<tr>
							<td nowrap><span fckLang="DlgCMSCommentTitle">Title</span>&nbsp;</td>
							<td width="100%" style="align:right;"><input id="txtTitle" style="WIDTH: 98%" type="text" name="txtTitle"></td>
						</tr>
						<tr>
							<td nowrap><span fckLang="DlgCMSCommentTagLink">Generate taglink?</span>&nbsp;</td>
							<td><input id="chkTaglink" type="checkbox" name="chkTaglink" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
	</body>
</html> 