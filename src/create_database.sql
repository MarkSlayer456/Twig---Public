create table user(uid int(10) NOT NULL AUTO_INCREMENT, username varchar(32), password varchar(256), usrinfo text, PRIMARY KEY(uid));
alter table user auto_increment=1000;
