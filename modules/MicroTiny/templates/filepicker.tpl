<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{$mod->Lang('filepickertitle')}</title>
		<link rel="stylesheet" type="text/css" href="{$mod->GetModuleURLPath()}/lib/css/filepicker.css" />
		<script type="text/javascript" src="{root_url}/lib/jquery/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="{root_url}/admin/cms_js_setup.php?_sk_=8ba24bf9ca02a992"></script>
		<script type="text/javascript" src="{$mod->GetModuleURLPath()}/lib/js/tinymce/plugins/cmsms_filebrowser/filebrowser.js"></script>
		<script type="text/javascript">
			var filebrowser_global = {
				field_id : '{$field}'
			}
		</script>
	</head>
	{strip}
	<body class="cmsms-filepicker">
		<div id="full-fp">
			<div class="filepicker-navbar-inner">
				<div class="filepicker-view-option">
					<p>File View:&nbsp; 
						<span class="js-trigger view-list filepicker-button" title="List"><i class="cmsms-fp-th-list"></i></span>&nbsp;
						<span class="js-trigger view-grid filepicker-button" title="Grid"><i class="cmsms-fp-th"></i></span>
					</p>
				</div>
			</div>
			<div class="filepicker-container">
				<div class="filepicker-breadcrumb">
					<p title="{$mod->Lang('youareintext')}:"><i class="cmsms-fp-folder-open filepicker-icon"></i> {$startpath}</p>
				</div>
				<div id="filelist">
					<ul class="filepicker-list" id="filepicker-items">
						{foreach $files as $file}
						{$type_alt='File'}
						{if $file.isdir}{$type_alt='Folder'}{/if}
						<li class="filepicker-item" data-fb-filetype='{$file.filetype}' data-fb-ext='{$file.ext}'>
							<div class="filepicker-thumb{if (isset($file.thumbnail) && $file.thumbnail != '') || $file.isdir} no-background{/if}">
							{if $showthumbnails && isset($file.thumbnail) && $file.thumbnail != ''}
								<a class="filepicker-file-action" href="{$file.fullurl}">{$file.thumbnail}</a>
							{elseif $file.isdir}
								<a class="icon-no-thumb" href="{$file.chdir_url}"><i class="cmsms-fp-folder-close"></i></a>
							{else}
								<a class="filepicker-file-action icon-no-thumb" href="{$file.fullurl}">
									{if $file.filetype == 'image'}
										<i class="cmsms-fp-picture"></i>
									{elseif $file.filetype == 'video'}
										<i class="cmsms-fp-facetime-video"></i>
									{elseif $file.filetype == 'audio'}
										<i class="cmsms-fp-music"></i>
									{elseif $file.filetype == 'archive'} 
										<i class="cmsms-fp-zip"></i>
									{else}
										<i class="cmsms-fp-file"></i>
									{/if}
								</a>
							{/if}
							{*<pre>{$file|print_r}</pre>*}
							</div>
							<div class="filepicker-file-information">
								<h4 class="filepicker-file-title">
								{if $file.isdir}
									<a class="filepicker-dir-action" href="{$file.chdir_url}">{$file.name}</a>
								{else}
									<a class="filepicker-file-action" href="{$file.fullurl}" data-fb-filetype='{$file.filetype}'>{$file.name}</a>
								{/if}
								</h4>
								<div class="filepicker-file-details visuallyhidden">
								{if !empty($file.dimensions)}
									<span class="filepicker-file-dimension">
										{$file.dimensions}
									</span>
								{/if}
								{if !empty($file.size)}
									<span class="filepicker-file-size">
										{$file.size}
									</span>
								{/if}
								</div>
							</div>
						</li>
						{/foreach}
					</ul>
				</div>
			</div>
		</div>
	</body>
	{/strip}
</html>
