# Rediscope

<p align="center">
  <strong>A Modern Redis UI for Laravel Applications</strong>
</p>

<p align="center">
  <a href="https://github.com/tushar-arote/rediscope/actions"><img src="https://github.com/tushar-arote/rediscope/workflows/tests/badge.svg" alt="Build Status"></a>  
  <a href="https://packagist.org/packages/tushar-arote/rediscope"><img src="https://poser.pugx.org/tushar-arote/rediscope/d/total.svg" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/tushar-arote/rediscope"><img src="https://poser.pugx.org/tushar-arote/rediscope/v/unstable.svg" alt="Latest Unstable Version"></a>
  <a href="https://packagist.org/packages/tushar-arote/rediscope"><img src="https://poser.pugx.org/tushar-arote/rediscope/license.svg" alt="License"></a>
</p>

## 📋 Introduction

Rediscope is a powerful, elegant Redis cache manager and UI for the Laravel framework. It provides an intuitive web interface to manage, monitor, and interact with Redis data structures in real-time.

### ✨ Features

- **Redis Management**: View, create, edit, and delete Redis keys effortlessly
- **Data Type Support**: Full support for Strings, Hashes, Lists, Sets, and Sorted Sets
- **Key Scanning**: Efficiently scan Redis keys with pattern matching
- **TTL Management**: View and manage key expiration times
- **Real-time Monitoring**: View Redis server information and statistics
- **Dark Mode**: Beautiful dark theme for comfortable viewing
- **Responsive UI**: Works seamlessly on desktop and mobile devices
- **Type-Safe**: Modern PHP 8.1+ and Laravel 8-11 support

## 🚀 Requirements

- **PHP**: 8.1 or higher
- **Laravel**: 8.0, 9.0, 10.0, or 11.0
- **Redis**: 4.0 or higher
- **Node.js**: 14.0 or higher (for building assets)

## 📦 Installation

### Step 1: Install via Composer

```bash
composer require tushar-arote/rediscope
```

### Step 2: Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="Rediscope\RediscopeServiceProvider"
```

This publishes the configuration file to `config/rediscope.php`.

### Step 3: Build Frontend Assets

```bash
npm install && npm run prod
```

### Step 4: Configure Authorization (Optional)

By default, Rediscope is only accessible in the `local` environment. To customize access, update your `AppServiceProvider`:

```php
use Rediscope\Rediscope;

public function boot()
{
    Rediscope::auth(function ($request) {
        return auth()->check() && auth()->user()->isAdmin();
    });
}
```

## 🔧 Configuration

The configuration file contains settings for:

- **Path**: Dashboard path (default: `/rediscope`)
- **Pagination**: Results per page (default: 50)
- **Theme**: Default theme (light or dark)

Edit `config/rediscope.php` to customize these settings.

## 🎯 Usage

### Accessing the Dashboard

After installation, access Rediscope at:

```
http://your-app.local/rediscope
```

### Key Operations

#### View Keys
- Navigate to the "Keys" section
- Use the search bar to filter keys by pattern
- Click any key to view its content

#### Manage Strings
- View string values
- Edit values directly in the UI
- Set expiration time (TTL)

#### Manage Hashes
- Add, edit, or remove hash fields
- View all fields in a hash at once

#### Manage Lists
- Push and pop items from lists
- Edit list items by index
- View list length

#### Manage Sets
- Add and remove set members
- View all members
- Check set cardinality

#### Manage Sorted Sets
- Add, edit, and remove members with scores
- Sort by score or member name
- View range queries

### Server Information

The "Info" section displays:
- Memory usage and statistics
- CPU metrics
- Client connections
- Command statistics

## 🎨 UI Features

### Dark Mode
Toggle dark mode using the theme switcher in the top-right corner. Your preference is remembered locally.

### Responsive Design
The interface is fully responsive and works on:
- Desktop browsers (Chrome, Firefox, Safari, Edge)
- Tablets
- Mobile devices

## 🔐 Security

### Authentication
By default, Rediscope is only accessible in the `local` environment. In production, you **must** configure proper authentication using the `Rediscope::auth()` method.

### Authorization
Implement your own authorization logic to restrict access:

```php
Rediscope::auth(function ($request) {
    return auth()->check() && auth()->user()->hasRole('developer');
});
```

## 🛠️ Development

### Building Assets

```bash
# Development build with watch
npm run watch

# Production build
npm run prod

# Hot reload development
npm run hot
```

### Running Tests

```bash
php vendor/bin/phpunit
```

## 📋 Supported Redis Data Types

- ✅ **Strings** - Simple key-value storage
- ✅ **Hashes** - Field-value pairs
- ✅ **Lists** - Ordered collections
- ✅ **Sets** - Unordered unique collections
- ✅ **Sorted Sets** - Scored members

## 🔄 Version Support

| Version | PHP | Laravel | Status |
|---------|-----|---------|--------|
| 2.0 | 8.1+ | 8, 9, 10, 11 | ✅ Current |
| 1.0 | 7.2+ | 6, 7 | 🔄 Legacy |

## 📚 Documentation

For detailed documentation, migration guides, and API reference, see:
- [UPGRADE_SUMMARY.md](UPGRADE_SUMMARY.md) - Version 2.0 upgrade details
- [DETAILED_CHANGELOG.md](DETAILED_CHANGELOG.md) - Complete changelog

## 🐛 Troubleshooting

### Rediscope Dashboard Not Loading

1. Verify Redis is running: `redis-cli ping`
2. Check Laravel Redis configuration in `config/database.php`
3. Clear application cache: `php artisan cache:clear`
4. Rebuild assets: `npm run prod`

### Authentication Issues

1. Verify your `Rediscope::auth()` configuration
2. Check application environment (`APP_ENV`)
3. Ensure you're accessing from correct domain/IP

### Asset Loading Issues

1. Run `npm install` and `npm run prod`
2. Check web server document root
3. Verify public path in `webpack.mix.js`

## 🤝 Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

### Development Setup

1. Clone the repository
2. Run `composer install` and `npm install`
3. Create a feature branch
4. Make your changes
5. Submit a pull request

## 📄 License

The Rediscope package is open-sourced software licensed under the [MIT license](LICENSE.md).

## 👨‍💻 Author

**Tushar Arote**
- Email: tushararote123@gmail.com
- GitHub: [@tushar-arote](https://github.com/tushar-arote)

## 🙏 Acknowledgments

- Inspired by [Laravel Telescope](https://laravel.com/docs/telescope)
- Built with [Vue.js](https://vuejs.org/) and [Bootstrap 5](https://getbootstrap.com/)
- Powered by [Predis](https://github.com/predis/predis)

## 📞 Support

For issues, questions, or suggestions:
1. Open an issue on [GitHub](https://github.com/tushar-arote/rediscope)
2. Check existing issues first
3. Provide detailed reproduction steps

---

**Made with ❤️ for Laravel developers**