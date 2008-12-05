TAR/GZIP/BZIP2/ZIP ARCHIVE CLASSES 2.1
By Devin Doucette
Copyright (c) 2005 Devin Doucette
Email: darksnoopy@shaw.ca

Requirements:
PHP 4 or greater (there is a chance tar and zip archives will work with PHP 3)
Compiled using --with-bz2 for bzip2 support.
Compiled using --with-zlib for gzip and zip support.
 (Zip archives created using method 0 do not require zlib)

Features:
- Can create tar, gzip, bzip2, and zip archives.
- Can create self-extracting zip archives.
- Can recurse through and store directories.
- Can create archives in memory or on disk.
- Can allow client to download file directly from memory.
- Errors are placed in an array named "errors" in the object.
- Supports in zip file comments.
- Files are automatically sorted within archive for greater 
  compression in gzip and bzip2 archives.
- Supports archiving and extraction of symbolic links (not 
  applicable for zip files).

Note:
Bzip2 and gzip archives are always created as tar files and then compressed, so the 
recommended file extensions are .tbz/.tbz2 and .tgz respectively.

Limitations:
- Currently only USTAR archives are supported for tar file extraction.
- Only bzip2 and gzip files that contain a tar file can be extracted. (Opening these 
  files otherwise is already supported by PHP)
- Cannot extract zip files. (This feature is already supported by PHP)

Extraction of bzip2 and gzip archives is limited to compatible tar files that have 
been compressed by either bzip2 or gzip.  For greater support, use the functions 
bzopen and gzopen respectively for bzip2 and gzip extraction.

Zip extraction is not supported due to the wide variety of algorithms that may be 
used for compression and newer features such as encryption.  If you need to extract 
zip files, use the functions detailed at http://www.php.net/manual/en/ref.zip.php.

Downloading Files:
The download_file function only works for files that are stored in memory.  To have 
users download files that are on disk redirect to the file, or use the following method: 
- Send the appropriate Content-Type header for the file being sent.
- Send a "Content-Disposition: attachment; filename=[insert filename]" header.
- Write out the file contents.



Usage:
For tar use tar_file (eg. $example = new tar_file("example.tar");)
For gzip use gzip_file (eg. $example = new gzip_file("example.tgz");)
For bzip2 use bzip_file (eg. $example = new bzip_file("example.tbz");)
For zip use zip_file (eg. $example = new zip_file("example.zip");)

Options:
To set options, send an array containing the options that you wish to set to the 
function set_options. (eg. $example->setoptions($options);)
The options array can include any of the following:

basedir (default ".")
   Sets the that all filenames are taken as being relative to (except optional sfx header).
   Used both when creating and when extracting (will extract to basedir if inmemory == 0).

name (no default)
   The name (and path, if necessary) of the archive, relative to basedir.
   Should be set when creating object (eg. $example = new zip_file("test/example.zip");).

prepend (no default)
   The path that is added to the beginning of every filename in the archive.
   Example: If prepend is set to "src/zlib/" then the file "docs/readme.txt" will be 
      stored in the archive as "src/zlib/docs/readme.txt".

inmemory (default 0)
   Set to 1 to create/extract archive in memory, set to 0 to write to disk.

overwrite (default 0)
   Set to 1 to overwrite existing files when creating/extracting archives.
   If set to 0, will give error message if file already exists.

recurse (default 1)
   Set to 1 to recurse through subdirectories, 0 to not recurse.

storepaths (default 1)
   Set to 1 to store paths in the archive, 0 to strip paths from the filenames.

followlinks (default 0)
   Set to 1 to store the file that the symbolic link points to, 0 to store the link itself.

level (default 3, zip and gzip only) [1-9]
   Level of compression for zip and gzip files, 0 is none.

method (default 1, zip only)
   Set to 1 to compress files in the zip archive, 0 to store files only (no compression).

sfx (no default, zip only)
   Filename of a valid sfx header for a zip archive, NOT relative to basedir.
   SFX capability is added to a zip file by simply prepending a valid executable, so this 
      options takes the path of such a file. An example of a valid file for this would be 
      "Zip.SFX" that is included in WinRAR.

comment (no default)
   The comment added to a zip archive; may be used to set options for some sfx modules.

Example options array: $options = array('basedir' => "../example", 'overwrite' => 1);

Adding Files and Directories:
To add files use the add_files function, which takes either an array or a single 
file/path.  The * character can be used but be careful, as it is the equivalent 
of placing .* in a regular expression.
Examples: $example->add_files("htdocs");
          $example->add_files(array("test.php", "htdocs/*.txt"));
          $example->add_files("../*.gif");

To exclude files or directories from the archive use the exclude_files function, which works 
the same as the add_files function, except it excludes any files or directories would 
otherwise be added to the archive. (eg. $example->exclude_files("*.html");)

To store files without compression (zip only), use the store_files function. This function 
works the same as add_files and exclude_files. (eg. $example->store_files("htdocs/test.txt");)

Creating and Extracting Archives:
To create an archive, call the create_archive function. (eg. $example->create_archive();)
The archive created is the one passed when creating the object.  If the file is downloaded, 
the filename sent for the download is the name passed when creating the object.

To extract an archive, call the extract_files function. (eg. $example->extract_files();)
The archive extracted is the one passed when creating the object.  If the file is extracted 
to memory, the file information is located in an array called files (eg. $example->files)

The structure of the array into which files are extracted in memory is as follows:
$files = array(
'name'=>filename,
'stat'=>array(
   2=>mode
   4=>uid
   5=>gid
   7=>size
   9=>mtime),
'type'=>0 for file, 2 for symbolic link, 5 for directory,
'data'=>file contents);

Errors:
Any errors that occur during any process will be logged in the errors array 
(eg. $example->errors). If any serious errors occur they will report errors as
usual; only errors directly related to the creation or extraction of the archive
will be suppressed any stored in the errors array.
