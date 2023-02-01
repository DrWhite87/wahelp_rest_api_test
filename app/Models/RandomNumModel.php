<?php

namespace App\Models;


class RandomNumModel extends Database
{
    /**
     * Минимальное значение числа
     */
    const MIN_VALUE = 1;

    /**
     * Макчимальное значение числа
     */
    const MAX_VALUE = 999999999;

    /**
     * Сохранение числа в БД
     *
     * @param array $data
     * @return array
     */
    public function create(): array
    {
        // сохраняем число в БД
        $insertID = $this->query(
            "INSERT INTO `random_nums` (`num`, `created_at`) VALUES (?, ?)",
            $this->generateRandomNum(),
            date('Y-m-d H:i:s')
        )
            ->lastInsertID();

        // возвращаем последний добавленный элемент
        return $this->query("SELECT * FROM `random_nums` WHERE `id` = ?", $insertID)->fetchOne();
    }

    /**
     * Получение элемента по ID
     *
     * @param int $id
     * @return array
     */
    public function getOne(int $id): array
    {
        return $this->query("SELECT * FROM `random_nums` WHERE `id` = ?", $id)->fetchOne();
    }

    /**
     * Получение списка элементов
     *
     * @param array $options
     * @return array
     */
    public function getAll(array $options): array
    {
        $queryStr = "SELECT * FROM `random_nums`";

        // если задан параметр limit, применяем к запросу
        if (!empty($options['limit']) && is_numeric($options['limit']) && $options['limit'] > 0) {
            $queryStr .= " LIMIT " . (int) $options['limit'];
        }

        // если задан параметр offset, применяем к запросу
        if (!empty($options['offset']) && is_numeric($options['offset']) && $options['offset'] >= 0) {
            $queryStr .= " OFFSET " . (int) $options['offset'];
        }

        return $this->query($queryStr)->fetchAll();
    }

    /**
     * Генерация случайного числа
     *
     * @return int
     * @throws \Exception
     */
    private function generateRandomNum(): int
    {
        try {
            return random_int(self::MIN_VALUE, self::MAX_VALUE);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}