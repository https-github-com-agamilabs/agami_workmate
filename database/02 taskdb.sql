-- asp_color(colorno, code, title)
-- asp_storyphase(storyphaseno, storyphasetitle, colorno)
-- asp_workstatus(wstatusno, statustitle, colorno)
-- asp_prioritylevel(prioritylevelno, priorityleveltitle, priorityleveldescription, colorno)

-- asp_channelbacklog(backlogno,channelno,story,storytype,points,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
-- asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,duration,userno)
-- asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,percentile, userno)
-- asp_deadlines(dno,cblscheduleno,deadline,entrytime,userno)

CREATE TABLE asp_filetype(
	filetypeno int AUTO_INCREMENT,
	filetypetitle varchar(255) NOT NULL,
	PRIMARY KEY(filetypeno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO asp_filetype(filetypeno,filetypetitle)
VALUES(1,'Document'),
(2,'Program Source'),
(3,'Application'),
(4,'Image'),
(5,'Audio'),
(6,'Video'),
(7,'Others');

CREATE TABLE asp_color(
    colorno int AUTO_INCREMENT,
    colorcode char(7) NOT NULL,
    colortitle varchar(63) NOT NULL,
    PRIMARY KEY(colorno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO asp_color(colorno, colorcode, colortitle)
VALUES(1, '#CC0000','RED'),
(2, '#FF8000','ORANGE'),
(3, '#FFFF00','YELLOW'),
(4, '#66CC00','GREEN'),
(5, '#00FFFF','OCEAN-BLUE'),
(6, '#0066CC','SKY-BLUE'),
(7, '#0000FF','BLUE'),
(8, '#9933FF','VIOLET'),
(9, '#CC00CC','PINK'),
(10, '#404040','BLACK'),
(11,'#808080','GREY');

CREATE TABLE asp_storyphase(
    storyphaseno int AUTO_INCREMENT,
    storyphasetitle varchar(63) NOT NULL,
    colorno int DEFAULT NULL,
    PRIMARY KEY(storyphaseno),
    CONSTRAINT fk_storyphase_colorno FOREIGN KEY (colorno) REFERENCES asp_color(colorno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO asp_storyphase(storyphaseno,storyphasetitle,colorno)
VALUES(1, 'CONTENT: ILLUSTRATIVE PRESENTATION',6),
(2, 'CONTENT: IMAGE',6),
(3,'CONTENT: VOICE OVER',6),
(4,'CONTENT: MUSIC',6),
(5, 'CONTENT: GRAPHICS',6),
(6, 'CONTENT: ANIMATED',9),
(7, 'WP: WEBSITE DEVELOPMENT',2),
(8, 'WP: WEBSITE MAINTENANCE',2),
(9, 'SEO: WEBSITE',2),
(10, 'MKT: SOCIAL MEDIA MARKETING',4),
(11, 'MKT: EMAIL MARKETING',4),
(12, 'MKT: MESSAGING',4),
(13, 'MKT: PAID DIGITAL ADVERTISING',4),
(14, 'MKT: AI/SPECIAL MARKETING',4),
(15, 'MISC: HYBRID',8),
(16, 'MISC: OTHERS AS SPECIFIED',8);

CREATE TABLE asp_workstatus(
    wstatusno int AUTO_INCREMENT,
    statustitle varchar(63) NOT NULL,
    colorno int DEFAULT NULL,
    PRIMARY KEY(wstatusno),
    CONSTRAINT fk_workstatus_colorno FOREIGN KEY (colorno) REFERENCES asp_color(colorno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO asp_workstatus(wstatusno,statustitle,colorno)VALUES
(1, 'TO-DO / UNASSIGNED',10),
(2, 'IN PROGRESS',2),
(3, 'COMPLETED',8),
(4, 'ABONDONED',1);

CREATE TABLE asp_prioritylevel(
    prioritylevelno int NOT NULL,
    priorityleveltitle varchar(63) NOT NULL,
    priorityleveldescription varchar(255) DEFAULT NULL,
    colorno int DEFAULT NULL,
    PRIMARY KEY(prioritylevelno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO asp_prioritylevel(prioritylevelno,priorityleveltitle,priorityleveldescription, colorno)
VALUES(1, 'VERY URGENT','Stop your other work, do it now', 1),
(2, 'URGENT','Complete your handy work, then start this one',2),
(3, 'NORMAL PRIORITY','We need it eventually',9),
(4, 'LOW PRIORITY','If you have no work, do it.',3),
(5, 'NO PRIORITY','If you have no work, do it.',11);

CREATE TABLE asp_storytype(
    storytypeno int NOT NULL,
    storytypetitle varchar(63) NOT NULL,
    PRIMARY KEY(storytypeno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO asp_storytype(storytypeno,storytypetitle)
VALUES(1, 'Chat'),
(2, 'Notification'),
(3, 'Task');

-- asp_channelbacklog(backlogno,channelno,story,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
-- ========= END SETTINGS ==============
CREATE TABLE asp_channelbacklog(
    backlogno int AUTO_INCREMENT,
    channelno int NOT NULL,
    story VARCHAR(512) NOT NULL,
    storytype int DEFAULT 3,
    points INT DEFAULT 1,
    prioritylevelno int DEFAULT 4,
    relativepriority int DEFAULT 0,
    storyphaseno int NOT NULL,
    parentbacklogno int DEFAULT NULL,
    approved int DEFAULT 0,
    accessibility tinyint DEFAULT 0,
    lastupdatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    userno int NOT NULL,
    PRIMARY KEY(backlogno),
    CONSTRAINT fk_channelbacklog_channelno FOREIGN KEY (channelno) REFERENCES msg_channel(channelno) ON UPDATE CASCADE,
    CONSTRAINT fk_channelbacklog_storyphaseno FOREIGN KEY (storyphaseno) REFERENCES asp_storyphase(storyphaseno) ON UPDATE CASCADE,
    CONSTRAINT fk_channelbacklog_prioritylevelno FOREIGN KEY (prioritylevelno) REFERENCES asp_prioritylevel(prioritylevelno) ON UPDATE CASCADE,
    CONSTRAINT fk_channelbacklog_parentbacklogno FOREIGN KEY (parentbacklogno) REFERENCES asp_channelbacklog(backlogno) ON UPDATE CASCADE,
    CONSTRAINT fk_channelbacklog_userno FOREIGN KEY (userno) REFERENCES hr_user(userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE asp_channelbacklog
-- ADD COLUMN points INT DEFAULT 1;

-- asp_logattachment(attachno,chatno,shorttitle,fileurl,filetypeno,uploadtime)
CREATE TABLE asp_logattachment(
	attachno int AUTO_INCREMENT,
	backlogno int NOT NULL,
	shorttitle varchar(24) DEFAULT NULL,
	fileurl varchar(255) NOT NULL,
	filetypeno int NOT NULL,
	uploadtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(attachno),
	CONSTRAINT fk_logattachment_backlogno FOREIGN KEY(backlogno) REFERENCES asp_channelbacklog(backlogno) ON UPDATE CASCADE,
	CONSTRAINT fk_logattachment_filetypeno FOREIGN KEY(filetypeno) REFERENCES asp_filetype(filetypeno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE asp_cblschedule(
    cblscheduleno int AUTO_INCREMENT,
    backlogno int NOT NULL,
    howto TEXT DEFAULT NULL,
    assignedto int NOT NULL,
    assigntime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    scheduledate Date NOT NULL,
    duration decimal(4,2) DEFAULT 1.0,
    userno int NOT NULL,
    PRIMARY KEY(cblscheduleno),
    CONSTRAINT fk_cblschedule_backlogno FOREIGN KEY (backlogno) REFERENCES asp_channelbacklog(backlogno) ON UPDATE CASCADE,
    CONSTRAINT fk_cblschedule_userno FOREIGN KEY (userno) REFERENCES hr_user(userno) ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE asp_cblschedule
-- DROP COLUMN points;

CREATE TABLE asp_deadlines(
    dno int AUTO_INCREMENT,
    cblscheduleno int NOT NULL,
    deadline Date NOT NULL,
    entrytime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    userno int NOT NULL,
    PRIMARY KEY(dno),
    CONSTRAINT fk_deadlines_cblscheduleno FOREIGN KEY (cblscheduleno) REFERENCES asp_cblschedule(cblscheduleno) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_deadlines_userno FOREIGN KEY (userno) REFERENCES hr_user(userno) ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE asp_cblprogress(
    cblprogressno int AUTO_INCREMENT,
    cblscheduleno int NOT NULL,
    progresstime DateTime DEFAULT CURRENT_TIMESTAMP,
    result TEXT DEFAULT NULL,
    wstatusno int DEFAULT 1,
    percentile INT DEFAULT 0,
    userno int NOT NULL,
    PRIMARY KEY(cblprogressno),
    CONSTRAINT fk_cblprogress_cblscheduleno FOREIGN KEY (cblscheduleno) REFERENCES asp_cblschedule(cblscheduleno) ON UPDATE CASCADE,
    CONSTRAINT fk_cblprogress_userno FOREIGN KEY (userno) REFERENCES hr_user(userno) ON UPDATE CASCADE,
    CONSTRAINT fk_cblprogress_wstatusno FOREIGN KEY (wstatusno) REFERENCES asp_workstatus(wstatusno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE asp_cblprogress
-- ADD COLUMN percentile INT DEFAULT 0;