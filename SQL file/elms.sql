CREATE DATABASE IF NOT EXISTS `elms`;
USE `elms`;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', '5c428d8875d2948607f3e3fe134d71b4', '2025-08-11 10:00:00');

CREATE TABLE `tblattendance` (
  `id` int(11) NOT NULL,
  `empid` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `status` enum('Present','Absent','On Leave') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `tblattendance` (`id`, `empid`, `attendance_date`, `in_time`, `out_time`, `status`) VALUES
(1, 1, CURDATE(), '09:28:00', '18:32:00', 'Present');

CREATE TABLE `tbldepartments` (
  `id` int(11) NOT NULL,
  `DepartmentName` varchar(150) DEFAULT NULL,
  `DepartmentShortName` varchar(100) NOT NULL,
  `DepartmentCode` varchar(50) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `tbldepartments` (`id`, `DepartmentName`, `DepartmentShortName`, `DepartmentCode`) VALUES
(1, 'Human Resource', 'HR', 'HR001'),
(2, 'Information Technology', 'IT', 'IT001'),
(3, 'Operations', 'OP', 'OP01');

CREATE TABLE `tblemployees` (
  `id` int(11) NOT NULL,
  `EmpId` varchar(100) NOT NULL,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `EmailId` varchar(200) NOT NULL,
  `Password` varchar(180) NOT NULL,
  `Gender` varchar(100) NOT NULL,
  `Dob` varchar(100) NOT NULL,
  `Department` int(11) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `City` varchar(200) NOT NULL,
  `Country` varchar(150) NOT NULL,
  `Phonenumber` char(11) NOT NULL,
  `Status` int(1) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `tblemployees` (`id`, `EmpId`, `FirstName`, `LastName`, `EmailId`, `Password`, `Gender`, `Dob`, `Department`, `Address`, `City`, `Country`, `Phonenumber`, `Status`) VALUES
(1, 'EMP1080', 'Johnny', 'Doe', 'johnny@gmail.com', 'f925916e2754e5e03f75dd58a5733251', 'Male', '3 February, 1990', 1, '123 Main St', 'New Delhi', 'India', '9876543210', 1),
(2, 'DEMP2132', 'James', 'Smith', 'james@gmail.com', 'f925916e2754e5e03f75dd58a5733251', 'Male', '15 July, 1992', 2, '456 Park Ave', 'Mumbai', 'India', '9876543211', 1);

CREATE TABLE `tblleaves` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(110) NOT NULL,
  `ToDate` date NOT NULL,
  `FromDate` date NOT NULL,
  `Description` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `AdminRemark` mediumtext,
  `AdminRemarkDate` varchar(120) DEFAULT NULL,
  `Status` int(1) NOT NULL,
  `IsRead` int(1) NOT NULL,
  `empid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `tblleaves` (`id`, `LeaveType`, `ToDate`, `FromDate`, `Description`, `Status`, `IsRead`, `empid`) VALUES
(1, 'Casual Leave', '2025-10-30', '2025-10-29', 'Urgent personal work.', 2, 1, 1),
(2, 'Medical Leave', '2025-11-25', '2025-11-21', 'Fever and cold.', 1, 1, 1),
(3, 'Medical Leave', '2025-12-12', '2025-12-08', 'Server migration activity.', 0, 1, 2),
(4, 'Restricted Holiday(RH)', '2025-12-25', '2025-12-25', 'Christmas Holiday.', 1, 1, 1);

CREATE TABLE `tblleavetype` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(200) DEFAULT NULL,
  `Description` mediumtext,
  `CreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `tblleavetype` (`id`, `LeaveType`, `Description`) VALUES
(1, 'Casual Leave', 'Standard Casual Leave'),
(2, 'Medical Leave', 'Leave for medical reasons'),
(3, 'Restricted Holiday(RH)', 'Optional holiday');

ALTER TABLE `admin` ADD PRIMARY KEY (`id`);
ALTER TABLE `tblattendance` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `employee_date` (`empid`,`attendance_date`);
ALTER TABLE `tbldepartments` ADD PRIMARY KEY (`id`);
ALTER TABLE `tblemployees` ADD PRIMARY KEY (`id`);
ALTER TABLE `tblleaves` ADD PRIMARY KEY (`id`), ADD KEY `empid` (`empid`);
ALTER TABLE `tblleavetype` ADD PRIMARY KEY (`id`);

ALTER TABLE `admin` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tblattendance` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tbldepartments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `tblemployees` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `tblleaves` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `tblleavetype` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;