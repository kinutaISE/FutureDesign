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


-- 都道府県情報の追加
INSERT INTO
  prefectures (name, health_insurance_rate)
VALUES
  ("北海道", 0.1039),
  ("青森県", 0.1003),
  ("岩手県", 0.0991),
  ("宮城県", 0.1018),
  ("秋田県", 0.1027),
  ("山形県", 0.0999),
  ("福島県", 0.0965),
  ("茨城県", 0.0977),
  ("栃木県", 0.0990),
  ("群馬県", 0.0973),
  ("埼玉県", 0.0971),
  ("千葉県", 0.0976),
  ("東京都", 0.0981),
  ("神奈川県", 0.0985),
  ("新潟県", 0.0951),
  ("富山県", 0.0961),
  ("石川県", 0.0989),
  ("福井県", 0.0996),
  ("山梨県", 0.0966),
  ("長野県", 0.0967),
  ("岐阜県", 0.0982),
  ("静岡県", 0.0975),
  ("愛知県", 0.0993),
  ("三重県", 0.0991),
  ("滋賀県", 0.0983),
  ("京都府", 0.0995),
  ("大阪府", 0.1022),
  ("兵庫県", 0.1013),
  ("奈良県", 0.0996),
  ("和歌山県", 0.1018),
  ("鳥取県", 0.0994),
  ("島根県", 0.1035),
  ("岡山県", 0.1025),
  ("広島県", 0.1009),
  ("山口県", 0.1015),
  ("徳島県", 0.1043),
  ("香川県", 0.1034),
  ("愛媛県", 0.1026),
  ("高知県", 0.1030),
  ("福岡県", 0.1021),
  ("佐賀県", 0.1100),
  ("長崎県", 0.1047),
  ("熊本県", 0.1045),
  ("大分県", 0.1052),
  ("宮崎県", 0.1014),
  ("鹿児島県", 0.1065),
  ("沖縄県", 0.1009) ;
