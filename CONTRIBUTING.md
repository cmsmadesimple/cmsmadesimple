# Contributing to CMS Made Simple

Thank you for your interest in contributing to CMS Made Simple (CMSMS 🤯)!  
We welcome contributions from the community to help make our content management system even better.
This page describes how to maintain a copy of CMSMS for development purposes.  
>This is the way ...
## Code of Conduct

By participating in the CMSMS community, you agree to abide by our [Code of Conduct](CODE_OF_CONDUCT.md). 


## The repository
This our official repository : [https://github.com/cmsmadesimple/cmsmadesimple](https://github.com/cmsmadesimple/cmsmadesimple)  
As of the reading of this document, the latest version is ![latest](https://badgen.net/github/release/cmsmadesimple/cmsmadesimple)


## Forking the repository
Use Github to fork the repository, as per [this guide](https://docs.github.com/en/get-started/quickstart/fork-a-repo).  
As a general guideline, forking is mandatory and will allow you to work on your own copy of the repository while still being able to contribute to the main repository.

## Development Setup
For the moment, the development setup is done in two steps: 
1. Setup CMS Made Simple as a regular installation (English only, check [releases](https://github.com/cmsmadesimple/cmsmadesimple/releases)), save the config.php file and remove all other files/directories
2. Clone the repository, paste your saved [config.php](#setup-config.php) file in the root of the repository
  
Following these steps, you should have a running CMS Made Simple installation in a working directory (usually named cmsmadesimple).
You can now start to load your modules and custom assests to fit your needs.

## Reporting Bugs

Before submitting a bug report:
- Check the [Forum](https://forum.cmsmadesimple.org) to see if the issue has been discussed
- Verify the bug exists in the latest stable version of CMSMS ![latest](https://badgen.net/github/release/cmsmadesimple/cmsmadesimple) 
- Check the [Forge](https://dev.cmsmadesimple.org/bug/list/6) for existing issues

When submitting a bug report, please include:
- Your CMSMS version
- PHP version and server environment details
- MySQL/MariaDB version
- A clear description of the steps to reproduce
- Any relevant error messages or logs
- Any modifications or custom modules installed

### How to create a Bug Report fixing Branch
Bug Report branches are usually created from the `master` branch. The feature will be included in a future/next release.
Your branch should be named `BR-45678` where `45678` is the number of the feature request in the [Forge](https://dev.cmsmadesimple.org/bug/list/6).

```git
$ cd /path/to/your/webroot
$ git clone git@github.com:cmsmadesimple/cmsmadesimple.git .        (1)
$ git checkout -b BR-45678                                          (2)
```
* The command (1) initializes the new local repository.
* The command (2) creates a new branch called `BR-45678` and switches to it.

## Suggesting Enhancements

Enhancement suggestions, [Feature Requests](https://dev.cmsmadesimple.org/feature_request/list/6), should be posted on the CMSMS Forge . Please provide:
- A clear use case for the enhancement
- How it benefits CMSMS users
- Any potential impacts on existing functionality
- Consideration for backward compatibility

### How to create a Feature Request Branch
Feature Request branches are usually created from the `master` branch. The feature will be included in a future/next release.
Your branch should be named `FR-12345` where `12345` is the number of the feature request in the [Forge](https://dev.cmsmadesimple.org/feature_request/list/6).

```git
$ cd /path/to/your/webroot
$ git clone git@github.com:cmsmadesimple/cmsmadesimple.git .        (1)
$ git checkout -b FR-12345                                          (2)
```
* The command (1) initializes the new local repository.
* The command (2) creates a new branch called `FR-12345` and switches to it.

## Pull Requests

Your changes should be made in the `FR-12345` branch and you are ready to submit a Pull Request upstream to CMSMS.

```git
$git pull --rebase upstream/master                     (1)
$git push --force                                      (2)
```	
* The command (1) will rebase your changes on top of the latest master branch.
* This will be the moment where you should resolve any conflicts.
* The command (2) will push upstream changes to your fork.

At this point, you can create a Pull Request on [Github](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request-from-a-fork) from your fork.

#### Pull Request Guidelines

- Follow the CMSMS coding standards
- Maintain PHP 7.4+ compatibility
- Ensure backward compatibility when possible
- Add PHPUnit tests for new features
- Update the relevant documentation
- Keep security in mind - CMSMS is used in production environments

## Style Guidelines

### Code Formatting

- Follow reasonable coding standards
- Use meaningful variable and function names
- Comment complex logic
- Use consistent indentation (4 spaces)
- Follow CMSMS naming conventions for modules and classes

### PHP Standards

- Write PHP 7.4+ compatible code
- Use type hints where appropriate
- Follow SOLID principles
- Use prepared statements for all database queries
- Properly escape output using appropriate methods

### Commit Messages

Format:
```
[Component] Brief description

Detailed explanation if needed
Reference to Feature Request FR-# or Bug Report BR-#
```

Example:
```
[AdminUI] Add responsive tables to content list
- FR-12345
- Implement responsive table layout
- Add mobile-friendly sorting
- Update documentation
```

## Documentation

- Update the [Documentation](https://docs.cmsmadesimple.org) when adding features
- Add PHPDoc blocks to new classes and methods
- Include code examples for API changes
- Update the changelog

## Module Development

When developing modules:
- Use the [god sent Tutorial](https://docs.cmsmadesimple.org/uploads/Module_Writing_Tutorial.pdf) if starting from scratch
- Follow the CMSMS module API
- Use standard module interfaces where appropriate
- Include language files for internationalization
- Provide clear documentation
- Include upgrade scripts if needed

## Testing

- Write PHPUnit tests for new features
- Test across supported PHP versions
- Test with different database types (MySQL, MariaDB)
- Verify admin and frontend functionality
- Test with different themes and templates

## Security

- Follow secure coding practices
- Use CMSMS's built-in security features
- Properly sanitize all input
- Use parameterized queries
- Implement proper permission checks
- Report security issues privately to the core team

## Getting Help

- Visit the [CMSMS Forum](https://forum.cmsmadesimple.org)
- Join the [CMSMS Slack](https://www.cmsmadesimple.org/support/documentation/chat)
- Check the [Official Documentation](https://docs.cmsmadesimple.org)
- Contact the core development team on the forum

## License

CMS Made Simple is released under [the GNU General Public License v3](LICENSE) or later. By contributing, you agree that your contributions will be licensed under the same terms.

Thank you for contributing to CMS Made Simple!





## Setup config.php
The config.php file is a crucial part of any CMSMS installation. It is where you can define various settings and options that will be used throughout your site. Copy the provided bak.config.php and rename it config.php. You will find more information about the available parameters in the config.php [Reference here](https://docs.cmsmadesimple.org/configuration/config-file/config-reference)

```php
<?php
# CMS Made Simple Configuration File
# Documentation: /doc/CMSMS_config_reference.pdf
#
$config['dbms'] = 'mysqli';
$config['db_hostname'] = 'localhost';
$config['db_username'] = 'your_database_username';
$config['db_password'] = 'your_database_password';
$config['db_name'] = 'your_database_name';
$config['db_prefix'] = 'cms_';

// Timezone setting
$config['timezone'] = 'UTC';

// Site settings
#$config['root_url'] = 'http://yourwebsite.com/';
#$config['upload_url'] = 'http://yourwebsite.com/uploads/';
#$config['uploads_path'] = '/path/to/your/cms/uploads/';

// Other settings...
#$config['url_rewriting'] = 'mod_rewrite';
#$config['page_extension'] = '/';
#$config['permissive_smarty'] = true;
// ...

// Error reporting
#$config['debug'] = false; // Set to true for debugging purposes
?>
```
