<?php declare(strict_types = 1);

namespace ShipMonk\Turbocov;

use PHPUnit\Framework\TestCase;
use turbocov\CannotCollectException;
use function array_map;
use function escapeshellarg;
use function exec;
use function file_get_contents;
use function implode;
use function preg_match_all;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;
use const PHP_BINARY;
use const PREG_SET_ORDER;

final class TurbocovDriverTest extends TestCase
{

    private const string PROJECT = __DIR__ . '/_fixtures/project';

    public function testCoverageOfProject(): void
    {
        $clover = tempnam(sys_get_temp_dir(), 'turbocov-clover');
        self::assertIsString($clover);

        try {
            [$exitCode, $output] = $this->runPhp([
                '-d',
                'turbocov.collect_paths=' . self::PROJECT . '/src',
                __DIR__ . '/../vendor/bin/phpunit',
                '--configuration',
                self::PROJECT . '/phpunit.xml',
                '--cache-directory',
                sys_get_temp_dir() . '/turbocov-phpunit-cache',
                '--coverage-clover',
                $clover,
            ]);

            self::assertSame(0, $exitCode, $output);
            self::assertStringContainsString('with turbocov ', $output);
            self::assertStringContainsString('OK (3 tests, 3 assertions)', $output);

            self::assertSame(
                [
                    'Calculator.php' => [
                        8 => 2, // add(), two tests
                        10 => 2,
                        13 => 1, // divide(), a test in a separate process
                        15 => 1,
                        16 => 0,
                        19 => 1,
                        22 => 0, // neverCalled()
                        24 => 0,
                    ],
                    'Untouched.php' => [ // no test loads this file
                        8 => 0,
                        10 => 0,
                    ],
                ],
                $this->readLineCounts($clover),
            );
        } finally {
            unlink($clover);
        }
    }

    public function testRunStopsWhenCollectPathsAreNotSet(): void
    {
        [$exitCode, $output] = $this->runPhp([
            __DIR__ . '/../vendor/bin/phpunit',
            '--configuration',
            self::PROJECT . '/phpunit.xml',
            '--cache-directory',
            sys_get_temp_dir() . '/turbocov-phpunit-cache',
            '--coverage-text',
        ]);

        self::assertNotSame(0, $exitCode, $output);
        self::assertStringContainsString('turbocov cannot collect coverage: turbocov.collect_paths is not set', $output);
        self::assertStringNotContainsString('OK (', $output);
    }

    /**
     * @throws CannotCollectException
     * @throws TurbocovNotLoadedException
     */
    public function testConstructorThrowsWhenCollectPathsAreNotSet(): void
    {
        $this->expectException(CannotCollectException::class);
        $this->expectExceptionMessage('turbocov cannot collect coverage: turbocov.collect_paths is not set');

        new TurbocovDriver();
    }

    public function testConstructorThrowsWhenExtensionIsNotLoaded(): void
    {
        $code = <<<'PHP'
            require $argv[1];

            try {
                new ShipMonk\Turbocov\TurbocovDriver();
            } catch (Throwable $e) {
                echo $e::class, ': ', $e->getMessage();
            }
            PHP;

        // -n starts PHP without any ini file, thus without the extension
        [$exitCode, $output] = $this->runPhp(['-n', '-r', $code, __DIR__ . '/../vendor/autoload.php']);

        self::assertSame(0, $exitCode, $output);
        self::assertStringStartsWith(TurbocovNotLoadedException::class . ': The turbocov extension is not loaded. Install it with "pie install shipmonk/turbocov".', $output);
    }

    /**
     * @param list<string> $arguments
     * @return array{int, string}
     */
    private function runPhp(array $arguments): array
    {
        $command = implode(' ', array_map(escapeshellarg(...), [PHP_BINARY, ...$arguments]));

        exec("{$command} 2>&1", $output, $exitCode);

        return [$exitCode, implode("\n", $output)];
    }

    /**
     * @return array<string, array<int, int>> file => line => number of tests
     */
    private function readLineCounts(string $clover): array
    {
        $xml = file_get_contents($clover);
        self::assertIsString($xml);

        $counts = [];

        preg_match_all('~<file name="[^"]*/([^"/]+)">(.*?)</file>~s', $xml, $files, PREG_SET_ORDER);

        foreach ($files as $file) {
            preg_match_all('~<line num="(\d+)"[^>]* count="(\d+)"~', $file[2], $lines, PREG_SET_ORDER);

            foreach ($lines as $line) {
                $counts[$file[1]][(int) $line[1]] = (int) $line[2];
            }
        }

        return $counts;
    }

}
