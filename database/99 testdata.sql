INSERT INTO hr_user(userno,username,firstname,lastname,email,primarycontact,passphrase,isactive,userstatusno)
VALUES(1,'agami','Workmate','Admin','agamilabs@gmail.com','+880 1928718272','$2y$10$zwz0EaC8gN1oLJZN68I.2OeCspGusUqJKrkAjPqUcHezW/rm2y2wu',1,9);

INSERT INTO acc_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, contactno, orgnote, weekend1, weekend2, starttime, endtime, verifiedno) VALUES
(1, 'AGAMiLabs', 'NK Bhaban, CU Road #1, Hathazari', 'Chattogram', 'Chattogram', 'Bangladesh', '22.4741655', '91.8079191', 1, 2, 'agami_logo.png', '01711308141', 'Note here', NULL, NULL, '08:00:00', '22:00:00', 1);

INSERT INTO com_userorg (uono,orgno,userno,uuid,ucatno,moduleno,jobtitle,permissionlevel,timezoneno,isactive) VALUES
(1,1,1,'Agami!Com@Bd1',19,1,'Admin',1,1);
