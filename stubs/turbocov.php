<?php declare(strict_types = 1);

// Declarations of the turbocov extension, for static analysis and for an IDE. PHP never loads this file.

namespace turbocov;

use RuntimeException;

final class CannotCollectException extends RuntimeException
{

}

/**
 * @throws CannotCollectException
 */
function ensure_can_collect(): void
{
}

/**
 * @throws CannotCollectException
 */
function start(): void
{
}

/**
 * @return array<non-empty-string, array<int<1, max>, 1>> file => line => 1, for the lines that ran since start()
 */
function stop(): array
{
    return [];
}

/**
 * @param list<string> $files
 * @return array<non-empty-string, array<int<1, max>, -1>> file => line => -1, for each executable line
 */
function get_executable_lines(array $files): array
{
    return [];
}
