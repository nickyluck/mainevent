<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// Après un déploiement FTP, Symfony peut servir d'anciens templates Twig compilés.
// public/.deploy-id change à chaque deploy : on purge var/cache une fois si besoin.
$deployIdFile = __DIR__.'/.deploy-id';
$cacheDir = dirname(__DIR__).'/var/cache';
$marker = $cacheDir.'/.deploy-id';
$deployId = is_file($deployIdFile) ? trim((string) file_get_contents($deployIdFile)) : '';
if ($deployId !== '' && (!is_file($marker) || trim((string) file_get_contents($marker)) !== $deployId)) {
    if (is_dir($cacheDir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            $path = $file->getPathname();
            $file->isDir() ? @rmdir($path) : @unlink($path);
        }
    } else {
        @mkdir($cacheDir, 0775, true);
    }
    if (is_dir($cacheDir) || @mkdir($cacheDir, 0775, true)) {
        @file_put_contents($marker, $deployId);
    }
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
