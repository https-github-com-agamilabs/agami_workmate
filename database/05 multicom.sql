-- com_timezone(timezoneno,timezonetitle)
CREATE TABLE com_timezone(
    timezoneno INT not null,
    timezonetitle varchar(50) not null,
    primary key(timezoneno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO com_timezone (timezoneno, timezonetitle) VALUES
(1, 'Asia/Dhaka'),
(2, 'Europe/London');

-- com_modules(moduleno,moduletitle)
CREATE TABLE com_modules(
    moduleno tinyint not null,
    moduletitle varchar(50) not null,
    primary key(moduleno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO com_modules (moduleno, moduletitle) VALUES
(1, 'ALL MODULES'),
(2, 'TIME-KEEPER MODULE'),
(3, 'TASK MODULE');

-- com_orgprivacy (id, privacytext)
CREATE TABLE com_orgprivacy (
  id tinyint NOT NULL,
  privacytext varchar(15) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO com_orgprivacy (id, privacytext) VALUES
(1, 'Public'),
(2, 'Private');

-- com_orgtype (orgtypeid,orgtypename,typetag,iconurl)
CREATE TABLE com_orgtype (
  orgtypeid int NOT NULL,
  orgtypename varchar(63) NOT NULL,
  typetag varchar(255) DEFAULT NULL,
  iconurl varchar(255) DEFAULT NULL,
  PRIMARY KEY(orgtypeid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO com_orgtype (orgtypeid, orgtypename, typetag, iconurl) VALUES
(10, 'Agriculture and Farming', '', ''),
(20, 'Mining and Extraction', '', ''),
(30, 'Manufacturing', '', ''),
(40, 'Construction and Engineering', '', ''),
(50, 'Retail', '', ''),
(60, 'Technology', '', ''),
(70, 'Finance', '', ''),
(80, 'Healthcare', '', ''),
(90, 'Education and Training', '', ''),
(100, 'Transportation and Logistics', '', ''),
(110, 'Real Estate', '', ''),
(120, 'Tourism and Travel Agency', '', ''),
(130, 'Energy', '', ''),
(140, 'Entertainment and Media', '', ''),
(150, 'Telecommunications', '', ''),
(160, 'Automotive', '', ''),
(170, 'Fashion and Apparel', '', ''),
(180, 'Consulting and Professional Services', '', ''),
(190, 'Nonprofit and Social Services', '', ''),
(200, 'Fitness and Wellness', '', ''),
(210, 'Beauty and Cosmetics','',''),
(220, 'Environmental Services','','');

-- com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, primarycontact, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
CREATE TABLE com_orgs (
    orgno int NOT NULL AUTO_INCREMENT,
    orgname varchar(50) NOT NULL,
    street varchar(100) DEFAULT NULL,
    city varchar(30) DEFAULT NULL,
    state varchar(30) DEFAULT NULL,
    country varchar(30) DEFAULT NULL,
    gpslat DECIMAL(11,8) DEFAULT NULL,
    gpslon DECIMAL(11,8) DEFAULT NULL,
    orgtypeid int NOT NULL,
    privacy tinyint DEFAULT 2,
    picurl varchar(255) DEFAULT NULL,
    primarycontact char(15) NOT NULL,
    orgnote varchar(255) DEFAULT NULL,
    weekend1 enum('SAT','SUN','MON','TUE','WED','THU','FRI','') DEFAULT NULL,
    weekend2 enum('SAT','SUN','MON','TUE','WED','THU','FRI','') DEFAULT NULL,
    starttime time DEFAULT NULL,
    endtime time DEFAULT NULL,
    verifiedno int(11) DEFAULT '0',
    createdat DATETIME DEFAULT CURRENT_TIMESTAMP,
    createdby INT DEFAULT NULL,
    PRIMARY KEY(orgno),
    CONSTRAINT fk_orgs_orgtypeid FOREIGN KEY (orgtypeid) REFERENCES com_orgtype (orgtypeid) ON UPDATE CASCADE,
    CONSTRAINT fk_orgs_privacy FOREIGN KEY (privacy) REFERENCES com_orgprivacy (id) ON UPDATE CASCADE,
    CONSTRAINT fk_userorgs_createdby FOREIGN KEY (createdby) REFERENCES hr_user (userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, primarycontact, orgnote, weekend1, weekend2, starttime, endtime, verifiedno) VALUES
(1, 'AGAMiLabs', 'NK Bhaban, CU Road #1, Hathazari', 'Chattogram', 'Chattogram', 'Bangladesh', '22.4741655', '91.8079191', 60, 1, 'agami_logo.png', '+8801711308141', 'Note here', NULL, NULL, '08:00:00', '22:00:00', 1);


CREATE TABLE com_timeflexsettings (
    timeflexno tinyint AUTO_INCREMENT,
    timeflextitle VARCHAR(63) DEFAULT NULL,
    PRIMARY KEY(timeflexno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Time Flexibility: 1 (Flexible), 2 (Encourage Scheduling), 3 (Strict Time-frame)
INSERT INTO com_timeflexsettings(timeflexno,timeflextitle) VALUES
(1,'Flexible'),
(2,'Encourage Scheduling'),
(3,'Strict Timeframe');

CREATE TABLE com_shiftsettings (
    shiftno tinyint AUTO_INCREMENT,
    shifttitle VARCHAR(63) DEFAULT NULL,
    starttime TIME DEFAULT '9:00:00',
    endtime TIME DEFAULT '18:00:00',
    PRIMARY KEY(shiftno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Time Flexibility: 1 (Flexible), 2 (Encourage Scheduling), 3 (Strict Time-frame)
INSERT INTO com_shiftsettings(shiftno,shifttitle,starttime,endtime) VALUES
(1,'One Shift','09:00:00','18:00:00'),
(2,'3S: Morning Shift','06:00:00','14:00:00'),
(3,'3S: Day Shift','14:00:00','22:00:00'),
(4,'3S: Night Shift','22:00:00','06:00:00'),
(5,'2S: Morning Shift','06:00:00','14:00:00'),
(6,'2S: Night Shift','14:00:00','22:00:00');

-- com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,designation,hourlyrate,monthlysalary,permissionlevel,dailyworkinghour,timeflexibility,shiftno,starttime,endtime,timezone,isactive)
CREATE TABLE com_userorg (
    uono INT AUTO_INCREMENT,
    orgno int NOT NULL,
    userno int NOT NULL,
    uuid VARCHAR(255) DEFAULT NULL,
    ucatno int DEFAULT 1,
    supervisor int DEFAULT NULL,
    moduleno tinyint DEFAULT NULL,
    designation varchar(63) DEFAULT NULL,
    hourlyrate DECIMAL(6,2) DEFAULT NULL,
    monthlysalary DECIMAL(12,2) DEFAULT NULL,
    permissionlevel int DEFAULT NULL,
    dailyworkinghour tinyint DEFAULT 8,
    timeflexibility tinyint DEFAULT 1,
    shiftno tinyint DEFAULT 1,
    starttime TIME DEFAULT '9:00:00',
    endtime TIME DEFAULT '18:00:00',
    timezone VARCHAR(255) DEFAULT 'Asia/Dhaka',
    isactive tinyint DEFAULT 0,
    PRIMARY KEY(uono),
    CONSTRAINT uk_userorg_uuid UNIQUE(uuid),
    CONSTRAINT uk_userorg_orgno_userno UNIQUE (orgno,userno),
    CONSTRAINT fk_userorg_orgno FOREIGN KEY (orgno) REFERENCES com_orgs (orgno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorg_moduleno FOREIGN KEY (moduleno) REFERENCES com_modules (moduleno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorg_ucatno FOREIGN KEY(ucatno) REFERENCES hr_usercat(ucatno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorg_supervisor FOREIGN KEY(orgno,supervisor) REFERENCES com_userorg(orgno,userno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorg_timeflexibility FOREIGN KEY (timeflexibility) REFERENCES com_timeflexsettings (timeflexno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorg_shiftno FOREIGN KEY (shiftno) REFERENCES com_shiftsettings (shiftno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorg_userno FOREIGN KEY (userno) REFERENCES hr_user (userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE com_userorg 
-- CHANGE jobtitle designation VARCHAR(63) DEFAULT NULL;

-- com_authenticationtype(auth_type_id, auth_type_title)

-- INSERT INTO com_authenticationtype(auth_type_id, auth_type_title) VALUES
-- ('RFID','RFID'),
-- ('BIOM','Biometric'),
-- ('BC','Barcode'),
-- ('QR','Quick Response');

-- com_authentication(uuid, auth_type, auth_data)

-- ========== Settings =======================
-- com_settings(setid, settitle)
CREATE TABLE com_settings(
    setid char(10) NOT NULL,
    settitle varchar(127) DEFAULT NULL,
    PRIMARY KEY(setid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO com_settings(setid, settitle) VALUES
('TIME','0 (Flexible), 1 (Schedule), 2 (Strict Timeframe)'),
('TASK','1 (FB-Feed Style), 2 (Tabular)'),
('DIGEST','0 (None), 1 (Daily), 7 (Weekly), 30 (Monthly)');

-- com_orgsettings(orgno,setid, setlabel, fileurl)
CREATE TABLE com_orgsettings(
    orgno INT NOT NULL,
    setid char(10) NOT NULL,
    setlabel varchar(127) DEFAULT NULL,
    fileurl varchar(255) DEFAULT NULL,
    PRIMARY KEY(orgno,setid),
    CONSTRAINT fk_orgsettings_orgno FOREIGN KEY (orgno) REFERENCES com_orgs (orgno) ON UPDATE CASCADE,
    CONSTRAINT fk_orgsettings_setid FOREIGN KEY (setid) REFERENCES com_settings (setid) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ====== EXISTING TABLE MODIFICATION =============
-- change msg_channel
ALTER TABLE msg_channel
ADD COLUMN orgno int DEFAULT NULL,
ADD CONSTRAINT fk_channel_orgno FOREIGN KEY (orgno) REFERENCES com_orgs(orgno) ON UPDATE CASCADE;

-- change emp_workingtime 
ALTER TABLE emp_workingtime
ADD COLUMN orgno int DEFAULT NULL,
ADD CONSTRAINT fk_workingtime_orgno FOREIGN KEY (orgno) REFERENCES com_orgs(orgno) ON UPDATE CASCADE;

ALTER TABLE asp_watchlist
ADD COLUMN orgno int DEFAULT NULL,
ADD CONSTRAINT fk_watchlist_orgno FOREIGN KEY (orgno) REFERENCES com_orgs(orgno) ON UPDATE CASCADE;