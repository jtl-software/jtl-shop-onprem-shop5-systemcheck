<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="JTL-Shop-Systemcheck">
    <meta name="author" content="JTL-Software GmbH">
    <link rel="shortcut icon" href="favicon.ico"/>
    <title>JTL-Shop-Systemcheck</title>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans"
          media="screen, projection">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Ubuntu"
          media="screen, projection">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
          integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />
    <style>
        body {
            background: #f4f4f4;
            font-family: 'Open Sans', Verdana, Arial, sans-serif;
            color: #333;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Ubuntu', sans-serif;
            font-weight: normal;
        }

        a.logo {
            display: block;
            font-size: 0px;
            margin-bottom: 20px;
        }

        .btn-test-result {
            margin-right: 5px;
        }

        @media print {
            #logo-headline {
                display: none;
            }
        }

        #nav-headline {
            background-color: #333;
        }
    </style>
</head>

<body>
<div class="containedr">
    <nav class="navbar navbar-expand-sm bg-body-tertiary" role="navigation" id="nav-headline">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="layout/images/JTL-Shop-Logo.svg" alt="JTL-Shop">
            </a>
            <div class="navbar-items" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.jtl-software.de/">JTL-Software GmbH</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://guide.jtl-software.com/jtl-shop/">Wiki</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://forum.jtl-software.de/">Supportforum</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
<div class="container mt-4">
    <h1>JTL-Shop-Systemcheck</h1>

    <div class="form-horizontal">
        <h3 class="mt-3">Webhosting-Plattform</h3>
        <div class="row">
            <label class="col col-sm-2 text-end fw-bold">Provider:</label>
            <div class="col col-sm-10">
                <p class="form-control-static">
                    {if $platform->getProvider() === 'jtl'}
                        JTL-Software GmbH
                    {elseif $platform->getProvider() === 'hosteurope'}
                        HostEurope
                    {elseif $platform->getProvider() === 'strato'}
                        Strato
                    {elseif $platform->getProvider() === '1und1'}
                        1&amp;1
                    {elseif $platform->getProvider() === 'alfahosting'}
                        Alfahosting
                    {else}
                        <em>unbekannt</em>
                        (Hostname: {$platform->getHostname()})
                    {/if}
                </p>
            </div>
        </div>
        <div class="row">
            <label class="col col-sm-2 text-end fw-bold">PHP-Version:</label>
            <div class="col col-sm-10">
                <p class="form-control-static">{$platform->getPhpVersion()}</p>
            </div>
        </div>
        <div class="row">
            {$docRoot = $platform->getDocumentRoot()}
            <label class="col col-sm-2 text-end fw-bold">Document Root:</label>
            <div class="col col-sm-10">
                <p class="form-control-static">{if $docRoot === '?'}<em>unbekannt</em>{else}{$docRoot}{/if}</p>
            </div>
        </div>
    </div>

    {if count($tests.programs) > 0}
        <h3 class="mt-3">Installierte Software</h3>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="col-xs-7">Software</th>
                <th class="col-xs-3">Voraussetzung</th>
                <th class="col-xs-2">Ihr System</th>
            </tr>
            </thead>
            <tbody>
            {foreach $tests.programs as $progTest}
                {if !$progTest->getIsOptional() || $progTest->getIsRecommended()}
                    <tr>
                        <td>
                            <div class="test-name">
                                <strong>{$progTest->getName()}</strong><br>
                                <p class="hidden-xs expandable">{$progTest->getDescription()}</p>
                            </div>
                        </td>
                        <td>{$progTest->getRequiredState()}</td>
                        <td>{getResults test=$progTest}</td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
        </table>
    {/if}

    {if count($tests.php_modules) > 0}
        <h3 class="mt-3">Benötigte PHP-Erweiterungen und -Funktionen:</h3>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="col-xs-10">Extension/Funktion</th>
                <th class="col-xs-2">Ihr System</th>
            </tr>
            </thead>
            <tbody>
            {foreach $tests.php_modules as $test}
                {if !$test->getIsOptional() || $test->getIsRecommended()}
                    <tr>
                        <td>
                            <div class="test-name">
                                <strong>{$test->getName()}</strong><br>
                                <p class="hidden-xs expandable">{$test->getDescription()}</p>
                            </div>
                        </td>
                        <td>{getResults test=$test}</td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
        </table>
    {/if}

    {if count($tests.php_config) > 0}
        <h3 class="mt-3">Benötigte PHP-Einstellungen:</h3>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="col-xs-7">Einstellung</th>
                <th class="col-xs-3">Benötigter Wert</th>
                <th class="col-xs-2">Ihr System</th>
            </tr>
            </thead>
            <tbody>
            {foreach $tests.php_config as $test}
                {if !$test->getIsOptional() || $test->getIsRecommended()}
                    <tr>
                        <td>
                            <div class="test-name">
                                <strong>{$test->getName()}</strong><br>
                                <p class="hidden-xs expandable">{$test->getDescription()}</p>
                            </div>
                        </td>
                        <td>{$test->getRequiredState()}</td>
                        <td>{getResults test=$test}</td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
        </table>
    {/if}
    {if count($tests.recommendations) > 0}
        <table class="table table-striped table-hover">
            <caption>Empfohlene Anpassungen:</caption>
            <thead>
            <tr>
                <th class="col-xs-7">&nbsp;</th>
                <th class="col-xs-3">Empfohlener Wert</th>
                <th class="col-xs-2">Ihr System</th>
            </tr>
            </thead>
            <tbody>
            {foreach $tests.recommendations as $test}
                <tr>
                    <td>
                        <div class="test-name">
                            <strong>{$test->getName()}</strong><br>
                            <p class="hidden-xs expandable">{$test->getDescription()}</p>
                        </div>
                    </td>
                    <td>{$test->getRequiredState()}</td>
                    <td>{getResults test=$test}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {/if}
</div>

<div class="container mb-3">
    <div class="pull-right">
        <img src="layout/images/JTL-Shop-Logo.svg" alt="JTL-Shop">
    </div>
</div>
</body>
</html>
