<?php
// side effect: change ini settings
//ini_set('error_reporting', E_ALL);
date_default_timezone_set('America/New_York');

$settings = require __DIR__ . '/settings.php';

require __DIR__ . '/vendor/autoload.php';

use Utils\Template;
use Utils\Template1;
use Utils\Db;
use Utils\Mail;
use Utils\DateCheck;
use Utils\Helper;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \Katzgrau\KLogger\Logger;
use \Psr\Log\LogLevel;

try {
    $logger = new Logger(
        $settings['LOGGER']['PATH'],
        LogLevel::WARNING,
        array (
            'extension' => 'log',
            'dateFormat' => 'Y-m-d G:i:s'
        )
    );

} catch (\Exception $e) {
    die('Logger error ' . $e->getMessage());
}

$helper = new Helper($settings, $logger, new DateCheck());

//  php index.php 20140303 20140304 template1
// or
// php index.php template1
try {

    $data = $helper->getInputs($argc, $argv);
    if (empty($data)) {
        throw new Exception('Correct Date parameter not available.' . PHP_EOL);
    }

    // create object dynamically
    $class = ($argc == 4) ? 'Utils\\' . ucwords($argv[3]) : 'Utils\\' . ucwords($argv[1]);
    if (!class_exists($class)) {
        $logger->error('Template class not found: ' . $class);
        die('Template class not found: ' . $class . PHP_EOL);
    }

    $templateObj = new $class($settings, $data, new Mail($settings, $logger), new Spreadsheet(), $logger);

    $templateObj->setTemplateHeader();
    $cnt = $templateObj->getResultFromSql();
    // if result set more than 0
    if ($cnt > 0) {
        $templateObj->setResultToCsv();
    }

    if ($result = $templateObj->createCsv()) {
        echo 'File created here ' . $result . PHP_EOL;
    } else {
        die('File could not created' . PHP_EOL);
    }

} catch (Exception $e) {
    die('Template class creation error: ' . $e->getMessage() . PHP_EOL);
}
