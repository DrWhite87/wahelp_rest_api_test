<?php

namespace App\Models;


class Database
{
    //
    protected $connection;
    // Запрос
    protected $query;
    // Показывать ли ошибки
    protected $showErrors = TRUE;
    //
    protected $queryClosed = TRUE;
    //Счетчик запросов в текущем соединение
    public $queryCount = 0;

    protected static $instance;

    /**
     * в конструкторе создаем соединение с БД
     */
    public function __construct()
    {
        $this->connection = new \mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);;
        if ($this->connection->connect_error) {
            $this->error('Ошибка подключения к MySQL - ' . $this->connection->connect_error);
        }
        $this->connection->set_charset('utf8');
    }

    /**
     * метод для создания запросов
     * @param $query
     * @return Database
     * @throws \Exception
     */
    public function query($query): Database
    {
        if (!$this->queryClosed) {
            $this->query->close();
        }
        if ($this->query = $this->connection->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_slice($x, 1);
                $types = '';
                $args_ref = array();
                foreach ($args as $k => &$arg) {
                    if (is_array($args[$k])) {
                        foreach ($args[$k] as $j => &$a) {
                            $types .= $this->_gettype($args[$k][$j]);
                            $args_ref[] = &$a;
                        }
                    } else {
                        $types .= $this->_gettype($args[$k]);
                        $args_ref[] = &$arg;
                    }
                }
                array_unshift($args_ref, $types);
                call_user_func_array(array($this->query, 'bind_param'), $args_ref);
            }
            $this->query->execute();
            if ($this->query->errno) {
                $this->error('Невозможно обработать запрос MySQL (проверьте параметры) - ' . $this->query->error);
            }
            $this->queryClosed = FALSE;
            $this->queryCount++;
        } else {
            $this->error('Невозможно подготовить оператор MySQL (проверьте синтаксис) - ' . $this->connection->error);
        }
        return $this;
    }


    /**
     * метод возвращает все результаты запроса
     * @param null $callback
     * @return array
     */
    public function fetchAll($callback = null): array
    {
        $params = array();
        $row = array();
        $meta = $this->query->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            $r = array();
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }
            if ($callback != null && is_callable($callback)) {
                $value = call_user_func($callback, $r);
                if ($value == 'break') break;
            } else {
                $result[] = $r;
            }
        }
        $this->query->close();
        $this->queryClosed = TRUE;
        return $result;
    }

    /**
     * метод возвращает все результаты запроса
     */
    public function fetchOne(): array
    {
        $params = array();
        $row = array();
        $meta = $this->query->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            foreach ($row as $key => $val) {
                $result[$key] = $val;
            }
        }
        $this->query->close();
        $this->queryClosed = TRUE;
        return $result;
    }

    /**
     * Закрываем соединение с БД
     * @return bool
     */
    public function close(): bool
    {
        return $this->connection->close();
    }

    /**
     * ID последней добавленной записи
     * @return int|string
     */
    public function lastInsertID()
    {
        return $this->connection->insert_id;
    }

    /**
     * Обработка ошибок
     * @param $error
     * @throws \Exception
     */
    public function error($error)
    {
        if ($this->showErrors) {
            throw new \Exception($error);
        }
    }

    /**
     * Типизация параметров
     * @param $var
     * @return string
     */
    private function _gettype($var): string
    {
        if (is_string($var)) return 's';
        if (is_float($var)) return 'd';
        if (is_int($var)) return 'i';
        return 'b';
    }
}