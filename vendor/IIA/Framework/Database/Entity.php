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

public function getAll(array $conditions = [], array $order = [], int $limit = 0): array
    {
        $sql = "SELECT * FROM " . $this->table;
        $params = [];
        
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    foreach ($value as $operator => $val) {
                        $paramName = ":{$column}";
                        $whereClauses[] = "{$column} {$operator} {$paramName}";
                        $params[$paramName] = $val;
                    }
                } else {
                    $paramName = ":{$column}";
                    $whereClauses[] = "{$column} = {$paramName}";
                    $params[$paramName] = $value;
                }
            }
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }
        
        if (!empty($order)) {
            $orderClauses = [];
            foreach ($order as $column => $direction) {
                $orderClauses[] = "{$column} {$direction}";
            }
            $sql .= " ORDER BY " . implode(", ", $orderClauses);
        }
        
        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->database->query($sql . $params , static::class, );
    }

        public function getOne(int $ids): array
    {
        return $this->database->query("SELECT * FROM " . $this->table . "WHERE" . $ids, static::class);
    }

    public function delete(): array
    {
        return $this->database->query("DELETE * FROM " . $this->table . " WHERE id = :id");
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
    
    public function save(): bool
    {
        $pdo = $this->database->getPDO();
        $data = get_object_vars($this);
        
        unset($data['database']);
        unset($data['table']);
        unset($data['id']);
        
        if (isset($this->id) && $this->id > 0) {
            $sets = [];
            foreach ($data as $column => $value) {
                $sets[] = $column . " = " . $pdo->quote($value);
            }
            
            $sql = "UPDATE " . $this->table . " SET " . implode(', ', $sets) . " WHERE id = " . $this->id;
            $result = $pdo->exec($sql);
            
            if ($result !== false) {
                return true;
            }
            return false;
        } else {
            $columns = [];
            $values = [];
            
            foreach ($data as $column => $value) {
                $columns[] = $column;
                $values[] = $pdo->quote($value);
            }
            
            $sql = "INSERT INTO " . $this->table . " (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
            $result = $pdo->exec($sql);
            
            if ($result !== false) {
                $this->id = (int) $pdo->lastInsertId();
                return true;
            }
            return false;
        }
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
