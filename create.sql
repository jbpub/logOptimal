#
# This is the database initialization sql
# Runas:
#
#     mysql --user=root --password=root <  create.sql
#

CREATE DATABASE IF NOT EXISTS havelock;
use havelock;

DROP TABLE IF EXISTS trades;
CREATE TABLE IF NOT EXISTS trades (
  id INT(11) NOT NULL UNIQUE, 
  fund_code varchar(10) NOT NULL,
  tran_time DATETIME NOT NULL,
  price decimal(10,8) NOT NULL,
  qty INT(11) NOT NULL,
  PRIMARY KEY(id),
  INDEX(fund_code)
);

DROP TABLE IF EXISTS funds;
CREATE TABLE IF NOT EXISTS funds (
  id INT(11) NOT NULL UNIQUE AUTO_INCREMENT, 
# fund_code examples MTN 7C ALC
  fund_code varchar(10) NOT NULL UNIQUE,
# if 1 then trades will be check for this fund_code
  check_trades int(1) DEFAULT 0,
  PRIMARY KEY(id)
);
