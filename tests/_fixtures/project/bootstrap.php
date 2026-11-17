<?php declare(strict_types = 1);

require __DIR__ . '/../../../vendor/autoload.php';

spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'Fixture\\')) {
        $name = substr($class, strlen('Fixture\\'));

        foreach (['src', 'tests'] as $directory) {
            $file = __DIR__ . "/{$directory}/{$name}.php";

            if (is_file($file)) {
                require $file;
            }
        }
    }
});
