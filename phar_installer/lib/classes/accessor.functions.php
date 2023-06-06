<?php

namespace __appbase;

use __appbase\app;

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

function &get_app()
{
  return app::get_instance();
}

?>
