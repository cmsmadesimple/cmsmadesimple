<?php

include_once "modifier.markdown.php";

function smarty_block_cms_markdown($params, $content, &$smarty)
{
  if( isset( $content ) )
  {
    return Markdown( $content );
  }
}

?>
