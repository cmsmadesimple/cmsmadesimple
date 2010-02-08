<?php
$lang['friendlyname'] = 'ファイル管理';
$lang['permission'] = 'ファイル管理モジュールを使用';
$lang['permissionadvanced'] = 'ファイル管理モジュールの高度な使用';
$lang['moddescription'] = 'Handling of files and directories in the upload-filesection of cmsms';
$lang['installed'] = 'ファイル管理バージョン%sがインストールされました。';
$lang['postinstall'] = 'ファイル管理モジュールがインストールされました。';
$lang['uninstalled'] = 'ファイル管理モジュールがアンインストールされました。';
$lang['really_uninstall'] = 'ファイル管理モジュールのアンインストールされます。よろしいでしょうか?';
$lang['upgraded'] = 'ファイル管理モジュールがバージョン%sにアップグレードされました。';
$lang['fileview'] = 'File view';
$lang['settings'] = '設定';
$lang['filename'] = 'ファイル名';
$lang['fileinfo'] = 'ファイル情報';
$lang['filesize'] = 'サイズ';
$lang['filedate'] = '日付';
$lang['actions'] = 'アクション';
$lang['delete'] = '削除';
$lang['bytes'] = 'バイト';
$lang['kb'] = 'キロバイト';
$lang['mb'] = 'メガバイト';
$lang['files'] = 'ファイル';
$lang['bytesin'] = 'bytes in';
$lang['file'] = 'ファイル';
$lang['fileno'] = 'File no.';
$lang['subdirs'] = 'サブディレクトリ';
$lang['subdir'] = 'サブディレクトリ';
$lang['and'] = 'and';
$lang['confirmsingledelete'] = 'よろしいですか?';
$lang['confirmsingledirdelete'] = 'Are you sure this directory should be deleted?';
$lang['dirtreedeletesuccess'] = 'ディレクトリに含まれるコンテンツは正常に削除されました。';
$lang['dirtreedeletefail'] = 'An error occured when deleting this dir. Some of the contents may have been deleted, however.';
$lang['singlefiledeletesuccess'] = 'ファイルは正常に削除されました。';
$lang['filedeletesuccess'] = 'は正常に削除されました。';
$lang['singlefiledeletefail'] = 'ファイル削除時にエラーが発生しました。';
$lang['filedeletefail'] = 'はエラーにより削除されません。';
$lang['singledirdeletesuccess'] = 'ディレクトリは正常に削除されました。';
$lang['singledirdeletefail'] = 'ディレクトリ削除時にエラーが発生しました(空でしょうか?)。';
$lang['deleteselected'] = '選択されたファイルを削除します。';
$lang['confirmselected'] = 'よろしいでしょうか?';
$lang['go'] = 'Go...';
$lang['dirnotemptyconfirm'] = 'This dir is not empty. Do you really want to delete it and all content, including subdirs?';
$lang['dirtreedeletecancelled'] = 'ディレクトリの削除はキャンセルされました。';
$lang['imsure'] = 'はい';
$lang['cancel'] = 'キャンセル';
$lang['uploadnewfile'] = '新しいファイルをアップロード:';
$lang['unpackafterupload'] = 'アップロード後にファイルを展開しますか(tgzかzipファイルのみ)?';
$lang['uploadsuccess'] = '正常にアップロードされました。';
$lang['uploadfail'] = 'アップロードに失敗しました。';
$lang['unpacksuccess'] = 'は正常に展開されました。';
$lang['unpackfail'] = '次のエラーにより失敗: ';
$lang['packfileopenfail'] = 'ファイルの展開ができません(サポートされないフォーマットの可能性?)';
$lang['packfilewritefail'] = 'ファイル&sに書き込めません。';
$lang['newdirname'] = '新しいディレクトリを作成:';
$lang['newdirsuccess'] = 'ディレクトリは正常に作成されました。';
$lang['newdirfail'] = 'ディレクトリ作成時にエラーが発生しました。';
$lang['filedateformat'] = '月/日/年 時:分:秒';
$lang['iconsize'] = 'アイコンサイズ';
$lang['smallicons'] = '小アイコン';
$lang['largeicons'] = '大アイコン';
$lang['uploadfilesto'] = '次にファイルをアップロード:';
$lang['uploadview'] = 'アップロードファイル';
$lang['uploadboxes'] = 'Number of uploadboxes';
$lang['nothinguploaded'] = '何もアップロードされていません。';
$lang['unsupportedarchive'] = 'サポート外のアーカイブ形式です。';
$lang['uploadmethod'] = 'アップロード方法';
$lang['uploaderstandard'] = '標準htmlインプット方法(展開を許可)';
$lang['uploaderdom'] = 'Fancy DOM-controlled form';
$lang['uploaderpostlet'] = 'Postlet, Java-based, allows multiple file-selection';
$lang['enableadvanced'] = '上級操作モードを有効にしますか? (許可されている場合)';
$lang['showhiddenfiles'] = '隠しファイルを表示';
$lang['settingsconfirmsingledelete'] = 'ファイルごとに削除を確認しますか?';
$lang['settingssaved'] = '設定の保存';
$lang['help'] = '		<h3>何ができるのでしょうか?</h3>
		<p>このモジュールでファイル管理が行なえ、往来のビルドインさえｒたファイル管理機能に取って代わり利用できます。</p>
		<h3>使用方法</h3>
		<p>アドミンのコンテンツメニューから利用できます。往来のファイル管理が存在する為に注意が必要となりますが、一番下のファイル管理を選択してください。</p>';
$lang['changelog'] = '		<ul>
		  <li><b>Version 0.1.4</b></li>
		  <li>Fixed cancelling recursive deletion of dirs</li>

		  <li><b>Version 0.1.3</b></li>
		  <li>Fixed cancelling recursive deletion of dirs</li>
		  <li>Added Java-applet multifile upload method</li>
		  <li>Implemented deletion of multiple files</li>
		  <li>Fixed select all checkbox</li>

		  <li><b>Version 0.1.2</b></li>
		  <li>Added recursive deletion of dirs</li>

		  <li><b>Version 0.1.1</b></li>
		  <li>Added support for multiple uploads, and support for unpacking tar.gz-files</li>
		  <li>Rewrote to use more of the module-api-functions</li>
		  <li><b>Version 0.1.0</b></li>
		  <li>First version which work properly and equals the builtin filemanager which is intends to render obsolete</li>
		</ul>';
$lang['utma'] = '156861353.421210939.1147253499.1151823476.1152236148.8';
$lang['utmz'] = '156861353.1149854982.6.3.utmccn=(organic)|utmcsr=google|utmctr=cmsms |utmcmd=organic';
?>