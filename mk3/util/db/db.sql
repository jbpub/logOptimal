
use qvantage;


DROP TABLE IF EXISTS reminder;
CREATE TABLE IF NOT EXISTS `reminder` (
  id INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
  rmndr_date  DATE NOT NULL,
  rmndr_time  TIME NOT NULL,
  rmndr_desc  VARCHAR(255), 
  PRIMARY KEY(id),
  KEY `idx_reminder_rmndr_date_vantage` (`rmndr_date`)
);

DROP TABLE IF EXISTS reminder_weekly;
CREATE TABLE IF NOT EXISTS reminder_weekly
(
  id INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
  weekday INT(1),                 -- (1 = Sunday, 2 = Monday, ., 7 = Saturday).
  rmndr_time  TIME,
  rmndr_desc  VARCHAR(255), 
  PRIMARY KEY(id)
);


DROP TABLE IF EXISTS site_prefs;
CREATE TABLE IF NOT EXISTS site_prefs
(
  id INT(11) NOT NULL,
  rmndr_interval INT NOT NULL,
  rmndr_start_hr INT NOT NULL,
  rmndr_end_hr   INT NOT NULL, 
  session_timeout  INT NOT NULL, 
  PRIMARY KEY(id)
);

DROP TABLE IF EXISTS messages;
CREATE TABLE IF NOT EXISTS `messages` (
  id INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
  msg_sender VARCHAR(255) NOT NULL,
  msg_type CHAR(1) NOT NULL,    -- 0=normal 1=alert
  msg_status CHAR(1) NOT NULL,  -- 0=submitted 1=sent
  msg_time DATETIME NOT NULL,
  msg_text TEXT NOT NULL,
  PRIMARY KEY(id)
);


INSERT INTO reminder ( rmndr_date, rmndr_time, rmndr_desc ) values
('2012-05-05', '8:30:0', "Wake up"),
('2012-05-05', '17:0:0', "Sleep");

INSERT INTO reminder_weekly ( weekday, rmndr_time, rmndr_desc ) values
(1, '8:0:0', "Wake up"),
(2, '18:0:0', "Sleep"),
(7, '6:0:0', "Wake up"),
(7, '18:0:0', "Sleep");

INSERT INTO site_prefs ( id, rmndr_interval, rmndr_start_hr, rmndr_end_hr, session_timeout ) values
(17, 30, 8, 18, 1200),
(18, 30, 8, 18, 1200);
-- INSERT INTO messages ( msg_sender, msg_type, msg_status, msg_time, msg_text ) values
-- ('Mary','0','0', '2012-05-10 23:00:00', "once upon a time there was three bears");


select * from reminder;
select * from reminder_weekly;
select * from site_prefs;


