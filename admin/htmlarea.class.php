<?php
/**
  * A wrapper to deal with HTMLArea's from php
  *
  * Works with HTMLArea 3.0 RC1
  *
  * @version RC1
  * @author troels knak-nielsen <troels@kyberfabrikken.dk>
  * @license LGPL (GNU Lesser Public License)
  */

/**
  * @var The default web path to htmlarea
  */
if (!defined('DIR_WS_HTMLAREA'))
	define('DIR_WS_HTMLAREA', "http://localhost/htmlarea/");
/**
  * @var The default filesystem path to configs
  */
if (!defined('DIR_FS_HTMLAREA_CONFIG'))
	define('DIR_FS_HTMLAREA_CONFIG', dirname(__FILE__) . "/htmlarea/config/");

class HTMLArea
{
	/**
	  * Holds the filedname of the editor
	  * @var string
	  * @public
	  */
	var $fieldname = '';
	/**
	  * Holds the initial content
	  * @var string
	  * @public
	  */
	var $content = '';
	/**
	  * If not set to null, this config will be loaded
	  * @var string
	  * @public
	  */
	var $config = null;
	/**
	  * The width of the editor
	  * @var string
	  * @public
	  */
	var $width = 'auto';
	/**
	  * The height of the editor
	  * @var string
	  * @public
	  */
	var $height = 'auto';
	/**
	  * Whether or not to use custom undo.
	  * @var string
	  * @public
	  */
	var $enableCustomUndo=false;
	/**
	  * Lists the plugins, that this instance need.
	  * Use addPlugin() to add plugins to the list.
	  * @var array
	  * @private
	  */
	var $plugins=array();

	/**
	  * Default constructor
	  * @param string name The fieldname of the widget
	  * @param string content The initial content
	  * @public
	  */
	function HTMLArea($name="ta", $content="")
	{
		HTMLArea::initStatic();
		$this->fieldname = $name;
		$this->content = $content;
		$this->config = null;
		$this->enableCustomUndo = ereg('MSIE ([0-9].[0-9])', $_SERVER['HTTP_USER_AGENT']);
	}

	/**
	  * Makes the widget utilize the requested plugin
	  * Multiple calls will not cause conflict (but will override params)
	  * @param string plug The plugin to use
	  * @param mixed args Arguments needed by the plugin. Most plugs don't use this.
	  * @public
	  */
	function addPlugin($plug, $args=null)
	{
		$this->plugins[$plug] = $args;
		HTMLArea::loadPlugin($plug);
	}

	/**
	  * Renders the component to HTML
	  * This method renders the HTML of the instance.
	  * @see getStatic
	  * @public
	  * @return string The rendered component - ready to echo into your page
	  */
	function get()
	{
		if (!isset($this)) {
			trigger_error("Use HTMLArea::getStatic() to retrieve static render.", E_USER_WARNING);
			return ;
		}
		ob_start();
//////////////////////////////////////////////////////////////////////////////
?>
<script type="text/javascript">
var editor_<?=$this->fieldname?> = null;
function init_editor_<?=$this->fieldname?>() {

var cfg = new HTMLArea.Config();
<?php
if ($this->config != null) :
?>
<?php include($GLOBALS['_htmlarea']['dir_fs_config'].$this->config.".config.js"); ?>
<?php endif; ?>
  cfg.width = "<?=$this->width?>";
  cfg.height = "<?=$this->height?>";

  // create an editor for the "<?=$this->fieldname?>" textbox
  editor_<?=$this->fieldname?> = new HTMLArea("<?=$this->fieldname?>", cfg);
<?php
// enable custom undo
if ($this->enableCustomUndo) {
	echo "editor_".$this->fieldname."._customUndo = true;\n";
}
?>

<?php
foreach ($this->plugins as $plug => $args) : ?>
	// register the <?=$plug?> plugin
<?php
	switch ($plug) {
		case 'CSS' :
?>
  editor_<?=$this->fieldname?>.registerPlugin(CSS, {
    combos : [
      { label: "Syntax:",
                   // menu text       // CSS class
        options: { "None"           : "",
                   "Code" : "code",
                   "String" : "string",
                   "Comment" : "comment",
                   "Variable name" : "variable-name",
                   "Type" : "type",
                   "Reference" : "reference",
                   "Preprocessor" : "preprocessor",
                   "Keyword" : "keyword",
                   "Function name" : "function-name",
                   "Html tag" : "html-tag",
                   "Html italic" : "html-helper-italic",
                   "Warning" : "warning",
                   "Html bold" : "html-helper-bold"
                 },
        context: "pre"
      },
      { label: "Info:",
        options: { "None"           : "",
                   "Quote"          : "quote",
                   "Highlight"      : "highlight",
                   "Deprecated"     : "deprecated"
                 }
      }
    ]
  });
  // load the stylesheet used by our CSS plugin configuration
  editor_<?=$this->fieldname?>.config.pageStyle = "@import url(<?=$this->plugins['CSS']?>);";
<?php
		break;
		default :
?>
  editor_<?=$this->fieldname?>.registerPlugin(<?=$plug?>);
<?php
		break;
	}
endforeach;
?>
  setTimeout(function() {
    editor_<?=$this->fieldname?>.generate();
  }, 500);
  return false;
}
_htmlarea_onload_init[_htmlarea_onload_init.length] = init_editor_<?=$this->fieldname?>;
</script>
<?php
	if ($this->width == 'auto')
		$style = "width:100%;";
	else if (is_numeric($this->width))
		$style = "width:".$this->width."px;";
	else
		$style = "width:".$this->width.";";

	if ($this->height == 'auto') ;
	else if (is_numeric($this->height))
		$style .= "height:".$this->height."px;";
	else
		$style .= "height:".$this->height.";";

	echo "<div style=\"".$style."\">\n";
?>
<textarea id="<?=$this->fieldname?>" name="<?=$this->fieldname?>" style="<?=$style?>" rows="24" cols="80"><?=htmlentities($this->content)?></textarea>
</div>
<?php
//////////////////////////////////////////////////////////////////////////////
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
	}

