<h3>{translate}Welcome to CMSMS!{/translate}</h3>

<p>
  {translate}This is an introductory paragraph.{/translate}
</p>

<h4>{translate}Choose a language{/translate}</h4>

<p>
  <form action="index.php" method="post">
    <select id="select_language" name="select_language" onchange="this.form.submit();">
      {html_options options=$languages selected=$selected_language}
    </select>
  </form>
</p>

<p>
  <form action="index.php" method="get">
    <input type="submit" name="next" value="{translate}Next{/translate}" />
    <input type="hidden" name="action" value="check" />
  </form>
</p>
