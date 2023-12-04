PRAGMA foreign_keys=OFF; 

CREATE TABLE dbcomprador( 
      id  INTEGER    NOT NULL  , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbcontrato( 
      id  INTEGER    NOT NULL  , 
      cendereco varchar  (100)   , 
      cnome varchar  (100)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfiador( 
      id  INTEGER    NOT NULL  , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfinanciador( 
      id  INTEGER    NOT NULL  , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfinanciamentos( 
      id  INTEGER    NOT NULL  , 
      dbcontrato_id int   , 
      dbfinanciador_id int   , 
      dbfiador_id int   , 
      dbcomprador_id int   , 
      nindex int   , 
      cnome varchar  (200)   , 
      nvalor double  (20)   , 
      ccep varchar  (20)   , 
      cestado char  (2)   , 
      ccidade varchar  (200)   , 
      cendereco varchar  (200)   , 
      cnumero varchar  (30)   , 
      cbairro varchar  (200)   , 
 PRIMARY KEY (id),
FOREIGN KEY(dbcontrato_id) REFERENCES dbcontrato(id),
FOREIGN KEY(dbfinanciador_id) REFERENCES dbfinanciador(id),
FOREIGN KEY(dbfiador_id) REFERENCES dbfiador(id),
FOREIGN KEY(dbcomprador_id) REFERENCES dbcomprador(id)) ; 

CREATE TABLE dbinteracoes( 
      id  INTEGER    NOT NULL  , 
      ddatahora datetime   , 
      cevento text   , 
      dbfinanciamento_id int   , 
 PRIMARY KEY (id),
FOREIGN KEY(dbfinanciamento_id) REFERENCES dbfinanciamentos(id)) ; 

 
 