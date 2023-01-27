This is an incremental release in the CMSMS 2.x series, addressing bugs and minor concerns.

Once again we battled with the locking, and for the most part we think we have it nailed.  We fixed some minor template errors in Design Manager and Content Manager thatoccurred when locking was disabled.  We also revised the locking JavaScript to use an asynchronous process that works asynchronously in almost all circumstances for common browsers.

The new locking JavaScript code is apparently not supported in Safari (particularly on iOS) and in Internet Explorer or Edge.   If your clients are primarily using these browsers, we suggest that you disable locking entirely and exercise caution so that users don't accidentally overwrite each other's work.

New functionality also allows non-admin users to explicitly, manually clear their locks, and for admin users (members of the admin group) to explicitly clear all locks.

We fixed a problem where permissions related to viewing and managing user settings and the user profile were not created in sites upgraded from 1.12.  If you are running some non-standard user groups you may need to modify the permissions associated with those groups to ensure they have the 'Manage My Account',  'Manage My Bookmarks' and 'Manage My Settings' permissions as is appropriate for your site.

Additionally, there were fixes to the Navigator module related to install and uninstall,  fixes to the {content_image} plugin, and fixes related to the initialization of the setting so that modules like FrontEndUsers would not generate a 403 error at the wrong time.

A complete list of the changes for CMSMS 2.1.1 is as usual available in the doc/CHANGELOG.txt file in the installation, and is available in the installation assistant.

We encourage you to upgrade your installations of CMSMS at your earliest convenience. 

Thank you and enjoy CMSMS.
