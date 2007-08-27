<h3>{tr}Welcome to CMSMS!{/tr}</h3>

<p>
  {tr}This is an introductory paragraph.{/tr}
</p>

<h4>{tr}Choose a language{/tr}</h4>

<p>
  <form action="index.php" method="post">
    <select id="select_language" name="select_language" onchange="this.form.submit();">
      {html_options options=$languages selected=$selected_language}
    </select>
  </form>
</p>

<p>
  <form action="index.php" method="get">
    <input type="submit" name="next" value="{tr}Next{/tr}" />
    <input type="hidden" name="action" value="check" />
  </form>
</p>
