# CommonPHP Cache

CommonPHP Cache provides simple driver-based caching support for CommonPHP applications. It defines cache contracts and manager behavior while allowing storage implementations such as memory, filesystem, database, Redis, or Symfony-backed cache drivers to be plugged in separately.

The package is intended to make common cache operations easy to use while keeping storage details behind focused drivers.

## Requirements

- PHP `^8.5`
- `comphp/runtime:^0.3`
- `psr/simple-cache` if PSR-16 support is enabled

## Installation

Once this package is available through your Composer repositories, install it with:

```bash
composer require comphp/cache
```

## Usage

```php
<?php

// TODO: Write usage
```

## Package Notes

This package should expose a simple cache API backed by driver implementations. Concrete storage integrations such as Symfony Cache, filesystem cache, database cache, Redis, or Memcached should live in driver packages.

## Error Handling

Driver errors, invalid keys, serialization failures, and storage failures should throw CommonPHP cache exceptions rather than returning ambiguous false values.

## Documentation

- [Usage](docs/usage.md)
- [Testing](TESTING.md)
- [Contributing](CONTRIBUTING.md)
- [Security](SECURITY.md)

## License

MIT. See [LICENSE.md](LICENSE.md).
