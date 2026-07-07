# ModuleManager

A client for the CMS Made Simple Module Repository. Browse, install, upgrade, and manage modules directly from the CMSMS admin panel.

## Requirements

- CMS Made Simple 2.2.3+
- PHP 7.4+

## Third-Party Services

This module connects to the following external services:

### CMSMS CDN (https://cdn.cmsmadesimple.org)

- **Purpose:** Serves module listings (letter caches, recent modules), module XML packages, version checks, and module icons.
- **Data sent:** Standard HTTP GET requests. No user data is transmitted.
- **Endpoints used:**
  - `repository/version.json` — connection/version check
  - `repository/cache/moduledetailsgetall_{letter}.json` — module browse by letter
  - `repository/cache/moduledetailsgetall_recent.json` — recently updated modules
  - `repository/cache/module_{filename}.json` — per-module download metadata
  - `modules/{name}/icon.png` — module icons

### CMSMS API (https://api.cmsmadesimple.org)

- **Purpose:** Handles exact module lookups, dependency resolution, search, help, and about content.
- **Data sent:** Module name, version, CMS version, and search terms.
- **Endpoint:** `ModuleRepository/request/v2/`

### CMSMS Events API (https://api.cmsmadesimple.org/v1/modules/)

- **Purpose:** Tracks module install, upgrade, and uninstall events for repository statistics.
- **Data sent:** Module name, event type (install/upgrade/uninstall), module version, CMS version.
- **Behaviour:** Fire-and-forget POST with 3-second timeout. Does not block module operations on failure.

## License

GPL v2 or later. See LICENSE for details.
