DROP TABLE IF EXISTS pre_users ;
CREATE TABLE pre_users (
  id VARCHAR(255) NOT NULL, -- 仮登録ユーザーID（主キー）
  urltoken VARCHAR(255) NOT NULL, -- トークン
  email VARCHAR(255) NOT NULL, -- メールアドレス
  register_date DATETIME NOT NULL, -- 仮登録日時
  PRIMARY KEY (id)
) ;

DROP TABLE IF EXISTS users ;
CREATE TABLE users(
  id VARCHAR(255) NOT NULL, -- ユーザーID（主キー）
  password VARCHAR(255) NOT NULL, -- パスワード
  email VARCHAR(255) NOT NULL, -- メールアドレス
  age INT, -- 年齢
  business_type_id VARCHAR(255), -- 事業種ID
  prefecture_id VARCHAR(255), -- 都道府県ID
  dependents_num INT, -- 扶養人数
  partner_id INT, -- パートナーID
  PRIMARY KEY (id)
) ;

DROP TABLE IF EXISTS earnings ;
CREATE TABLE earnings(
  id INT NOT NULL AUTO_INCREMENT, -- 給与ID（主キー）
  name VARCHAR(255) NOT NULL, -- 給与項目名
  amount INT NOT NULL, -- 給与額
  is_taxation BOOLEAN NOT NULL, -- 課税 or 非課税（課税ならばtrue）
  user_id VARCHAR(255) NOT NULL, -- ユーザーID
  PRIMARY KEY (id)
) ;

DROP TABLE IF EXISTS prefectures ;
CREATE TABLE prefectures(
  id INT NOT NULL AUTO_INCREMENT, -- 都道府県ID（主キー）
  name VARCHAR(255) NOT NULL, -- 都道府県名
  health_insurance_rate FLOAT NOT NULL, -- 健康保険料率
  PRIMARY KEY (id)
) ;

DROP TABLE IF EXISTS cost_items ;
CREATE TABLE cost_items(
  id INT NOT NULL AUTO_INCREMENT, -- 支出項目ID（主キー）
  name VARCHAR(255) NOT NULL, -- 支出項目名
  value INT NOT NULL, -- 支出額
  user_id VARCHAR(255) NOT NULL, -- ユーザーID
  PRIMARY KEY (id)
) ;