	/// static methods below

	/**
	  * Initializes the static data
	  * @private
	  * @static
	  */
	function initStatic()
	{
		if (isset($GLOBALS['_htmlarea'])) {
			return ;
		}
		$GLOBALS['_htmlarea']['path'] = DIR_WS_HTMLAREA;
		$GLOBALS['_htmlarea']['dir_fs_config'] = DIR_FS_HTMLAREA_CONFIG;
		$GLOBALS['_htmlarea']['plugins'] = array();
		$GLOBALS['_htmlarea']['fire_on_load'] = true;
		$GLOBALS['_htmlarea']['lang'] = 'en';
	}

	/**
	  * Sets the path
	  * @public
	  * @static
	  */
	function setPath($path)
	{
		HTMLArea::initStatic();
		$GLOBALS['_htmlarea']['path'] = $path;
	}

	/**
	  * Retrieves the path
	  * @public
	  * @static
	  * @return string
	  */
	function getPath()
	{
		HTMLArea::initStatic();
		return $GLOBALS['_htmlarea']['path'];
	}

	/**
	  * Sets the language
	  * @public
	  * @static
	  */
	function setLang($lang)
	{
		HTMLArea::initStatic();
		$GLOBALS['_htmlarea']['lang'] = $lang;
	}

	/**
	  * Retrieves the language
	  * @public
	  * @static
	  * @return string
	  */
	function getLang()
	{
		HTMLArea::initStatic();
		return $GLOBALS['_htmlarea']['lang'];
	}

	/**
	  * Add a plugin to the static load queue. This must be done before using getStatic()
	  * @param string plugin The plug to load
	  * @public
	  * @static
	  */
	function loadPlugin($plugin)
	{
		HTMLArea::initStatic();
		if (!in_array($plugin, $GLOBALS['_htmlarea']['plugins'])) {
			$GLOBALS['_htmlarea']['plugins'][] = $plugin;
		}
	}

	/**
	  * Controls wether the initiation should be triggered on the onload event.
	  * If you disable this default behaviour, you must call the init-function manually.
	  * @param bool doit Set to false to disable triggering
	  * @public
	  * @static
	  */
	function setFireOnLoad($doit=true)
	{
		HTMLArea::initStatic();
		$GLOBALS['_htmlarea']['fire_on_load'] = $doit;
	}

	/**
	  * Renders the static data to HTML
	  * This should be put inside you page's HEAD tags. You should not call getStatic before all
	  * instances of HTMLArea has been created.
	  * @static
	  * @public
	  * @return string The rendered data - ready to echo into your page
	  */
	function getStatic()
	{
		HTMLArea::initStatic();
		if (isset($this)) {
			trigger_error("Use HTMLArea->get() to retrieve instance render.", E_USER_WARNING);
			return ;
		}
		ob_start();
//////////////////////////////////////////////////////////////////////////////
?>
<script type="text/javascript">
function init_htmlarea() {
	for (var i=0; i < _htmlarea_onload_init.length; i++) {
		_htmlarea_onload_init[i]();
	}
}
</script>
<script type="text/javascript">
  _editor_url = "<?=$GLOBALS['_htmlarea']['path']?>";
  _editor_lang = "<?=HTMLArea::getLang()?>";
  _htmlarea_onload_init = new Array();
<?php if ($GLOBALS['_htmlarea']['fire_on_load']) : ?>
  window.onload = init_htmlarea;
<?php endif; ?>
</script>
<script type="text/javascript" src="<?=HTMLArea::getPath()?>htmlarea.js"></script>
<script type="text/javascript">
<?php foreach ($GLOBALS['_htmlarea']['plugins'] as $pluggit) : ?>
      HTMLArea.loadPlugin("<?=$pluggit?>");
<?php endforeach; ?>
</script>
<?php
//////////////////////////////////////////////////////////////////////////////
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
	}

} // end class HTMLArea
?>