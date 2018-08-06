CREATE DATABASE todolist;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `nodes` (
  `node_id` int(11) NOT NULL,
  `node` text COLLATE utf8_czech_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `node_done` varchar(4) COLLATE utf8_czech_ci NOT NULL,
  `position` int(11) NOT NULL,
  `date` date NOT NULL,
  `subnode` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


ALTER TABLE `nodes`
  ADD PRIMARY KEY (`node_id`);

ALTER TABLE `nodes`
  MODIFY `node_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `subnodes` (
  `id` int(11) NOT NULL,
  `subnode` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `subnode_done` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `node_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `subnodes`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `subnodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;


CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(65) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;