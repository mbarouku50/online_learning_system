-- phpMyAdmin SQL Dump
-- version 5.2.2deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 20, 2025 at 07:46 AM
-- Server version: 11.4.5-MariaDB-1
-- PHP Version: 8.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `03.2481.01.01.2023`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountancy_books`
--

CREATE TABLE `accountancy_books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_email`, `admin_pass`) VALUES
(1, 'mbarouk', 'mbarouk@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `business_admin_books`
--

CREATE TABLE `business_admin_books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_books`
--

CREATE TABLE `business_books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `name`, `email`, `created_at`) VALUES
(1, 'mbarouk hemed', 'mbarukhemedy50@gmail.com', '2025-05-13 02:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message_content` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `email`, `message_content`, `created_at`) VALUES
(1, 'mbarukhemedy50@gmail.com', 'the author of the bookiping subject i\'m not understanding ', '2025-05-13 02:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `contact_subjects`
--

CREATE TABLE `contact_subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `contact_subjects`
--

INSERT INTO `contact_subjects` (`subject_id`, `subject_name`, `created_at`) VALUES
(1, 'bookiping', '2025-05-13 02:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_name` text NOT NULL,
  `course_desc` text NOT NULL,
  `course_author` varchar(255) NOT NULL,
  `course_img` text NOT NULL,
  `course_duration` text NOT NULL,
  `course_price` text NOT NULL,
  `course_original_price` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `course_name`, `course_desc`, `course_author`, `course_img`, `course_duration`, `course_price`, `course_original_price`) VALUES
(1, 'BIT', 'study information technology in easy way CIT,DIT,BIT all level', 'm_boy', '../image/courseimg/1746533813_IT.jpeg', '2month', '10000', '20000'),
(2, 'Methrology', 'Learn Methrology Easy way', 'm_boy50', '../image/courseimg/metrology.jpg', '2month', '1000', '20000'),
(3, 'Learn Procurement the Easy Way', 'Study procurement in easy way - CPS, DPS, BPS all levels', 'abdulstkz', '../image/courseimg/procure.jpeg', '3year', '1000000000', '1100000000'),
(4, 'Business Administration Fundamentals', 'Comprehensive business training for CBA, DBA, and BBA certification levels.', 'abiba', '../image/courseimg/BA.png', '2year', '20000', '2000000');

-- --------------------------------------------------------

--
-- Table structure for table `courseorder`
--

CREATE TABLE `courseorder` (
  `co_id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `stuemail` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `respomsg` text NOT NULL,
  `course_price` decimal(10,2) NOT NULL,
  `order_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `courseorder`
--

INSERT INTO `courseorder` (`co_id`, `order_id`, `stuemail`, `course_id`, `status`, `respomsg`, `course_price`, `order_date`) VALUES
(1, 'ORDS34949307', 'mbarukhemedy50@gmail.com', 2, 'completed', 'Payment complite', 0.00, '2025-05-11'),
(2, 'ORDS30631945', 'kan@gmail.com', 2, 'Pending', 'Payment initiated', 0.00, '2025-05-11');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `f_id` int(11) NOT NULL,
  `f_content` text NOT NULL,
  `stud_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`f_id`, `f_content`, `stud_id`) VALUES
(3, 'I am grateful to CBE. Both the faculty and placement department helped me secure a job in my second interview. The practical training was exceptional.', 10),
(4, 'CBE is a place of learning, fun, culture, and many life-impacting activities. Studying here brought added value to my life and career.', 9),
(5, 'My life at CBE made me stronger and took me a step ahead for being an IT professional. I am very grateful for the institution for providing us the best placement opportunities.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ict_books`
--

CREATE TABLE `ict_books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson`
--

CREATE TABLE `lesson` (
  `lesson_id` int(11) NOT NULL,
  `lesson_name` text NOT NULL,
  `lesson_desc` text NOT NULL,
  `lesson_link` text NOT NULL,
  `course_id` int(11) NOT NULL,
  `course_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`lesson_id`, `lesson_name`, `lesson_desc`, `lesson_link`, `course_id`, `course_name`) VALUES
(1, 'introduction to BIT co', 'this video is intro for learn IT co', '../lessonvid/681d26f0e0355_Facebook_1746472799343(720p).mp4', 1, 'BIT');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_books`
--

CREATE TABLE `marketing_books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `metrology_books`
--

CREATE TABLE `metrology_books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procurement_books`
--

CREATE TABLE `procurement_books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `stud_id` int(11) NOT NULL,
  `studname` varchar(255) NOT NULL,
  `studreg` varchar(255) DEFAULT NULL,
  `stuemail` varchar(255) NOT NULL,
  `stupass` varchar(255) NOT NULL,
  `stu_occ` text DEFAULT NULL,
  `stu_img` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`stud_id`, `studname`, `studreg`, `stuemail`, `stupass`, `stu_occ`, `stu_img`) VALUES
(1, 'm_boy', '03.2481.01.01.2023', 'mbarukhemedy50@gmail.com', '$2y$12$oiHKhXHsvrP2xOiTFm9t1eDI1F.P7WwRJk0wGuFLp9TEltV4KYBE6', 'web developer', '../image/stu/PXL_20250116_163852301.NIGHT.jpg'),
(9, 'Kan', '03.2481.01.01.2023', 'kan@gmail.com', '$2y$12$hkWirudxwu5nLJ8rNe/IAuG20wS6pl68qCGAxGoGaicnx6aPAbGl.', 'Tapelii', 'image/stu/1747055824_PXL_20250116_163852301.NIGHT.jpg'),
(10, 'Xhidy', '03.0001.01.01.2023', 'xhidy@gmail.com', '$2y$12$Up7IGjcNuJL46QqzpNAMs.NH.NcsqpXvC6BZXcAVCdtoM3tOHTGy.', 'doctori', '/Online learning system/image/stu/PXL_20250116_163852301.NIGHT.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accountancy_books`
--
ALTER TABLE `accountancy_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `business_admin_books`
--
ALTER TABLE `business_admin_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `business_books`
--
ALTER TABLE `business_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `contact_subjects`
--
ALTER TABLE `contact_subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `courseorder`
--
ALTER TABLE `courseorder`
  ADD PRIMARY KEY (`co_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`f_id`);

--
-- Indexes for table `ict_books`
--
ALTER TABLE `ict_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `lesson`
--
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`lesson_id`);

--
-- Indexes for table `marketing_books`
--
ALTER TABLE `marketing_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `metrology_books`
--
ALTER TABLE `metrology_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `procurement_books`
--
ALTER TABLE `procurement_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`stud_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountancy_books`
--
ALTER TABLE `accountancy_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `business_admin_books`
--
ALTER TABLE `business_admin_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_books`
--
ALTER TABLE `business_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_subjects`
--
ALTER TABLE `contact_subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `courseorder`
--
ALTER TABLE `courseorder`
  MODIFY `co_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `f_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ict_books`
--
ALTER TABLE `ict_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson`
--
ALTER TABLE `lesson`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `marketing_books`
--
ALTER TABLE `marketing_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `metrology_books`
--
ALTER TABLE `metrology_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procurement_books`
--
ALTER TABLE `procurement_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `stud_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accountancy_books`
--
ALTER TABLE `accountancy_books`
  ADD CONSTRAINT `accountancy_books_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL;

--
-- Constraints for table `business_admin_books`
--
ALTER TABLE `business_admin_books`
  ADD CONSTRAINT `business_admin_books_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL;

--
-- Constraints for table `business_books`
--
ALTER TABLE `business_books`
  ADD CONSTRAINT `business_books_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL;

--
-- Constraints for table `ict_books`
--
ALTER TABLE `ict_books`
  ADD CONSTRAINT `ict_books_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL;

--
-- Constraints for table `marketing_books`
--
ALTER TABLE `marketing_books`
  ADD CONSTRAINT `marketing_books_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL;

--
-- Constraints for table `metrology_books`
--
ALTER TABLE `metrology_books`
  ADD CONSTRAINT `metrology_books_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL;

--
-- Constraints for table `procurement_books`
--
ALTER TABLE `procurement_books`
  ADD CONSTRAINT `procurement_books_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
