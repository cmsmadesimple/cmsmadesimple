<h3>{$mod->Lang('title_edittextfile')}</h3>

<div class="c_full cf">
    <label class="grid_2">{$mod->Lang('filename')}</label>
    <p>{$cwd}/{$filename}</p>
</div>

{form_start action=textedit encoded=$encoded_file}
<div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('lbl_content')}</p>
    <p class="pageinput">
        {cms_textarea wantedsyntax=$filetype name="content" text=$content rows=20}
    </p>
</div>
<div class="c_full cf">
    <input type="submit" id="submit" name="submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="cancel" value="{$mod->Lang('cancel')}" formnovalidate/>
</div>
{form_end}