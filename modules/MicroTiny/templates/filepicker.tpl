<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{$mod->Lang('filepickertitle')}</title>
		<link rel="stylesheet" type="text/css" href="{$mod->GetModuleURLPath()}/lib/css/filepicker.min.css" />
	</head>
	{strip}
	<body class="cmsms-filepicker">
		<div id="full-fp">
			<div class="filepicker-navbar">
				<div class="filepicker-navbar-inner">
					<div class="filepicker-view-option">
						<p><span class="filepicker-option-title">{$mod->Lang('fileview')}:&nbsp;</span> 
							<span class="js-trigger view-list filepicker-button" title="{$mod->Lang('switchlist')}"><i class="cmsms-fp-th-list"></i></span>&nbsp;
							<span class="js-trigger view-grid filepicker-button active" title="{$mod->Lang('switchgrid')}"><i class="cmsms-fp-th"></i></span>
						</p>
					</div>
					{if !isset($type) || (isset($type) && ($type == 'file' || $type == 'any'))}
					<div class="filepicker-type-filter">
						<p><span class="filepicker-option-title">{$mod->Lang('filterby')}:&nbsp;</span>
							<span class="js-trigger filepicker-button" data-fb-type='image' title="{$mod->Lang('switchimage')}"><i class="cmsms-fp-picture"></i></span>&nbsp;
							<span class="js-trigger filepicker-button" data-fb-type='video' title="{$mod->Lang('switchvideo')}"><i class="cmsms-fp-film"></i></span>&nbsp;
							<span class="js-trigger filepicker-button" data-fb-type='audio' title="{$mod->Lang('switchaudio')}"><i class="cmsms-fp-music"></i></span>&nbsp;
							<span class="js-trigger filepicker-button" data-fb-type='archive' title="{$mod->Lang('switcharchive')}"><i class="cmsms-fp-zip"></i></span>&nbsp;
							<span class="js-trigger filepicker-button" data-fb-type='file' title="{$mod->Lang('switchfiles')}"><i class="cmsms-fp-file"></i></span>&nbsp;
							<span class="js-trigger filepicker-button active" data-fb-type='reset' title="{$mod->Lang('switchreset')}"><i class="cmsms-fp-reorder"></i></span>
						</p>
					</div>
					{/if}
				</div>
			</div>
			<div class="filepicker-container">
				<div class="filepicker-breadcrumb">
					<p title="{$mod->Lang('youareintext')}:"><i class="cmsms-fp-folder-open filepicker-icon"></i> {$startpath}</p>
				</div>
				<div id="filelist">
					<ul class="filepicker-list" id="filepicker-items">
						<li class="filepicker-item filepicker-item-heading">
							<div class="filepicker-thumb no-background">&nbsp;</div>
							<div class="filepicker-file-information">
								<h4 class="filepicker-file-title">{$mod->Lang('filename')}</h4>
							</div>
							<div class="filepicker-file-details">
								<span class="filepicker-file-dimension">
									{$mod->Lang('dimension')}
								</span>
								<span class="filepicker-file-size">
									{$mod->Lang('size')}
								</span>
								<span class="filepicker-file-ext">
									{$mod->Lang('type')}
								</span>
							</div>
						</li>
						{foreach $files as $file}
						<li class="filepicker-item {$file.filetype}" data-fb-ext='{$file.ext}'>
							<div class="filepicker-thumb{if (isset($file.thumbnail) && $file.thumbnail != '') || $file.isdir} no-background{/if}">
							{if $showthumbnails && isset($file.thumbnail) && $file.thumbnail != ''}
								<a class="filepicker-file-action js-trigger-insert" href="{$file.fullurl}">{$file.thumbnail}</a>
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
							</div>
							<div class="filepicker-file-information">
								<h4 class="filepicker-file-title">
								{if $file.isdir}
									<a class="filepicker-dir-action" href="{$file.chdir_url}">{$file.name}</a>
								{else}
									<a class="filepicker-file-action" href="{$file.fullurl}" data-fb-filetype='{$file.filetype}'>{$file.name}</a>
								{/if}
								</h4>
							</div>
							<div class="filepicker-file-details visuallyhidden">
								<span class="filepicker-file-dimension">
									{$file.dimensions}
								</span>
								<span class="filepicker-file-size">
									{if !$file.isdir}{$file.size}{/if}
								</span>
								<span class="filepicker-file-ext">
									{$file.ext|default:'dir'}
								</span>
							</div>
						</li>
						{/foreach}
					</ul>
				</div>
			</div>
		</div>
	</body>
	{/strip}
	<script type="text/javascript" src="{root_url}/lib/jquery/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="{$mod->GetModuleURLPath()}/lib/js/tinymce/plugins/cmsms_filebrowser/filebrowser.js"></script>
	<script type="text/javascript">
		var filebrowser_global = {
			field_id : '{$field}'
		}
	</script>
</html>
