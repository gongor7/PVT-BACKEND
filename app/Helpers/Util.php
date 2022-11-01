<?php

namespace App\Helpers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Affiliate\AffiliateRecord;
use App\Models\Affiliate\Affiliate;
use Auth;
use Config;

class Util
{
    public static function open_connect_database_default()
    {
            $db_host= env("DB_HOST");
            $db_connection_aux = env("DB_CONNECTION_AUX");
            $db_connection_name_dblink =env("DB_CONNECTION_DBLINK");
            $db_port = env("DB_PORT");
            $db_database = env("DB_DATABASE");
            $db_username= env("DB_USERNAME");
            $db_password = env("DB_PASSWORD");
            $open_conection= DB::connection($db_connection_aux)->select("SELECT dblink_connect('$db_connection_name_dblink','hostaddr=$db_host port=$db_port dbname=$db_database user=$db_username password=$db_password');");
            return $open_conection;
    }

    public static function close_conection_database_default()
    {
        $db_connection_aux = env("DB_CONNECTION_AUX");
        $db_connection_name_dblink = env("DB_CONNECTION_DBLINK");
        $close_conection= DB::connection($db_connection_aux)->select("SELECT dblink_disconnect('$db_connection_name_dblink');");
        return $close_conection;
    }

    public static function open_connect_database_aux()
    {
            $db_host_aux= env("DB_HOST_AUX");
            $db_connection = env("DB_CONNECTION");
            $db_connection_name_dblink_aux =env("DB_CONNECTION_DBLINK_AUX");
            $db_port_aux = env("DB_PORT_AUX");
            $db_database_aux = env("DB_DATABASE_AUX");
            $db_username_aux= env("DB_USERNAME_AUX");
            $db_password_aux = env("DB_PASSWORD_AUX");
            $open_conection_aux= DB::connection($db_connection)->select("SELECT dblink_connect('$db_connection_name_dblink_aux','hostaddr=$db_host_aux port=$db_port_aux dbname=$db_database_aux user=$db_username_aux password=$db_password_aux');");
            return $open_conection_aux;
    }

    public static function close_conection_database_aux()
    {
        $db_connection = env("DB_CONNECTION");
        $db_connection_name_dblink_aux = env("DB_CONNECTION_DBLINK_AUX");
        $close_conection_aux= DB::connection($db_connection)->select("SELECT dblink_disconnect('$db_connection_name_dblink_aux');");
        return $close_conection_aux;
    }

    public static function list_years($start_year)
    {
        $end_year =Carbon::now()->format('Y');
           $list_years =[];
           while ($start_year <= $end_year  ) {
               array_push($list_years, (int)$end_year);
               $end_year--;
           }
           return $list_years;
    }

    public static function connection_db_aux()
    {
        $dbname_input = ENV('DB_DATABASE_AUX');
        $port_input = ENV('DB_PORT_AUX');
        $host_input = ENV('DB_HOST_AUX');
        $user_input = ENV('DB_USERNAME_AUX');
        $password_input = ENV('DB_PASSWORD_AUX');

        return "dbname=$dbname_input port=$port_input host=$host_input user=$user_input password=$password_input";
    }

    public static function trim_spaces($string)
    {
        return preg_replace('/[[:blank:]]+/', ' ', $string);
    }

    public static function male_female($gender, $capìtalize = false)
    {
        if ($gender) {
            $ending = strtoupper($gender) == 'M' ? 'o' : 'a';
        } else {
            $ending = strtoupper($gender) == 'M' ? 'el' : 'la';
        }
        if ($capìtalize) $ending = strtoupper($ending);
        return $ending;
    }

    public static function get_civil_status($status, $gender = null)
    {
        $status = self::trim_spaces($status);
        switch ($status) {
            case 'S':
            case 's':
                $status = 'solter';
                break;
            case 'D':
            case 'd':
                $status = 'divorciad';
                break;
            case 'C':
            case 'c':
                $status = 'casad';
                break;
            case 'V':
            case 'v':
                $status = 'viud';
                break;
            default:
                return '';
                break;
        }
        if (is_null($gender) || is_bool($gender) || $gender == '') {
            $status .= 'o(a)';
        } else {
            switch ($gender) {
                case 'M':
                case 'm':
                case 'F':
                case 'f':
                    $status .= self::male_female($gender);
                    break;
                default:
                    return '';
                    break;
            }
        }
        return $status;
    }

