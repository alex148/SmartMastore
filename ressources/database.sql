/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 23/12/2015
 * Time: 14:15
 */
drop table if exists contact ;
drop table if exists address ;
drop table if exists type;


CREATE TABLE ADDRESS (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
line1 VARCHAR(100),
line2 VARCHAR(100),
zipcode VARCHAR(5),
city VARCHAR(100),
latitude FLOAT(12),
longitude FLOAT(12)
);

CREATE TABLE TYPE (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
label VARCHAR(100)
);

CREATE TABLE CONTACT(
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
firstName VARCHAR(100),
name VARCHAR(100),
mail VARCHAR(100),
phone VARCHAR(20),
phone2 VARCHAR(20),
phone3 VARCHAR(20),
company VARCHAR(100),
address INT(6) UNSIGNED,
type INT(6) UNSIGNED,
exchangeId VARCHAR(255),
CONSTRAINT fk_address FOREIGN KEY (address)
REFERENCES ADDRESS(id),
CONSTRAINT fk_type FOREIGN KEY (type)
REFERENCES TYPE(id)
);

INSERT INTO TYPE VALUES(1,'plombier');
INSERT INTO TYPE VALUES(2,'électricien');
INSERT INTO TYPE VALUES(3,'livreur');

INSERT INTO ADDRESS VALUES(1,'58 rue alexandre boutin',null,'69100','villeurbanne',45.767207,4.868657);
INSERT INTO ADDRESS VALUES(2,null,'le lanchet','01120','le lanchet',45.767208,4.868658);
INSERT INTO ADDRESS VALUES(3,null,null,'69000','villeurbanne',45.767207,4.868657);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('1', 'alex', 'brosse', 'alex@hotmail.fr', '0651272726', NULL, NULL, 'CGI', '1', '1', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('2', 'robin', 'montes', NULL, '0625897560', '0654785030', '0657410230', NULL, '2', '3', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('3', 'zizah', 'mohammed', 'test@test.fr', '0678953026', '0654785230', '0654785231', NULL, '3', '3', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('4', 'test', 'testy', 'testy@test.fr', '0663256320', NULL, '0547895230', 'Test', '2', '2', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('5', 'ezfezf', 'ezfezfez', 'fezfez@gg.fr', NULL, NULL, NULL, 'fezfezf', '2', '3', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('6', 'jean', 'valjean', 'jean@tet.fr', '0651478520', NULL, NULL, 'LOL', NULL, '2', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('7', 'Blob', 'jean', NULL, NULL, NULL, NULL, NULL, '3', '2', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('8', 'alex', NULL, 'alexf@hotmail.fr', '0651248950', NULL, NULL, 'fff', '1', '1', NULL);

INSERT INTO `contact` (`id`, `firstName`, `name`, `mail`, `phone`, `phone2`, `phone3`, `company`, `address`, `type`, `exchangeId`)
VALUES ('9', NULL, NULL, 'fezez@fezfz.fr', NULL, NULL, NULL, 'CGI', NULL, NULL, NULL);