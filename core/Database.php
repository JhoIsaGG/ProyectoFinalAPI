<?php 

declare(strict_types=1);

class Database {

    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        
        if(self::$connection instanceof PDO){
            return self::$connection;
        }

        $config = require __DIR__ . '/../config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {

            self::$connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

        }catch (PDOException $exception){
            Response::json(
                [
                    'success' => false,
                    'message' => 'Error de conexión con la base de datos',
                    'error' => $exception->getMessage(),
                ],
                500
            );
            exit;
        }
        return self::$connection;
    }

}

?>