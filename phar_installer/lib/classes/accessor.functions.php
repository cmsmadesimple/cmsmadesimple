<?php

namespace __appbase;

function smarty()
{
  return cms_smarty::get_instance();
}

function nls()
{
  return nlstools::get_instance();
}

function translator()
{
  return langtools::get_instance();
}

?>