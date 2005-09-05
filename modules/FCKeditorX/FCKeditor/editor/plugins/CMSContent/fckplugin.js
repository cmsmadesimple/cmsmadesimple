/* 
 *  FCKPlugin.js
 *  ------------
 *  This is a generic file which is needed for plugins that are developed
 *  for FCKEditor. With the below statements that toolbar is created and
 *  several options are being activated.
 *
 *  See the online documentation for more information:
 *  http://wiki.fckeditor.net/
 */

// Register the related commands.
FCKCommands.RegisterCommand(
	'CMSContent',
	new FCKDialogCommand(
		'CMSContent',
		FCKLang["CMSContentDlgTitle"],
		FCKPlugins.Items['CMSContent'].Path + 'fck_cmscontent.php',
		400,
		300
	)
);
 
// Create the "CMSContent" toolbar button.
// FCKToolbarButton( commandName, label, tooltip, style, sourceView, contextSensitive )
var oCMSContentItem = new FCKToolbarButton( 'CMSContent', FCKLang["CMSContentBtn"], null, null, false, true ); 
oCMSContentItem.IconPath = FCKConfig.PluginsPath + 'CMSContent/cmscontent.gif'; 

// 'CMSContent' is the name that is used in the toolbar config.
FCKToolbarItems.RegisterItem( 'CMSContent', oCMSContentItem );
