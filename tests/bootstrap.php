<?php

use Symfony\Component\Dotenv\Dotenv;

if (!file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    throw new \RuntimeException('Install the dependencies to run the test suite.');
}

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    passthru(sprintf(
        'php "%s/../bin/console" cache:clear --env=%s --no-warmup',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));

    passthru(sprintf(
        'php "%s/../bin/console" api:swagger:export --env=%s',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
}

if (isset($_ENV['RECREATE_DATABASE'])
    && filter_var($_ENV['RECREATE_DATABASE'], FILTER_VALIDATE_BOOL) === true
    && isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])
) {
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:database:drop --if-exists --force --env=%s',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:database:create --env=%s',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:migrations:migrate --no-interaction --env=%s',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
}

$loader = require $file;

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env.test');
}
