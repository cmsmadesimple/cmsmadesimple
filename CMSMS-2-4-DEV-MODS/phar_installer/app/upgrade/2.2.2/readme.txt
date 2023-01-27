Continuing with our commitment to quality code, we are announcing the release of 2.2.2 "Hearts Content", a security and stability release.

This release fixes or blocks a couple of very important security issues, addresses a number of bugs that existed in the system, and generally improves stability and usability.

Some important things to note are:
a:  The security issues addressed effect all previous versions of CMS Made Simple, not just the 2.x series.

b:  Due to the security fixes, Smarty resource specifications with paths or wildcard characters will no longer work.   This will affect a few third party modules--notably JMFilePicker.   The maintainers of affected modules should be able to address this issue without too much difficulty.   Additionally, any and all occurrences of {php} tags that may have been able to function in old versions of CMSMS should now fail.

c: We have once again changed the template processing order, specifically related to mact preprocessing.   Now, mact-preprocessing occurs AFTER the top portion of the template, but before the body portion.  This specifically addresses issues with multi-lang sites.  As of now, the template processing order is:
        1.  The top portion of the page template
        2. mact-preprocesing (if enabled) caches a module action intended for the {content} block
        3.  The body portion of the page template
        4.  The head portion of the page template.

d: fixes to cms_selflink, to content pages and to various API functions such that entirely numeric page aliases are invalid.  This is to prevent them from being confused with numeric page ids.
When adding or editing a page, if the resulting page alias is entirely numeric (i.e: 12345 or 123-123) then a non-numeric character ('p') will be prepended to the alias.    aliases such as 123-foo are not entirely numeric and therefore are valid.

e: Upgraded MicroTiny to use TinyMce 4.6.x and added the tabfocus and hr plugins.

As usual, a complete list of the items fixed and changed are available in the changelog that is displayed during the upgrade process and included with the release.

Because this is a security release as well as a stability release we encourage everybody to upgrade their websites as soon as possible.

Again we would like to thank  Daniel Le Gall from SCRT SA, Switzerland for identifying these vulnerabilities, reporting them to us in a professional manner, and working with us to ensure that they were resolved.

The CMSMS Dev Team now only officially supports CMSMS 2.2.2 and CMSMS 2.2.1.  Therefore, it is to your advantage to upgrade as soon as possible.

Thank you, and have fun with CMSMS.
