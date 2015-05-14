
-- insert rule
INSERT INTO `gc_rules` (`label`, `date`, `latlong`, `radius_km`, `is_active`) VALUES ('reitai', '2015-05-10', GeomFromText('POINT(35.673343 139.710388)'), 100, 1);

-- show storage
select round(sum(data_length)/1024/1024) as 'data_volume(MB)' from information_schema.tables where table_schema = 'gc_tweets';

select table_name, round(data_length/1024/1024, 2) as 'data_size(MB)', round(index_length/1024/1024, 2) as 'index_size(MB)' from information_schema.tables;

-- 
DELETE FROM gc_tweets WHERE id in ( SELECT id FROM (SELECT id FROM gc_tweets GROUP BY tweet_id HAVING COUNT(*) >= 2) AS x );

DELETE FROM table_name1 WHERE id in ( SELECT id FROM (SELECT id FROM table_name1 GROUP BY column1 HAVING COUNT(*) >= 2) AS x )

-- id3 から id4
UPDATE gc_tweets SET rule_id = 2 WHERE rule_id = 3;
