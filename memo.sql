
-- insert rule
INSERT INTO `gc_rules` (`label`, `date`, `latlong`, `radius_km`, `is_active`) VALUES ('reitai', '2015-05-10', GeomFromText('POINT(35.673343 139.710388)'), 100, 1);

-- show storage
select round(sum(data_length)/1024/1024) as 'data_volume(MB)' from information_schema.tables where table_schema = 'gc_tweets';

select table_name, round(data_length/1024/1024, 2) as 'data_size(MB)', round(index_length/1024/1024, 2) as 'index_size(MB)' from information_schema.tables;
