CREATE TABLE emp_specialdaytype(
	sdtypeid varchar(20) NOT NULL,
	displaytitle varchar(63) DEFAULT NULL,
	minworkinghour decimal(4,2) DEFAULT 0,
	color varchar(27) DEFAULT null,
	PRIMARY KEY(sdtypeid)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO emp_specialdaytype(sdtypeid, displaytitle, minworkinghour, color) VALUES
('WEEK_END', 'Week End', 0, '#794c8a'),
('PUBLIC_specialDAY', 'Public specialday', 0, '#3f6ad8'),
('OTHERS', 'Others', 0, '#16aaff'),
('HOME_OFFICE', 'Home Office (Full day)', 8, '#6610f2'),
('HOME_OFFICE_HALF_DAY', 'Home Office (Half day)', 4, '#6f42c1'),
('HALF_DAY', 'Half Day Office', 4, '#20c997'),
('FULL_DAY', 'Full Day Office', 8, '#17a2b8');

CREATE TABLE emp_specialdays(
	specialdayno int NOT NULL AUTO_INCREMENT,
	specialdate DATE NOT NULL,
	reasontext varchar(50) DEFAULT null,
	sdtypeid varchar(20) NOT NULL,
	minworkinghour decimal(4,2) DEFAULT 0,
	PRIMARY KEY(specialdayno),
	CONSTRAINT fk_specialdays_sdtypeid FOREIGN KEY(sdtypeid) REFERENCES emp_specialdaytype(sdtypeid) ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
