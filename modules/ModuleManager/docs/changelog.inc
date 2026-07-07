<ul>
<li>Version 2.2.0
<ul>
  <li>Finalized the ModuleManager release line for CMSMS 2.2.23</li>
</ul>
</li>
<li>Version 3.0.1-beta.2
<ul>
  <li>Version renamed to 3.0.1-beta.2 (follows versioning convention)</li>
  <li>Browse modules now fetches letter caches from CDN instead of API</li>
  <li>XML downloads served via CDN instead of API</li>
  <li>Connection check uses CDN version.json (GET) instead of API (POST)</li>
  <li>Event tracking: install/upgrade/uninstall events sent to Events API</li>
  <li>Compatibility system: modules filtered by php_max, cmsms_max, cmsms_tested</li>
  <li>Hide incompatible modules by default (preference: show_incompatible)</li>
  <li>Hide beta/pre-release modules by default (preference: show_beta)</li>
  <li>Beta filter extended to catch version patterns like 0.3.0b1</li>
  <li>Module icons from CDN on browse tab, local icons on installed tab</li>
  <li>No icon request made when icon doesn't exist (no more 403 errors)</li>
  <li>Status icons: incompatible (red), untested (yellow), new (&lt;3 months)</li>
  <li>Added preferences: show_beta, show_incompatible, always fetch fresh data, allowuninstall</li>
  <li>Featured Modules tab as default landing (CDN recent cache, card layout)</li>
  <li>Community notice collapsed to single-line info bar on Browse tab</li>
  <li>Fixed unserialize() vulnerability — replaced with json_decode()</li>
  <li>Replaced direct HTML error output with ShowErrors() across all tabs</li>
  <li>Fixed deprecated $this->smarty usage — now uses cmsms()->GetSmarty()</li>
  <li>Added permission constants (MANAGE_PERM, PREFS_PERM)</li>
  <li>Added GetDependencies() metadata method</li>
  <li>Silent exception catches now log via audit()</li>
  <li>Removed display_errors suppression from module export</li>
  <li>Added license header to class.ModuleManagerModuleInfo.php</li>
  <li>Added readme.md with Third-Party Services documentation</li>
  <li>Replaced per-file GPL license blocks with reference to docs/LICENSE</li>
  <li>Modernized direct access guards: if (!defined('CMS_VERSION')) exit;</li>
  <li>Version check cached for 24 hours instead of default ~60 minutes</li>
  <li>Fixed Smarty variable leak between Featured and Upgrades tabs</li>
  <li>Replaced @ error suppression with proper file_exists() checks</li>
  <li>Replaced sizeof() with count()</li>
  <li>Replaced global $CMS_VERSION with CMS_VERSION constant</li>
  <li>Internal cache files now use json_encode/json_decode instead of serialize</li>
  <li>Scoped Smarty templates: all ProcessTemplate() calls converted to CreateTemplate()</li>
  <li>Renamed modulerep_client to modmgr_rep_client (naming convention)</li>
  <li>Extracted install/dependency helpers to lib/class.modmgr_install_helper.php</li>
  <li>Featured modules CDN cache set to 24 hours</li>
  <li>Renamed iw_IL locale to he_IL (modern Hebrew code)</li>
  <li>Fixed unescaped quotes in he_IL translation</li>
  <li>Updated help text with v3.0.x features, tabs, settings, and third-party services</li>
  <li>PHP 8.4 readiness: no serialize, no @, no global, no sizeof</li>
</ul>
</li>
<li>Version 3.0.0 - April, 2026
<ul>
  <li>Introduced CloudFront CDN (cdn.cmsmadesimple.org) — module browsing, help, about, dependencies, and XML downloads now served from edge locations worldwide</li>
  <li>Default repository URL changed to api.cmsmadesimple.org</li>
  <li>Switched all API requests from POST to GET (except search) for CDN cacheability</li>
  <li>Version check served from static CDN file — dramatically faster startup</li>
  <li>Module icons displayed next to module names when available</li>
  <li>New setting: Show beta/pre-release modules (default: hidden)</li>
  <li>Increased connection check timeout to prevent false "cannot connect" errors</li>
  <li>Removed stale/warning labels from older modules</li>
  <li>Fixed date column width</li>
  <li>Response times reduced from ~500ms to ~100ms for most operations</li>
</ul>
</li>
<li>Version 1.0. 10 January 2006. Initial Release.</li>
<li>Version 1.1. July, 2006. Released with the 1.0- beta</li>
<li>Version 1.1.1 August, 2006.  Require 1.0.1 of nuSOAP</li>
<li>Version 1.1.2 September, 2006.  Fixed a mistake that resulted in upgrade not not working at all</li>
<li>Version 1.1.3 September, 2006.
  <ul>
  <li>Bumped minimum CMS Version to 1.0</li>
  <li>Now use 1 request to get the complete list of modules from the repository</li>
  <li>Added some missing lang strings</li>
  <li>Added the ability to reset the local cache of repository information</li>
  <li>Added the ability to restore the repository url to factory defaults</li>
  </ul>
</li>
<li>Version 1.1.4 February, 2007.  Now handles the safe mode check, and disables upgrading or installing modules if the permissions are wrong.</li>
<li>Version 1.1.5 September, 2007. New preference to make only latest module version show. Added nice message after saving preferences</li>
</li>
<li>Version 1.1.6 May, 2008. Now show if available modules are incompatible with the current CMS_VERSION.</li>
</li>
<li>Version 1.2 June, 2008.<br />
This version should reduce the memory requirements of this module, and trade it off for performance on the server, and more requests to the server.
   <ul>
    <li>Bumped Minimum CMS Version to 1.3</li>
    <li>Bumped Minimum repository version to 1.1</li>
    <li>Get rid of all of the session stuff</li>
    <li>Add support for requesting modules beginning with a prefix (usually a single letter)</li>
    <li>Add support for requesting only the newest versions of the modules</li>
   </ul>
</li>
<li>Version 1.2.1 August, 2008.<br />
Added a warning message to the top of the admin display.
</li>
<li>Version 1.3 May, 2009.<br />
Added dependency checking.
</li>
<li>Version 1.3.3 March, 2010.<br />
<ul>
  <li>PHP 5.x improvements (specifically remove warnings for PHP 5.3)</li>
  <li>Minor bug fixes.</li>
</ul>
</li>
<li>Version 1.4 June, 2010.<br />
<ul>
  <li>Implemented automatic dependency calculation, and one-click installation.</li>
  <li>Assorted usability improvements.</li>
  <li>Minor bug fixes.</li>
</ul>
<li>Version 1.5  - July, 2011
<ul>
  <li>Changes to REST API.. No longer uses nuSOAP.</li>
  <li>Many optimizations to downloading and installing modules.</li>
  <li>Can now install older versions of modules easily.</li>
  <li>Handle automatic upgrading of ddependencies.</li>
</ul>
</li>
</ul>
