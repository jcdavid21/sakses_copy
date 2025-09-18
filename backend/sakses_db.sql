-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 18, 2025 at 03:29 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sakses_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `district` int(11) NOT NULL,
  `population` int(11) DEFAULT NULL,
  `poverty_rate` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `name`, `district`, `population`, `poverty_rate`, `created_at`) VALUES
(1, 'Alicia', 4, NULL, NULL, '2025-09-10 06:11:20'),
(2, 'Apolonio Samson', 4, NULL, NULL, '2025-09-10 06:11:20'),
(3, 'Baesa', 4, NULL, NULL, '2025-09-10 06:11:20'),
(4, 'Bagong Pag-asa', 4, NULL, NULL, '2025-09-10 06:11:20'),
(5, 'Bahay Toro', 4, NULL, NULL, '2025-09-10 06:11:20'),
(6, 'Balong Bato', 4, NULL, NULL, '2025-09-10 06:11:20'),
(7, 'Culiat', 4, NULL, NULL, '2025-09-10 06:11:20'),
(8, 'New Era', 4, NULL, NULL, '2025-09-10 06:11:20'),
(9, 'Pasong Tamo', 4, NULL, NULL, '2025-09-10 06:11:20'),
(10, 'Sangandaan', 4, NULL, NULL, '2025-09-10 06:11:20'),
(11, 'Soccoro', 4, NULL, NULL, '2025-09-10 06:11:20'),
(12, 'Talipapa', 4, NULL, NULL, '2025-09-10 06:11:20'),
(13, 'Tandang Sora', 4, NULL, NULL, '2025-09-10 06:11:20'),
(14, 'Unang Sigaw', 4, NULL, NULL, '2025-09-10 06:11:20'),
(15, 'Bagong Silangan', 2, NULL, NULL, '2025-09-10 06:11:20'),
(16, 'Batasan Hills', 2, NULL, NULL, '2025-09-10 06:11:20'),
(17, 'Commonwealth', 2, NULL, NULL, '2025-09-10 06:11:20'),
(18, 'Holy Spirit', 2, NULL, NULL, '2025-09-10 06:11:20'),
(19, 'Payatas', 2, NULL, NULL, '2025-09-10 06:11:20'),
(20, 'Bago Bantay', 3, NULL, NULL, '2025-09-10 06:11:20'),
(21, 'Bagong Lipunan ng Crame', 3, NULL, NULL, '2025-09-10 06:11:20'),
(22, 'Camp Aguinaldo', 3, NULL, NULL, '2025-09-10 06:11:20'),
(23, 'Damayang Lagi', 3, NULL, NULL, '2025-09-10 06:11:20'),
(24, 'Del Monte', 3, NULL, NULL, '2025-09-10 06:11:20'),
(25, 'Kamuning', 3, NULL, NULL, '2025-09-10 06:11:20'),
(26, 'Laging Handa', 3, NULL, NULL, '2025-09-10 06:11:20'),
(27, 'Malaya', 3, NULL, NULL, '2025-09-10 06:11:20'),
(28, 'Marilag', 3, NULL, NULL, '2025-09-10 06:11:20'),
(29, 'Masambong', 3, NULL, NULL, '2025-09-10 06:11:20'),
(30, 'Obrero', 3, NULL, NULL, '2025-09-10 06:11:20'),
(31, 'Old Balara', 3, NULL, NULL, '2025-09-10 06:11:20'),
(32, 'Paang Bundok', 3, NULL, NULL, '2025-09-10 06:11:20'),
(33, 'Pag-ibig sa Nayon', 3, NULL, NULL, '2025-09-10 06:11:20'),
(34, 'Pinagkaisahan', 3, NULL, NULL, '2025-09-10 06:11:20'),
(35, 'Roxas', 3, NULL, NULL, '2025-09-10 06:11:20'),
(36, 'Sacred Heart', 3, NULL, NULL, '2025-09-10 06:11:20'),
(37, 'San Antonio', 3, NULL, NULL, '2025-09-10 06:11:20'),
(38, 'San Isidro Labrador', 3, NULL, NULL, '2025-09-10 06:11:20'),
(39, 'San Jose', 3, NULL, NULL, '2025-09-10 06:11:20'),
(40, 'Siena', 3, NULL, NULL, '2025-09-10 06:11:20'),
(41, 'Sto. Cristo', 3, NULL, NULL, '2025-09-10 06:11:20'),
(42, 'Tagumpay', 3, NULL, NULL, '2025-09-10 06:11:20'),
(43, 'Teachers Village East', 3, NULL, NULL, '2025-09-10 06:11:20'),
(44, 'Teachers Village West', 3, NULL, NULL, '2025-09-10 06:11:20'),
(45, 'U.P. Campus', 3, NULL, NULL, '2025-09-10 06:11:20'),
(46, 'Valencia', 3, NULL, NULL, '2025-09-10 06:11:20'),
(47, 'Bagumbayan', 1, NULL, NULL, '2025-09-10 06:11:20'),
(48, 'Bagumbuhay', 1, NULL, NULL, '2025-09-10 06:11:20'),
(49, 'Balingasa', 1, NULL, NULL, '2025-09-10 06:11:20'),
(50, 'Bungad', 1, NULL, NULL, '2025-09-10 06:11:20'),
(51, 'Damar', 1, NULL, NULL, '2025-09-10 06:11:20'),
(52, 'Damayan', 1, NULL, NULL, '2025-09-10 06:11:20'),
(53, 'Del Monte', 1, NULL, NULL, '2025-09-10 06:11:20'),
(54, 'Katipunan', 1, NULL, NULL, '2025-09-10 06:11:20'),
(55, 'Mariblo', 1, NULL, NULL, '2025-09-10 06:11:20'),
(56, 'Navotas West', 1, NULL, NULL, '2025-09-10 06:11:20'),
(57, 'Niugan', 1, NULL, NULL, '2025-09-10 06:11:20'),
(58, 'Pagkakaisa', 1, NULL, NULL, '2025-09-10 06:11:20'),
(59, 'Pag-ibig sa Nayon', 1, NULL, NULL, '2025-09-10 06:11:20'),
(60, 'Sangandaan', 1, NULL, NULL, '2025-09-10 06:11:20'),
(61, 'Sikatuna Village', 1, NULL, NULL, '2025-09-10 06:11:20'),
(62, 'Sto. Domingo', 1, NULL, NULL, '2025-09-10 06:11:20'),
(63, 'Talayan', 1, NULL, NULL, '2025-09-10 06:11:20'),
(64, 'Bagong Bayan', 5, NULL, NULL, '2025-09-10 06:11:20'),
(65, 'Batasan Hills', 5, NULL, NULL, '2025-09-10 06:11:20'),
(66, 'Payatas', 5, NULL, NULL, '2025-09-10 06:11:20'),
(67, 'Bagong Silangan', 5, NULL, NULL, '2025-09-10 06:11:20'),
(68, 'San Mateo', 5, NULL, NULL, '2025-09-10 06:11:20'),
(69, 'Apolonio de la Cruz', 6, NULL, NULL, '2025-09-10 06:11:20'),
(70, 'Baesa', 6, NULL, NULL, '2025-09-10 06:11:20'),
(71, 'Bagong Pag-asa', 6, NULL, NULL, '2025-09-10 06:11:20'),
(72, 'Culiat', 6, NULL, NULL, '2025-09-10 06:11:20'),
(73, 'New Era', 6, NULL, NULL, '2025-09-10 06:11:20'),
(74, 'Pasong Tamo', 6, NULL, NULL, '2025-09-10 06:11:20'),
(75, 'Soccoro', 6, NULL, NULL, '2025-09-10 06:11:20'),
(76, 'Talipapa', 6, NULL, NULL, '2025-09-10 06:11:20'),
(77, 'Tandang Sora', 6, NULL, NULL, '2025-09-10 06:11:20'),
(78, 'San Roque', 1, 15000, 18.50, '2025-09-10 06:14:46'),
(79, 'San Miguel', 2, 22000, 22.00, '2025-09-10 06:14:46'),
(80, 'San Pedro', 3, 18500, 16.75, '2025-09-10 06:14:46'),
(81, 'San Juan', 4, 26000, 20.30, '2025-09-10 06:14:46'),
(82, 'San Isidro', 5, 14000, 19.40, '2025-09-10 06:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(11) NOT NULL,
  `beneficiary_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') NOT NULL,
  `education_level` enum('Elementary','High School','Senior High','Vocational','College','Post Graduate') NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `barangay_id` int(11) NOT NULL,
  `complete_address` text NOT NULL,
  `family_size` int(11) NOT NULL,
  `monthly_income_before` decimal(10,2) DEFAULT NULL,
  `employment_status_before` enum('unemployed','underemployed','self_employed','employed') NOT NULL,
  `is_pantawid_beneficiary` tinyint(1) DEFAULT 0,
  `is_indigenous` tinyint(1) DEFAULT 0,
  `has_disability` tinyint(1) DEFAULT 0,
  `household_head` tinyint(1) DEFAULT 0,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beneficiaries`
--

INSERT INTO `beneficiaries` (`id`, `beneficiary_id`, `first_name`, `last_name`, `middle_name`, `date_of_birth`, `gender`, `civil_status`, `education_level`, `contact_number`, `email`, `barangay_id`, `complete_address`, `family_size`, `monthly_income_before`, `employment_status_before`, `is_pantawid_beneficiary`, `is_indigenous`, `has_disability`, `household_head`, `registration_date`, `created_at`, `updated_at`) VALUES
(1, 'B001', 'Pedro', 'Cruz', 'Santos', '1990-01-10', 'Male', 'Married', 'College', '09565535401', 'pedro@example.com', 1, '123 Main St', 5, 12000.00, 'unemployed', 1, 0, 0, 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', '2025-09-10 16:00:12'),
(2, 'B002', 'Ana', 'Reyes', 'Dela', '1985-02-15', 'Female', 'Single', 'High School', '0918000002', 'ana@example.com', 2, '456 Second St', 3, 8000.00, 'underemployed', 0, 1, 0, 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(3, 'B003', 'Jose', 'Garcia', 'Lopez', '1992-05-20', 'Male', 'Married', 'Senior High', '0918000003', 'jose@example.com', 3, '789 Third St', 4, 10000.00, 'employed', 0, 0, 1, 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(4, 'B004', 'Maria', 'Dela Cruz', 'Santos', '1995-07-25', 'Female', 'Single', 'Vocational', '0918000004', 'maria@example.com', 4, '321 Fourth St', 2, 5000.00, 'unemployed', 1, 0, 0, 0, '2025-09-10 06:14:46', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(5, 'B005', 'Juan', 'Torres', 'Mendoza', '1988-09-30', 'Male', 'Married', 'College', '0918000005', 'juan@example.com', 5, '654 Fifth St', 6, 15000.00, 'self_employed', 0, 0, 0, 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(6, '6', 'Juancarlo', 'David', '', '2001-04-21', 'Male', 'Single', 'Elementary', '09565535401', 'jcdavid@gmail.com', 3, 'Loraine Street\nParkway', 5, 12000.00, 'employed', 1, 0, 0, 0, '2025-09-10 17:00:34', '2025-09-10 17:00:34', '2025-09-10 17:01:31'),
(7, 'B100', 'Karla', 'Miller', 'J', '1986-11-09', 'Female', 'Single', 'Post Graduate', '49385351723', 'trogers@gmail.com', 1, '5791 Pratt Village Suite 214', 5, 13481.00, 'unemployed', 1, 1, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(8, 'B101', 'Kristina', 'Fletcher', 'B', '2005-01-05', 'Female', 'Married', 'Vocational', '19879545813', 'bberry@hotmail.com', 1, '20953 Knight Row Suite 755', 2, 11679.00, 'underemployed', 1, 0, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(9, 'B102', 'Jonathan', 'Crawford', 'J', '1993-08-24', 'Male', 'Married', 'High School', '68420902554', 'ywhite@gmail.com', 3, '130 Seth Inlet Apt. 363', 6, 7497.00, 'self_employed', 1, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(10, 'B103', 'Mario', 'Maynard', 'R', '1992-12-31', 'Male', 'Separated', 'College', '47574891864', 'monicagray@lindsey.com', 5, '9598 Betty Stream', 2, 8181.00, 'unemployed', 1, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(11, 'B104', 'Tyler', 'Haynes', 'S', '1982-06-28', 'Female', 'Single', 'Elementary', '14157629276', 'michele88@gmail.com', 1, '5888 Michael Squares Suite 412', 3, 8618.00, 'unemployed', 0, 0, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(12, 'B105', 'Frederick', 'Arnold', 'R', '2004-06-22', 'Female', 'Widowed', 'College', '55300405190', 'floydchad@clark-jacobs.com', 4, '89531 Richard Gateway', 4, 4202.00, 'employed', 1, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(13, 'B106', 'Matthew', 'Barnett', 'S', '1990-02-23', 'Female', 'Separated', 'Senior High', '37738368421', 'barryramos@yahoo.com', 4, '05802 Paul Fork Suite 396', 7, 8451.00, 'unemployed', 1, 0, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(14, 'B107', 'Laura', 'White', 'L', '2001-07-18', 'Male', 'Separated', 'Post Graduate', '97078343296', 'andersonmatthew@gmail.com', 8, '50167 Erickson Land Apt. 990', 7, 13178.00, 'self_employed', 1, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(15, 'B108', 'Laura', 'Hart', 'C', '1979-09-15', 'Female', 'Separated', 'College', '66126521327', 'andrestone@bailey.info', 1, '119 William Isle', 6, 9309.00, 'unemployed', 0, 1, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(16, 'B109', 'Ruth', 'Sanders', 'G', '1989-01-22', 'Female', 'Separated', 'High School', '45952221568', 'asnyder@hotmail.com', 1, '367 Walton Forge Apt. 062', 6, 14190.00, 'employed', 0, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(17, 'B110', 'Eric', 'Anderson', 'S', '1994-11-28', 'Male', 'Separated', 'Senior High', '96628311398', 'timothy27@gmail.com', 4, '90020 Tracy Heights', 3, 12475.00, 'employed', 0, 0, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(18, 'B111', 'Shawn', 'Freeman', 'T', '1999-05-28', 'Male', 'Separated', 'High School', '74218974567', 'samanthasmith@gmail.com', 3, '7994 Amanda Land', 3, 8923.00, 'underemployed', 0, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(19, 'B112', 'Robert', 'Tanner', 'N', '2005-05-16', 'Female', 'Married', 'Post Graduate', '86321807323', 'richardgalloway@williams-contreras.net', 7, '264 Snyder Cliffs', 5, 7244.00, 'underemployed', 0, 1, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(20, 'B113', 'Shannon', 'Moore', 'R', '2002-01-26', 'Female', 'Separated', 'High School', '90868533772', 'taylormichael@hotmail.com', 7, '562 Robert Groves Suite 176', 3, 10857.00, 'self_employed', 1, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(21, 'B114', 'Andrew', 'Alvarez', 'M', '2001-04-12', 'Male', 'Single', 'Vocational', '51999532677', 'aaron16@peters-reynolds.com', 8, '684 Andrew Bypass Suite 459', 7, 6876.00, 'unemployed', 1, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(22, 'B115', 'Tracy', 'Miller', 'D', '1981-12-13', 'Female', 'Married', 'Vocational', '62162041492', 'amygonzales@hotmail.com', 6, '50525 Billy Divide Suite 630', 3, 6862.00, 'self_employed', 1, 0, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(23, 'B116', 'Kevin', 'Bryant', 'M', '2004-04-01', 'Female', 'Married', 'College', '16127136380', 'vasquezkelsey@stokes.com', 9, '400 Bernard Creek Apt. 915', 7, 13703.00, 'underemployed', 1, 1, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(24, 'B117', 'Michael', 'Chaney', 'J', '1988-11-05', 'Female', 'Single', 'Vocational', '60486241834', 'ymoore@gmail.com', 5, '67429 Moreno Center', 3, 6707.00, 'self_employed', 1, 0, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(25, 'B118', 'Patricia', 'Medina', 'J', '1992-08-26', 'Female', 'Married', 'High School', '92472174410', 'kristensimpson@hill-myers.com', 3, '333 Tanner Circles', 3, 8225.00, 'employed', 1, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(26, 'B119', 'Rose', 'Preston', 'M', '1987-07-07', 'Male', 'Widowed', 'College', '68308009379', 'natalie85@mccoy.com', 6, '9268 Kaiser Ridge Suite 624', 3, 11457.00, 'employed', 1, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(27, 'B120', 'Ashley', 'Moses', 'P', '2000-01-11', 'Male', 'Married', 'Elementary', '83173349083', 'michaelcoleman@lewis-fitzgerald.com', 5, '410 Sanders Pike', 6, 12266.00, 'self_employed', 1, 0, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(28, 'B121', 'Charles', 'Buckley', 'A', '1981-04-23', 'Male', 'Separated', 'High School', '46514126322', 'elizabeththompson@smith.com', 1, '197 Rachel Parkway', 7, 6817.00, 'employed', 0, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(29, 'B122', 'Paul', 'Bryant', 'C', '2000-10-06', 'Female', 'Single', 'College', '46736679274', 'holtandrea@johnson.com', 10, '715 Vernon Isle Apt. 929', 2, 9066.00, 'employed', 1, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(30, 'B123', 'Shirley', 'Harris', 'S', '1982-01-19', 'Female', 'Separated', 'Vocational', '94907447598', 'thomasryan@graham.com', 10, '918 Carney Dam', 5, 6487.00, 'unemployed', 1, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(31, 'B124', 'Michael', 'Pierce', 'T', '1999-08-10', 'Female', 'Widowed', 'College', '25849444155', 'joann58@hughes.com', 2, '2207 Sherman Turnpike Apt. 460', 7, 6647.00, 'unemployed', 0, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(32, 'B125', 'David', 'French', 'L', '1998-04-26', 'Female', 'Single', 'Senior High', '58347590551', 'kingemily@moore-anderson.com', 1, '5547 Brittany Shore Suite 623', 3, 6137.00, 'employed', 1, 0, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(33, 'B126', 'Denise', 'Gordon', 'C', '1981-12-21', 'Female', 'Widowed', 'Elementary', '64825450597', 'tanya95@gmail.com', 9, '1559 Zamora Crest Suite 318', 4, 9771.00, 'self_employed', 1, 0, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(34, 'B127', 'John', 'Howard', 'R', '1995-12-26', 'Female', 'Single', 'Vocational', '84084331265', 'alexanderkimberly@gmail.com', 4, '035 Hinton Meadows', 3, 4342.00, 'employed', 0, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(35, 'B128', 'Scott', 'Jones', 'J', '2003-07-14', 'Female', 'Separated', 'Vocational', '29968355143', 'autumn67@hotmail.com', 2, '29298 Peggy Trail', 6, 13900.00, 'unemployed', 0, 1, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(36, 'B129', 'Paul', 'Watts', 'M', '1981-01-03', 'Male', 'Widowed', 'High School', '95208372881', 'donnaflores@whitehead.biz', 1, '765 Rachel Fort', 2, 10683.00, 'self_employed', 0, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(37, 'B130', 'Jennifer', 'Chambers', 'M', '1984-08-23', 'Female', 'Single', 'College', '94414132716', 'karenramos@crawford-kane.com', 9, '88919 William Circle', 4, 10831.00, 'underemployed', 0, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(38, 'B131', 'Misty', 'Taylor', 'P', '1993-03-16', 'Female', 'Single', 'High School', '52976178511', 'turnerveronica@chen.com', 3, '8212 Linda Club', 6, 7084.00, 'self_employed', 0, 0, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(39, 'B132', 'Danny', 'Santos', 'E', '1993-07-13', 'Female', 'Single', 'Vocational', '99368865295', 'william78@murphy-jackson.biz', 5, '3980 Carl Fort', 5, 9106.00, 'employed', 1, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(40, 'B133', 'Bryan', 'Morgan', 'D', '1988-03-07', 'Female', 'Married', 'Vocational', '27476638455', 'gperry@yahoo.com', 6, '26394 Jennifer Ferry Suite 847', 6, 10674.00, 'underemployed', 1, 1, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(41, 'B134', 'Tyler', 'Cook', 'J', '1991-06-30', 'Male', 'Married', 'College', '69036392588', 'daniellelynch@hotmail.com', 3, '830 Smith Plains Suite 212', 3, 11733.00, 'self_employed', 1, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(42, 'B135', 'Robin', 'Davenport', 'M', '1981-05-12', 'Male', 'Separated', 'College', '93100893712', 'wilsonkaitlyn@yahoo.com', 6, '50179 Todd Knoll Suite 556', 5, 12685.00, 'self_employed', 0, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(43, 'B136', 'Steven', 'Spencer', 'M', '1995-10-24', 'Female', 'Widowed', 'Vocational', '14393633090', 'acox@gonzalez-stewart.net', 2, '6167 Hensley Mountains Apt. 354', 5, 10268.00, 'self_employed', 0, 0, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(44, 'B137', 'Ariana', 'Santos', 'D', '1984-12-10', 'Female', 'Separated', 'Elementary', '26958754146', 'cmoses@yahoo.com', 7, '00567 Sylvia Greens Apt. 828', 2, 5145.00, 'self_employed', 0, 1, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(45, 'B138', 'John', 'Davis', 'D', '1987-10-21', 'Male', 'Widowed', 'Vocational', '53796350242', 'ssalazar@brown.com', 4, '565 Moses Fall', 5, 13748.00, 'underemployed', 1, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(46, 'B139', 'Meghan', 'Steele', 'N', '1980-07-20', 'Male', 'Married', 'College', '83320435035', 'joanne99@gmail.com', 6, '953 White Inlet', 3, 10820.00, 'self_employed', 1, 1, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(47, 'B140', 'Joseph', 'Hunter', 'L', '1982-02-04', 'Female', 'Widowed', 'Elementary', '23092766438', 'lorigraham@dixon-gentry.com', 9, '481 Samantha Dale Suite 060', 5, 8205.00, 'unemployed', 0, 0, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(48, 'B141', 'Robin', 'Kane', 'A', '2005-09-07', 'Male', 'Widowed', 'Elementary', '38604920936', 'johnbriggs@arnold.com', 9, '126 Jerry Junctions', 5, 4783.00, 'self_employed', 1, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(49, 'B142', 'Michelle', 'Ingram', 'R', '1999-03-08', 'Female', 'Married', 'Elementary', '61131306705', 'kimcollins@mason.com', 3, '6565 Victor Common', 7, 5908.00, 'self_employed', 1, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(50, 'B143', 'Todd', 'Sanders', 'D', '1990-11-26', 'Male', 'Widowed', 'Senior High', '20851273698', 'wallacebrandon@hotmail.com', 8, '486 Amanda Pine Suite 580', 3, 5923.00, 'self_employed', 0, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(51, 'B144', 'Sandy', 'Cochran', 'A', '2003-12-07', 'Female', 'Married', 'College', '22313685647', 'richardchavez@hotmail.com', 9, '79734 Young Summit Apt. 965', 6, 6050.00, 'underemployed', 0, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(52, 'B145', 'Katherine', 'Jackson', 'J', '1979-07-07', 'Male', 'Widowed', 'Vocational', '88537409672', 'cainbrian@doyle-morrison.org', 10, '1752 Rhonda Ranch', 5, 7384.00, 'unemployed', 1, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(53, 'B146', 'Kimberly', 'Gibson', 'A', '1976-09-06', 'Male', 'Widowed', 'Post Graduate', '93018907286', 'lcaldwell@harvey-harris.com', 3, '5390 David Fall', 7, 4136.00, 'underemployed', 1, 1, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(54, 'B147', 'Melissa', 'Collier', 'E', '1976-01-08', 'Male', 'Single', 'Vocational', '00138888973', 'lkelly@gmail.com', 2, '648 Palmer Motorway Suite 912', 6, 9861.00, 'self_employed', 0, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(55, 'B148', 'Dana', 'Middleton', 'A', '1984-03-30', 'Female', 'Widowed', 'Senior High', '88727320158', 'usimpson@vasquez-lane.net', 3, '13317 Austin Bypass', 6, 9194.00, 'employed', 0, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(56, 'B149', 'Susan', 'Miller', 'J', '1979-02-08', 'Male', 'Separated', 'High School', '48222453373', 'johnsims@smith.biz', 7, '408 Stacy Court', 2, 8770.00, 'underemployed', 1, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(57, 'B150', 'James', 'Shields', 'J', '1982-10-25', 'Male', 'Married', 'Vocational', '10080037605', 'stephenford@wells.biz', 3, '592 Hale Forges Apt. 765', 4, 11570.00, 'unemployed', 0, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(58, 'B151', 'Kevin', 'Brown', 'A', '1982-06-14', 'Male', 'Married', 'Elementary', '41395176652', 'handerson@smith.net', 8, '590 Kennedy Road', 5, 5087.00, 'employed', 0, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(59, 'B152', 'Tracy', 'Mendoza', 'D', '1974-12-09', 'Female', 'Married', 'Post Graduate', '59476976126', 'clarkdebbie@yahoo.com', 4, '47416 Wilson Stravenue', 4, 10179.00, 'employed', 1, 1, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(60, 'B153', 'Nicole', 'Rodgers', 'J', '1994-09-03', 'Female', 'Widowed', 'College', '71916732971', 'amberlopez@herrera.com', 6, '99535 Mark Shoals Suite 638', 5, 14510.00, 'underemployed', 1, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(61, 'B154', 'Jeremy', 'Day', 'S', '1999-01-24', 'Female', 'Single', 'Elementary', '84502681706', 'billy94@hotmail.com', 5, '4139 Amy Well', 6, 11314.00, 'employed', 0, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(62, 'B155', 'Morgan', 'Jackson', 'H', '1987-04-24', 'Male', 'Married', 'Elementary', '25130434207', 'lisamorgan@dunlap.com', 6, '5169 Luis Ridge', 6, 10279.00, 'unemployed', 1, 1, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(63, 'B156', 'Scott', 'Marshall', 'C', '1987-04-28', 'Male', 'Widowed', 'Senior High', '92105938588', 'porterroy@gmail.com', 8, '952 Walker Shore', 6, 9901.00, 'employed', 1, 1, 1, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(64, 'B157', 'Elaine', 'Martin', 'P', '1986-01-01', 'Female', 'Separated', 'Post Graduate', '26843241316', 'debrastephens@gmail.com', 2, '26913 Brady Radial Apt. 715', 6, 13437.00, 'underemployed', 1, 0, 0, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(65, 'B158', 'Amy', 'Lynch', 'C', '2005-02-09', 'Male', 'Single', 'Senior High', '29507771614', 'gperez@simpson-mitchell.biz', 6, '4746 Miller Court Suite 155', 2, 8942.00, 'self_employed', 0, 0, 1, 0, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58'),
(66, 'B159', 'Joanna', 'Meyer', 'M', '1982-02-10', 'Female', 'Single', 'Post Graduate', '92029159933', 'emilywells@gmail.com', 4, '07780 Smith Ferry Apt. 594', 2, 4301.00, 'employed', 1, 0, 0, 1, '2025-09-11 17:18:58', '2025-09-11 17:18:58', '2025-09-11 17:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `employment_outcomes`
--

CREATE TABLE `employment_outcomes` (
  `id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `outcome_type` enum('employed','self_employed','business_started','unemployed','underemployed') NOT NULL,
  `employer_name` varchar(200) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `business_name` varchar(200) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `monthly_income_after` decimal(10,2) DEFAULT NULL,
  `employment_date` date DEFAULT NULL,
  `is_program_related` tinyint(1) DEFAULT 0,
  `location` varchar(200) DEFAULT NULL,
  `employment_status` enum('full_time','part_time','contractual','seasonal') DEFAULT 'full_time',
  `follow_up_date` date NOT NULL,
  `follow_up_period` enum('3_months','6_months','1_year','2_years') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employment_outcomes`
--

INSERT INTO `employment_outcomes` (`id`, `beneficiary_id`, `program_id`, `outcome_type`, `employer_name`, `job_title`, `business_name`, `business_type`, `monthly_income_after`, `employment_date`, `is_program_related`, `location`, `employment_status`, `follow_up_date`, `follow_up_period`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'employed', 'ABC Bakery', 'Baker', NULL, NULL, 15000.00, '2025-04-10', 1, 'Quezon City', 'full_time', '2025-07-10', '3_months', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(2, 2, 2, 'self_employed', NULL, NULL, 'Ana Sari-Sari Store', 'Retail', 10000.00, '2025-08-01', 1, 'Quezon City', 'full_time', '2025-11-01', '3_months', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(3, 3, 3, 'employed', 'BPO Solutions', 'CSR', NULL, NULL, 18000.00, '2025-05-15', 1, 'Makati', 'full_time', '2025-08-15', '3_months', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(4, 4, 4, 'business_started', NULL, NULL, 'Maria’s Food Cart', 'Food Business', 12000.00, '2025-06-20', 1, 'Manila', 'full_time', '2025-09-20', '3_months', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(5, 5, 5, 'employed', 'SewTech Inc', 'Seamstress', NULL, NULL, 14000.00, '2025-07-10', 1, 'Quezon City', 'full_time', '2025-10-10', '3_months', '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(6, 6, 5, 'employed', 'Tech Solutions Inc.', 'IT Support Specialist', NULL, NULL, 18000.00, '2025-09-15', 1, 'Quezon City', 'full_time', '2025-12-15', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(7, 7, 1, 'self_employed', NULL, NULL, 'Karla\'s Bakery', 'Food Business', 15000.00, '2025-09-20', 1, 'Manila', 'full_time', '2025-12-20', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(8, 8, 2, 'employed', 'Retail Chain Corp', 'Store Manager', NULL, NULL, 16000.00, '2025-09-10', 1, 'Pasig', 'full_time', '2025-12-10', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(9, 9, 3, 'business_started', NULL, NULL, 'Mario\'s Tech Repair', 'Electronics Repair', 14000.00, '2025-09-25', 1, 'Quezon City', 'full_time', '2025-12-25', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(10, 10, 4, 'employed', 'Food Services Ltd', 'Food Preparer', NULL, NULL, 12000.00, '2025-09-05', 1, 'Makati', 'full_time', '2025-12-05', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(11, 11, 5, 'self_employed', NULL, NULL, 'Tyler\'s Tailoring', 'Clothing Business', 13000.00, '2025-09-18', 1, 'Mandaluyong', 'full_time', '2025-12-18', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(12, 12, 1, 'employed', 'Construction Firm', 'Labor Supervisor', NULL, NULL, 15000.00, '2025-09-12', 0, 'Quezon City', 'full_time', '2025-12-12', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(13, 13, 2, 'business_started', NULL, NULL, 'Matthew\'s Sari-Sari', 'Retail', 11000.00, '2025-09-22', 1, 'Manila', 'full_time', '2025-12-22', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(14, 14, 3, 'employed', 'Call Center Inc', 'Customer Service Rep', NULL, NULL, 17000.00, '2025-09-08', 1, 'Pasig', 'full_time', '2025-12-08', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(15, 15, 4, 'self_employed', NULL, NULL, 'Laura\'s Food Cart', 'Food Business', 12500.00, '2025-09-30', 1, 'Quezon City', 'full_time', '2025-12-30', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(16, 16, 5, 'employed', 'Manufacturing Co', 'Production Worker', NULL, NULL, 14000.00, '2025-09-14', 0, 'Valenzuela', 'full_time', '2025-12-14', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(17, 17, 1, 'business_started', NULL, NULL, 'Eric\'s Bakery', 'Food Business', 16000.00, '2025-09-28', 1, 'Manila', 'full_time', '2025-12-28', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(18, 18, 2, 'employed', 'Supermarket Chain', 'Cashier', NULL, NULL, 13000.00, '2025-09-03', 1, 'Pasig', 'full_time', '2025-12-03', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(19, 19, 3, 'self_employed', NULL, NULL, 'Shawn\'s Repair Shop', 'Electronics', 14500.00, '2025-09-17', 1, 'Quezon City', 'full_time', '2025-12-17', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(20, 20, 4, 'employed', 'Restaurant Group', 'Cook', NULL, NULL, 13500.00, '2025-09-11', 1, 'Makati', 'full_time', '2025-12-11', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(21, 21, 5, 'business_started', NULL, NULL, 'Robert\'s Clothing', 'Fashion Retail', 15500.00, '2025-09-26', 1, 'Mandaluyong', 'full_time', '2025-12-26', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(22, 22, 1, 'employed', 'Logistics Company', 'Delivery Driver', NULL, NULL, 12500.00, '2025-09-09', 0, 'Quezon City', 'full_time', '2025-12-09', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(23, 23, 2, 'self_employed', NULL, NULL, 'Shannon\'s Store', 'Retail', 14000.00, '2025-09-24', 1, 'Manila', 'full_time', '2025-12-24', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(24, 24, 3, 'employed', 'BPO Company', 'Technical Support', NULL, NULL, 19000.00, '2025-09-07', 1, 'Pasig', 'full_time', '2025-12-07', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(25, 25, 4, 'business_started', NULL, NULL, 'Andrew\'s Eatery', 'Food Business', 15000.00, '2025-09-21', 1, 'Quezon City', 'full_time', '2025-12-21', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(26, 26, 5, 'employed', 'Construction Firm', 'Carpenter', NULL, NULL, 14500.00, '2025-09-13', 0, 'Valenzuela', 'full_time', '2025-12-13', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(27, 27, 1, 'self_employed', NULL, NULL, 'Tracy\'s Bakery', 'Food Business', 16500.00, '2025-09-29', 1, 'Manila', 'full_time', '2025-12-29', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(28, 28, 2, 'employed', 'Retail Store', 'Sales Associate', NULL, NULL, 13500.00, '2025-09-04', 1, 'Pasig', 'full_time', '2025-12-04', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(29, 29, 3, 'business_started', NULL, NULL, 'Kevin\'s Tech Shop', 'Electronics', 15500.00, '2025-09-19', 1, 'Quezon City', 'full_time', '2025-12-19', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(30, 30, 4, 'employed', 'Food Chain', 'Kitchen Staff', NULL, NULL, 14000.00, '2025-09-06', 1, 'Makati', 'full_time', '2025-12-06', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(31, 31, 5, 'self_employed', NULL, NULL, 'Michael\'s Tailoring', 'Clothing', 15000.00, '2025-09-23', 1, 'Mandaluyong', 'full_time', '2025-12-23', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(32, 32, 1, 'employed', 'Transport Company', 'Driver', NULL, NULL, 13000.00, '2025-09-16', 0, 'Quezon City', 'full_time', '2025-12-16', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(33, 33, 2, 'business_started', NULL, NULL, 'Patricia\'s Sari-Sari', 'Retail', 14500.00, '2025-09-01', 1, 'Manila', 'full_time', '2025-12-01', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(34, 34, 3, 'employed', 'Call Center', 'Customer Service', NULL, NULL, 17500.00, '2025-09-27', 1, 'Pasig', 'full_time', '2025-12-27', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(35, 35, 4, 'self_employed', NULL, NULL, 'Rose\'s Food Stand', 'Food Business', 16000.00, '2025-09-10', 1, 'Quezon City', 'full_time', '2025-12-10', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(36, 36, 5, 'employed', 'Factory', 'Assembly Worker', NULL, NULL, 14000.00, '2025-09-15', 0, 'Valenzuela', 'full_time', '2025-12-15', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(37, 37, 1, 'business_started', NULL, NULL, 'Ashley\'s Bakery', 'Food Business', 17000.00, '2025-09-20', 1, 'Manila', 'full_time', '2025-12-20', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(38, 38, 2, 'employed', 'Department Store', 'Cashier', NULL, NULL, 13500.00, '2025-09-05', 1, 'Pasig', 'full_time', '2025-12-05', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(39, 39, 3, 'self_employed', NULL, NULL, 'Charles\' Repair', 'Electronics', 15000.00, '2025-09-25', 1, 'Quezon City', 'full_time', '2025-12-25', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(40, 40, 4, 'employed', 'Restaurant', 'Cook', NULL, NULL, 14500.00, '2025-09-08', 1, 'Makati', 'full_time', '2025-12-08', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(41, 41, 5, 'business_started', NULL, NULL, 'Paul\'s Clothing', 'Fashion', 15500.00, '2025-09-22', 1, 'Mandaluyong', 'full_time', '2025-12-22', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(42, 42, 1, 'employed', 'Delivery Service', 'Rider', NULL, NULL, 12500.00, '2025-09-12', 0, 'Quezon City', 'full_time', '2025-12-12', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(43, 43, 2, 'self_employed', NULL, NULL, 'Shirley\'s Store', 'Retail', 14000.00, '2025-09-28', 1, 'Manila', 'full_time', '2025-12-28', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(44, 44, 3, 'employed', 'BPO Industry', 'Support Agent', NULL, NULL, 18000.00, '2025-09-03', 1, 'Pasig', 'full_time', '2025-12-03', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(45, 45, 4, 'business_started', NULL, NULL, 'Michael\'s Eatery', 'Food Business', 16000.00, '2025-09-17', 1, 'Quezon City', 'full_time', '2025-12-17', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(46, 46, 5, 'employed', 'Construction', 'Laborer', NULL, NULL, 13500.00, '2025-09-11', 0, 'Valenzuela', 'full_time', '2025-12-11', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(47, 47, 1, 'self_employed', NULL, NULL, 'David\'s Bakery', 'Food Business', 16500.00, '2025-09-26', 1, 'Manila', 'full_time', '2025-12-26', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(48, 48, 2, 'employed', 'Supermarket', 'Stock Clerk', NULL, NULL, 13000.00, '2025-09-07', 1, 'Pasig', 'full_time', '2025-12-07', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(49, 49, 3, 'business_started', NULL, NULL, 'Denise\'s Tech Shop', 'Electronics', 15000.00, '2025-09-21', 1, 'Quezon City', 'full_time', '2025-12-21', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(50, 50, 4, 'employed', 'Food Service', 'Prep Cook', NULL, NULL, 14000.00, '2025-09-14', 1, 'Makati', 'full_time', '2025-12-14', '3_months', '2025-09-11 17:31:06', '2025-09-11 17:31:06');

-- --------------------------------------------------------

--
-- Table structure for table `livelihood_programs`
--

CREATE TABLE `livelihood_programs` (
  `id` int(11) NOT NULL,
  `program_code` varchar(20) NOT NULL,
  `program_name` varchar(200) NOT NULL,
  `program_type` enum('skills_training','microenterprise','employment_facilitation','entrepreneurship') NOT NULL,
  `description` text DEFAULT NULL,
  `duration_months` int(11) NOT NULL,
  `target_beneficiaries` int(11) DEFAULT NULL,
  `budget_allocated` decimal(15,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planning','active','completed','suspended') DEFAULT 'planning',
  `success_criteria` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `livelihood_programs`
--

INSERT INTO `livelihood_programs` (`id`, `program_code`, `program_name`, `program_type`, `description`, `duration_months`, `target_beneficiaries`, `budget_allocated`, `start_date`, `end_date`, `status`, `success_criteria`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'LP001', 'Bakery Skills Training', 'skills_training', 'Bread and pastry training', 3, 50, 500000.00, '2025-01-01', '2025-03-31', 'active', '80% completion', 1, '2025-09-10 06:14:46', '2025-09-11 13:22:52'),
(2, 'LP002', 'Sari-Sari Store Start-up', 'microenterprise', 'Support for small store owners', 6, 30, 300000.00, '2025-02-01', '2025-07-31', 'active', 'Increase income by 20%', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(3, 'LP003', 'Call Center Job Readiness', 'employment_facilitation', 'BPO skills program', 2, 40, 200000.00, '2025-03-01', '2025-04-30', 'active', '60% hired in BPOs', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(4, 'LP004', 'Food Cart Business', 'entrepreneurship', 'Start-up assistance for food carts', 4, 25, 400000.00, '2025-04-01', '2025-07-31', 'planning', '70% business survival in 6 months', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(5, 'LP005', 'Dressmaking Skills', 'skills_training', 'Sewing and tailoring', 5, 35, 350000.00, '2025-05-01', '2025-09-30', 'planning', 'At least 15 hired', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `ml_predictions`
--

CREATE TABLE `ml_predictions` (
  `id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `prediction_type` enum('success_probability','dropout_risk','employment_likelihood','income_potential') NOT NULL,
  `predicted_value` decimal(5,2) NOT NULL,
  `confidence_score` decimal(5,2) NOT NULL,
  `model_version` varchar(50) NOT NULL,
  `prediction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `actual_outcome` decimal(5,2) DEFAULT NULL,
  `features_used` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features_used`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ml_predictions`
--

INSERT INTO `ml_predictions` (`id`, `beneficiary_id`, `program_id`, `prediction_type`, `predicted_value`, `confidence_score`, `model_version`, `prediction_date`, `actual_outcome`, `features_used`) VALUES
(1, 1, 1, 'success_probability', 85.00, 90.00, 'v1.0', '2025-09-10 06:14:46', 80.00, '{\"income\":\"low\",\"education\":\"college\"}'),
(2, 2, 2, 'dropout_risk', 30.00, 85.00, 'v1.0', '2025-09-10 06:14:46', NULL, '{\"income\":\"medium\",\"education\":\"highschool\"}'),
(3, 3, 3, 'employment_likelihood', 70.00, 88.00, 'v1.0', '2025-09-10 06:14:46', 75.00, '{\"skills\":\"bpo\",\"attendance\":\"high\"}'),
(4, 4, 4, 'income_potential', 60.00, 92.00, 'v1.0', '2025-09-10 06:14:46', NULL, '{\"skills\":\"food\",\"business\":\"startup\"}'),
(5, 5, 5, 'success_probability', 78.00, 89.00, 'v1.0', '2025-09-10 06:14:46', NULL, '{\"skills\":\"dressmaking\"}');

-- --------------------------------------------------------

--
-- Table structure for table `program_enrollments`
--

CREATE TABLE `program_enrollments` (
  `id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `enrollment_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `status` enum('enrolled','active','completed','dropped_out','transferred') DEFAULT 'enrolled',
  `attendance_rate` decimal(5,2) DEFAULT 0.00,
  `pre_assessment_score` decimal(5,2) DEFAULT NULL,
  `post_assessment_score` decimal(5,2) DEFAULT NULL,
  `skills_acquired` text DEFAULT NULL,
  `certification_received` varchar(200) DEFAULT NULL,
  `dropout_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_enrollments`
--

INSERT INTO `program_enrollments` (`id`, `beneficiary_id`, `program_id`, `enrollment_date`, `completion_date`, `status`, `attendance_rate`, `pre_assessment_score`, `post_assessment_score`, `skills_acquired`, `certification_received`, `dropout_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-01-05', '2025-03-31', 'completed', 95.00, 60.00, 85.00, 'Baking, Pastry Making', 'Bread Baking Cert', '', '2025-09-10 06:14:46', '2025-09-11 16:54:24'),
(2, 2, 2, '2025-02-05', '2025-07-31', 'active', 80.00, 50.00, NULL, 'Retail Management', NULL, NULL, '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(3, 3, 3, '2025-03-05', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(4, 4, 4, '2025-04-05', NULL, 'active', 70.00, 45.00, NULL, 'Food Prep', NULL, NULL, '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(5, 5, 5, '2025-05-05', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-10 06:14:46', '2025-09-10 06:14:46'),
(6, 6, 5, '2025-09-11', NULL, 'enrolled', 0.00, 5.00, NULL, 'asd', '', '', '2025-09-11 16:27:43', '2025-09-11 16:51:35'),
(187, 7, 1, '2025-08-01', '2025-10-31', 'completed', 92.50, 65.00, 88.00, 'Baking, Pastry Making', 'Bread Baking Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(188, 8, 2, '2025-08-05', '2025-11-30', 'active', 85.00, 55.00, NULL, 'Retail Management', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(189, 9, 3, '2025-08-10', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(190, 10, 4, '2025-08-15', NULL, 'active', 78.00, 48.00, NULL, 'Food Preparation', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(191, 11, 5, '2025-08-20', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(192, 12, 1, '2025-08-25', '2025-11-25', 'completed', 89.00, 62.00, 85.00, 'Baking Techniques', 'Bakery Skills Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(193, 13, 2, '2025-09-01', NULL, 'active', 82.00, 52.00, NULL, 'Inventory Management', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(194, 14, 3, '2025-09-05', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(195, 15, 4, '2025-09-10', NULL, 'active', 75.00, 45.00, NULL, 'Cooking Basics', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(196, 16, 5, '2025-09-15', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(197, 17, 1, '2025-09-20', '2025-12-20', 'completed', 91.00, 68.00, 90.00, 'Advanced Baking', 'Pastry Chef Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(198, 18, 2, '2025-09-25', NULL, 'active', 84.00, 58.00, NULL, 'Customer Service', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(199, 19, 3, '2025-10-01', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(200, 20, 4, '2025-10-05', NULL, 'active', 77.00, 47.00, NULL, 'Food Safety', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(201, 21, 5, '2025-10-10', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(202, 22, 1, '2025-10-15', '2026-01-15', 'completed', 93.00, 70.00, 92.00, 'Artisan Baking', 'Artisan Baker Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(203, 23, 2, '2025-10-20', NULL, 'active', 86.00, 60.00, NULL, 'Sales Techniques', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(204, 24, 3, '2025-10-25', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(205, 25, 4, '2025-11-01', NULL, 'active', 79.00, 50.00, NULL, 'Menu Planning', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(206, 26, 5, '2025-11-05', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(207, 27, 1, '2025-11-10', '2026-02-10', 'completed', 94.00, 72.00, 94.00, 'Cake Decorating', 'Cake Artist Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(208, 28, 2, '2025-11-15', NULL, 'active', 87.00, 62.00, NULL, 'Merchandising', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(209, 29, 3, '2025-11-20', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(210, 30, 4, '2025-11-25', NULL, 'active', 80.00, 52.00, NULL, 'Nutrition Basics', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(211, 31, 5, '2025-12-01', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(212, 32, 1, '2025-12-05', '2026-03-05', 'completed', 95.00, 75.00, 96.00, 'Bread Making', 'Master Baker Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(213, 33, 2, '2025-12-10', NULL, 'active', 88.00, 65.00, NULL, 'Store Operations', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(214, 34, 3, '2025-12-15', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(215, 35, 4, '2025-12-20', NULL, 'active', 81.00, 55.00, NULL, 'Food Costing', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(216, 36, 5, '2025-12-25', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(217, 37, 1, '2026-01-01', '2026-04-01', 'completed', 96.00, 78.00, 98.00, 'Pastry Arts', 'Pastry Chef Diploma', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(218, 38, 2, '2026-01-05', NULL, 'active', 89.00, 68.00, NULL, 'Retail Marketing', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(219, 39, 3, '2026-01-10', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(220, 40, 4, '2026-01-15', NULL, 'active', 82.00, 58.00, NULL, 'Culinary Skills', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(221, 41, 5, '2026-01-20', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(222, 42, 1, '2026-01-25', '2026-04-25', 'completed', 97.00, 80.00, 99.00, 'International Baking', 'International Baker Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(223, 43, 2, '2026-02-01', NULL, 'active', 90.00, 70.00, NULL, 'Business Management', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(224, 44, 3, '2026-02-05', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(225, 45, 4, '2026-02-10', NULL, 'active', 83.00, 60.00, NULL, 'Advanced Cooking', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(226, 46, 5, '2026-02-15', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(227, 47, 1, '2026-02-20', '2026-05-20', 'completed', 98.00, 82.00, 100.00, 'Specialty Breads', 'Specialty Bread Certificate', NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(228, 48, 2, '2026-02-25', NULL, 'active', 91.00, 72.00, NULL, 'Leadership Skills', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(229, 49, 3, '2026-03-01', NULL, 'enrolled', 0.00, NULL, NULL, NULL, NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06'),
(230, 50, 4, '2026-03-05', NULL, 'active', 84.00, 62.00, NULL, 'Restaurant Management', NULL, NULL, '2025-09-11 17:31:06', '2025-09-11 17:31:06');

-- --------------------------------------------------------

--
-- Table structure for table `program_resources`
--

CREATE TABLE `program_resources` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `resource_type` enum('equipment','materials','venue','instructor','budget') NOT NULL,
  `resource_name` varchar(200) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `cost` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(200) DEFAULT NULL,
  `acquisition_date` date DEFAULT NULL,
  `status` enum('available','in_use','maintenance','damaged') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_resources`
--

INSERT INTO `program_resources` (`id`, `program_id`, `resource_type`, `resource_name`, `quantity`, `cost`, `supplier`, `acquisition_date`, `status`, `created_at`) VALUES
(1, 1, 'equipment', 'Oven', 5, 100000.00, 'Kitchen Supply Co.', '2025-01-01', 'in_use', '2025-09-10 06:14:46'),
(2, 2, 'materials', 'Store Shelves', 10, 50000.00, 'Retail Depot', '2025-02-01', 'in_use', '2025-09-10 06:14:46'),
(3, 3, 'venue', 'Training Center A', 1, 20000.00, 'City Hall', '2025-03-01', 'available', '2025-09-10 06:14:46'),
(4, 4, 'instructor', 'Chef Ramon', 1, 30000.00, 'Freelance', '2025-04-01', 'in_use', '2025-09-10 06:14:46'),
(5, 5, 'budget', 'Tailoring Kit', 30, 60000.00, 'Sewing World', '2025-05-01', 'available', '2025-09-10 06:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `success_metrics`
--

CREATE TABLE `success_metrics` (
  `id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `completion_rate` decimal(5,2) DEFAULT NULL,
  `employment_rate` decimal(5,2) DEFAULT NULL,
  `income_increase_percentage` decimal(5,2) DEFAULT NULL,
  `skill_improvement_score` decimal(5,2) DEFAULT NULL,
  `satisfaction_rating` decimal(3,1) DEFAULT NULL,
  `success_score` decimal(5,2) DEFAULT NULL,
  `success_category` enum('high_success','moderate_success','low_success','unsuccessful') NOT NULL,
  `calculation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `success_metrics`
--

INSERT INTO `success_metrics` (`id`, `beneficiary_id`, `program_id`, `completion_rate`, `employment_rate`, `income_increase_percentage`, `skill_improvement_score`, `satisfaction_rating`, `success_score`, `success_category`, `calculation_date`) VALUES
(1, 1, 1, 95.00, 90.00, 25.00, 80.00, 4.5, 85.00, 'high_success', '2025-09-10 06:14:46'),
(2, 2, 2, 80.00, 70.00, 20.00, 75.00, 4.0, 78.00, 'moderate_success', '2025-09-10 06:14:46'),
(3, 3, 3, 85.00, 80.00, 22.00, 82.00, 4.3, 82.00, 'high_success', '2025-09-10 06:14:46'),
(4, 4, 4, 70.00, 60.00, 15.00, 70.00, 3.8, 68.00, 'low_success', '2025-09-10 06:14:46'),
(5, 5, 5, 90.00, 85.00, 28.00, 85.00, 4.7, 88.00, 'high_success', '2025-09-10 06:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `action`, `description`, `created_at`) VALUES
(1, 1, 'login', 'Admin logged in', '2025-09-10 06:14:46'),
(2, 1, 'add_program', 'Added Bakery Skills Training', '2025-09-10 06:14:46'),
(3, 2, 'update_profile', 'Updated beneficiary info', '2025-09-10 06:14:46'),
(4, 3, 'view_report', 'Viewed employment outcomes', '2025-09-10 06:14:46'),
(5, 4, 'logout', 'Supervisor logged out', '2025-09-10 06:14:46'),
(20, 1, 'train_model', 'Models retrained - Success: False, Income: False', '2025-09-10 14:38:30'),
(21, 1, 'train_model', 'Models retrained - Success: False, Income: False', '2025-09-11 00:01:16'),
(22, 1, 'Insert', 'Inserted new livelihood program ID: 6, Name: Bakery Skills Training', '2025-09-11 13:52:10'),
(23, 1, 'Update', 'Updated livelihood program ID: 6, Name: Bakery Skills Training', '2025-09-11 14:00:41'),
(24, 1, 'Update', 'Updated livelihood program ID: 6, Name: Bakery Skills Training', '2025-09-11 14:01:11'),
(25, 1, 'Update', 'Updated livelihood program ID: 6, Name: Bakery Skills Training', '2025-09-11 14:01:47'),
(26, 1, 'Delete', 'Deleted livelihood program ID: 6, Name: Bakery Skills Training', '2025-09-11 14:08:08'),
(27, 1, 'Insert', 'Inserted new livelihood program ID: 7, Name: Bakery Skills Training', '2025-09-11 14:11:42'),
(28, 1, 'Delete', 'Deleted livelihood program ID: 7, Name: Bakery Skills Training', '2025-09-11 14:11:46'),
(29, 1, 'Insert', 'Inserted new livelihood program ID: 8, Name: Bakery Skills Training', '2025-09-11 14:13:08'),
(30, 1, 'Delete', 'Deleted livelihood program ID: 8, Name: Bakery Skills Training', '2025-09-11 14:13:14'),
(31, 1, 'Insert', 'Saved enrollment for Beneficiary ID: 6 in Program ID: 5', '2025-09-11 16:27:43'),
(32, 1, 'Update', 'Updated enrollment ID: 1 for Beneficiary ID: 0 in Program ID: ', '2025-09-11 16:51:31'),
(33, 1, 'Update', 'Updated enrollment ID: 6 for Beneficiary ID: 0 in Program ID: ', '2025-09-11 16:51:35'),
(34, 1, 'Update', 'Updated enrollment ID: 1 for Beneficiary ID: 0 in Program ID: ', '2025-09-11 16:51:43'),
(35, 1, 'Update', 'Updated enrollment ID: 1 for Beneficiary ID: 0 in Program ID: ', '2025-09-11 16:54:24'),
(36, 1, 'train_model', 'Models retrained - Success: False, Income: False', '2025-09-11 15:35:25');

-- --------------------------------------------------------

--
-- Table structure for table `training_datasets`
--

CREATE TABLE `training_datasets` (
  `id` int(11) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `family_size` int(11) DEFAULT NULL,
  `monthly_income_before` decimal(10,2) DEFAULT NULL,
  `employment_status_before` varchar(50) DEFAULT NULL,
  `program_type` varchar(50) DEFAULT NULL,
  `duration_months` int(11) DEFAULT NULL,
  `attendance_rate` decimal(5,2) DEFAULT NULL,
  `pre_assessment_score` decimal(5,2) DEFAULT NULL,
  `post_assessment_score` decimal(5,2) DEFAULT NULL,
  `program_completed` tinyint(1) DEFAULT NULL,
  `employment_success` tinyint(1) DEFAULT NULL,
  `skill_development_success` tinyint(1) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_modules`
--

CREATE TABLE `training_modules` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `module_name` varchar(200) NOT NULL,
  `module_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_hours` int(11) NOT NULL,
  `sequence_order` int(11) NOT NULL,
  `learning_objectives` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_modules`
--

INSERT INTO `training_modules` (`id`, `program_id`, `module_name`, `module_code`, `description`, `duration_hours`, `sequence_order`, `learning_objectives`, `created_at`) VALUES
(1, 1, 'Basic Baking', 'BAK101', 'Introduction to baking', 20, 1, 'Understand baking basics', '2025-09-10 06:14:46'),
(2, 1, 'Advanced Baking', 'BAK201', 'Advanced pastries', 30, 2, 'Create pastries', '2025-09-10 06:14:46'),
(3, 2, 'Retail Basics', 'RET101', 'Intro to sari-sari store', 15, 1, 'Manage store inventory', '2025-09-10 06:14:46'),
(4, 3, 'Call Handling', 'BPO101', 'Customer service basics', 25, 1, 'Handle customer calls', '2025-09-10 06:14:46'),
(5, 5, 'Dressmaking Basics', 'DRS101', 'Introduction to sewing', 40, 1, 'Operate sewing machine', '2025-09-10 06:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `training_progress`
--

CREATE TABLE `training_progress` (
  `id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `status` enum('not_started','in_progress','completed','failed') DEFAULT 'not_started',
  `attendance_hours` decimal(5,2) DEFAULT 0.00,
  `assessment_score` decimal(5,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','analyst','field_worker','supervisor') NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `department`, `contact_number`, `is_active`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'admin', 'admin@sakses.dswd.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'admin', 'ICT Department', NULL, 1, '2025-09-10 06:11:20', '2025-09-10 06:11:20', NULL),
(2, 'analyst1', 'analyst1@sakses.dswd.gov.ph', 'hashedpw1', 'Anna', 'Lopez', 'analyst', 'Planning', '09171234501', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', NULL),
(3, 'field1', 'field1@sakses.dswd.gov.ph', 'hashedpw2', 'Juan', 'Dela Cruz', 'field_worker', 'Field Ops', '09171234502', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', NULL),
(4, 'field2', 'field2@sakses.dswd.gov.ph', 'hashedpw3', 'Maria', 'Santos', 'field_worker', 'Field Ops', '09171234503', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', NULL),
(5, 'super1', 'super1@sakses.dswd.gov.ph', 'hashedpw4', 'Jose', 'Reyes', 'supervisor', 'Monitoring', '09171234504', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', NULL),
(6, 'analyst2', 'analyst2@sakses.dswd.gov.ph', 'hashedpw5', 'Liza', 'Garcia', 'analyst', 'Research', '09171234505', 1, '2025-09-10 06:14:46', '2025-09-10 06:14:46', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `barangay_id` (`barangay_id`);

--
-- Indexes for table `employment_outcomes`
--
ALTER TABLE `employment_outcomes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `livelihood_programs`
--
ALTER TABLE `livelihood_programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_code` (`program_code`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `ml_predictions`
--
ALTER TABLE `ml_predictions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `program_enrollments`
--
ALTER TABLE `program_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_beneficiary_program` (`beneficiary_id`,`program_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `program_resources`
--
ALTER TABLE `program_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `success_metrics`
--
ALTER TABLE `success_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `training_datasets`
--
ALTER TABLE `training_datasets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_modules`
--
ALTER TABLE `training_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `training_progress`
--
ALTER TABLE `training_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `employment_outcomes`
--
ALTER TABLE `employment_outcomes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `livelihood_programs`
--
ALTER TABLE `livelihood_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ml_predictions`
--
ALTER TABLE `ml_predictions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `program_enrollments`
--
ALTER TABLE `program_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT for table `program_resources`
--
ALTER TABLE `program_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `success_metrics`
--
ALTER TABLE `success_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `training_datasets`
--
ALTER TABLE `training_datasets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_modules`
--
ALTER TABLE `training_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `training_progress`
--
ALTER TABLE `training_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD CONSTRAINT `beneficiaries_ibfk_1` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`);

--
-- Constraints for table `employment_outcomes`
--
ALTER TABLE `employment_outcomes`
  ADD CONSTRAINT `employment_outcomes_ibfk_1` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  ADD CONSTRAINT `employment_outcomes_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `livelihood_programs` (`id`);

--
-- Constraints for table `livelihood_programs`
--
ALTER TABLE `livelihood_programs`
  ADD CONSTRAINT `livelihood_programs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `ml_predictions`
--
ALTER TABLE `ml_predictions`
  ADD CONSTRAINT `ml_predictions_ibfk_1` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  ADD CONSTRAINT `ml_predictions_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `livelihood_programs` (`id`);

--
-- Constraints for table `program_enrollments`
--
ALTER TABLE `program_enrollments`
  ADD CONSTRAINT `program_enrollments_ibfk_1` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  ADD CONSTRAINT `program_enrollments_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `livelihood_programs` (`id`);

--
-- Constraints for table `program_resources`
--
ALTER TABLE `program_resources`
  ADD CONSTRAINT `program_resources_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `livelihood_programs` (`id`);

--
-- Constraints for table `success_metrics`
--
ALTER TABLE `success_metrics`
  ADD CONSTRAINT `success_metrics_ibfk_1` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  ADD CONSTRAINT `success_metrics_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `livelihood_programs` (`id`);

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `training_modules`
--
ALTER TABLE `training_modules`
  ADD CONSTRAINT `training_modules_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `livelihood_programs` (`id`);

--
-- Constraints for table `training_progress`
--
ALTER TABLE `training_progress`
  ADD CONSTRAINT `training_progress_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `program_enrollments` (`id`),
  ADD CONSTRAINT `training_progress_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `training_modules` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
