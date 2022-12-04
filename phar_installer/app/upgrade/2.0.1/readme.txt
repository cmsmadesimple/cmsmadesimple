Announcing CMSMS 2.0.1 - Adelaide
----------------------

We knew that shortly after releasing 2.0, regardless of how many months we were in beta, we would be creating bug-fix releases.   This is because the number of users who actually use the system increase when you remove the 'beta' or 'release candidate' suffixes.  Therefore, more issues are reported that need to be fixed.  CMSMS 2.0.1 - Adelaide is the first of those bug fix releases.

The bugs fixed in this release are important to almost everybody, they will affect upgrading, modules, templates, and users.   Therefore, we encourage you to upgrade your CMSMS 2.0 sites as soon as possible.

Some of the issues fixed in this release include
- Template, Stylesheet, and Content Locking
  We fixed numerous page locking issues with respect to different browsers and handling of unloading pages and various things.
  We also changed the respective template, stylesheet, and content lists to automatically refresh every 30 seconds so that they will show your locks properly.  This will help to solve problems with people who keep multiple tabs open.

- Smarty Scope
  Numerous issues with respect to passing Smarty variables from parent templates to child templates were attacked and solved.

- The new page selector
  Issues related to the new page-selector jQuery plugin were resolved.

- Editing templates with restricted permissions
  We solved a few relatively minor issues which occurred when a user had only 'additional-editor' access to a template, but nothing more.

- Implement the missing 403 error handler page type.
  We re-merged some changes from the 1.11.13 release that did not make it into 2.0 with respect to handling 403 (permission denied) errors.

- More
There are lots of issues that were fixed in this release... you can find more details about them by viewing the changelog that is included with your install after upgrading.   You can also see the changelog before upgrading from within the installation assistant.

- Installation Assistant
The installation assistant also got a few minor tweaks for this release.  Most of those tweaks are just related to displaying more useful messages.

Supported Versions
--------
At this time, the CMSMS Dev team will support general issues in CMSMS 2.0 and CMSMS 2.0.1.
CMSMS 1.12.1 will be supported with respect to absolutely critical bugs, and security issues until September 6, 2016.

Thanks:
--------
We would like to thank all of the people that reported issues to us, and made it easy for us to reproduce and therefore fix the issues.  The Dev Team has worked hard to test, re-test and document each and every issue.
