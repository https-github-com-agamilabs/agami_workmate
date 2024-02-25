-- emp_leavetype(leavetypeno,leavetypeshort, leavetypetitle, leavedescription)
CREATE TABLE emp_leavetype(
	leavetypeno int NOT NULL AUTO_INCREMENT,
	leavetypeshort varchar(10) NOT NULL,
	leavetypetitle varchar(50) NOT NULL,
	leavedescription varchar(255) DEFAULT NULL,
	CONSTRAINT pk_leavetype_leavetypeno PRIMARY KEY(leavetypeno)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO emp_leavetype(leavetypeshort, leavetypetitle, leavedescription)VALUES
('CL', 'Casual Leave', 'https://www.greythr.com/leave-management/leave-types/'),
('LWP', 'Leave without Pay', 'https://www.greythr.com/leave-management/leave-types/'),
('EL', 'Earned Leave', 'https://www.greythr.com/leave-management/leave-types/'),
('SL', 'Sick Leave','https://www.greythr.com/leave-management/leave-types/' ),
('UL', 'Urgent Leave', 'https://www.greythr.com/leave-management/leave-types/'),
('ML', 'Maternity Leave', 'https://www.greythr.com/leave-management/leave-types/'),
('MGL', 'Marriage Leave','https://www.greythr.com/leave-management/leave-types/' ),
('PTL', 'Paternity Leave','https://www.greythr.com/leave-management/leave-types/' ),
('BL', 'Bereavement Leave','https://www.greythr.com/leave-management/leave-types/' ),
('Comp-off', 'Compensatory Off', 'https://www.greythr.com/leave-management/leave-types/');

-- emp_leavestatus(leavestatusno,leavestatustitle, leavestatucolor)
CREATE TABLE emp_leavestatus(
	leavestatusno int NOT NULL AUTO_INCREMENT,
	leavestatustitle varchar(20) NOT NULL,
	leavestatucolor varchar(10) NOT NULL,
	CONSTRAINT pk_leavestatus_leavestatusno PRIMARY KEY(leavestatusno)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO emp_leavestatus(leavestatustitle, leavestatucolor)
VALUES
('PENDING', '#f7b924'),
('APPROVED', '#3ac47d'),
('REJECTED', '#d92550'),
('DELECTED', '#6c757d');

-- emp_leaveapplication(lappno,orgno,empno,leavetypeno,reasontext,leavestatusno,actiontakenby,createdatetime,updatetime)
CREATE TABLE emp_leaveapplication(
	lappno int NOT NULL AUTO_INCREMENT,
    orgno int NOT NULL,
	empno int NOT NULL,
	leavetypeno int NOT NULL,
	reasontext text NOT NULL,
	leavestatusno int NOT NULL,
	actiontakenby int NULL,
	createdatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	CONSTRAINT pk_leavestatus_lappno PRIMARY KEY(lappno),
    CONSTRAINT fk_leavestatus_orgno FOREIGN KEY (orgno) REFERENCES com_orgs (orgno) ON UPDATE CASCADE,
	CONSTRAINT fk_leavestatus_empno FOREIGN KEY(empno) REFERENCES hr_user(userno) ON UPDATE CASCADE,
	CONSTRAINT fk_leavestatus_leavetypeno FOREIGN KEY(leavetypeno) REFERENCES emp_leavetype(leavetypeno) ON UPDATE CASCADE,
	CONSTRAINT fk_leavestatus_leavestatusno FOREIGN KEY(leavestatusno) REFERENCES emp_leavestatus(leavestatusno) ON UPDATE CASCADE,
	CONSTRAINT fk_leavestatus_actiontakenby FOREIGN KEY(actiontakenby) REFERENCES hr_user(userno) ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE emp_leaveapplication
-- ADD COLUMN orgno int NOT NULL AFTER lappno,
-- ADD CONSTRAINT fk_leavestatus_orgno FOREIGN KEY (orgno) REFERENCES com_orgs (orgno) ON UPDATE CASCADE;

-- emp_leavedates(lappno,leavedate)
CREATE TABLE emp_leavedates(
	lappno int NOT NULL,
	leavedate DATE NOT NULL,
	CONSTRAINT fk_leavedates_lappno FOREIGN KEY(lappno) REFERENCES emp_leaveapplication(lappno) ON UPDATE CASCADE,
	CONSTRAINT uk_leavedates_lappno_leavedate UNIQUE(lappno, leavedate)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
