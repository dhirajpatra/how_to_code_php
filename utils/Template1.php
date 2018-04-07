<?php
namespace Utils;

use PhpOffice\PhpSpreadsheet\Exception;

/**
 * Class Template1
 */
class Template1 extends Template
{
    private $cellAddress;
    private $result;
    protected $spreadSheet;
    protected $db;
    protected $startDate;
    protected $endDate;

    /**
     * Template1 constructor.
     * @param $settings
     * @param $data
     * @param Mail $mail
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @param $logger
     */
    function __construct($settings, $data, $mail, $spreadsheet, $logger)
    {
        $this->startDate = $data[0];
        $this->endDate = $data[1];
        parent::__construct($settings, $data, $mail, $spreadsheet, $logger);
        $this->db = $this->dbConn();
    }

    /**
     * @return bool
     */
    public function setTemplateHeader()
    {
        try {
            // dynamic cell address
            $this->cellAddress = [
                '1' => 'A',
                '2' => 'B',
                '3' => 'C',
                '4' => 'D',
                '5' => 'E',
                '6' => 'F',
                '7' => 'G',
                '8' => 'H',
                '9' => 'I',
                '10' => 'J',
                '11' => 'K',
                '12' => 'L',
                '13' => 'M',
                '14' => 'N',
                '15' => 'O',
                '16' => 'P',
                '17' => 'Q'
            ];

            // header
            $this->spreadSheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'payment_type')
                ->setCellValue('B1', 'id')
                ->setCellValue('C1', 'sid')
                ->setCellValue('D1', 'organization_name')
                ->setCellValue('E1', 'city')
                ->setCellValue('F1', 'state')
                ->setCellValue('G1', 'zip')
                ->setCellValue('H1', 'start_date')
                ->setCellValue('I1', 'end_date')
                ->setCellValue('J1', 'course_id')
                ->setCellValue('K1', 'course_name')
                ->setCellValue('L1', 'ltp_url')
                ->setCellValue('M1', 'price')
                ->setCellValue('N1', 'created_at')
                ->setCellValue('O1', 'updated_at')
                ->setCellValue('P1', 'deleted_at')
                ->setCellValue('Q1', 'status');
        } catch (Exception $e) {
            $this->logger->error('Spreadsheet header couldnt be created' . __METHOD__);
            return false;
        }

    }

    /**
     * @return int
     */
    public function getResultFromSql() : int
    {

        try {

            $sql = 'SELECT payment_type,
                          o.id,
                          o.sid,
                          organization_name,
                          f.city,
                          state,
                          f.zip,
                          start_date,
                          end_date,
                          c.course_id,
                          c.course_name,
                          ltp_url,
                          price,
                          o.created_at,
                          o.updated_at,
                          o.deleted_at,
                          o.status 
                        FROM offerings o 
                        join carts ct 
                        join cart_items ci 
                        join facilities f
                        join courses_for_cps  c on ct.id=ci.cart_id 
                        and ci.offering_id=o.id 
                        and o.facility_sid=f.sid 
                        and o.course_sid=c.course_sid 
                        where  date(STR_TO_DATE(o.start_date,\'%m/%d/%Y\')) > ?
                        and date(o.updated_at) = ? and o.status=1 
                        and o.deleted_at is null  order by 1;';

            $sth = $this->db->prepare($sql);
            $sth->execute($this->data);
            $this->result = $sth->fetchAll();
            $cnt = count($this->result);

            if ($cnt == 0) {
                $mail = [
                    'subject' => 'SQL executed with no result - ' . $this->settings['HOST'],
                    'msg' => 'SQL executed with no result',
                    'attachment' => null
                ];
                $this->sendEmail($mail);
                $this->logger->info('SQL executed with no result' . __METHOD__);
            }

            return $cnt;

        }  catch (PDOException $e) {
            echo 'PDO error: ' . $e->getMessage();
            $this->logger->error('PDO error: ' . $e->getMessage() . __METHOD__);
        }  catch (Exception $e) {
            echo $e->getMessage();
            $this->logger->error('Sql couldnt executed' . __METHOD__);
        }

        return 0;
    }

    /**
     * @return bool
     */
    public function setResultToCsv() : bool
    {

        try {
            // need to create spread sheet rows
            $i = 2;
            foreach ($this->result as $row) {
                $j = 1;
                foreach ($row as $cell) {
                    $this->spreadSheet->setActiveSheetIndex(0)
                        ->setCellValue($this->cellAddress[$j] . $i, $cell);
                    $j++;
                }
                $i++;
            }

            return true;

        }  catch (Exception $e) {
            echo $e->getMessage();
            $this->logger->error('Result set couldnt set to csv');
            $mail = [
                'subject' => 'Result set couldnt set to csv - ' . $this->settings['HOST'],
                'msg' => 'Result set couldnt set to csv at ' . __METHOD__,
                'attachment' => null
            ];
            $this->sendEmail($mail);
        }

        return false;
    }

    /**
     * @return null|string
     */
    public function createCsv()
    {

        $filePath = null;
        try {
            // saving into parent template class $fileName
            $this->fileName = __DIR__ . '/../reports/data-' . substr(date('Y-m-d',strtotime($this->startDate)), 6, 4) . '.xlsx';
            parent::createCsv($this->fileName);

            $filePath = realpath($this->fileName);

            $mail = [
                'subject' => 'CSV File Created Successfully - ' . $this->settings['HOST'],
                'msg' => 'CSV File Created Successfully',
                'attachment' => $filePath
            ];
            $this->sendEmail($mail);

        }  catch (Exception $e) {
            echo $e->getMessage();
            $this->logger->error('Result set couldnt set to csv');
            $mail = [
                'subject' => 'Result set couldnt set to csv - ' . $this->settings['HOST'],
                'msg' => 'Result set couldnt set to csv at ' . __METHOD__,
                'attachment' => null
            ];
            $this->sendEmail($mail);
        }

        return  $filePath;
    }
}
