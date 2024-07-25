<?php

namespace api\modules\v1\helpers;

use Yii;
use yii\base\Component;

class Timeanddate extends Component
{

    public static function startedRegisteredYear()
    {
        //Untuk mencatat tahun mana saja yang masuk ke dalam sistem
        return 2013; //Pencatatan sistem dimulai dari tahun 2013
    }

    public static function getListRegisteredYear()
    {
        $all[0] = "SEMUA";
        $listdatatahun = Timeanddate::getListyear2(Timeanddate::startedRegisteredYear(), 1);
        return $listdatatahun;
        //return array_merge($all,$listdatatahun);
    }

    public static function getCurrentDateTime()
    {
        return date("Y-m-d H:i:s");
    }

    public static function getCurrentDateTimeSerial()
    {
        return date("Ymd_His");
    }

    public static function getCurrentTime()
    {
        return date("H:i:s");
    }

    public static function getCurrentDate()
    {
        return date("Y-m-d");
    }

    public static function getCurrentYear()
    {
        return date("Y") * 1;
    }

    public static function getNextYear()
    {
        return (date("Y") * 1) + 1;
    }

    public static function getCurrentMonth()
    {
        return date("m") * 1;
    }

    public static function getCurrentDay()
    {
        return date("d") * 1;
    }

    public static function getDayDigit($datestring)
    {
        $dw = date('w', strtotime($datestring));

        return $dw;
    }

    public static function getDisplayStandardDate($date)
    {

        if ($date == '') {
            return "[Tanggal Belum diset]";
        } else {
            if ($date != '0000-00-00') {
                $dataDate = explode("-", $date);
                if ($dataDate[0] >= 2038) {
                    return $date;
                } else {
                    //return Yii::app()->dateFormatter->formatDateTime($date, "medium", "");
					return Yii::$app->formatter->format($date, 'date'); 
                }
            } else {
                return "[Tanggal Belum diset]";
            }
        }
    }

    public static function getDisplayReportDate($date)
    {
        return Yii::app()->dateFormatter->format("d MMMM y", strtotime($date));
    }

    public static function getDisplayStandardDatetime($date)
    {
        return Yii::app()->dateFormatter->formatDateTime(strtotime($date), "medium");
    }

    public static function getMonthIndo($month)
    {
        switch ($month) {
            case 1 :
                return "Januari";
            case 2 :
                return "Februari";
            case 3 :
                return "Maret";
            case 4 :
                return "April";
            case 5 :
                return "Mei";
            case 6 :
                return "Juni";
            case 7 :
                return "Juli";
            case 8 :
                return "Agustus";
            case 9 :
                return "September";
            case 10 :
                return "Oktober";
            case 11 :
                return "November";
            case 12 :
                return "Desember";
        }
    }

    public static function getShortMonthIndo($month)
    {
        $month = $month * 1;
        switch ($month) {
            case 1 :
                return "Jan";
            case 2 :
                return "Feb";
            case 3 :
                return "Mar";
            case 4 :
                return "Apr";
            case 5 :
                return "Mei";
            case 6 :
                return "Juni";
            case 7 :
                return "Jul";
            case 8 :
                return "Ags";
            case 9 :
                return "Sep";
            case 10 :
                return "Okt";
            case 11 :
                return "Nov";
            case 12 :
                return "Des";
        }
    }

    public static function getDateIndo($date)
    {
        //$date must in format Y-m-d;
        $item = explode('-', $date);
        if (count($item) == 3) {
            return $item[2] . ' ' . Timeanddate::getMonthIndo($item[1]) . ' ' . $item[0];
        }
    }

    public static function getShortDateIndo($date)
    {
        //$date must in format Y-m-d;
        $item = explode('-', $date);
        if (count($item) == 3) {
            if ($item[0] == "0000")
                return "-";
            else
                return $item[2] . ' ' . Timeanddate::getShortMonthIndo($item[1]) . ' ' . $item[0];
        } else {
            return "-";
        }
    }

    public static function getShortDateIndoFromTime($datetime)
    {
        $item = explode(' ', $datetime);
        if (count($item) == 2) {
            return Timeanddate::getShortDateIndo($item[0]);
        } else {
            return "-";
        }
    }

    public static function getDateTimeIndo($datetime)
    {
        $item = explode(' ', $datetime);
        if (count($item) == 2) {
            return Timeanddate::getShortDateIndo($item[0]) . " - " . $item[1];
        } else {
            return "-";
        }
    }

    public static function getDateOnly($date)
    {
        //$date must in format YYYY-mm-dd;
        $item = explode('-', $date);
        if (count($item) == 3) {
            return $item[2] * 1;
        } else {
            return 0;
        }
    }

    public static function getMonthOnly($date)
    {
        //$date must in format YYYY-mm-dd;
        $item = explode('-', $date);
        if (count($item) >= 3) {
            return $item[1] * 1;
        } else {
            return 0;
        }
    }

    public static function getYearOnly($date)
    {
        //$date must in format YYYY-mm-dd;
        $item = explode('-', $date);
        if (count($item) >= 3) {
            return $item[0] * 1;
        } else {
            return 0;
        }
    }

    public static function getMonthOnlyString($date)
    {
        //$date must in format YYYY-mm-dd;
        $item = explode('-', $date);
        if (count($item) == 3) {
            return $item[1];
        } else {
            return 0;
        }
    }

    public static function getYearOnlyString($date)
    {
        //$date must in format YYYY-mm-dd;
        $item = explode('-', $date);
        if (count($item) == 3) {
            return $item[0];
        } else {
            return 0;
        }
    }

