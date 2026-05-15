create table schopnost (
    schopnost_id varchar(32) primary key,
    nazev varchar(64) not null,
    popis varchar(255) not null
);

CREATE TABLE karta (
    Karta_id int primary key,
    nazev varchar(64) not null,
    rada varchar(32) not null CHECK (rada in ('melee', 'ranged', 'siege', 'leader')),
    cena int,
    schopnost varchar(32)
);