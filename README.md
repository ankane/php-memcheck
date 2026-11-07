# php-memcheck

Check PHP FFI bindings for memory issues

[![Build Status](https://github.com/ankane/php-memcheck/actions/workflows/build.yml/badge.svg)](https://github.com/ankane/php-memcheck/actions)

## Installation

First, install Valgrind. For Ubuntu, use:

```sh
sudo apt-get install valgrind
```

Then run:

```sh
composer require --dev ankane/php-memcheck
```

## Getting Started

Add a script to `composer.json` with `php-memcheck <test-command>`:

```json
    "scripts": {
        "test": "phpunit tests",
        "memcheck": "php-memcheck phpunit tests"
    }
```

And run:

```sh
composer memcheck
```

## Reference

See if Memcheck is running

```php
function memcheckRunning()
{
    return getenv('PHP_MEMCHECK_RUNNING') !== false;
}
```

## Thanks

Thanks to [Valgrind](https://valgrind.org), which does all of the work, and [ruby_memcheck](https://github.com/Shopify/ruby_memcheck) for the inspiration.

## History

View the [changelog](https://github.com/ankane/php-memcheck/blob/master/CHANGELOG.md)

## Contributing

Everyone is encouraged to help improve this project. Here are a few ways you can help:

- [Report bugs](https://github.com/ankane/php-memcheck/issues)
- Fix bugs and [submit pull requests](https://github.com/ankane/php-memcheck/pulls)
- Write, clarify, or fix documentation
- Suggest or add new features

To get started with development:

```sh
git clone https://github.com/ankane/php-memcheck.git
cd php-memcheck
composer install
composer test
```
