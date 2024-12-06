# Contributing to CMS Made Simple

Thank you for your interest in contributing to CMS Made Simple (CMSMS)! We welcome contributions from the community to help make our content management system even better.

## Code of Conduct

By participating in the CMSMS community, you agree to abide by our Code of Conduct. We are committed to providing a welcoming and inclusive environment for all contributors.

## How to Contribute

### Reporting Bugs

Before submitting a bug report:
- Check the [CMSMS Forum](https://forum.cmsmadesimple.org) to see if the issue has been discussed
- Verify the bug exists in the latest stable version of CMSMS
- Check the [CMSMS Forge](https://dev.cmsmadesimple.org) for existing issues

When submitting a bug report, please include:
- Your CMSMS version
- PHP version and server environment details
- MySQL/MariaDB version
- A clear description of the steps to reproduce
- Any relevant error messages or logs
- Any modifications or custom modules installed

### Suggesting Enhancements

Enhancement suggestions should be posted on the CMSMS Forge. Please provide:
- A clear use case for the enhancement
- How it benefits CMSMS users
- Any potential impacts on existing functionality
- Consideration for backward compatibility

### Pull Requests

1. Fork the [CMSMS repository](https://github.com/CMS-Made-Simple/CMS-Made-Simple)
2. Create a new branch (`git checkout -b feature/your-feature`)
3. Make your changes
4. Write or update tests as needed
5. Ensure all tests pass
6. Submit your Pull Request

#### Pull Request Guidelines

- Follow the CMSMS coding standards
- Maintain PHP 7.4+ compatibility
- Ensure backward compatibility when possible
- Add PHPUnit tests for new features
- Update the relevant documentation
- Keep security in mind - CMSMS is used in production environments

## Development Setup

1. Set up your development environment:

```bash
git clone https://github.com/CMS-Made-Simple/CMS-Made-Simple.git
cd CMS-Made-Simple
composer install
```

2. Configure your web server (Apache/Nginx) with PHP 7.4+
3. Set up a MySQL/MariaDB database
4. Copy config.php.sample to config.php and configure your database settings
5. Run the installation process

## Style Guidelines

### Code Formatting

- Follow PSR-12 coding standards
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
```

Example:
```
[AdminUI] Add responsive tables to content list

- Implement responsive table layout
- Add mobile-friendly sorting
- Update documentation
```

## Documentation

- Update the [CMSMS Documentation](https://docs.cmsmadesimple.org) when adding features
- Add PHPDoc blocks to new classes and methods
- Include code examples for API changes
- Update the changelog

## Module Development

When developing modules:
- Use the ModuleBuilder if starting from scratch
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
- Join the [CMSMS Discord](https://discord.gg/247wcY8)
- Check the [Official Documentation](https://docs.cmsmadesimple.org)
- Contact the core development team on the forum

## License

CMS Made Simple is released under the GNU General Public License v2 or later. By contributing, you agree that your contributions will be licensed under the same terms.

Thank you for contributing to CMS Made Simple!