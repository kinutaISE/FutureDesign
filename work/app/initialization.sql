DROP TABLE IF EXISTS users ;
CREATE TABLE users(
  id VARCHAR(255) NOT NULL, -- ユーザーID（主キー）
  password VARCHAR(255) NOT NULL, -- パスワード
  email VARCHAR(255) NOT NULL, -- メールアドレス
  age INT, -- 年齢
  prefecture_id INT, -- 都道府県ID
  dependents_num INT, -- 扶養人数
  anual_income_type ENUM( -- 昨年度の年収価格帯
    'range_1', -- 195万円以下
    'range_2', -- 195万円超、330万円以下
    'range_3', -- 330万円超、695万円以下
    'range_4', -- 695万円超、900万円以下
    'range_5', -- 900万円超、1,800万円以下
    'range_6', -- 1,800万円越、4,000万円以下
    'range_7' -- 4,000万円越
  ),
  income INT, -- 給与（額面）
  partner_id INT, -- パートナーID
  PRIMARY KEY (id)
) ;

DROP TABLE IF EXISTS prefectures ;
CREATE TABLE prefectures(
  id INT NOT NULL AUTO_INCREMENT, -- 都道府県ID（主キー）
  name VARCHAR(255) NOT NULL, -- 都道府県名
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
