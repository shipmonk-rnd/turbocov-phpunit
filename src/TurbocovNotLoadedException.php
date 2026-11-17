<?php declare(strict_types = 1);

namespace ShipMonk\Turbocov;

use RuntimeException;

final class TurbocovNotLoadedException extends RuntimeException
{

    public function __construct()
    {
        parent::__construct(
            'The turbocov extension is not loaded. Install it with "pie install shipmonk/turbocov". '
            . 'A build with --enable-turbocov-impersonate-pcov does not need this driver: remove the "driver" attribute from the PHPUnit configuration.',
        );
    }

}
