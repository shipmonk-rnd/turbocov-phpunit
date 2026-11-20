# turbocov for PHPUnit

PHPUnit coverage driver for the [turbocov](https://github.com/shipmonk-rnd/turbocov) extension.

PHPUnit knows only Xdebug and PCOV. This package gives it one class, `ShipMonk\Turbocov\TurbocovDriver`, that collects coverage with turbocov.

## Installation

```sh
pie install shipmonk/turbocov
composer require --dev shipmonk/turbocov-phpunit
```

## Usage

Set the driver in `phpunit.xml`:

```xml
<coverage driver="ShipMonk\Turbocov\TurbocovDriver"/>
```

Then run PHPUnit with the paths to collect:

```sh
php -d turbocov.collect_paths=src vendor/bin/phpunit --coverage-html coverage
```

The [README of turbocov](https://github.com/shipmonk-rnd/turbocov#readme) describes the settings, the limitations and the use with older PHPUnit.

## Requirements

- PHPUnit 13.3 or later
- The turbocov extension

## Behavior

- The driver stops the run before the first test when turbocov cannot collect coverage. PHPUnit shows the reason. A wrong setup does not give an empty report.
- The driver uses classes of `phpunit/php-code-coverage` that have no promise of backward compatibility. The tests of this package run with the lowest and the highest supported version.

## Development

The tests need the turbocov extension in `php.ini`. A `-d extension=` option is not sufficient, because the tests start more PHP processes.

```sh
composer check     # all checks
composer fix:cs    # fix the coding style
```

## License

MIT. See [LICENSE](LICENSE).
