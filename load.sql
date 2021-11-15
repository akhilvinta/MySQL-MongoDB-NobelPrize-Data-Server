DROP TABLE IF EXISTS Laureate;
DROP TABLE IF EXISTS Person;
DROP TABLE IF EXISTS Organization;
DROP TABLE IF EXISTS Prize;
DROP TABLE IF EXISTS Affiliation;
DROP TABLE IF EXISTS Awarded;




CREATE TABLE Laureate(id integer not null, birth_date date, birth_city varchar(100), birth_country varchar(100), primary key(id));
CREATE TABLE Person(id integer not null, given_name varchar(100), family_name varchar(100), gender varchar(100), primary key(id));
CREATE TABLE Organization(id integer not null, org_name varchar(100), primary key(id));

CREATE TABLE Prize(id integer not null, award_year integer, category varchar(100), sort_order integer, primary key(id));
CREATE TABLE Affiliation(id integer not null, name varchar(100),city varchar(100),country varchar(100), primary key(id));


CREATE TABLE Awarded(laureate_id integer not null , prize_id integer not null , affiliation_id integer );


LOAD DATA LOCAL INFILE './laureates.del' INTO TABLE Laureate fields terminated by '|';
LOAD DATA LOCAL INFILE './people.del' INTO TABLE Person fields terminated by '|' ;
LOAD DATA LOCAL INFILE './orgs.del' INTO TABLE Organization fields terminated by '|' ;
LOAD DATA LOCAL INFILE './prizes.del' INTO TABLE Prize fields terminated by '|';
LOAD DATA LOCAL INFILE './affiliations.del' INTO TABLE Affiliation fields terminated by '|' ;
LOAD DATA LOCAL INFILE './awarded.del' INTO TABLE Awarded fields terminated by '|' ;
