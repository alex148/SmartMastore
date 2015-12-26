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
name VARCHAR(100),
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
company VARCHAR(100),
address INT(6) UNSIGNED,
type INT(6) UNSIGNED,
CONSTRAINT fk_address FOREIGN KEY (address)
REFERENCES ADDRESS(id),
CONSTRAINT fk_type FOREIGN KEY (type)
REFERENCES TYPE(id)
);

INSERT INTO TYPE VALUES(1,'plombier');
INSERT INTO TYPE VALUES(2,'électricien');
INSERT INTO TYPE VALUES(3,'livreur');

INSERT INTO ADDRESS VALUES(1,'alex','58 rue alexandre boutin',null,'69100','villeurbanne',45.767207,4.868657);
INSERT INTO ADDRESS VALUES(2,null,'58 rue test','le lanchet','69000','lyon',45.767208,4.868658);
INSERT INTO ADDRESS VALUES(3,'test','58 rue du test',null,'69000','testville',45.767207,4.868657);

INSERT INTO CONTACT VALUES(1,'alex','brosse','alex148@hotmail.fr','0651272726','sitalia',1,1);
INSERT INTO CONTACT VALUES(2,'jean','test','test@test.fr','0651272728','test',3,1);
INSERT INTO CONTACT VALUES(3,'test','testons','test@hotmail.fr','0651272727','testeprise',2,2);