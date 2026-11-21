<?php
class Database
{
    public static function conectar(array $cfg): PDO
    {
        return new PDO($cfg['dsn'], $cfg['usuario'], $cfg['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}