CREATE TABLE dbcomprador( 
      id number(10)    NOT NULL , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbcontrato( 
      id number(10)    NOT NULL , 
      cendereco varchar  (100)   , 
      cnome varchar  (100)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfiador( 
      id number(10)    NOT NULL , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfinanciador( 
      id number(10)    NOT NULL , 
      cnome varchar  (200)   , 
      cpublickey varchar  (100)   , 
      cchaveprivada varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbfinanciamentos( 
      id number(10)    NOT NULL , 
      dbcontrato_id number(10)   , 
      dbfinanciador_id number(10)   , 
      dbfiador_id number(10)   , 
      dbcomprador_id number(10)   , 
      nindex number(10)   , 
      cnome varchar  (200)   , 
      nvalor binary_double  (20)   , 
      ccep varchar  (20)   , 
      cestado char  (2)   , 
      ccidade varchar  (200)   , 
      cendereco varchar  (200)   , 
      cnumero varchar  (30)   , 
      cbairro varchar  (200)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE dbinteracoes( 
      id number(10)    NOT NULL , 
      ddatahora timestamp(0)    DEFAULT current_timestamp , 
      cevento varchar(3000)   , 
      dbfinanciamento_id number(10)   , 
 PRIMARY KEY (id)) ; 

 
  
 ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_1 FOREIGN KEY (dbcontrato_id) references dbcontrato(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_2 FOREIGN KEY (dbfinanciador_id) references dbfinanciador(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_3 FOREIGN KEY (dbfiador_id) references dbfiador(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_4 FOREIGN KEY (dbcomprador_id) references dbcomprador(id); 
ALTER TABLE dbinteracoes ADD CONSTRAINT fk_dbinteracoes_1 FOREIGN KEY (dbfinanciamento_id) references dbfinanciamentos(id); 
 CREATE SEQUENCE dbcomprador_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER dbcomprador_id_seq_tr 

BEFORE INSERT ON dbcomprador FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT dbcomprador_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE dbcontrato_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER dbcontrato_id_seq_tr 

BEFORE INSERT ON dbcontrato FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT dbcontrato_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE dbfiador_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER dbfiador_id_seq_tr 

BEFORE INSERT ON dbfiador FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT dbfiador_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE dbfinanciador_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER dbfinanciador_id_seq_tr 

BEFORE INSERT ON dbfinanciador FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT dbfinanciador_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE dbfinanciamentos_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER dbfinanciamentos_id_seq_tr 

BEFORE INSERT ON dbfinanciamentos FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT dbfinanciamentos_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE dbinteracoes_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER dbinteracoes_id_seq_tr 

BEFORE INSERT ON dbinteracoes FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT dbinteracoes_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
 