CREATE TABLE dbcomprador( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `cnome` varchar  (200)   , 
      `cpublickey` varchar  (100)   , 
      `cchaveprivada` varchar  (100)   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE dbcontrato( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `cendereco` varchar  (100)   , 
      `cnome` varchar  (100)   , 
      `cpublickey` varchar  (100)   , 
      `cchaveprivada` varchar  (100)   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE dbfiador( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `cnome` varchar  (200)   , 
      `cpublickey` varchar  (100)   , 
      `cchaveprivada` varchar  (100)   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE dbfinanciador( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `cnome` varchar  (200)   , 
      `cpublickey` varchar  (100)   , 
      `cchaveprivada` varchar  (100)   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE dbfinanciamentos( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `dbcontrato_id` int   , 
      `dbfinanciador_id` int   , 
      `dbfiador_id` int   , 
      `dbcomprador_id` int   , 
      `nindex` int   , 
      `cnome` varchar  (200)   , 
      `nvalor` double   , 
      `ccep` varchar  (20)   , 
      `cestado` char  (2)   , 
      `ccidade` varchar  (200)   , 
      `cendereco` varchar  (200)   , 
      `cnumero` varchar  (30)   , 
      `cbairro` varchar  (200)   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE dbinteracoes( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `ddatahora` datetime     DEFAULT current_timestamp, 
      `cevento` text   , 
      `dbfinanciamento_id` int   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

 
  
 ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_1 FOREIGN KEY (dbcontrato_id) references dbcontrato(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_2 FOREIGN KEY (dbfinanciador_id) references dbfinanciador(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_3 FOREIGN KEY (dbfiador_id) references dbfiador(id); 
ALTER TABLE dbfinanciamentos ADD CONSTRAINT fk_dbfinanciamentos_4 FOREIGN KEY (dbcomprador_id) references dbcomprador(id); 
ALTER TABLE dbinteracoes ADD CONSTRAINT fk_dbinteracoes_1 FOREIGN KEY (dbfinanciamento_id) references dbfinanciamentos(id); 
