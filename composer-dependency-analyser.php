<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

if (!file_exists(getcwd() . '/composer.json')) {
    throw new \RuntimeException('No composer.json found.');
}

$config = null;

if (!$config instanceof Configuration) {
    $config = new Configuration();
}

if (empty($config->getPathsToScan())) {
    $paths = [
        './src' => false,
        './config' => false,
        './contao' => false,
        './templates' => false,
        './tests' => true,
    ];

    foreach ($paths as $path => $isDev) {
        if (file_exists($path)) {
            $config->addPathToScan($path, $isDev);
        }
    }
}

$config
    //->enableAnalysisOfUnusedDevDependencies()
    ->disableReportingUnmatchedIgnores()
    // The manager plugin is a dev dependency because it is only required in the
    // managed edition.
    ->ignoreErrorsOnPackage('contao/manager-plugin', [ErrorType::DEV_DEPENDENCY_IN_PROD])

    // Ignore ErrorType UNUSED_DEPENDENCY as it is used in dca
    ->ignoreErrorsOnPackage('mvo/contao-group-widget', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('codefog/contao-haste', [ErrorType::UNUSED_DEPENDENCY])
;

$composerJson = json_decode(file_get_contents(getcwd().'/composer.json'), true, 512, JSON_THROW_ON_ERROR);

return $config;