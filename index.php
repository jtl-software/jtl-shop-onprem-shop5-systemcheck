<?php

use Systemcheck\Environment;
use Systemcheck\Platform\Hosting;

require __DIR__ . '/vendor/autoload.php';

$templatePath = __DIR__ . '/templates';
$twig         = new Twig_Environment(new Twig_Loader_Filesystem($templatePath), ['cache' => false]);
$systemcheck  = new Environment();
$tests        = $systemcheck->executeTestGroup('Shop5');
$platform     = new Hosting();

header('Content-Type: text/html; charset=utf-8');
echo $twig->render('systemcheck.html.twig', [
    'passed'   => $systemcheck->getIsPassed(),
    'tests'    => $tests,
    'platform' => $platform
]);
