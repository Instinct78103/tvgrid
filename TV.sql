-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 08 2018 г., 14:30
-- Версия сервера: 5.6.37
-- Версия PHP: 7.0.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `TV`
--

-- --------------------------------------------------------

--
-- Структура таблицы `DeleteAll`
--

CREATE TABLE `DeleteAll` (
  `id` int(11) NOT NULL,
  `item` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Все найденные значения item в строке будут удалены';

-- --------------------------------------------------------

--
-- Структура таблицы `DeleteAllExcept`
--

CREATE TABLE `DeleteAllExcept` (
  `id` int(11) NOT NULL,
  `item` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оставляемые фразы (прочее в строке будет удалено)';

--
-- Дамп данных таблицы `DeleteAllExcept`
--

INSERT INTO `DeleteAllExcept` (`id`, `item`) VALUES
(1, 'Профессиональный бокс'),
(2, 'Смешанные единоборства'),
(3, 'Кино в деталях'),
(4, 'Радзишевский и К'),
(9, '\"20:30\"'),
(10, '\"7 кун\"'),
(11, '\"X factor\"'),
(12, '\"ГЛАВНАЯ РЕДАКЦИЯ\"'),
(13, '\"Жди меня\"'),
(14, '\"Любимые актеры\"'),
(15, '\"ПОРТРЕТ НЕДЕЛИ\"'),
(16, '\"Я стесняюсь своего тела\"'),
(17, '\"Битва экстрасенсов\"'),
(18, '\"Вопрос времени\"'),
(19, 'В гостях у Митрофановны'),
(20, 'Все на Матч!'),
(21, 'Галыгин.ru'),
(22, 'Дела семейные'),
(23, 'Искры камина'),
(24, 'Истории в деталях'),
(25, 'История советской эстрады'),
(27, 'Нахлыст'),
(28, 'Особенности охоты'),
(29, 'ОТВдетям. Мультфильмы'),
(30, 'Пешком...'),
(31, 'Сати. Нескучная классика'),
(32, 'Сквозной эфир'),
(35, 'Съешьте это немедленно!'),
(36, 'Дaчныe радoсти');

-- --------------------------------------------------------

--
-- Структура таблицы `FindReplace`
--

CREATE TABLE `FindReplace` (
  `id` int(11) NOT NULL,
  `find_what` text NOT NULL,
  `replace_with` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `FindReplace`
--

INSERT INTO `FindReplace` (`id`, `find_what`, `replace_with`) VALUES
(1, 'Охота в Восточной Пруссии', 'В Восточной Пруссии'),
(2, 'Зимняя рыбалка в Приволжье', 'Зимняя рыбалка');

-- --------------------------------------------------------

--
-- Структура таблицы `RealNames`
--

CREATE TABLE `RealNames` (
  `id` int(11) NOT NULL,
  `item` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Имена собственные';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `DeleteAll`
--
ALTER TABLE `DeleteAll`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `DeleteAllExcept`
--
ALTER TABLE `DeleteAllExcept`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `FindReplace`
--
ALTER TABLE `FindReplace`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `RealNames`
--
ALTER TABLE `RealNames`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `DeleteAll`
--
ALTER TABLE `DeleteAll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `DeleteAllExcept`
--
ALTER TABLE `DeleteAllExcept`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT для таблицы `FindReplace`
--
ALTER TABLE `FindReplace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `RealNames`
--
ALTER TABLE `RealNames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
