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
	'CMSModules',
	new FCKDialogCommand(
		'CMSModules',
		FCKLang["CMSModulesDlgTitle"],
		FCKPlugins.Items['CMSModules'].Path + 'fck_cmsmodules.php',
		600,
		500
	)
);
 
// Create the "CMSContent" toolbar button.
// FCKToolbarButton( commandName, label, tooltip, style, sourceView, contextSensitive )
var oCMSModulesItem = new FCKToolbarButton( 'CMSModules', FCKLang["CMSModulesBtn"], null, null, false, true ); 
oCMSModulesItem.IconPath = FCKConfig.PluginsPath + 'CMSModules/cmsmodules.gif'; 

// 'CMSContent' is the name that is used in the toolbar config.
FCKToolbarItems.RegisterItem( 'CMSModules', oCMSModulesItem );
