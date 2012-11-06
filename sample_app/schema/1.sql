BEGIN;

CREATE TABLE projects (
    id bigint primary key NOT NULL auto_increment,
    name varchar(28) NOT NULL,
    description text NOT NULL,
    is_active BOOLEAN NOT NULL,
    create_date INTEGER NOT NULL,
    update_date INTEGER NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
