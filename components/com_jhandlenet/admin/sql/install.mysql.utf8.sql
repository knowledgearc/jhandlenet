CREATE TABLE IF NOT EXISTS nas (
   `na` varchar(255) not null,
   PRIMARY KEY(`na`)
);

CREATE TABLE IF NOT EXISTS handles (
    `handle` varchar(255) not null,
    `idx` int not null,
    `type` blob,
    `data` blob,
    `ttl_type` int,
    `ttl` int,
    `timestamp` int,
    `refs` blob,
    `admin_read` bool,
    `admin_write` bool,
    `pub_read` bool,
    `pub_write` bool,
    `na` varchar(255) not null,
    `context` varchar(255) not null,
    PRIMARY KEY(`handle`, `idx`)
);
