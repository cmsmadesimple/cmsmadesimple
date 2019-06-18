<h3>{$mod->Lang('title_newtextfile')}</h3>

{form_start action=newtextfile}
<div class="c_full cf">
    <label class="grid_2">{$mod->Lang('currentpath')}:</label>
    <span>{$cwd}</span>
</div>
<div class="c_full cf">
    <label for="filename" class="grid_2">{$mod->Lang('filename')}:</label>
    <input type="text" class="grid_9" id="filename" name="filename" value="{$filename}"/>
</div>
<div class="pageoverflow">
    <div class="pagetext">{$mod->Lang('lbl_content')}:</div>
    <div class="pageinput">
        {cms_textarea wantedsyntax=true name="content" text=$content rows=20}
    </div>
</div>
<div class="c_full cf">
   <input type="submit" name="submit" value="{$mod->Lang('submit')}"/>
   <input type="submit" name="cancel" value="{$mod->Lang('cancel')}" formnovalidate/>
</div>
{form_end}