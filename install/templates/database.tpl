<h3>{translate}Database Setup{/translate}</h3>

<p>
  {translate}This is a paragraph about the wonders of database setup.{/translate}
</p>

<p>
  <form action="index.php" method="post">
    {translate}Database Driver:{/translate}
    <select id="select_language" name="select_language" onchange="this.form.submit();">
      {html_options options=$databases selected=$selected_database}
    </select>
    <input type="hidden" name="action" value="database" />
  </form>
</p>

<p>
  <a href="index.php?action=check">{translate}Back{/translate}</a>
  <a href="index.php?action=account">{translate}Next{/translate}</a>
</p>
