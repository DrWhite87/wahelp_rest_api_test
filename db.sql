--
-- База данных: `wahelp_rest_api`
--
CREATE
DATABASE IF NOT EXISTS `wahelp_rest_api` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE
`wahelp_rest_api`;

-- --------------------------------------------------------

--
-- Структура таблицы `random_nums`
--

CREATE TABLE `random_nums`
(
    `id`         int UNSIGNED NOT NULL,
    `num`        int       NOT NULL,
    `created_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;