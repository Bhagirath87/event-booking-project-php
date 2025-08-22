-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2025 at 10:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `seats_booked` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `event_id`, `seats_booked`, `total_price`, `booking_date`, `status`) VALUES
(1, 1, 1, 2, 1798.00, '2025-08-03 04:33:30', 'Confirmed'),
(2, 3, 1, 3, 2697.00, '2025-08-03 04:53:25', 'Confirmed'),
(6, 1, 1, 2, 1798.00, '2025-08-15 16:21:42', 'Confirmed'),
(7, 1, 6, 2, 5000.00, '2025-08-22 08:15:20', 'Confirmed'),
(8, 1, 6, 1, 2500.00, '2025-08-22 08:26:23', 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `city_name`, `state`) VALUES
(1, 'Ahmedabad', 'Gujarat'),
(2, 'Surat', 'Gujarat'),
(3, 'Vadodara', 'Gujarat'),
(4, 'Rajkot', 'Gujarat'),
(5, 'Bhavnagar', 'Gujarat'),
(6, 'Jamnagar', 'Gujarat'),
(7, 'Gandhinagar', 'Gujarat'),
(8, 'Junagadh', 'Gujarat'),
(9, 'Anand', 'Gujarat'),
(10, 'Nadiad', 'Gujarat'),
(11, 'Mumbai', 'Maharashtra'),
(12, 'Pune', 'Maharashtra'),
(13, 'Nagpur', 'Maharashtra'),
(14, 'Delhi', 'Delhi'),
(15, 'New Delhi', 'Delhi'),
(16, 'Bengaluru', 'Karnataka'),
(17, 'Mysuru', 'Karnataka'),
(18, 'Chennai', 'Tamil Nadu'),
(19, 'Coimbatore', 'Tamil Nadu'),
(20, 'Hyderabad', 'Telangana'),
(21, 'Warangal', 'Telangana'),
(22, 'Kolkata', 'West Bengal'),
(23, 'Asansol', 'West Bengal'),
(24, 'Jaipur', 'Rajasthan'),
(25, 'Udaipur', 'Rajasthan'),
(26, 'Lucknow', 'Uttar Pradesh'),
(27, 'Kanpur', 'Uttar Pradesh'),
(28, 'Agra', 'Uttar Pradesh'),
(29, 'Varanasi', 'Uttar Pradesh'),
(30, 'Noida', 'Uttar Pradesh'),
(31, 'Patna', 'Bihar'),
(32, 'Gaya', 'Bihar'),
(33, 'Ranchi', 'Jharkhand'),
(34, 'Jamshedpur', 'Jharkhand'),
(35, 'Bhopal', 'Madhya Pradesh'),
(36, 'Indore', 'Madhya Pradesh'),
(37, 'Raipur', 'Chhattisgarh'),
(38, 'Bhubaneswar', 'Odisha'),
(39, 'Cuttack', 'Odisha'),
(40, 'Chandigarh', 'Chandigarh'),
(41, 'Dehradun', 'Uttarakhand'),
(42, 'Haridwar', 'Uttarakhand'),
(43, 'Shimla', 'Himachal Pradesh'),
(44, 'Amritsar', 'Punjab'),
(45, 'Ludhiana', 'Punjab'),
(46, 'Srinagar', 'Jammu & Kashmir'),
(47, 'Leh', 'Ladakh'),
(48, 'Panaji', 'Goa'),
(49, 'Aizawl', 'Mizoram'),
(50, 'Shillong', 'Meghalaya'),
(51, 'Gangtok', 'Sikkim'),
(52, 'Imphal', 'Manipur'),
(53, 'Itanagar', 'Arunachal Pradesh'),
(54, 'Kohima', 'Nagaland'),
(55, 'Dispur', 'Assam'),
(56, 'Guwahati', 'Assam'),
(57, 'Puducherry', 'Puducherry'),
(58, 'Port Blair', 'Andaman and Nicobar Islands'),
(59, 'Silvassa', 'Dadra and Nagar Haveli and Daman and Diu'),
(60, 'Agartala', 'Tripura');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `city_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `event_date` date NOT NULL,
  `total_seats` int(11) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `city_id`, `address`, `event_date`, `total_seats`, `available_seats`, `price`, `category`, `status`, `image`, `created_at`) VALUES
(1, 'DJ Consert', 'Get ready to experience the ultimate fusion of sound, light, and energy at the Electro Pulse: DJ Mega Concert 2025! This is not just another music event — it\'s a full-blown sensory explosion that will ignite your soul and keep you dancing until the break of dawn.\r\n\r\nSet in the heart of the city at a stunning open-air venue, Electro Pulse brings together internationally renowned DJs, rising underground talent, and cutting-edge stage production for a night that pushes the boundaries of live electronic music. Expect massive LED walls, mind-bending visuals, synchronized light shows, and bass you can feel in your chest.\r\n\r\n🎧 Headliners include:\r\n\r\nDJ Nova (EDM/Progressive House)\r\n\r\nXyloBeat (Techno)\r\n\r\nRoxy Vibe (Future Bass/Trap)\r\n\r\nThe Frequency Twins (Live Vinyl Set)\r\n\r\nWhether you\'re a die-hard raver, a casual fan of electronic music, or someone just looking for an unforgettable night out, this concert promises nonstop music, interactive installations, VIP experiences, and a vibrant crowd that knows how to party.\r\n\r\nGrab your crew, put on your brightest rave gear, and join thousands of music lovers for what promises to be one of the biggest DJ events of the year.\r\n\r\n🎟️ Limited early bird tickets available now — don’t miss out!', 4, 'The Grand Lawn, Kalavad Road, \r\nNear Crystal Mall, Rajkot - 360005, \r\nGujarat, India', '2025-08-22', 300, 293, 899.00, 'Music', 'Active', 'event_688eeefa6e67b8.03108738.jpg', '2025-08-03 04:03:33'),
(6, 'Arijit Singh Live Concert', 'Get ready for an enchanting evening of music, emotion, and unforgettable memories as one of India\'s most beloved playback singers, Arijit Singh, takes the stage for a magical live performance. Known for his soul-stirring voice, Arijit Singh has captured millions of hearts with timeless hits like Tum Hi Ho, Channa Mereya, Raabta, Ae Dil Hai Mushkil, and many more.\r\n\r\nThis grand musical event promises an immersive experience, complete with a state-of-the-art sound system, dazzling lights, and a stage set-up designed to bring you closer to the magic of Arijit\'s voice. Whether you\'re a long-time fan or discovering his music for the first time, this concert offers a chance to witness one of the most versatile and powerful voices of our generation live in action.\r\n\r\nThe concert will feature a blend of Arijit\'s greatest hits, romantic ballads, high-energy numbers, and special acoustic sets that showcase his vocal mastery. Accompanied by a full live band and special visual effects, this evening will be a celebration of music, emotion, and artistry.\r\n\r\nPerfect for couples, families, friends, and music lovers of all ages, this is more than just a concert — it\'s an experience that will stay with you forever. Don\'t miss the chance to be part of this incredible night!', 11, 'NSCI Dome, Worli, Mumbai, Maharashtra 400018', '2025-08-28', 5000, 4997, 2500.00, 'Music', 'Active', 'pexels-teddy-2263436.jpg', '2025-08-22 08:14:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE `user_data` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(10) NOT NULL,
  `password` varchar(300) NOT NULL,
  `city_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_data`
--

INSERT INTO `user_data` (`id`, `name`, `email`, `number`, `password`, `city_id`) VALUES
(1, 'bhagi', 'abc@gmail.com', '8780441298', '$2y$10$/lHULx.FN9yCt.atmNnVEe92i2GA2EcTQZfqFrT13RHL1xojmCd7i', 4),
(2, 'nj', 'e@gmail.com', '87802', '$2y$10$yzJKoFkp79rDLWhMu8JuM.zTfrW516fObhpaPsZMitRTaOB/pSRdS', 7),
(3, 'bhagirath', 'bhagirath@gmail.com', '8780441298', '$2y$10$dZUkZV1ShWTGnWkW1zimp.IjXKtwehzYpKWh3X/OZdrGF28b2VjB6', 6),
(4, 'ronak', 'ronak123@gmail.com', '6354129852', '$2y$10$KnIIABLlKGdP2rflPousxuTpCI2W4cQlRn.GL84ombXFw9bprs3y2', 48);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_event` (`event_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_data`
--
ALTER TABLE `user_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
