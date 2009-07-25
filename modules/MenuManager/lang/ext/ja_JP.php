<?php
$lang['addtemplate'] = 'テンプレートを追加';
$lang['areyousure'] = '本当に削除しますか？';
$lang['changelog'] = '	<ul>
	<li>1.1 -- Added handling of target parameter, mainly for the Link content type</li>
	<li>1.0 -- 初期リリース</li>
	</ul> ';
$lang['dbtemplates'] = 'データベーステンプレート';
$lang['description'] = '想定可能な方法で、メニューを表示するメニューテンプレートを管理します。';
$lang['deletetemplate'] = 'テンプレートを削除';
$lang['edittemplate'] = 'テンプレートを編集';
$lang['filename'] = 'ファイル名';
$lang['filetemplates'] = 'ファイルテンプレート';
$lang['help_collapse'] = '有効に設定する(1に設定する)ことで、該当ページに関係しないメニュー項目をメニューから隠します。';
$lang['help_items'] = 'この項目を使って、メニューに表示されるページリストを選択します。値は、コンマで区切られたページエイリアスリストになります。';
$lang['help_number_of_levels'] = 'この設定によりメニューは指定された階層レベルのメニューを表示します。';
$lang['help_show_root_siblings'] = 'この機能はstart_elementもしくはstart_pageが使用されている場合にのみ使用可能です。選択したstart_page/elementと兄弟(Sibling)要素が表示されます。';
$lang['help_start_level'] = 'このオプションにより、指定されたレベルから始まるメニュー項目のみ表示します。簡単な例では、 「number_of_levels=\'1\'」と指定することで、1列目のメニューしか表示しません。2列目のメニューを表示するには、「start_level=\'2\'」とします。そうすることで、一段目のメニューで選択された項目の2列目のメニューを表示します。';
$lang['help_start_element'] = 'メニューを開始します。特定のstart_element、その要素と子要素のみ表示されます。 階層構造で表示されます。(例： 5.1.2).';
$lang['help_start_page'] = 'メニューの表示開始点を示します。start_pageを指定すると、その要素と子要素のみ表示されます。 ページエイリアスで指定します。';
$lang['help_template'] = 'メニュー表示用テンプレート。テンプレート名の最後が.tplであるもの以外は、データベーステンプレートから表示されます。テンプレート名の最後が.tplの場合、メニュー管理テンプレートディレクトリのファイルが表示されます。';
$lang['help'] = '	<h3>何ができるのでしょうか?</h3>
	<p>メニュー管理はメニューを簡単に導入、カスタマイズ可能にするモジュールです。Smartyテンプレートでメニューの表示処理部分を抽象化させることで、ユーザーにより簡単にメニューをカスタマイズできるようになります。つまり、メニュー管理自体は、テンプレートを活用する為のエンジンです。テンプレートをカスタマイズすること、或いは独自のものを作成することで、思いのままのメニューを作成できます。</p>
	<h3>使用方法</h3>
	<p>次のようにテンプレートやページにタグを挿入するだけで利用できます。<code>{cms_module module=\'menumanager\'}</code>。パラメーターはリストされたものだけ利用できます。</p>
	<h3>テンプレートでの利用</h3>
	<p>メニューマネージャーは導入にはテンプレートを利用します。 「bulletmenu.tpl」, 「cssmenu.tpl」 や 「ellnav.tpl」の3つのデフォルトテンプレートで構成されます。 それらで、CSSスタイルの異なるクラスやIDにより簡単な順不同のページリストを作成します。全バージョンの「bulletmenu」, 「CSSMenu」や「EllNav」でのメニューシステムとの同様です。</p>
	<p>CSSでメニューの見栄えを整えるさいに注意が必要です。スタイルシートはメニューマネージャに含まれず、別にページテンプレートへ添付される必要があります。IEで「cssmenu.tpl」テンプレートを使用する場合は、ページテンプレートのヘッド部分でJavaスクリプトにリンクを挿入する必要があり、IEでhover効果を利用するのに必要となります。</p>
	<p>特別なテンプレートを作成したい場合は、データベースにインポートし、CMSMS管理パネルで直接編集します。以下は例です。
		<ol>
			<li>メニュー管理をクリックします。</li>
			<li>ファイルテンプレートタブをクリックし、「bulletmenu.tpl」の横にある「データベースにテンプレートをインポート」ボタンをクリックします。</li>
			<li>テンプレートを名づけます。例では「テストテンプレート」とします。</li>
			<li>データベーステンプレートに「テストテンプレート」が表示されます。</li>
		</ol>
	</p>
	<p>この状態で、必要な状態になるようにテンプレートの編集が可能になります。ClassやID、その他タグを利用し、自由にフォーマットが行なえます。編集後、サイトに次の形でテンプレートを挿入できます。{cms_module module=\'menumanager\' template=\'テストテンプレート\'}。ファイルの拡張子は必ず「.tpl」である必要があります。</p>
	<p>$nodeオブジェクトのパラメーターは次のように利用できます。:
		<ul>
			<li>$node->id -- コンテンツID</li>
			<li>$node->url -- コンテンツのURL</li>
			<li>$node->accesskey -- アクセスキー（定義されている場合）</li>
			<li>$node->tabindex -- タブインデックス（定義されている場合）</li>
			<li>$node->titleattribute -- 概要またはタイトル（定義されている場合）</li>
			<li>$node->hierarchy -- 階層位置（例：1.3.3）</li>
			<li>$node->depth -- 現メニューでのこの項目の深さ（レベル）</li>
			<li>$node->prevdepth -- この項目の一つ前の項目の深さ（レベル）</li>
			<li>$node->haschildren -- この項目に表示する子がある場合にtrueを返します</li>
			<li>$node->menutext -- メニューテキスト</li>
			<li>$node->target -- リンクのターゲット。コンテンツで設定されていない場合は空白になります</li>
			<li>$node->index --メニュー全体でのこの項目の順番</li>
			<li>$node->parent -- この項目が現在選択されているページの親である場合にtrueを返します</li>
		</ul>
	</p>';
$lang['importtemplate'] = 'テンプレートをデータベースにインポート';
$lang['menumanager'] = 'メニュー管理';
$lang['newtemplate'] = '新規テンプレート名';
$lang['nocontent'] = 'コンテンツが指定されていません。';
$lang['notemplatefiles'] = '%sにファイルテンプレートがありません。';
$lang['notemplatename'] = 'テンプレート名が指定されていません。';
$lang['templatecontent'] = 'テンプレートコンテンツ';
$lang['templatenameexists'] = 'この名前のテンプレートが既に存在します。';
$lang['utma'] = '156861353.541437335.1154032112.1154032112.1154032112.1';
$lang['utmz'] = '156861353.1154032112.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)';
?>