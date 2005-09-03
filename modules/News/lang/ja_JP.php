<?php
$lang['addarticle'] = '記事の追加';
$lang['addcategory'] = 'カテゴリーの追加';
$lang['addnewsitem'] = 'ニュース項目の追加';
$lang['allcategories'] = '全カテゴリー';
$lang['allentries'] = '全エントリー';
$lang['areyousure'] = '削除しますか?';
$lang['articles'] = '記事';
$lang['cancel'] = 'キャンセル';
$lang['category'] = 'カテゴリー';
$lang['categories'] = 'カテゴリー';
$lang['content'] = 'コンテンツ';
$lang['dateformat'] = '%sは有効なフォーマットではありません。「yyyy-mm-dd hh:mm:ss」の必要があります。';
$lang['delete'] = '削除';
$lang['description'] = 'ニュースエントリーの追加、編集、削除';
$lang['detailtemplate'] = 'テンプレートの詳細';
$lang['displaytemplate'] = 'テンプレートの表示';
$lang['edit'] = '編集';
$lang['enddate'] = '終了日';
$lang['endrequiresstart'] = '終了日を入力する際に開始日も必要';
$lang['entries'] = '%sのエントリー';
$lang['expiry'] = '有効期限切れ';
$lang['filter'] = 'フィルター';
$lang['more'] = 'さらに表示';
$lang['moretext'] = 'さらにテキストを表示';
$lang['name'] = '名前';
$lang['news'] = 'ニュース';
$lang['newcategory'] = '新規カテゴリー';
$lang['needpermission'] = 'この機能を利用するには\'%s\'パーミッションが必要です。';
$lang['nocategorygiven'] = 'カテゴリーが存在しません。';
$lang['nocontentgiven'] = 'コンテンツが存在しません。';
$lang['noitemsfound'] = 'カテゴリー%sに対する項目がありません。';
$lang['nopostdategiven'] = '投稿日付がありません。';
$lang['note'] = '<em>注意:</em> 日付は\'yyyy-mm-dd hh:mm:ss\'フォーマットの必要があります。';
$lang['notitlegiven'] = 'タイトルが存在しません。';
$lang['numbertodisplay'] = '表示数(空欄の場合は全てのレコードが表示されます)';
$lang['print'] = '印刷';
$lang['postdate'] = '投稿日';
$lang['postinstall'] = 'ニュース項目を管理するユーザーには"ニュースを修正"パーミッションを設定する必要があります。';
$lang['rsstemplate'] = 'RSSテンプレート';
$lang['selectcategory'] = 'カテゴリーの選択';
$lang['sortascending'] = '昇順並び替え';
$lang['startdate'] = '開始日';
$lang['startrequiresend'] = '開始日を入力する際に終了日も必要';
$lang['submit'] = 'OK';
$lang['summary'] = '要約';
$lang['summarytemplate'] = '要約テンプレート';
$lang['title'] = 'タイトル';
$lang['useexpiration'] = '期限切れ日付を使用';
$lang['help'] = <<<EOF
	<h3>何ができるのでしょうか?</h3>
	<p>ニュースはページにニュースイベントを表示するモジュールで、多くの機能があり、例えばブログのような利用ができます。モジュールがインストールされると、ニュース管理ページが管理メニューに追加され、ニュースの管理はメニューから利用できます。ニュースカテゴリーが一旦作成、選択されると、該当カテゴリーのニュース項目がリスト表示されます。そこから、そのニュース項目の追加、編集、削除等が行えます。</p>
	<h3>セキュリティ</h3>
	<p>利用ユーザーは、入力や編集等の作業を行う為に、必ず'ニュースの修正'パーミッションを持ったグループに所属する必要があります。</p>
	<h3>使用方法</h3>
	<p>もっとも簡単な利用方法はcms_moduleタグと合わて使うことです。この方法はモジュールをテンプレートやページのどこかニュース項目を表示したい場所に挿入することです。コードの形式は以下のようになります： <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>パラメーターに関して</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - 表示できる項目の最大数の値 =- 空欄は全項目を表示</li>
	<li><em>(optional)</em> makerssbutton="true" - ニュース項目のRSSフィードへのリンクボタンを作成</li>
	<li><em>(optional)</em> category="category" - 該当カテゴリーとその子カテゴリーの項目だけを表示。空欄は全カテゴリを表示。</li>
	<li><em>(optional)</em> moretext="more..." - 要約のサイズを超えている場合に、ニュース項目を最後まで表示します。デフォルトは"さらに表示..."。</li>
	<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - 要約記事の表示で異なるテンプレートを利用。 ファイルはmodules/News/templatesに存在します。
	<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - 記事詳細の表示の為に分離したテンプレートを使用。ファイルはmodules/News/templatesに置く必要があります。
	<li><em>(optional)</em> sortby="news_date" - 並び替えの優先項目。項目は: "news_date", "summary", "news_data", "news_category", "news_title".  デフォルトは"news_date"</li>
	<li><em>(optional)</em> sortasc="true" - ニュース項目を昇順に並べ替え。</li>
	</ul>
	</p>
EOF;
?>