    public static function full_name($object, $style = "uppsercase"){
        $name = null;
        switch($style) {
            case 'uppercase':
                $name = mb_strtoupper($object->first_name ?? '').' '.mb_strtoupper($object->second_name ?? '').' '.mb_strtoupper($object->last_name ?? '')
                .' '.mb_strtoupper($object->mothers_last_name ?? '').' '.mb_strtoupper($object->surname_husband ?? '');
                break;
            case 'lowercase':
                $name = mb_strtolower($object->first_name ?? '').' '.mb_strtolower($object->second_name ?? '').' '.mb_strtolower($object->last_name ?? '')
                .' '.mb_strtolower($object->mothers_last_name ?? '').' '.mb_strtolower($object->surname_husband ?? '');
                break;
            case 'capitalize':
                $name = ucfirst(mb_strtolower($object->first_name ?? '')).' '.ucfirst(mb_strtolower($object->second_name ?? '')).' '.ucfirst(mb_strtolower($object->last_name))
                .' '.ucfirst(mb_strtolower($object->mothers_last_name)).' '.ucfirst(mb_strtolower($object->surname_husband ?? ''));
                break;
        }
        // $name = self::removeSpaces($name);
        return $name;
    }
    public static function money_format($value, $literal = false)
    {
        if ($literal) {
            $f = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
            $data = $f->format(intval($value)) . ' ' . explode('.', number_format(round($value, 2), 2))[1] . '/100';
            $mil = explode(" ",$data);
            $mil = $mil[0] == "mil" ? 'un ':"";
            $data =   $mil.$data;
        } else {
            $data = number_format($value, 2, ',', '.');
        }
        return $data;
    }
    public static function round2($value)
    {
        return round($value, 2, PHP_ROUND_HALF_EVEN);
    }
    public static function save_record_affiliate($object,$action)
    {
        if(!empty($action) && $action != 'modificó'){
            $old = Affiliate::find($object->id);
            $message = 'El usuario '.Auth::user()->username.' ';
            $affiliate_record = new AffiliateRecord;
            $affiliate_record->user_id = Auth::user()->id;
            $affiliate_record->affiliate_id = $object->id;
            $affiliate_record->message = $message.$action;
            $affiliate_record->save();
        }
    }
    public static function concat_action($object, $message = 'modificó')//aqui
    {
        $old = app(get_class($object));
        $old->fill($object->getOriginal());
        $action = $message;
        $updated_values = $object->getDirty();
        try {
            $relationships = $object->relationships();
        } catch (\Exception $e) {
            $relationships = [];
        }
        foreach ($updated_values as $key => $value) {
            $concat = false;
            if (substr($key, -3, 3) != '_id' && substr($key, -3, 3) != '_at') {
                $action .= ' [' . self::translate($key) . '] ';
                if (!$concat) {
                    $action .= self::bool_to_string($old[$key]) . ' a ' . self::bool_to_string($object[$key]);
                }
                if (next($updated_values)) {
                    $action .= ', ';
                }
            }
        }
        return $action;
    }
    public static function translate($string)
    {
        $translation = static::translate_table($string);
        if ($translation) {
            return $translation;
        } else {
            return static::translate_attribute($string);
        }
    }

    public static function translate_table($string)
    {
        if (array_key_exists($string, Config::get('translations'))) {
            return Config::get('translations')[$string];
        } else {
            return null;
        }
    }

    public static function translate_attribute($string)
    {
        $path = app_path() . '/resources/lang/es/validation.php';
        if(@include $path) {
            $translations_file = include(app_path().'/resources/lang/es/validation.php');
        }
        if (isset($translations_file)) {
            if (array_key_exists($string, $translations_file['attributes'])) {
                return $translations_file['attributes'][$string];
            }
        }
        return $string;
    }
    public static function bool_to_string($value)
    {
        if (is_bool($value)) {
            if ($value) {
                $value = 'SI';
            } else {
                $value = 'NO';
            }
        } else {
            try {
                $value = Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y');
            } catch (\Exception $e) {}
        }
        return $value;
    }
}
