<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

//error_reporting(15); // 15

$res = array();//'OK';
$succes_save = TRUE;

//подключаем класс GA API
include("gapi.class.php");

//////////////////////////////////////
////preliminary config

$goo_options = mso_get_option('googlitics', 'plugins', array());

//учетная запись GA
$goo_user = "email@gmail.com";
$goo_pass = "password";
$goo_id = 12345678;

$goo_filekey = isset($goo_options['secretkey']) ? $goo_options['secretkey'] : '';
$goo_loaddate_file = 'goodata_last_load';

if ($goo_post = mso_check_post(array('force')))
{
    mso_checkreferer();
    if ( ! mso_checksession($goo_post['sess']))
    {
        echo "Попытка взлома!";
        exit;
    }
} else {
    echo "Попытка взлома!";
    exit;
}

//дата, начиная с которой необходимо получить данные из GA для отчета. Формат YYYY-MM-DD
if ( ! isset($goo_options['datefrom']))
{
    //$res->msg = 'Не указана дата начала отсчета';
    echo 'Не указана дата начала отсчета';
    exit;
}
$goo_datestart = $goo_options['datefrom'];

//дата, заканчивая которой
//$datefinish="";
//или вычисляем дату - конец предыдущего месяца
$goo_currentday = date("d");
$goo_currentmonth = date("m");
$goo_currentyear = date("Y");

$goo_currenttime = date("H:i");
$res[] = "Сегодня ".$goo_currentday."-".$goo_currentmonth."-".$goo_currentyear.",
     сейчас ".$goo_currenttime;

if ( ! isset($goo_post['force']) OR intval($goo_post['force']) == 0) // compare times
{
    $goo_lastmk = googlitics_load_file($goo_loaddate_file);
    if ($goo_lastmk === FALSE)
    {
        $res[] = 'Не удалось узнать время последней загрузки статистики, загружаем данные.';

    } else {
        $goo_lastdate = getdate(trim($goo_lastmk));
        
        $res[] = 'В последний раз статистка загружалась '.$goo_lastdate['mday'].'-'.
            $goo_lastdate['mon'].'-'.$goo_lastdate['year'].' в '.$goo_lastdate['hours'].':'.$goo_lastdate['minutes'];

        if (mktime() - $goo_lastmk < 86300 
            AND $goo_lastdate['year'] == $goo_currentyear 
                AND $goo_lastdate['mon'] == $goo_currentmonth
                    AND $goo_lastdate['mday'] == $goo_currentday) // some timegap
        {
            $res[] = 'С момента последней загрузки статистики прошло менее суток,
                поэтому автоматическое обновление не делалась. Если нужно, загрузите вручную.';
            echo implode('<br>',$res);
            exit;
        }
        
    }
}

$goo_datefinish = date("Y-m-d",mktime(0,0,0,$goo_currentmonth,0,$goo_currentyear));

//дата 3 месяца назад
$goo_date3MonthStart = date("Y-m-d",mktime(0,0,0,$goo_currentmonth-3,$goo_currentday-2,$goo_currentyear));
$goo_date3MonthFinish = date("Y-m-d",mktime(0,0,0,$goo_currentmonth,$goo_currentday-2,$goo_currentyear));

//дата месяц назад
$goo_date1MonthStart = date("Y-m-d",mktime(0,0,0,$goo_currentmonth-1,$goo_currentday-1,$goo_currentyear));
$goo_date1MonthFinish = date("Y-m-d",mktime(0,0,0,$goo_currentmonth,$goo_currentday-1,$goo_currentyear));

//количество стран
$goo_countryRows = $goo_options['numcountries'];


//csv-файл для отчета Посетители
$goo_visitorsCSV = "visitors".$goo_filekey.".csv";
//csv-файл для отчета Посетители за посл. 3 месяца
$goo_visitors3CSV = "visitors_3".$goo_filekey.".csv";
//csv-файл для отчета География по странам
$goo_countryCSV = "country".$goo_filekey.".csv";
//файл со статистикой до начала использования GA. Формат: дата;посетители;просмотры
//$addFile="default.csv";
$goo_addFile = FALSE;

/////////////////////////////////////