    public static function getlistmonthIndo()
    {
        $listdatabulan = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );

        return $listdatabulan;
    }

    public static function getlistmonth()
    {
        $listdatabulan = array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        );

        return $listdatabulan;
    }

    public static function getFullMonthEng($month)
    {
        switch ($month) {
            case 1 :
                return "January";
            case 2 :
                return "February";
            case 3 :
                return "March";
            case 4 :
                return "April";
            case 5 :
                return "May";
            case 6 :
                return "June";
            case 7 :
                return "July";
            case 8 :
                return "August";
            case 9 :
                return "September";
            case 10 :
                return "October";
            case 11 :
                return "November";
            case 12 :
                return "December";
        }
    }

    public static function getlistmonthforsettypetugbarge()
    {
        $listdatabulan = array(
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        );

        return $listdatabulan;
    }

    public static function getlistAllDate()
    {
        $listDate = array();
        for ($i = 1; $i <= 31; $i++) {
            $date = sprintf("%02s", $i);
            $listDate[$date] = $date;
        }

        return $listDate;
    }


    public static function getlistyear($startYear = 2012, $penambahan_akhir_tahun = 1)
    {
        /*
        $listdatatahun=array(
            '2013' =>'2013',
            '2014' =>'2014',
            '2015' =>'2015' ,
        );
        */
        //$startYear=2012;
        //$endYear=2015;
        //$rangeYear=($endYear-$startYear);


        $endYear = date("Y");

        $rangeYear = ($endYear - $startYear) + $penambahan_akhir_tahun;
        $listdatatahun = array();

        for ($i = 0; $i <= $rangeYear; $i++) {
            $listdatatahun[$startYear] = $startYear;
            $startYear++;
        }


        return $listdatatahun;    // tahun 2012-2015
    }

    public static function getListyear2($startYear = 2012, $penambahan_akhir_tahun = 1)
    {
        $endYear = date("Y");
        $endYear = $endYear * 1;
        $listdatatahun = array();
        $listdatatahun[""] = "SEMUA";
        for ($i = $startYear; $i <= $endYear; $i++) {
            $listdatatahun[$i] = $i;
        }

        return $listdatatahun;
    }

    public static function getlistyearFuture()
    {
        $curyear = date('Y') * 1;
        for ($i = $curyear; $i <= $curyear + 2; $i++) {
            $listdatatahun[$i] = $i;
        }

        return $listdatatahun;
    }

    public static function getMaximumDateEachMonth($month, $year)
    {
        if ($year % 4 == 0) {
            if ($month == 2) {
                return 29;
            }
        }

        switch ($month) {
            case 1 :
                return 31;
            case 2 :
                return 28;
            case 3 :
                return 31;
            case 4 :
                return 30;
            case 5 :
                return 31;
            case 6 :
                return 30;
            case 7 :
                return 31;
            case 8 :
                return 31;
            case 9 :
                return 30;
            case 10 :
                return 31;
            case 11 :
                return 30;
            case 12 :
                return 31;
        }
    }

    public static function getMySqlDate($date, $month, $year)
    {
        $strdate = "";
        $strmonth = "";
        if (strlen($date) == 1) {
            $strdate = "0" . $date;
        } else {
            $strdate = $date;
        }


        if (strlen($month) == 1) {
            $strmonth = "0" . $month;
        } else {
            $strmonth = $month;
        }
        return $year . "-" . $strmonth . "-" . $strdate;
    }

    public static function adddate($vardate, $added)
    {
        $data = explode("-", $vardate);
        $date = new DateTime();
        $date->setDate($data[0], $data[1], $data[2]);
        $date->modify("" . $added . "");
        $day = $date->format("Y-m-d");
        return $day;
        // contoh penggunaan
        /*
        $day ="+1 day";
        $month ="+1 month";
        $year ="+1 year";
        echo $date=adddate("2015-11-17",$year);
        */
    }

    public static function countRangeDate($startdate, $endate)
    {
        $date1 = $startdate;
        $date2 = $endate;
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $difference = $datetime1->diff($datetime2);
        return $difference->days;
    }

    public static function getStartAndEndFromMonth($month, $year)
    {
        $startdate = strtotime($year . "-" . $month . "-01");
        $enddate = strtotime('-1 second', strtotime('+1 month', $startdate));

        $start = date('Y-m-d', $startdate);
        $end = date('Y-m-d', $enddate);
        $result = array();
        $result['start'] = $start;
        $result['end'] = $end;

        return $result;
    }

    public static function getMaxDayFromDate($date)
    {
        $month = date("m", strtotime($date));
        $year = date("Y", strtotime($date));
        $max = Timeanddate::getMaximumDateEachMonth($month, $year);

        return $max;
    }

    public static function getlistlongFuture()
    {
        $curyear = date('Y') * 1;
        $countYearFuture = 60;
        //$listdatatahun['']='--SEMUA--';
        for ($i = $curyear; $i <= $curyear + $countYearFuture; $i++) {
            $listdatatahun[$i] = $i;
        }

        return $listdatatahun;
    }

    public static function getGMTPlus7Time($_timestamp){
        $date = new \DateTime( $_timestamp );
        $date->modify('+7 hour');
        $timestamp = $date->format("Y-m-d H:i:s");
        return $timestamp;
    }

    public static function getGMTPlus7Date($_timestamp){
        $date = new \DateTime( $_timestamp );
        $date->modify('+7 hour');
        $timestamp = $date->format("Y-m-d");
        return $timestamp;
    }

}
