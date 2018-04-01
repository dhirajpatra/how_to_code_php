<?php

namespace utils;

/**
 * Class Helper
 * @package utils
 */
class Helper
{
    public $logger;
    private $settings;
    private $dateCheck;

    /**
     * Helper constructor.
     * @param $settings
     * @param $logger
     */
    function __construct($settings, $logger, $dateCheck)
    {
        $this->settings = $settings;
        $this->logger = $logger;
        $this->dateCheck = $dateCheck;
    }

    /**
     * validate
     * @param $argc
     * @param $argv
     * @return array
     */
    public function getInputs($argc, $argv)
    {

        $data = [];
        try {
            if (defined('STDIN') && $argc > 3) {
                if (strlen($argv[1]) != 8) {
                    throw new \Exception('Start date invalid');
                } else {
                    $startDate = substr($argv[1], 0, 4).'-'.substr($argv[1], 4, 2).'-'.substr($argv[1], 6, 2);
                    if (!$this->dateCheck->validateDate($startDate)) {
                        throw new \Exception('Start date invalid' . PHP_EOL);
                        $this->logger->error('Start date invalid' . __METHOD__);
                    }
                }

                if (strlen($argv[2]) != 8) {
                    throw new \Exception('End date invalid');
                } else {
                    $endDate = substr($argv[2], 0, 4).'-'.substr($argv[2], 4, 2).'-'.substr($argv[2], 6, 2);
                    if (!$this->dateCheck->validateDate($endDate)) {
                        throw new \Exception('End date invalid' . PHP_EOL);
                        $this->logger->error('End date invalid' . __METHOD__);
                    }
                }

                try {

                    if ($this->dateCheck->compareDates($startDate, $endDate) === true) {
                        $data = [
                            $startDate,
                            $endDate
                        ];
                    }

                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage() . __METHOD__);
                    die($e->getMessage());
                }

            } elseif (defined('STDIN') && $argc == 2) { // current date
                $data = $this->dateCheck->forCurrentDate();

            } else {
                throw new \Exception('Input all parameters required' . PHP_EOL);
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage() . __METHOD__);
            die($e->getMessage());
        }

        return $data;
    }
}