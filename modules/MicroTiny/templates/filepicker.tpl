<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{$mod->Lang('filepickertitle')}</title>
		<!-- tinymce stylesheet will be replaced with custom styles //-->
		<link rel="stylesheet" type="text/css" href="{$mod->GetModuleURLPath()}/lib/js/tinymce/skins/lightgray/content.min.css" />
		<link rel="stylesheet" type="text/css" href="{$mod->GetModuleURLPath()}/lib/js/tinymce/skins/lightgray/skin.min.css" />
		<link rel="stylesheet" type="text/css" href="{$mod->GetModuleURLPath()}/lib/css/filepicker.css" />
		<script type="text/javascript" src="{root_url}/lib/jquery/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="{root_url}/admin/cms_js_setup.php?_sk_=8ba24bf9ca02a992"></script>
		<!-- this part will change and will be moved to app.js or so //-->
		<script language="javascript" type="text/javascript">
            $(document).ready(function() {
                $('a.file_action').click(function(e) {
                    e.preventDefault();
                    var win = top.tinymce.activeEditor.windowManager.getParams().window;
                    var el = win.document.getElementById('{$field}');
                    el.value = $(this).attr('href');
                    el.selectionStart = el.selectionEnd = el.value.length
                    top.tinymce.activeEditor.windowManager.close();
                });
            });
		</script>
	</head>
	<body>
		<div id="full-fp">
			<div class="filepicker-navbar-inner">
				Is filepicker going to have any options? sort by, search, add folder, upload image?
			</div>
			<div class="filepicker-container">
				<div class="filepicker-breadcrumb">
					<p><i class="mce-ico mce-i-browse filepicker-icon"></i>{$mod->Lang('youareintext')}: {$startpath}</p>
				</div>
				<!-- this stuff will change, creating optional views, like grid or list //-->
				<div id="filelist" class="mce-container-body mce-abs-layout">
					<table class="filepicker-table">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>{$mod->Lang('dimensions')}</th>
								<th>{$mod->Lang('size')}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $files as $file}
							{$type_alt='File'}
							{if $file.isdir}{$type_alt='Folder'}{/if}
							<tr>
								<td> {if $showthumbnails && isset($file.thumbnail) && $file.thumbnail != ''}
								{$file.thumbnail}{* already an img tag *}
								{elseif $file.isdir} <a class="dir_icon" href="{$file.chdir_url}">{$file.icon}</a> {else}
								{$file.icon}
								{/if} </td>
								<td class="info-text"> {if $file.isdir} <a class="dir_action" href="{$file.chdir_url}">{$file.name}</a> {else} <a class="file_action" href="{$file.fullurl}">{$file.name}</a> {/if} </td>
								<td class="info-text"> {$file.dimensions|default:''} </td>
								<td class="info-text"> {$file.size|default:''} </td>
							</tr>
							{/foreach}
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
