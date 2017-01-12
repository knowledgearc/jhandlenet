CREATE TABLE IF NOT EXISTS #__jhandlenet_nas (
   `na` varchar(255) not null,
   `url` varchar(255) not null,
   `archive_endpoint` varchar(255),
   `archive_username` varchar(255),
   `archive_password` varchar(255),
   PRIMARY KEY(`na`)
);

CREATE TABLE IF NOT EXISTS #__jhandlenet_handles (
    `handle` varchar(255) not null,
    `idx` int not null,
    `type` blob,
    `data` int,
    `ttl_type` int,
    `ttl` int,
    `timestamp` int,
    `refs` blob,
    `admin_read` bool,
    `admin_write` bool,
    `pub_read` bool,
    `pub_write` bool,
    `na` varchar(255) not null,
    PRIMARY KEY(`handle`, `idx`),
    FOREIGN KEY (`na`) REFERENCES nas(`na`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);