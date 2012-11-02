BEGIN;

CREATE TABLE tickets (
    id bigint primary key auto_increment
);

CREATE TABLE projects (
    id bigint primary key,
    name varchar(28) NOT NULL,
    description text NOT NULL,
    is_active BOOLEAN NOT NULL,
    create_date INTEGER NOT NULL,
    update_date INTEGER NOT NULL
);