try
{
    
    $ga = new gapi($goo_user,$goo_pass);

    //////получаем пользователи/просмотры за все время
    $ga->requestReportData($goo_id,array('month','year'),array('visitors','pageviews'),'year',null,$goo_datestart, $goo_datefinish,1,1000);

    //переменная для записи резалта
    $goo_output = "";
    if ($goo_addFile)
    {
        $goo_add = file_get_contents(getinfo('uploads_dir').$addFile);
        $goo_output .= trim($goo_add)."\n";

    }

    //получаем и обрабатываем результаты
    foreach($ga->getResults() as $result)
    {
        $_m = $result; //месяц год
        $_visitors = $result->getVisitors(); //посетители
        $_pageviews = $result->getPageviews(); //просмотры

        //приводим дату к удобочитаемому виду ,мменяем пробелы на точки
        $_m = str_replace(" ",".",$_m);

        //формируем строку
        $goo_output .= $_m.";".$_visitors.";".$_pageviews."\n";
    }

    //пишем в файл
    $tmp = googlitics_save_data($goo_visitorsCSV, $goo_output);
    if ($tmp != 'OK')
    {
        $succes_save = FALSE;
    }
    $res[] = $tmp;

    //////получаем пользователи/просмотры/посещения за последние 3 месяца
    $ga->requestReportData($goo_id,array('day','month','year'),array('visitors','visits','pageviews'),'month',null,$goo_date3MonthStart, $goo_date3MonthFinish,1,1000);

    //переменная для записи резалта
    $goo_output="";

    //получаем и обрабатываем результаты
    foreach($ga->getResults() as $result)
    {
        $_d=$result; //день
        $_visitors=$result->getVisitors(); //посетители
        $_pageviews=$result->getPageviews(); //просмотры
        $_visits=$result->getVisits(); //посещения

        //приводим дату к удобочитаемому виду ,мменяем пробелы на точки
        $_d=str_replace(" ",".",$_d);

        //формируем строку
        $goo_output.=$_d.";".$_visitors.";".$_pageviews.";".$_visits."\n";
    }

    //пишем в файл
    $tmp = googlitics_save_data($goo_visitors3CSV, $goo_output);
    if ($tmp != 'OK')
    {
        $succes_save = FALSE;
    }
    $res[] = $tmp;

    //////получаем географию посещений за последний месяц
    $ga->requestReportData($goo_id,array('country'),array('visits'),'-visits',null,$goo_date1MonthStart, $goo_date1MonthFinish,1,$goo_countryRows);

    //переменная для записи резалта
    $goo_output="";

    //получаем общее число посещений для всех стран
    $goo_total_visits=$ga->getVisits();

    //получаем и обрабатываем результаты
    foreach($ga->getResults() as $result)
    {
        $_country=$result->getCountry(); //страна
        $_visits=$result->getVisits(); //кол-во посещений

        //нот сет переводим на русский
        $_country=str_replace("(not set)","не определено",$_country);

        //формируем строку
        $goo_output.=$_country.";".$_visits."\n";
    }

    $tmp = googlitics_save_data($goo_countryCSV, $goo_output);
    if ($tmp != 'OK')
    {
        $succes_save = FALSE;
    }
    $res[] = $tmp;
    
} catch (Exception $e) {
    $res[] = $e->getMessage();
    $succes_save = FALSE;
}

//пишем в файл
if ($succes_save)
{
    $goo_output = mktime();
    $res[] = googlitics_save_data($goo_loaddate_file,$goo_output);
    $res[] = 'Обновление статистических данных прошло успешно';
} else {
    $res[] = 'Произошла ошибка, загружены последние доступные данные';
}

echo implode('<br>',$res);

/**
 * save loaded data as CSV in cache dir
 * @param   string  $filename   name of file to write
 * @param   string  $data       data to write
 *
 * @return  string     OK or error message
 */
function googlitics_save_data ($filename, $data)
{
    //global $MSO;

    //$CI = & get_instance();

    $save_path = getinfo('uploads_dir');

    if ( ! is_dir($save_path))
    {

        return "Не могу найти каталог для сохранения ".$save_path;
    }

    if ( ! is_writable($save_path))
    {

        return "Каталог для сохранения недоступен для записи";
    }

    if ( ! $fp = @fopen($save_path.$filename, 'wb'))
    {

        return "Не удалось открыть для записи файл ".$filename;
    }

    //if ( ! @flock($fp, LOCK_EX))
//    {
//        return 'Не удалось заблокировать файл для записи '.$filename;
//    }

    if (@fwrite($fp, trim($data)) === FALSE)
    {
        return 'Не удалось записать в файл '.$filename;
    }

    //if ( ! @flock($fp, LOCK_UN))
//    {
//        return 'Не удалось разблокировать файл '.$filename;
//    }

    if ( ! @fclose($fp))
    {
        return 'Не удалось закрыть файл '.$filename;
    }

    //if ( ! @chmod($save_path.$filename, 0777))
//    {
//        return 'Не сменить права доступа на файл '.$filename;
//    }

    return 'OK';
}

function googlitics_load_file ($name)
{
    //global $MSO;

    //$CI = & get_instance();

    $load_path = getinfo('uploads_dir');

    if ( ! is_dir($load_path))
    {
        return FALSE;
//        return "Не могу найти каталог ".$load_path;
    }

    $filename = $load_path.$name;

    if ( ! file_exists($filename) OR ! is_file($filename))
    {
        return FALSE;
//        return "Файл ".$filename." не существует";
    }

    if ( ! is_readable($filename))
    {
        return FALSE;
//        return "Файл ".$filename." не доступен для чтения";
    }

    $data = @file_get_contents($filename);

    if ($data === FALSE)
    {
        return FALSE;
//        return "Не удалось открыть файл ".$filename;
    }

    return $data;
}