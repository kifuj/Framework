<?php

namespace IIA\Framework\Database;

class Entity
{
    protected Database $database;
    protected string $table;
    protected int $id;
    protected static $tableName = null;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAll( string $condition = none , string $order = none , int $lim = none): array
    {
        return $this->database->query("SELECT * FROM " . $this->table . "WHERE" . $condition . "ORDER BY" . $order . "LIMIT" . $lim , static::class);
    }

        public function getOne(int $ids): array
    {
        return $this->database->query("SELECT * FROM " . $this->table . "WHERE" . $ids, static::class);
    }
    public function __get(string $key)
    {
        $method = 'get' . ucfirst($key);
        return $this->$method();
    }

    public static function getTableName(): string
    {
        if (static::$tableName !== null) {
            return static::$tableName;
        }
        
        $className = (new \ReflectionClass(static::class))->getShortName();
        
        return self::toSnakeCase($className);
    }
    

    public static function setTableName(string $name): void
    {
        static::$tableName = $name;
    }

    protected static function toSnakeCase(string $string): string
    {
        $snake = preg_replace('/(?<!^)[A-Z]/', '_$0', $string);
        
        return strtolower($snake);
    }
}
