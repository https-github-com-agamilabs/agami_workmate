INSERT INTO hr_user(userno,username,firstname,lastname,email,primarycontact,passphrase,isactive,userstatusno)VALUES
(1,'agami','Workmate','Admin','agamilabs@gmail.com','+880 1928718272','$2y$10$zwz0EaC8gN1oLJZN68I.2OeCspGusUqJKrkAjPqUcHezW/rm2y2wu',1,9),
(2,'agamiadmin','AGAMi','Admin','agamilabs@gmail.com','+880 1928718272','$2y$10$zwz0EaC8gN1oLJZN68I.2OeCspGusUqJKrkAjPqUcHezW/rm2y2wu',1,7);


INSERT INTO com_userorg (uono,orgno,userno,uuid,ucatno,moduleno,designation,permissionlevel,timezone,isactive) VALUES
(1,1,1,'Agami!Com@Bd1',19,1,'Agamian',1,'Asia/Dhaka',1),
(2,1,2,'Agami!Com@Bd2',19,1,'Owner',1,'Asia/Dhaka',1);
