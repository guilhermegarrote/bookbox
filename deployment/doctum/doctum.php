<?php

declare(strict_types=1);

use Doctum\Doctum;
use Doctum\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$projectRoot = dirname(__DIR__, 2);

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in([
        $projectRoot . '/app',
        $projectRoot . '/database',
    ])
    ->exclude('storage')
    ->notPath('vendor');

$buildDir = $projectRoot . '/docs/doctum';
$cacheDir = $projectRoot . '/storage/doctum-cache';

$doctum = new Doctum($iterator, [
    'title' => 'Bookbox Project Documentation',
    'build_dir' => $buildDir,
    'cache_dir' => $cacheDir,
    'remote_repository' => new GitHubRemoteRepository(
        'guilhermegarrote/bookbox',
        $projectRoot,
        'main'
    ),
]);

return $doctum;
