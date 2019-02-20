-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 20, 2019 at 03:02 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testlana_`
--

-- --------------------------------------------------------

--
-- Table structure for table `accessTypes`
--

CREATE TABLE `accessTypes` (
  `accessTypeID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accessTypes`
--

INSERT INTO `accessTypes` (`accessTypeID`, `name`, `langID`) VALUES
(1, 'View', 1),
(2, 'Edit', 1),
(3, 'Owner', 1),
(4, 'Partial_access', 1),
(5, 'No_access', 1);

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE `actions` (
  `actionID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `actionTypes`
--

CREATE TABLE `actionTypes` (
  `actionTypeID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `categoryID` int(11) NOT NULL,
  `categoryParentID` int(11) NOT NULL,
  `categoryNameID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`categoryID`, `categoryParentID`, `categoryNameID`) VALUES
(1, 0, 1),
(2, 1, 2),
(3, 2, 3),
(4, 0, 4),
(5, 4, 5),
(6, 0, 6);

-- --------------------------------------------------------

--
-- Table structure for table `categoryNames`
--

CREATE TABLE `categoryNames` (
  `categoryNameID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categoryNames`
--

INSERT INTO `categoryNames` (`categoryNameID`, `text`, `langID`) VALUES
(1, 'Category 1', 1),
(1, 'Категория 1', 2),
(1, 'Կատեգորիա 1', 3),
(2, 'Category 1.1', 1),
(2, 'Категория 1.1', 2),
(2, 'Կատեգորիա 1.1', 3),
(3, 'Category 1.1.1', 1),
(3, 'Категория 1.1.1', 2),
(3, 'Կատեգորիա 1.1.1', 3),
(4, 'Category 2', 1),
(4, 'Категория 2', 2),
(4, 'Կատեգորիա 2', 3),
(5, 'Category 2.1', 1),
(5, 'Категория 2.1', 2),
(5, 'Կատեգորիա 2.1', 3),
(6, 'Category 3', 1),
(6, 'Категория 3', 2),
(6, 'Կատեգորիա 3', 3);

-- --------------------------------------------------------

--
-- Table structure for table `category_products`
--

CREATE TABLE `category_products` (
  `categoryID` int(11) NOT NULL,
  `productID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category_products`
--

INSERT INTO `category_products` (`categoryID`, `productID`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 4),
(3, 5),
(4, 6);

-- --------------------------------------------------------

--
-- Table structure for table `ContactInfo`
--

CREATE TABLE `ContactInfo` (
  `contactID` int(11) NOT NULL,
  `paramID` int(11) NOT NULL,
  `paramValueID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contactStructure`
--

CREATE TABLE `contactStructure` (
  `contactID` int(11) NOT NULL,
  `parentID` int(11) NOT NULL,
  `contactTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contactTypes`
--

CREATE TABLE `contactTypes` (
  `contactTypeID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `icons`
--

CREATE TABLE `icons` (
  `iconID` int(11) NOT NULL,
  `icon` text NOT NULL,
  `iconTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `icons`
--

INSERT INTO `icons` (`iconID`, `icon`, `iconTypeID`) VALUES
(1, 'contacts-icon.png', 2),
(2, 'store-icon.png', 2),
(3, 'users-icon.png', 2),
(4, 'portal-icon.png', 2),
(5, 'mailing-icon.png', 2),
(6, 'meeting-icon.png', 2);

-- --------------------------------------------------------

--
-- Table structure for table `iconTypes`
--

CREATE TABLE `iconTypes` (
  `iconTypeID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `iconTypes`
--

INSERT INTO `iconTypes` (`iconTypeID`, `name`, `langID`) VALUES
(1, 'class', 1),
(2, 'image', 2);

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `langID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `shortName` varchar(50) NOT NULL,
  `flag` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`langID`, `name`, `shortName`, `flag`) VALUES
(1, 'English', 'ENG', 'flag_uk.jpg'),
(2, 'Russian', 'RUS', 'flag_ru.png'),
(3, 'Armenian', 'ARM', 'flag_hy.png');

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

CREATE TABLE `list` (
  `listID` int(11) NOT NULL,
  `value` text NOT NULL,
  `langID` int(11) NOT NULL,
  `isDefault` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `userID` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `actionTypeID` int(11) NOT NULL,
  `actionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `loggedUsers`
--

CREATE TABLE `loggedUsers` (
  `userID` int(11) NOT NULL,
  `lastAction` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `loggedUsers`
--

INSERT INTO `loggedUsers` (`userID`, `lastAction`, `token`) VALUES
(1, '2019-02-20 14:26:12', '5T5RNiCNTMyZ');

-- --------------------------------------------------------

--
-- Table structure for table `loginSettings`
--

CREATE TABLE `loginSettings` (
  `settingsID` int(11) NOT NULL,
  `settingsNameID` int(11) NOT NULL,
  `value` text NOT NULL,
  `defaultValue` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `moduleAccess`
--

CREATE TABLE `moduleAccess` (
  `moduleID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `accessTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `moduleAccess`
--

INSERT INTO `moduleAccess` (`moduleID`, `userID`, `accessTypeID`) VALUES
(1, 1, 3),
(2, 1, 3),
(3, 1, 3),
(4, 1, 3),
(5, 1, 3),
(6, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `moduleNames`
--

CREATE TABLE `moduleNames` (
  `moduleNameID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL,
  `alias` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `moduleNames`
--

INSERT INTO `moduleNames` (`moduleNameID`, `name`, `langID`, `alias`) VALUES
(1, 'Contacts', 1, 'Contacts'),
(1, 'Контакты', 2, 'Contacts'),
(1, 'Կոնտակտներ', 3, 'Contacts'),
(2, 'Store', 1, 'Store'),
(2, 'Склад', 2, 'Store'),
(2, 'Պահեստ', 3, 'Store'),
(3, 'Users', 1, 'Users'),
(3, 'Пользователи', 2, 'Users'),
(3, 'Օգտագործողներ', 3, 'Users'),
(4, 'Portal', 1, 'Portal'),
(4, 'Портал', 2, 'Portal'),
(4, 'Պորտալ', 3, 'Portal'),
(5, 'Mailing', 1, 'Mailing'),
(5, 'Почтовое отправление', 2, 'Mailing'),
(5, 'Փոստ', 3, 'Mailing'),
(6, 'Meeting', 1, 'Meeting'),
(6, 'Обсуждение', 2, 'Meeting'),
(6, 'Քննարկում', 3, 'Meeting');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `moduleID` int(11) NOT NULL,
  `moduleNameID` int(11) NOT NULL,
  `moduleIconID` int(11) NOT NULL,
  `top` varchar(50) NOT NULL,
  `center` varchar(50) NOT NULL,
  `bottom` varchar(50) NOT NULL,
  `templateID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`moduleID`, `moduleNameID`, `moduleIconID`, `top`, `center`, `bottom`, `templateID`) VALUES
(1, 1, 1, '0', '0', '0', 0),
(2, 2, 2, '0', '0', '0', 0),
(3, 3, 3, '0', '0', '0', 0),
(4, 4, 4, '0', '0', '0', 0),
(5, 5, 5, '0', '0', '0', 0),
(6, 6, 6, '0', '0', '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `paramAccess`
--

CREATE TABLE `paramAccess` (
  `paramID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paramNames`
--

CREATE TABLE `paramNames` (
  `paramNameID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `params`
--

CREATE TABLE `params` (
  `paramID` int(11) NOT NULL,
  `paramAlias` varchar(50) NOT NULL,
  `paramNameID` int(11) NOT NULL,
  `paramTypeID` int(11) NOT NULL,
  `paramSearchID` int(11) NOT NULL,
  `listID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paramSearch`
--

CREATE TABLE `paramSearch` (
  `paramSearchID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paramTypes`
--

CREATE TABLE `paramTypes` (
  `paramTypeID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `templateID` int(11) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paramValues`
--

CREATE TABLE `paramValues` (
  `paramValueID` int(11) NOT NULL,
  `value` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `productDescription`
--

CREATE TABLE `productDescription` (
  `productDescID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `productDescription`
--

INSERT INTO `productDescription` (`productDescID`, `name`, `langID`) VALUES
(1, 'TV production by Ergo 32`1', 1),
(1, 'Телепроизводство Ergo 32`1', 2),
(1, 'Էրգո հեռուստաընկերություն 32`1', 3),
(2, 'TV production by Ergo 32`2', 1),
(2, 'Телепроизводство Ergo 32`2', 2),
(2, ' Էրգո հեռուստաընկերություն 32`2', 3),
(3, 'TV production by Ergo 43`1', 1),
(3, 'Телепроизводство Ergo 43`1', 2),
(3, 'Էրգո հեռուստաընկերություն 43`1', 3),
(4, 'TV production by Ergo 43`2', 1),
(4, 'Телепроизводство Ergo 43`2', 2),
(4, 'Էրգո հեռուստաընկերություն 43`2', 3),
(5, 'TV production by Ergo 43`3', 1),
(5, 'Телепроизводство Ergo 43`3', 2),
(5, 'Էրգո հեռուստաընկերություն 43`3', 3),
(6, 'TV production by Ergo 55`1', 1),
(6, 'Телепроизводство Ergo 55`1', 2),
(6, 'Էրգո հեռուստաընկերություն 55`1', 3);

-- --------------------------------------------------------

--
-- Table structure for table `productInfo`
--

CREATE TABLE `productInfo` (
  `productID` int(11) NOT NULL,
  `productParamNameID` int(11) NOT NULL,
  `productParamValueID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `productNames`
--

CREATE TABLE `productNames` (
  `productNameID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `productNames`
--

INSERT INTO `productNames` (`productNameID`, `name`, `langID`) VALUES
(1, 'Ergo LE32CT5000AK', 1),
(1, 'Эрго LE32CT5000AK', 2),
(1, 'Էրգո LE32CT5000AK', 3),
(2, 'Ergo LE32CT5500AK', 1),
(2, 'Эрго LE32CT5500AK', 2),
(2, 'Էրգո LE32CT5500AK', 3),
(3, 'Ergo LE43CT2000AK', 1),
(3, 'Эрго LE43CT2000AK', 2),
(3, 'Էրգո LE43CT2000AK', 3),
(4, 'Ergo LE43CT3500AK', 1),
(4, 'Эрго LE43CT3500AK', 2),
(4, 'Էրգո LE43CT3500AK', 3),
(5, 'Ergo LE43CT5500AK', 1),
(5, 'Эрго LE43CT5500AK', 2),
(5, 'Էրգո LE43CT5500AK', 3),
(6, 'Ergo LE55CT2000AK', 1),
(6, 'Эрго LE55CT2000AK', 2),
(6, 'Էրգո LE55CT2000AK', 3);

-- --------------------------------------------------------

--
-- Table structure for table `productParamNames`
--

CREATE TABLE `productParamNames` (
  `productParamNameID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `productParamValues`
--

CREATE TABLE `productParamValues` (
  `productParamValueID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productID` int(11) NOT NULL,
  `part_number` varchar(30) NOT NULL,
  `productNameID` int(11) NOT NULL,
  `productDescID` int(11) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`productID`, `part_number`, `productNameID`, `productDescID`, `count`) VALUES
(1, 'TS-001', 1, 1, 20),
(2, 'TS-002', 2, 2, 150),
(3, 'TS-003', 3, 3, 200),
(4, 'TS-004', 4, 4, 100),
(5, 'TS-005', 5, 5, 50),
(6, 'TS-006', 6, 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `settingsNames`
--

CREATE TABLE `settingsNames` (
  `settingsNameID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `templateID` int(11) NOT NULL,
  `accessTypeID` int(11) NOT NULL,
  `template` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userInfo`
--

CREATE TABLE `userInfo` (
  `userID` int(11) NOT NULL,
  `userParamNameID` int(11) NOT NULL,
  `userParamValueID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `userInfo`
--

INSERT INTO `userInfo` (`userID`, `userParamNameID`, `userParamValueID`) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3),
(1, 4, 4),
(1, 5, 5),
(1, 6, 7),
(1, 7, 8),
(1, 8, 9);

-- --------------------------------------------------------

--
-- Table structure for table `userParamNames`
--

CREATE TABLE `userParamNames` (
  `userParamNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `userParamNames`
--

INSERT INTO `userParamNames` (`userParamNameID`, `langID`, `text`) VALUES
(1, 1, 'name'),
(2, 1, 'lastname'),
(3, 1, 'user_photo'),
(4, 1, 'company'),
(5, 1, 'photo'),
(6, 1, 'address'),
(7, 1, 'logotype'),
(8, 1, 'position');

-- --------------------------------------------------------

--
-- Table structure for table `userParamValues`
--

CREATE TABLE `userParamValues` (
  `userParamValueID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `userParamValues`
--

INSERT INTO `userParamValues` (`userParamValueID`, `text`) VALUES
(1, 'Xaxaski'),
(2, '1234'),
(3, 'images/upload/155.jpg'),
(4, 'dogs corparation'),
(5, 'images/no-photo'),
(7, 'ara sargsyan 10/1'),
(8, 'images/upload/logo.png'),
(9, 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `isEnable` int(11) NOT NULL,
  `email` text NOT NULL,
  `host` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `username`, `password`, `isEnable`, `email`, `host`) VALUES
(1, 'admin', '098f6bcd4621d373cade4e832627b4f6', 0, 'lshirmazanyan@gmail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendorID` int(11) NOT NULL,
  `vendor_name` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendorID`, `vendor_name`, `langID`) VALUES
(1, 'Vendor name', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessTypes`
--
ALTER TABLE `accessTypes`
  ADD PRIMARY KEY (`accessTypeID`);

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`actionID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `actionTypes`
--
ALTER TABLE `actionTypes`
  ADD PRIMARY KEY (`actionTypeID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryID`);

--
-- Indexes for table `categoryNames`
--
ALTER TABLE `categoryNames`
  ADD PRIMARY KEY (`categoryNameID`,`langID`);

--
-- Indexes for table `ContactInfo`
--
ALTER TABLE `ContactInfo`
  ADD PRIMARY KEY (`contactID`);

--
-- Indexes for table `contactStructure`
--
ALTER TABLE `contactStructure`
  ADD PRIMARY KEY (`contactID`);

--
-- Indexes for table `contactTypes`
--
ALTER TABLE `contactTypes`
  ADD PRIMARY KEY (`contactTypeID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `icons`
--
ALTER TABLE `icons`
  ADD PRIMARY KEY (`iconID`);

--
-- Indexes for table `iconTypes`
--
ALTER TABLE `iconTypes`
  ADD PRIMARY KEY (`iconTypeID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`langID`);

--
-- Indexes for table `list`
--
ALTER TABLE `list`
  ADD PRIMARY KEY (`listID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `loggedUsers`
--
ALTER TABLE `loggedUsers`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `moduleAccess`
--
ALTER TABLE `moduleAccess`
  ADD PRIMARY KEY (`moduleID`);

--
-- Indexes for table `moduleNames`
--
ALTER TABLE `moduleNames`
  ADD PRIMARY KEY (`moduleNameID`,`langID`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`moduleID`);

--
-- Indexes for table `paramAccess`
--
ALTER TABLE `paramAccess`
  ADD PRIMARY KEY (`paramID`),
  ADD UNIQUE KEY `userID` (`userID`);

--
-- Indexes for table `paramNames`
--
ALTER TABLE `paramNames`
  ADD PRIMARY KEY (`paramNameID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `params`
--
ALTER TABLE `params`
  ADD PRIMARY KEY (`paramID`),
  ADD UNIQUE KEY `paramAlias` (`paramAlias`);

--
-- Indexes for table `paramSearch`
--
ALTER TABLE `paramSearch`
  ADD PRIMARY KEY (`paramSearchID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `paramTypes`
--
ALTER TABLE `paramTypes`
  ADD PRIMARY KEY (`paramTypeID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `paramValues`
--
ALTER TABLE `paramValues`
  ADD PRIMARY KEY (`paramValueID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `productDescription`
--
ALTER TABLE `productDescription`
  ADD PRIMARY KEY (`productDescID`,`langID`);

--
-- Indexes for table `productNames`
--
ALTER TABLE `productNames`
  ADD PRIMARY KEY (`productNameID`,`langID`);

--
-- Indexes for table `productParamNames`
--
ALTER TABLE `productParamNames`
  ADD PRIMARY KEY (`productParamNameID`,`langID`);

--
-- Indexes for table `productParamValues`
--
ALTER TABLE `productParamValues`
  ADD PRIMARY KEY (`productParamValueID`,`langID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`,`part_number`);

--
-- Indexes for table `settingsNames`
--
ALTER TABLE `settingsNames`
  ADD PRIMARY KEY (`settingsNameID`),
  ADD UNIQUE KEY `langID` (`langID`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`templateID`),
  ADD UNIQUE KEY `accessTypeID` (`accessTypeID`);

--
-- Indexes for table `userParamNames`
--
ALTER TABLE `userParamNames`
  ADD PRIMARY KEY (`userParamNameID`,`langID`);

--
-- Indexes for table `userParamValues`
--
ALTER TABLE `userParamValues`
  ADD PRIMARY KEY (`userParamValueID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `host` (`host`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendorID`,`langID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `categoryNames`
--
ALTER TABLE `categoryNames`
  MODIFY `categoryNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `moduleNames`
--
ALTER TABLE `moduleNames`
  MODIFY `moduleNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `productDescription`
--
ALTER TABLE `productDescription`
  MODIFY `productDescID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `productNames`
--
ALTER TABLE `productNames`
  MODIFY `productNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `productParamNames`
--
ALTER TABLE `productParamNames`
  MODIFY `productParamNameID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `userParamValues`
--
ALTER TABLE `userParamValues`
  MODIFY `userParamValueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
