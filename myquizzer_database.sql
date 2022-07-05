-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2022 at 03:16 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `questionId` int(11) NOT NULL,
  `optionValue` int(11) NOT NULL,
  `content` text NOT NULL,
  `optionId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`questionId`, `optionValue`, `content`, `optionId`) VALUES
(4, 1, 'adfadf', 13),
(4, 2, 'adfadf', 14),
(4, 3, 'adfadf', 15),
(4, 4, 'adfadf', 16),
(5, 1, 'Option 1', 17),
(5, 2, 'Option 2', 18),
(5, 3, 'Option 3', 19),
(5, 4, 'Option 4', 20),
(6, 1, 'Option 1', 21),
(6, 2, 'Option 2', 22),
(6, 3, 'Option 3', 23),
(6, 4, 'Option 4', 24);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `questionId` int(11) NOT NULL,
  `correctAns` int(11) NOT NULL,
  `questionNumber` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `question` text NOT NULL,
  `quizId` int(11) NOT NULL,
  `correctScore` int(11) NOT NULL,
  `wrongScore` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`questionId`, `correctAns`, `questionNumber`, `level`, `question`, `quizId`, `correctScore`, `wrongScore`) VALUES
(4, 1, 1, 1, 'ddafaf', 6, 1, 2),
(5, 1, 1, 1, 'Question 1', 7, 4, 1),
(6, 2, 2, 1, 'Question 2', 7, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quizId` int(11) NOT NULL,
  `quizName` varchar(256) NOT NULL,
  `quizType` int(11) NOT NULL,
  `quizMode` int(11) NOT NULL,
  `description` text NOT NULL,
  `quizAdmin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quizId`, `quizName`, `quizType`, `quizMode`, `description`, `quizAdmin`) VALUES
(6, 'quiz1', 1, 1, 'ravisrc first quiz', 1),
(7, 'Quiz 1', 1, 2, 'Description of the Quiz 1', 6);

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `userId` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `responseValue` int(11) DEFAULT NULL,
  `quizId` int(11) NOT NULL,
  `flag` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`userId`, `questionId`, `responseValue`, `quizId`, `flag`) VALUES
(3, 4, 2, 6, NULL),
(3, 5, 2, 7, NULL),
(3, 6, 2, 7, NULL),
(4, 4, 1, 6, 1),
(4, 5, 1, 7, NULL),
(4, 6, 2, 7, NULL),
(5, 4, 1, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `userquizrelation`
--

CREATE TABLE `userquizrelation` (
  `userId` int(11) NOT NULL,
  `quizId` int(11) NOT NULL,
  `relation` int(11) NOT NULL,
  `lastAttempt` int(11) DEFAULT NULL,
  `finished` int(11) DEFAULT NULL,
  `quizScore` int(11) DEFAULT NULL,
  `quizTime` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `userquizrelation`
--

INSERT INTO `userquizrelation` (`userId`, `quizId`, `relation`, `lastAttempt`, `finished`, `quizScore`, `quizTime`) VALUES
(1, 6, 1, 1, 0, 0, NULL),
(3, 6, 0, 1, 1, -2, 11),
(3, 7, 0, 1, 1, 3, 13),
(4, 6, 0, 1, 1, 1, 13),
(4, 7, 0, 1, 1, 8, 8),
(5, 6, 0, 1, 1, 1, 9),
(6, 7, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `name`, `password`) VALUES
(1, 'user4', '4a950d697785c9c181b98178e0a7979f'),
(3, 'user1', '4a950d697785c9c181b98178e0a7979f'),
(4, 'user2', '579d9ec9d0c3d687aaa91289ac2854e4'),
(5, 'user3', '579d9ec9d0c3d687aaa91289ac2854e4'),
(6, 'creator1', '579d9ec9d0c3d687aaa91289ac2854e4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`optionId`),
  ADD KEY `questionId` (`questionId`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`questionId`),
  ADD KEY `quizId` (`quizId`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quizId`),
  ADD KEY `quizAdmin` (`quizAdmin`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`userId`,`questionId`),
  ADD KEY `questionId` (`questionId`),
  ADD KEY `quizId` (`quizId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `userquizrelation`
--
ALTER TABLE `userquizrelation`
  ADD PRIMARY KEY (`userId`,`quizId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `quizId` (`quizId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `optionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `questionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quizId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`questionId`) REFERENCES `questions` (`questionId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quizId`) REFERENCES `quizzes` (`quizId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`quizAdmin`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`questionId`) REFERENCES `questions` (`questionId`),
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`quizId`) REFERENCES `quizzes` (`quizId`),
  ADD CONSTRAINT `responses_ibfk_3` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `userquizrelation`
--
ALTER TABLE `userquizrelation`
  ADD CONSTRAINT `userquizrelation_ibfk_1` FOREIGN KEY (`quizId`) REFERENCES `quizzes` (`quizId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userquizrelation_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
