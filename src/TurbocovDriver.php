<?php declare(strict_types = 1);

namespace ShipMonk\Turbocov;

use LogicException;
use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use turbocov\CannotCollectException;
use function array_diff_key;
use function array_keys;
use function array_replace;
use function extension_loaded;
use function phpversion;
use function turbocov\ensure_can_collect;
use function turbocov\get_executable_lines;
use function turbocov\start;
use function turbocov\stop;

final class TurbocovDriver extends Driver
{

    /**
     * @var array<string, true>
     */
    private array $knownFiles = [];

    /**
     * @throws CannotCollectException
     * @throws TurbocovNotLoadedException
     */
    public function __construct()
    {
        if (!extension_loaded('turbocov')) {
            throw new TurbocovNotLoadedException();
        }

        ensure_can_collect();
    }

    public function name(): string
    {
        return 'turbocov';
    }

    public function version(): string
    {
        $version = phpversion('turbocov');

        if ($version === false || $version === '') {
            throw new LogicException('The turbocov extension has no version');
        }

        return $version;
    }

    public function start(): void
    {
        try {
            start();
        } catch (CannotCollectException $e) {
            throw new LogicException($e->getMessage(), 0, $e); // the constructor did the same check
        }
    }

    public function stop(): RawCodeCoverageData
    {
        $lines = stop();
        $newFiles = array_diff_key($lines, $this->knownFiles);

        // php-code-coverage reads the lines that did not run only from the first data of a file
        if ($newFiles !== []) {
            $executableLines = get_executable_lines(array_keys($newFiles));

            foreach ($newFiles as $file => $executedLines) {
                if (!isset($executableLines[$file])) {
                    throw new LogicException("turbocov has no executable lines for {$file}");
                }

                $lines[$file] = array_replace($executableLines[$file], $executedLines);
                $this->knownFiles[$file] = true;
            }
        }

        return RawCodeCoverageData::fromLineCoverage($lines);
    }

}
