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

-- com_userorgmodules (uuid,orgno, userno, moduleno, verified)
CREATE TABLE com_userorgmodules (
    uuid VARCHAR(15) NOT NULL,
    userno int NOT NULL,
    orgno int NOT NULL,
    moduleno tinyint NOT NULL,
    isactive tinyint DEFAULT 0,
    CONSTRAINT uk_userorgmodules_uuid UNIQUE(uuid),
    CONSTRAINT uk_userorgmodules_orgno_userno UNIQUE (orgno,userno),
    CONSTRAINT fk_userorgmodules_orgno FOREIGN KEY (orgno) REFERENCES com_orgs (orgno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorgmodules_moduleno FOREIGN KEY (moduleno) REFERENCES com_modules (moduleno) ON UPDATE CASCADE,
    CONSTRAINT fk_userorgmodules_userno FOREIGN KEY (userno) REFERENCES hr_user (userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
('TIME','Time Flexibility: 0 (Flexible), 1 (Encourage Scheduling), 2 (Strict Time-frame)'),
('TASK','1 (FB-Feed Style), 2 (Tabular)');

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