<?php
namespace Utils;

/**
 * singleton class
 * Class Db
 */
class Db
{
    private static $db;

    /**
     * @return PDO
     */
    public static function init($settings)
    {
        if (!self::$db)
        {
            try {
                $dsn = 'mysql:host='.$settings['DB_HOST'].';dbname='.$settings['DB_NAME'].';';
                self::$db = new \PDO($dsn, $settings['DB_USER'], $settings['DB_PASS']);
                self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                die('Connection error: ' . $e->getMessage() . __CLASS__ . __METHOD__);
            }
        }
        return self::$db;
    }
}
