# turbocov for PHPUnit

Code coverage driver that connects PHPUnit to the [turbocov](https://github.com/shipmonk-rnd/turbocov) extension.

turbocov collects line coverage, and covered code runs at the speed of plain PHP. Read the README of the extension for the mechanism, the settings and the limits.

## Requirements

- PHP 8.4 or 8.5
- PHPUnit 13.3 or later
- The turbocov extension

Earlier versions of PHPUnit cannot load a custom coverage driver.

## Installation

```sh
pie install shipmonk/turbocov
composer require --dev shipmonk/turbocov-phpunit
```

## Usage

Set the driver in `phpunit.xml`:

```xml
<coverage driver="ShipMonk\Turbocov\TurbocovDriver">
    <report>
        <clover outputFile="clover.xml"/>
    </report>
</coverage>
```

Tell the extension which files to collect, then run PHPUnit:

```sh
php -d turbocov.collect_paths=src vendor/bin/phpunit
```

- `turbocov.collect_paths` takes files and directories. The `<source>` element of `phpunit.xml` does not set it.
- Set it with `-d` or in `php.ini`. An `<ini>` element in `phpunit.xml` has no effect, because PHP reads this setting only when it starts.
- PHPUnit gives the setting to the processes of tests that run in a separate process.

PHPUnit shows the driver in its header:

```
Runtime:       PHP 8.5.12 with turbocov 0.1.0
```

### Coverage of changed files only

turbocov has no cost for files that are not in `turbocov.collect_paths`. To collect coverage only for the files that a branch changes:

```sh
git diff --name-only --diff-filter=AMR origin/master...HEAD -- '*.php' > changed.txt
php -d turbocov.collect_paths=@changed.txt vendor/bin/phpunit
```

### When turbocov cannot collect coverage

The driver stops the run before the first test. PHPUnit shows the reason:

```
An error occurred inside PHPUnit.

Message:  turbocov cannot collect coverage: turbocov.collect_paths is not set
```

The driver does not continue without coverage. A run with a wrong setup must not give an empty report.

## Limits

- Line coverage only. No branch coverage and no path coverage.
- The driver uses classes of `phpunit/php-code-coverage` that have no promise of backward compatibility. The tests of this package run with the lowest and the highest supported version.

## Development

The tests need the turbocov extension in `php.ini`. A `-d extension=` option is not sufficient, because the tests start more PHP processes.

```sh
composer check     # all checks
composer fix:cs    # fix the coding style
```

## License

MIT. See [LICENSE](LICENSE).
