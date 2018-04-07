<?php
namespace Utils;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Exception;


/**
 * Abstract Class Template
 * @package utils
 */
abstract class Template
{
    protected $db;
    protected $spreadSheet;
    protected $sql;
    protected $logger;
    protected $settings;
    protected $data;
    protected $fileName;
    protected $mail;

    /**
     * Template constructor.
     * @param $settings
     * @param $data
     * @param Mail $mail
     * @param Spreadsheet $spreadsheet
     * @param $logger
     */
    function __construct($settings, $data, $mail, $spreadsheet, $logger)
    {
        $this->settings = $settings;
        $this->spreadSheet = $spreadsheet;
        $this->logger = $logger;
        $this->data = $data;
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    abstract protected function setTemplateHeader();

    /**
     * @return mixed
     */
    abstract protected function getResultFromSql();

    /**
     * @return mixed
     */
    abstract protected function setResultToCsv();

    /**
     * @return mixed
     */
    protected function dbConn()
    {

        try {
            $this->db = Db::init($this->settings);
            return $this->db;

        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $mail = [
                'subject' => 'DB connection error - ' . $this->settings['HOST'],
                'msg' => 'DB connection error at ' . __METHOD__,
                'attachment' => null
            ];
            $this->sendEmail($mail);
        }

        return false;
    }

    /**
     * @param $data
     * @return int
     */
    public function sendEmail($data) : int
    {

        try {
            // Send the message
            if(!$result = $this->mail->sendMail(
                $data['subject'],
                $this->settings['MAIL']['TO'],
                $data['msg'],
                $data['attachment']
            )) {
                throw new Exception('Mail couldnt send');
            }

            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->logger->error($e->getMessage() . __METHOD__);
        }

        return 0;
    }

    /**
     * @return bool
     */
    protected function createCsv()
    {

        try {
            $writer = IOFactory::createWriter($this->spreadSheet, 'Xlsx');
            $writer->save($this->fileName);
            
            return true;

        } catch (Exception $e) {
            echo $e->getMessage();
            $this->logger->error($e->getMessage());
            $mail = [
                'subject' => 'Create csv error - ' . $this->settings['HOST'],
                'msg' => 'Create csv error at ' . __METHOD__,
                'attachment' => null
            ];
            $this->sendEmail($mail);
        }

        return false;
    }
}
