-- hr_designationsetting(desigid,desigtitle,isactive)
CREATE TABLE hr_designationsetting(
	desigid INT NOT NULL,
	desigtitle varchar(63) NOT NULL,
	isactive tinyint DEFAULT 1,
	PRIMARY KEY(desigid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO hr_designationsetting(desigid,desigtitle,isactive) VALUES
(7101,'Virtual Assistant',1);

-- emp_kpisetting(kpino,kpititle,measureunit,indicator)
CREATE TABLE emp_kpisetting(
	kpino INT NOT NULL,
	kpititle varchar(63) NOT NULL,
    measureunit varchar(15) NOT NULL,
	indicator TINYINT NOT NULL,
	PRIMARY KEY(kpino)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO emp_kpisetting(kpino,kpititle,measureunit,indicator) VALUES
(101,'Working Hour','Hour',1),
(102,'Task Completion (intime)','Qty',1),
(103,'Task Completion with delay','Qty',1),
(104,'Average First Response Time (Ref.3)','Number',10),
(105,'Client Satisfaction','Star',1),
(501,'Uninformed Leave','Day',-1),
(502,'Accumulation of Daywise Deficit Time','Hour',-1),
(503,'Client Objection','Qty',-1),
(509,'CLIFE (Core-value) Violation','Score',-1);

-- emp_kpitarget(desigid,paylevelno,kpino,milestone,nscore)
CREATE TABLE emp_kpitarget(
	desigid INT NOT NULL,
    paylevelno TINYINT DEFAULT 1,
    kpino INT NOT NULL,
    milestone DECIMAL(9,2) NOT NULL,
	nscore DECIMAL(5,2) DEFAULT 10.00,
    CONSTRAINT uk_kpitarget_designno_paylevelno_kpino UNIQUE(desigid,paylevelno,kpino),
	CONSTRAINT fk_kpitarget_desigid FOREIGN KEY(desigid) REFERENCES hr_designationsetting(desigid) ON UPDATE CASCADE,
    CONSTRAINT fk_kpitarget_kpino FOREIGN KEY(kpino) REFERENCES emp_kpisetting(kpino) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO emp_kpitarget(desigid,paylevelno,kpino,milestone,nscore) VALUES
(7101,1,101,20,1),
(7101,1,102,22,1),
(7101,1,103,0,0),
(7101,1,104,3,10),
(7101,1,105,5,5),
(7101,1,501,0,-5),
(7101,1,502,0,-1),
(7101,1,503,0,-10),
(7101,1,509,0,-10);

-- emp_designation(userno,desigid,paylevelno,joiningdate,enddate
CREATE TABLE emp_designation(
    userno INT NOT NULL,
	desigid INT NOT NULL,
    paylevelno TINYINT DEFAULT 1,
	basicsalary INT DEFAULT 3000,
	timecategory TINYINT DEFAULT 1, -- 1=FULLTIME, 2=4HOURS
    joiningdate DATE NOT NULL,
    enddate DATE DEFAULT NULL,
	CONSTRAINT uk_emp_designation_userno_paylevelno_designno UNIQUE(userno,paylevelno,desigid),
    CONSTRAINT fk_emp_designation_userno FOREIGN KEY(userno) REFERENCES hr_user(userno) ON UPDATE CASCADE,
	CONSTRAINT fk_emp_designation_desigid FOREIGN KEY(desigid) REFERENCES hr_designationsetting(desigid) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE emp_designation
-- ADD COLUMN basicsalary INT DEFAULT 3000,
-- ADD COLUMN timecategory TINYINT DEFAULT 1;

-- emp_kpiscore(kpiscoreno,empno,paylevelno,desigid,kpino,score,comment,createtime,lastupdatetime,editcount,createdby)
CREATE TABLE emp_kpiscore(
	kpiscoreno int AUTO_INCREMENT,
	empno int NOT NULL,
    paylevelno TINYINT DEFAULT 1,
    desigid INT NOT NULL,
	kpino int NOT NULL,
	score  int NOT NULL,
	comment varchar(511) NOT NULL,
	createtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	lastupdatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	editcount int DEFAULT 0,
	createdby int NOT NULL,
	PRIMARY KEY(kpiscoreno),
	CONSTRAINT fk_kpiscore_empno FOREIGN KEY(empno) REFERENCES hr_user(userno) ON UPDATE CASCADE,
    CONSTRAINT fk_kpiscore_desigid FOREIGN KEY(desigid) REFERENCES hr_designationsetting(desigid) ON UPDATE CASCADE,
	CONSTRAINT fk_kpiscore_kpino FOREIGN KEY(kpino) REFERENCES emp_kpisetting(kpino) ON UPDATE CASCADE,
	CONSTRAINT fk_kpiscore_createdby FOREIGN KEY(createdby) REFERENCES hr_user(userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
