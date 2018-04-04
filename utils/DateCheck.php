<?php

namespace Utils;


class DateCheck
{

    /**
     * @param $date
     * @param string $format
     * @return bool
     * @throws \Exception
     */
    public function validateDate(string $date, string $format = 'Y-m-d')
    {
        if (!$this->checkExtremeDate($date)) {
            throw new \Exception('Invalid date, date is either < 19700101 or > current date');
        }

        $tempDate = \DateTime::createFromFormat($format, $date);
        return $tempDate && $tempDate->format($format) == $date;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return bool
     * @throws \Exception
     */
    public function compareDates(string $startDate, string $endDate)
    {
        $sd = date_create(date('Y-m-d', strtotime($startDate)));
        $ed = date_create(date('Y-m-d', strtotime($endDate)));
        $diff1 = date_diff($ed, $sd);

        $today = date_create(date('Y-m-d'));
        $diff2 = date_diff($sd, $today);

        $today = date_create(date('Y-m-d'));
        $diff3 = date_diff($sd, $today);

        if ($diff1->format("%R%a") < 0) {
            throw new \Exception('Invalid date, end/created date is bigger than start date.' . PHP_EOL);
        } elseif ($diff2->format("%R%a") < 0) {
            throw new \Exception('Invalid date, start date is bigger than current date.' . PHP_EOL);
        } elseif ($diff3->format("%R%a") < 0) {
            throw new \Exception('Invalid date, end/created date is bigger than current date.' . PHP_EOL);
        }

        return true;
    }

    /**
     * If no date provided then it will create between current date and 2 days before
     * @return array
     */
    public function forCurrentDate()
    {

        try {

            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime("-2 days", strtotime($startDate)));

            $data = [
                $startDate,
                $endDate
            ];

            return $data;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @param $date
     * @return bool
     */
    private function checkExtremeDate(string $date)
    {

        try {
            $date = date_create(date('Y-m-d', strtotime($date)));
            if ($date > date_create(date('Y-m-d')) || $date < date_create(date('Y-m-d', strtotime('1970-01-01')))) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
