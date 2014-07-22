create database XXXXXX;
grant all on XXXXXX.* to YYYYYY@localhost identified by 'ZZZZZZZZ';
use XXXXXX

create table users (
    id int not null auto_increment primary key,
    instagram_user_id int unique,
    instagram_user_name VARCHAR(255),
    instagram_profile_picture VARCHAR(255),
    instagram_access_token VARCHAR(255),
    created datetime,
    modified datetime
);