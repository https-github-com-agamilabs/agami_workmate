CREATE TABLE emp_holidaytype(
	hdtypeid varchar(20) NOT NULL,
	displaytitle varchar(63) DEFAULT NULL,
	minworkinghour decimal(4,2) DEFAULT 0,
	color varchar(27) DEFAULT null,
	CONSTRAINT pk_holidaytype_hdtypeid PRIMARY KEY(hdtypeid)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO emp_holidaytype(hdtypeid, displaytitle, minworkinghour, color) VALUES
('WEEK_END', 'Week End', 0, '#794c8a'),
('PUBLIC_HOLIDAY', 'Public Holiday', 0, '#3f6ad8'),
('OTHERS', 'Others', 0, '#16aaff'),
('HOME_OFFICE', 'Home Office (Full day)', 8, '#6610f2'),
('HOME_OFFICE_HALF_DAY', 'Home Office (Half day)', 4, '#6f42c1'),
('HALF_DAY', 'Half Day Office', 4, '#20c997');

CREATE TABLE emp_holidays(
	holidayno int NOT NULL AUTO_INCREMENT,
	holidaydate DATE NOT NULL,
	reasontext varchar(50) DEFAULT null,
	hdtypeid varchar(20) NOT NULL,
	minworkinghour decimal(4,2) DEFAULT 0,
	CONSTRAINT pk_holidays_holidayno PRIMARY KEY(holidayno),
	CONSTRAINT fk_holidays_hdtypeid FOREIGN KEY(hdtypeid) REFERENCES emp_holidaytype(hdtypeid) ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE `emp_holidays`
-- ADD `minworkinghour` DECIMAL(4,2) NOT NULL DEFAULT '0' AFTER `hdtypeid`;
