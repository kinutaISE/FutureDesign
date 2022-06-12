DROP TABLE IF EXISTS users ;
CREATE TABLE users(
  id VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  anual_incom_type ENUM(
    'range_1', -- 195万円以下
    'range_2', -- 195万円超、330万円以下
    'range_3', -- 330万円超、695万円以下
    'range_4', -- 695万円超、900万円以下
    'range_5', -- 900万円超、1,800万円以下
    'range_6', -- 1,800万円越、4,000万円以下
    'range_7', -- 4,000万円越
  ),
  age INT,
  prefecture_id INT,
  income INT,
  partner_id INT,
  PRIMARY KEY (id)
) ;

DROP TABLE IF EXISTS prefectures ;
CREATE TABLE prefectures(
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ;

DROP TABLE IF EXISTS cost_items ;
CREATE TABLE cost_items(
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  value INT NOT NULL,
  user_id VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ;
