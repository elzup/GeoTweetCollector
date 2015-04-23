CREATE TABLE gc_tweets (
    id int auto_increment primary key,
    tweet_id long,
    tweet_user_id long,
    `text` text,
    latlong geometry,
    rule_id int,
    timestamp timestamp
);

CREATE TABLE gc_rules (
    id int auto_increment primary key,
    label varchar(20),
    `date` date,
    latlong geometry,
    radius_km int,
    is_active boolean
);

https://www.google.co.jp/maps/place/東京都庁/,17z/data=!3m1!4b1!4m2!3m1!1s0x60188cd4b71a37a1:0xf1665c37f38661e8?hl=ja
INSERT INTO gc_rules (label, date, latlong, radius_km, is_active) VALUES
('first', '2015-04-22', GeomFromText('POINT(35.689634 139.692101)'), 100, 1);
