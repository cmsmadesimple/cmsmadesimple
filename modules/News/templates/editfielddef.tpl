{*
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$	
*}

<script type="text/javascript">
function handle_change(){
  var val = $('#fld_type').val();
  if( val == 'dropdown' ) {
    $('#area_maxlen').hide('slow');
    $('#area_options').show('slow');
  }
  else if( val == 'checkbox' || val == 'file' ) {
    $('#area_maxlen').hide('slow');
    $('#area_options').hide('slow');
  }
  else {
    $('#area_maxlen').show('slow');
    $('#area_options').hide('slow');
  }
}
$(document).ready(function(){
  handle_change();
  $('#fld_type').change(handle_change);
});
</script>

<h3>{$title}</h3>
{$startform}{$hidden|default:''}
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld_name">*{$nametext}:</label></p>
		<p class="pageinput">
                  <input type="text" id="fld_name" name="{$actionid}name" value="{$name}" size="30" maxlength="255"/>
                </p>
	</div>
	{if $showinputtype eq true}
		<div class="pageoverflow">
			<p class="pagetext"><label for="fld_type">*{$typetext}:</label></p>
			<p class="pageinput">
                          <select id="fld_type" name="{$actionid}type">
			  {html_options options=$fieldtypes selected=$type}
                          </select>
                        </p>
		</div>
        {else}
          <input type="hidden" id="fld_type" name="{$actionid}type" value="{$type}"/>
	{/if}
	<div class="pageoverflow" id="area_options">
          <p class="pagetext"><label for="fld_options">{$mod->Lang('options')}:</label>
	  <p class="pageinput">
            <textarea id="fld_options" name="{$actionid}options" rows="5" cols="80">{$options}</textarea>
          </p>
        </div>
	<div class="pageoverflow" id="area_maxlen">
		<p class="pagetext"><label for="fld_maxlen">{$maxlengthtext}:</label></p>
		<p class="pageinput">
                  <input type="text" id="fld_maxlen" name="{$actionid}max_length" value="{$max_length}" size="3" maxlength="3"/><br/>{$info_maxlength}
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld_public">{$userviewtext}:</label></p>
		<p class="pageinput">
                  <input type="hidden" name="{$actionid}public" value="0"/>
                  <input type="checkbox" id="fld_public" name="{$actionid}public" value="1" {if $public == 1}checked="checked"{/if}/>
		  <br/>{$mod->Lang('info_public')}
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">
                  <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
                  <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
                </p>
	</div>
{$endform}