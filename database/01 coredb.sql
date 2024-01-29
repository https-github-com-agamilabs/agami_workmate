-- hr_usercat(ucatno, ucattitle)
-- hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
-- msg_channel(channelno,channeltitle,parentchannel)
-- msg_channelmember(channelno, userno, entrytime)

-- emp_workingtime(timeno, empno, workfor, starttime, endtime, comment, isaccepted)
-- msg_lastvisit(userno,channelno,lastvisittime)

SET time_zone = "+06:00";

CREATE TABLE hr_usercat(
	ucatno int NOT NULL,
	ucattitle varchar(255) NOT NULL,
	PRIMARY KEY(ucatno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO hr_usercat(ucatno,ucattitle)
VALUES(1,'Employee (VA)'),
(13,'VA Owner'),
(19,'Admin');

CREATE TABLE hr_userstatus (
	userstatusno int not null,
	userstatustitle VARCHAR(15) not null,
	primary key(userstatusno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO hr_userstatus(userstatusno, userstatustitle)
VALUES
(0, 'Pending'),
(1, 'Active'),
(2, 'Inactive'),
(3, 'Banned'),
(9, 'AGAMian');

CREATE TABLE hr_user(
	userno int AUTO_INCREMENT,
	username varchar(63) NOT NULL,
	firstname varchar(63) NOT NULL,
	lastname varchar(63) DEFAULT NULL,
	photo_url varchar(255) DEFAULT NULL,
	affiliation varchar(127) DEFAULT NULL,
	jobtitle varchar(63) DEFAULT NULL,
	email varchar(255) DEFAULT NULL,
	primarycontact varchar(15) DEFAULT NULL,
	passphrase varchar(255) NOT NULL,
	ucatno int DEFAULT 1,
	supervisor int DEFAULT NULL,
	permissionlevel int DEFAULT NULL,
	createtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	lastupdatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	isactive int DEFAULT 0,
	userstatusno int DEFAULT 1,
	PRIMARY KEY(userno),
	CONSTRAINT uk_user_username UNIQUE(username),
	CONSTRAINT fk_user_ucatno FOREIGN KEY(ucatno) REFERENCES hr_usercat(ucatno) ON UPDATE CASCADE,
	CONSTRAINT fk_user_supervisor FOREIGN KEY(supervisor) REFERENCES hr_user(userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE hr_user
-- ADD COLUMN photo_url varchar(255) DEFAULT NULL;

-- ALTER TABLE hr_user
-- ADD COLUMN userstatusno int DEFAULT 1;

CREATE TABLE msg_channel(
	channelno int AUTO_INCREMENT,
	channeltitle varchar(255) NOT NULL,
	parentchannel int DEFAULT NULL,
	PRIMARY KEY(channelno),
	CONSTRAINT fk_channel_parentchannel FOREIGN KEY(parentchannel) REFERENCES msg_channel(channelno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE msg_channelmember(
	channelno int NOT NULL,
	userno int NOT NULL,
	entrytime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT uk_channelmember_channelno_userno UNIQUE(channelno,userno),
	CONSTRAINT fk_channelmember_channelno FOREIGN KEY(channelno) REFERENCES msg_channel(channelno) ON UPDATE CASCADE,
	CONSTRAINT fk_channelmember_userno FOREIGN KEY(userno) REFERENCES hr_user(userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- emp_workingtime(timeno,empno,workfor,starttime,endtime,comment,isaccepted)
CREATE TABLE emp_workingtime(
	timeno int AUTO_INCREMENT,
	empno int NOT NULL,
	workfor int DEFAULT NULL,
	starttime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	endtime DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	comment varchar(511) DEFAULT NULL,
	isaccepted int DEFAULT 0,
	PRIMARY KEY(timeno),
	CONSTRAINT fk_workingtime_empno FOREIGN KEY(empno) REFERENCES hr_user(userno) ON UPDATE CASCADE,
	CONSTRAINT fk_workingtime_workfor FOREIGN KEY(workfor) REFERENCES hr_user(userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE emp_workingtime
-- MODIFY COLUMN workfor int DEFAULT NULL;

CREATE TABLE msg_lastvisit(
	userno int NOT NULL,
	channelno int NOT NULL,
	lastvisittime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT uk_lastvisit_userno_channelno UNIQUE(userno,channelno)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

