CREATE TABLE dbcomprador( 
      id  SERIAL    NOT NULL  , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbcontrato( 
      id  SERIAL    NOT NULL  , 
      cendereco varchar  (100)   , 
      cnome varchar  (100)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfiador( 
      id  SERIAL    NOT NULL  , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfinanciador( 
      id  SERIAL    NOT NULL  , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfinanciamentos( 
      id  SERIAL    NOT NULL  , 
      dbcontrato_id integer   , 
      dbfinanciador_id integer   , 
      dbfiador_id integer   , 
      dbcomprador_id integer   , 
      nindex integer   , 
      cnome varchar  (200)   , 
      nvalor float   , 
      ccep varchar  (20)   , 
      cestado char  (2)   , 
      ccidade varchar  (200)   , 
      cendereco varchar  (200)   , 
      cnumero varchar  (30)   , 
      cbairro varchar  (200)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbinteracoes( 
      id  SERIAL    NOT NULL  , 
      ddatahora timestamp     DEFAULT current_timestamp, 
      cevento text   , 
      dbfinanciamento_id integer   , 
 PRIMARY KEY (id)) ; 

 
  
 ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_1 FOREIGN KEY (dbcontrato_id) references dbcontrato(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_2 FOREIGN KEY (dbfinanciador_id) references dbfinanciador(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_3 FOREIGN KEY (dbfiador_id) references dbfiador(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_4 FOREIGN KEY (dbcomprador_id) references dbcomprador(id); 
ALTER TABLE dbinteracoes ADD CONSTRAINT fk_dbinteracoes_1 FOREIGN KEY (dbfinanciamento_id) references dbfinanciamentos(id); 
