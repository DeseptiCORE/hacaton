-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 24 2026 г., 21:57
-- Версия сервера: 8.0.24
-- Версия PHP: 8.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `baza`
--

-- --------------------------------------------------------

--
-- Структура таблицы `difficulty`
--

CREATE TABLE `difficulty` (
  `difficulty_id` int NOT NULL,
  `difficulty_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `difficulty_coefficient` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `difficulty`
--

INSERT INTO `difficulty` (`difficulty_id`, `difficulty_name`, `difficulty_coefficient`) VALUES
(1, 'Лёгкий', 1),
(2, 'Средний', 1.5),
(3, 'Сложный', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `event`
--

CREATE TABLE `event` (
  `event_id` int NOT NULL,
  `promoter_id` int NOT NULL,
  `event_category_id` int NOT NULL,
  `difficulty_id` int NOT NULL,
  `status_id` int NOT NULL,
  `event_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `event_descr` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `event_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_format` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `base_score` int NOT NULL,
  `firstp_score` int NOT NULL,
  `secondp_score` int NOT NULL,
  `thirdp_score` int NOT NULL,
  `event_bonus` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `event`
--

INSERT INTO `event` (`event_id`, `promoter_id`, `event_category_id`, `difficulty_id`, `status_id`, `event_name`, `event_descr`, `event_date`, `event_format`, `base_score`, `firstp_score`, `secondp_score`, `thirdp_score`, `event_bonus`) VALUES
(1, 3, 1, 3, 4, 'Хакатон', 'Хакатон по программированию на котором участникам предстоит решить увлекательные кейсы!', '2026-03-25 04:00:00', 'online', 20, 50, 40, 30, 'Сертификаты'),
(2, 3, 1, 1, 2, 'Интенсив по созданию Веб приложений', 'Веб разработка с нуля', '2026-03-31 11:50:00', 'offline', 10, 0, 0, 0, 'Новые знания'),
(3, 3, 3, 1, 2, 'День открытых дверей', 'День открытых дверей в Аэрокосмическом колледже', '2026-03-31 11:50:00', 'offline', 10, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `event_category`
--

CREATE TABLE `event_category` (
  `event_category_id` int NOT NULL,
  `event_category_name` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `event_category_descr` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `event_category`
--

INSERT INTO `event_category` (`event_category_id`, `event_category_name`, `event_category_descr`) VALUES
(1, 'IT', '-'),
(2, 'Социальное проектирование', '-'),
(3, 'Медия', '-'),
(4, 'Общее', 'Общая категория для подсчёта баллов');

-- --------------------------------------------------------

--
-- Структура таблицы `participation`
--

CREATE TABLE `participation` (
  `participation_id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_id` int NOT NULL,
  `attendance` tinyint(1) NOT NULL,
  `place` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `participation`
--

INSERT INTO `participation` (`participation_id`, `user_id`, `event_id`, `attendance`, `place`) VALUES
(1, 3, 1, 1, '1'),
(2, 5, 1, 1, '2');

-- --------------------------------------------------------

--
-- Структура таблицы `promoter`
--

CREATE TABLE `promoter` (
  `promoter_id` int NOT NULL,
  `register_id` int NOT NULL,
  `promoter_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `promoter_city` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `promoter_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `promoter_prize` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `promoter_eventcount` int NOT NULL,
  `promoter_rating` decimal(3,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `promoter`
--

INSERT INTO `promoter` (`promoter_id`, `register_id`, `promoter_name`, `promoter_city`, `promoter_type`, `promoter_prize`, `promoter_eventcount`, `promoter_rating`) VALUES
(3, 38, 'Аэрокосмический Колледж', 'Красноярск', '2', '-', 0, '5.0');

-- --------------------------------------------------------

--
-- Структура таблицы `rating`
--

CREATE TABLE `rating` (
  `rating_id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_category_id` int NOT NULL,
  `scores` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `rating`
--

INSERT INTO `rating` (`rating_id`, `user_id`, `event_category_id`, `scores`) VALUES
(1, 1, 1, 0),
(2, 1, 2, 0),
(3, 1, 3, 0),
(4, 1, 4, 0),
(5, 2, 1, 0),
(6, 2, 2, 0),
(7, 2, 3, 0),
(8, 2, 4, 0),
(9, 3, 1, 140),
(10, 3, 2, 0),
(11, 3, 3, 0),
(12, 3, 4, 140),
(13, 4, 1, 0),
(14, 4, 2, 0),
(15, 4, 3, 0),
(16, 4, 4, 0),
(17, 5, 1, 120),
(18, 5, 2, 0),
(19, 5, 3, 0),
(20, 5, 4, 120),
(21, 6, 1, 0),
(22, 6, 2, 0),
(23, 6, 3, 0),
(24, 6, 4, 0),
(25, 7, 1, 0),
(26, 7, 2, 0),
(27, 7, 3, 0),
(28, 7, 4, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `rating_change`
--

CREATE TABLE `rating_change` (
  `rating_change_id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_category_id` int NOT NULL,
  `scores_change` int NOT NULL,
  `change_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `rating_change`
--

INSERT INTO `rating_change` (`rating_change_id`, `user_id`, `event_category_id`, `scores_change`, `change_date`) VALUES
(5, 3, 1, 140, '2026-03-24 21:50:41'),
(6, 3, 4, 140, '2026-03-24 21:50:41'),
(7, 5, 1, 120, '2026-03-24 21:50:41'),
(8, 5, 4, 120, '2026-03-24 21:50:41');

-- --------------------------------------------------------

--
-- Структура таблицы `register`
--

CREATE TABLE `register` (
  `register_id` int NOT NULL,
  `register_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `register_pass` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `register_role` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `register`
--

INSERT INTO `register` (`register_id`, `register_email`, `register_pass`, `register_role`) VALUES
(31, 'ivan.petrov@mail.ru', 'password123 ', '1'),
(32, 'anna.smirnova@mail.ru', 'password123', '1'),
(33, 'Ivan@gmail.com', '1234567', '1'),
(34, 'alexey.volkov@mail.ru', 'password123 ', '1'),
(35, 'denchik228@gmail.com', 'megaden', '1'),
(36, 'maria.popova@mail.ru', '1270307', '1'),
(37, 'andrey.fedorov@mail.ru', 'password123 ', '1'),
(38, 'aerokos@mail.ru', 'aerokosrulit', '2');

-- --------------------------------------------------------

--
-- Структура таблицы `review`
--

CREATE TABLE `review` (
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_id` int NOT NULL,
  `review_text` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `review_score` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `review`
--

INSERT INTO `review` (`review_id`, `user_id`, `event_id`, `review_text`, `review_score`) VALUES
(1, 3, 1, 'Суперский хакатон!', 5);

-- --------------------------------------------------------

--
-- Структура таблицы `status`
--

CREATE TABLE `status` (
  `status_id` int NOT NULL,
  `status_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `status`
--

INSERT INTO `status` (`status_id`, `status_name`) VALUES
(1, 'На модерации'),
(2, 'Приём заявок'),
(3, 'Идёт'),
(4, 'Завершено');

-- --------------------------------------------------------

--
-- Структура таблицы `talent_coordinator`
--

CREATE TABLE `talent_coordinator` (
  `talent_coordinator_id` int NOT NULL,
  `register_id` int NOT NULL,
  `talent_coordinator_lastname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `talent_coordinator_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `talent_coordinator_midname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `talent_coordinator_org` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `talent_coordinator_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `register_id` int NOT NULL,
  `user_lastname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_midname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_bdate` date NOT NULL,
  `user_city` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`user_id`, `register_id`, `user_lastname`, `user_name`, `user_midname`, `user_bdate`, `user_city`) VALUES
(1, 31, 'Петров ', 'Иван', 'Алексеевич ', '2007-06-13', 'Красноярск'),
(2, 32, 'Смирнова ', 'Анна ', 'Дмитриевна ', '2005-05-11', 'Москва'),
(3, 33, 'Яцко', 'Иван', 'Алексеевич', '2002-06-10', 'Красноярск'),
(4, 34, 'Волков ', 'Алексей ', 'Сергеевич ', '2008-06-10', 'Казань'),
(5, 35, 'Карлин', 'Денис', 'Денисович', '2005-05-24', 'Красноярск'),
(6, 36, 'Попова ', 'Мария ', 'Сергеевна ', '2005-10-04', 'Челябинск'),
(7, 37, 'Федоров ', 'Андрей ', 'Павлович ', '2003-09-25', 'Красноярск');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `difficulty`
--
ALTER TABLE `difficulty`
  ADD PRIMARY KEY (`difficulty_id`);

--
-- Индексы таблицы `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `promoter_id` (`promoter_id`),
  ADD KEY `event_category_id` (`event_category_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `difficulty_id` (`difficulty_id`);

--
-- Индексы таблицы `event_category`
--
ALTER TABLE `event_category`
  ADD PRIMARY KEY (`event_category_id`);

--
-- Индексы таблицы `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`participation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Индексы таблицы `promoter`
--
ALTER TABLE `promoter`
  ADD PRIMARY KEY (`promoter_id`),
  ADD KEY `register_id` (`register_id`);

--
-- Индексы таблицы `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_category_id` (`event_category_id`);

--
-- Индексы таблицы `rating_change`
--
ALTER TABLE `rating_change`
  ADD PRIMARY KEY (`rating_change_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_category_id` (`event_category_id`);

--
-- Индексы таблицы `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`register_id`);

--
-- Индексы таблицы `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Индексы таблицы `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Индексы таблицы `talent_coordinator`
--
ALTER TABLE `talent_coordinator`
  ADD PRIMARY KEY (`talent_coordinator_id`),
  ADD KEY `register_id` (`register_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `register_id` (`register_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `difficulty`
--
ALTER TABLE `difficulty`
  MODIFY `difficulty_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `event_category`
--
ALTER TABLE `event_category`
  MODIFY `event_category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `participation`
--
ALTER TABLE `participation`
  MODIFY `participation_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `promoter`
--
ALTER TABLE `promoter`
  MODIFY `promoter_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `rating`
--
ALTER TABLE `rating`
  MODIFY `rating_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `rating_change`
--
ALTER TABLE `rating_change`
  MODIFY `rating_change_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `register`
--
ALTER TABLE `register`
  MODIFY `register_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT для таблицы `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `talent_coordinator`
--
ALTER TABLE `talent_coordinator`
  MODIFY `talent_coordinator_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`promoter_id`) REFERENCES `promoter` (`promoter_id`),
  ADD CONSTRAINT `event_ibfk_2` FOREIGN KEY (`event_category_id`) REFERENCES `event_category` (`event_category_id`),
  ADD CONSTRAINT `event_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`),
  ADD CONSTRAINT `event_ibfk_4` FOREIGN KEY (`difficulty_id`) REFERENCES `difficulty` (`difficulty_id`);

--
-- Ограничения внешнего ключа таблицы `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

--
-- Ограничения внешнего ключа таблицы `promoter`
--
ALTER TABLE `promoter`
  ADD CONSTRAINT `promoter_ibfk_1` FOREIGN KEY (`register_id`) REFERENCES `register` (`register_id`);

--
-- Ограничения внешнего ключа таблицы `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`event_category_id`) REFERENCES `event_category` (`event_category_id`);

--
-- Ограничения внешнего ключа таблицы `rating_change`
--
ALTER TABLE `rating_change`
  ADD CONSTRAINT `rating_change_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `rating_change_ibfk_2` FOREIGN KEY (`event_category_id`) REFERENCES `event_category` (`event_category_id`);

--
-- Ограничения внешнего ключа таблицы `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

--
-- Ограничения внешнего ключа таблицы `talent_coordinator`
--
ALTER TABLE `talent_coordinator`
  ADD CONSTRAINT `talent_coordinator_ibfk_1` FOREIGN KEY (`register_id`) REFERENCES `register` (`register_id`);

--
-- Ограничения внешнего ключа таблицы `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`register_id`) REFERENCES `register` (`register_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
