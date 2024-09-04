CREATE USER 'user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON `db`.* TO 'user'@'localhost';
FLUSH PRIVILEGES;

create table profile(id int primary key not null auto_increment,
login varchar(255) not null,
password varchar(255) not null,
phone varchar(255) not null,
email varchar(255) not null,

CONSTRAINT uq_profile UNIQUE(login, email, phone));
