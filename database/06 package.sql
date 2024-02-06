-- pack_coupon(coupon,discount_fixed,discount_percentage,description,max_use,isactive,createdat,createdby)
CREATE TABLE pack_coupon(
    coupon CHAR(15) NOT NULL,
    discount_fixed decimal(12,3) DEFAULT 0.0,
    discount_percentage decimal(12,3) DEFAULT 0.0,
    description varchar(255) DEFAULT NULL,
    max_use INT DEFAULT 1,
    isactive TINYINT DEFAULT 0,
    createdat DATETIME DEFAULT CURRENT_TIMESTAMP,
    createdby int NOT NULL,
    PRIMARY KEY(coupon),
    CONSTRAINT fk_coupon_createdby FOREIGN KEY (createdby) REFERENCES hr_user(userno) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- pack_tag(tag)
CREATE TABLE pack_tag(
   tag varchar(15) NOT NULL,
   primary key(tag)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pack_tag(tag)
VALUES ('Hot Deal'),
('New Offer'),
('Just for You');

-- pack_items(item, itemtitle)
CREATE TABLE pack_items(
   item CHAR(10) NOT NULL,
   itemtitle VARCHAR(255) NOT NULL,
   primary key(item)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pack_items(item, itemtitle) VALUES
('TIME','Time-tracking and Shift Management'),
('TASK','Agile Task Management');

-- pack_offer(offerno, offertitle, offerdetail,users, duration, rate, tag, is_coupon_applicable, validuntil)
CREATE TABLE pack_offer(
   offerno int NOT NULL AUTO_INCREMENT,
   offertitle VARCHAR(127) NOT NULL,
   offerdetail text DEFAULT NULL,
   users tinyint DEFAULT 10,
   duration INT DEFAULT 365,
   rate decimal(12, 3) not null,
   discount decimal(12,3) DEFAULT 0.0,
   tag varchar(15) DEFAULT NULL,
   is_coupon_applicable TINYINT DEFAULT 1,
   validuntil DATETIME DEFAULT NULL,
   primary key(offerno)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- pack_offeritems(offerno,item,qty)
CREATE TABLE pack_offeritems(
  offerno int NOT NULL,
  item CHAR(10) NOT NULL,
  qty INT DEFAULT 1,
  CONSTRAINT uk_offeritems_offerno_item UNIQUE(offerno,item)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- pack_purchaseoffer(purchaseno, offerno, buyeruserno, foruserno, licensekey, entryat, coupon, amount, discount,
--                  txrefidbase, txrefid, paymentID, pgno, ispaid, trxID, paidamount, paidat);
CREATE TABLE pack_purchaseoffer(
  purchaseno int not null AUTO_INCREMENT,
  offerno int not null,
  buyeruserno int NOT NULL,
  foruserno INT DEFAULT NULL,
  licensekey VARCHAR(16) DEFAULT NULL,
  entryat DATETIME default CURRENT_TIMESTAMP,
  coupon CHAR(15) default null,
  amount decimal(12, 3) not null,
  discount decimal(12,3) default 0,
  txrefidbase VARCHAR(63) NOT NULL,
  txrefid VARCHAR(63) DEFAULT NULL,
  paymentID VARCHAR(127) DEFAULT NULL,
  pgno int default null,
  ispaid int default 0,
  trxID varchar(27) default null,
  paidamount decimal(12,3) default null,
  paidat DATETIME default null,
  primary key(purchaseno),
  CONSTRAINT uk_orgpurchaseinvoice_licensekey UNIQUE(licensekey),
  CONSTRAINT fk_orgpurchaseinvoice_offerno FOREIGN KEY (offerno) REFERENCES pack_offer(offerno) ON UPDATE CASCADE,
  CONSTRAINT fk_orgpurchaseinvoice_buyeruserno FOREIGN KEY (buyeruserno) REFERENCES hr_user(userno) ON UPDATE CASCADE,
  CONSTRAINT fk_orgpurchaseinvoice_foruserno FOREIGN KEY (foruserno) REFERENCES hr_user(userno) ON UPDATE CASCADE,
  CONSTRAINT fk_orgpurchaseinvoice_coupon FOREIGN KEY (coupon) REFERENCES pack_coupon(coupon) ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- pack_appliedpackage(appliedno,purchaseno,orgno,starttime, duration,appliedat, appliedby)
CREATE TABLE pack_appliedpackage(
  appliedno INT AUTO_INCREMENT,
  purchaseno INT NOT NULL,
  orgno INT NOT NULL,
  starttime DATETIME DEFAULT NULL,
  duration INT NOT NULL,
  appliedat DATETIME DEFAULT CURRENT_TIMESTAMP,
  appliedby int NOT NULL,
  PRIMARY KEY(appliedno),
  CONSTRAINT uk_appliedpackage_purchaseno UNIQUE(purchaseno),
  CONSTRAINT fk_appliedpackage_purchaseno FOREIGN KEY (purchaseno) REFERENCES pack_purchaseoffer(purchaseno) ON UPDATE CASCADE,
  CONSTRAINT fk_appliedpackage_orgno FOREIGN KEY (orgno) REFERENCES com_orgs(orgno) ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;