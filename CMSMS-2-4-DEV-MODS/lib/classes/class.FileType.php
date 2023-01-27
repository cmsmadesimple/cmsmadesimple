<?php

/**
 * This file defines constants for file types
 * @package CMS
 * @license GPL
 */

namespace CMSMS;

/**
 * A simple abstract class that defines constants for numerous file types.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since  2.2
 */
abstract class FileType
{
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_MEDIA = 'media';
    const TYPE_XML   = 'xml';
    const TYPE_DOCUMENT = 'document';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_ANY = 'any';
} // end of class.