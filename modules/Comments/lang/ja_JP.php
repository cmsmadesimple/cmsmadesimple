<?php
$lang['addacomment'] = 'コメントの追加';
$lang['cancel'] = 'キャンセル';
$lang['comment'] = 'コメント';
$lang['error'] = 'エラー';
$lang['errorenterauthor'] = '作者を入力して下さい';
$lang['errorentercomment'] = 'コメントを入力して下さい';
$lang['submit'] = 'OK';
$lang['yourname'] = 'あなたの名前';
$lang['help'] = <<<EOD
	<h3>何ができるのでしょうか?</h3>
	<p>コメントモジュールはタグモジュールです。訪問者が各ページにコメントを載せることができます。実用例では、ページへの追加コメントや情報を掲載するのにも便利です。</p>
	<h3>使用方法</h3>
	<p>コメントは単なるタグモジュールです。cms_moduleタグを利用して、ページやテンプレートで使うことができます。使用例: <code>{cms_module module="comments"}</code></p>
	<h3>パラメーターに関して</h3>
	<p>
	<ul>
		<li><em>(optional)</em> number="5" - 表示できる項目の最大数の値 =- 空欄は全項目を表示</li>
		<li><em>(optional)</em> dateformat - 日付/時間フォーマットはPHPのstrftime機能を使用して下さい。詳しくは<a href="http://php.net/strftime" target="_blank">こちら</a>からご覧頂けます。</li>
	</ul>
EOD;
?>
