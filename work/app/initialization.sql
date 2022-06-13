DROP TABLE IF EXISTS users ;
CREATE TABLE users(
  id VARCHAR(255) NOT NULL, -- ユーザーID（主キー）
  password VARCHAR(255) NOT NULL, -- パスワード
  email VARCHAR(255) NOT NULL, -- メールアドレス
  age INT, -- 年齢
  prefecture_id INT, -- 都道府県ID
  dependents_num INT, -- 扶養人数
  income INT, -- 給与（額面）
  partner_id INT, -- パートナーID
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
