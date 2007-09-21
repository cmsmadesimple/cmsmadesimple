<?php
/**
 * Show a list of images in a long horizontal table.
 * @author $Author: Wei Zhuo $
 * @version $Id$
 * @package ImageManager
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/include.php');
check_login();
$userid = get_userid();
if (!check_permission($userid, 'Modify Files')) die();

require_once(dirname(__FILE__).'/config.inc.php');
require_once(dirname(__FILE__).'/Classes/ImageManager.php');

//default path is /
$relative = '/';
$manager = new ImageManager($IMConfig);

//process any file uploads
$manager->processUploads();

$manager->deleteFiles();

$refreshDir = false;
//process any directory functions
if($manager->deleteDirs() || $manager->processNewDir())
        $refreshDir = true;

//check for any sub-directory request
//check that the requested sub-directory exists
//and valid

if(isset($_REQUEST['dir']))
{
        $path = rawurldecode($_REQUEST['dir']);
        if($manager->validRelativePath($path))
                $relative = $path;
}


$manager = new ImageManager($IMConfig);


//get the list of files and directories
$list = $manager->getFiles($relative);


/* ================= OUTPUT/DRAW FUNCTIONS ======================= */

/**
 * Draw the files in an table.
 */
function drawFiles($list, &$manager, $i)
{
        global $relative;
        $image_per_line = 5;

        foreach($list as $entry => $file)
        {
                $i++;
                if ($i==1) { echo "<tr>";}
                ?>
                <td><table width="100" cellpadding="0" cellspacing="0"><tr><td class="block">
                        <a href="<?php echo "{$manager->config['base_url']}{$file['relative']}";?>" TARGET="_blank" title="<?php echo $entry; ?> - <?php echo Files::formatSize($file['stat']['size']); ?>"><img src="<?php

        if (function_exists('imagecreate'))
        {
                echo $manager->getThumbnail($file['relative']);
        } else {
                if ($file['image']) {
                        $size_x = $file['image'][0];
                        $size_y = $file['image'][1];
                }
                if (($size_x < 96) && ($size_y < 96))
                {
                }
                elseif ($size_x > $size_y)
                {
                        $size_y = round($size_y / ($size_x / 96));
                        $size_x = 96;
                }
                else
                {
                        $size_x = round($size_x / ($size_y / 96));
                        $size_y = 96;
                }
                echo "{$manager->config['base_url']}{$file['relative']}";
                echo "\" width=\"$size_x\" height=\"$size_y";
        }

?>" alt="<?php echo $entry; ?> - <?php echo Files::formatSize($file['stat']['size']); ?>"/></a>
                </td></tr><tr><td class="edit">
                        <a href="images.php?dir=<?php echo $relative; ?>&amp;delf=<?php echo rawurlencode($file['relative']);?>" title="Trash" onclick="return confirmDeleteFile('<?php echo $entry; ?>');"><img src="img/edit_trash.gif" height="15" width="15" alt="Trash"/></a><a href="javascript:;" title="Edit" onclick="editImage('<?php echo rawurlencode($file['relative']);?>');"><img src="img/edit_pencil.gif" height="15" width="15" alt="Edit"/></a>
                <?php if($file['image']){ echo $file['image'][0].'x'.$file['image'][1]; } else echo $entry;?>
                </td></tr></table></td>
          <?php
                if ($i==$image_per_line) {
                        echo "</tr>";
                        $i=0;
                }
        }//foreach

    return ($i);

}//function drawFiles


/**
 * Draw the directory.
 */
