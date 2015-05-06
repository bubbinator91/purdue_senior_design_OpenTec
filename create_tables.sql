CREATE TABLE events (
	id int(11) NOT NULL AUTO_INCREMENT,
	latitude double NOT NULL DEFAULT -255.00,
	longitude double NOT NULL DEFAULT -255.00,
	depth double NOT NULL DEFAULT -255.00,
	magnitude double NOT NULL DEFAULT -255.00,
	magnitudetype varchar(45) NOT NULL DEFAULT 'unknown',
	timestamp timestamp NOT NULL DEFAULT 0,
	location varchar(255) NOT NULL DEFAULT 'unknown',
	cause varchar(255) NOT NULL DEFAULT 'unknown',
	network varchar(255) NOT NULL DEFAULT 'unknown',
	station varchar(255) NOT NULL DEFAULT 'unknown',
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