function drawDirs($list, &$manager, $i)
{
        global $relative;

        $image_per_line = 5;
        foreach($list as $path => $dir)
        {
                $i++;
                if ($i==1) { echo "<tr>";}
                ?>
                <td><table width="100" cellpadding="0" cellspacing="0"><tr><td class="block">
                <a href="images.php?dir=<?php echo rawurlencode($path); ?>" onclick="updateDir('<?php echo $path; ?>')" title="<?php echo $dir['entry']; ?>"><img src="img/folder.gif" height="80" width="80" alt="<?php echo $dir['entry']; ?>" /></a>
                </td></tr><tr>
                <td class="edit">
                        <?php if ($dir['entry']!='..') { ?>
                        <a href="images.php?dir=<?php echo $relative; ?>&amp;deld=<?php echo rawurlencode($path); ?>" title="Trash" onclick="return confirmDeleteDir('<?php echo $dir['entry']; ?>', <?php echo $dir['count']; ?>);"><img src="img/edit_trash.gif" height="15" width="15" alt="Trash"/></a>
                        <?php }; ?>
                        <?php echo $dir['entry']; ?>

                </td>
                </tr></table></td>
          <?php
                if ($i==$image_per_line) {
                        echo "</tr>";
                        $i=0;
                }
        } //foreach
    return ($i);
}//function drawDirs

function addEmpties($list, &$manager, $j)
{
        global $relative;

        $image_per_line = 5;

        if ($j != 0) {
                for ($i=$j; $i <= $image_per_line; $i++) {
                ?>
                        <td></td>
                <?php
                        if ($i==$image_per_line) {
                                echo "</tr>";
                                $i=0;
                                break;
                        }
                } //for
       }
}//function addEmpties



/**
 * No directories and no files.
 */
function drawNoResults()
{
?>
<table width="100%">
  <tr>
    <td class="noResult">No Images Found</td>
  </tr>
</table>
<?php
}

/**
 * No directories and no files.
 */
function drawErrorBase(&$manager)
{
?>
<table width="100%">
  <tr>
    <td class="error">Invalid base directory: <?php echo $manager->config['base_dir']; ?></td>
  </tr>
</table>
<?php
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
        <title>Image List</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="assets/imagelist.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="assets/dialog.js"></script>
<script type="text/javascript">

/*<![CDATA[*/

        if(window.top)
                I18N = window.top.I18N;


        function hideMessage()
        {
                var topDoc = window.top.document;
                var messages = topDoc.getElementById('messages');
                if(messages)
                        messages.style.display = "none";
        }

        init = function()
        {
                hideMessage();
                var topDoc = window.top.document;
                window.parent.document.uploader.reldir.value = "<?php echo $relative; ?>";

<?php
        //we need to refesh the drop directory list
        //save the current dir, delete all select options
        //add the new list, re-select the saved dir.
        if($refreshDir)
        {
                $dirs = $manager->getDirs();
?>
                var selection = topDoc.getElementById('dirPath');
                var currentDir = selection.options[selection.selectedIndex].text;

                while(selection.length > 0)
                {        selection.remove(0); }

                selection.options[selection.length] = new Option("/","<?php echo rawurlencode('/'); ?>");
                <?php foreach($dirs as $relative=>$fullpath) { ?>
                selection.options[selection.length] = new Option("<?php echo $relative; ?>","<?php echo rawurlencode($relative); ?>");
                <?php } ?>

                for(var i = 0; i < selection.length; i++)
                {
                        var thisDir = selection.options[i].text;
                        if(thisDir == currentDir)
                        {
                                selection.selectedIndex = i;
                                break;
                        }
                }
<?php } ?>
        }

        function editImage(image)
        {
                var url = "editor.php?img="+image;
                Dialog(url, function(param)
                {
                        if (!param) // user must have pressed Cancel
                                return false;
                        else
                        {
                                return true;
                        }
                }, null);
        }

/*]]>*/
</script>
<script type="text/javascript" src="assets/images.js"></script>
<script language="javascript">
</script>

</head>

<body>


<?php if ($manager->isValidBase() == false) { drawErrorBase($manager); }

        elseif(count($list[0]) > 0 || count($list[1]) > 0) { ?>

<table width="560" cellpadding="0" cellspacing="0">
        <?php
                $i = 0;
                $i = drawDirs($list[0], $manager,$i);
                $i = drawFiles($list[1], $manager,$i);
                addEmpties($list[1], $manager,$i);
        ?>

</table>
<?php } else { drawNoResults(); } ?>
</body>
</html>
