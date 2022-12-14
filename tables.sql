CREATE TABLE utilisateurs
(
    id_utilisateur INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nom VARCHAR(20),
    prenom VARCHAR(20),
    login VARCHAR(20) NOT NULL UNIQUE,
    hpass BINARY(64),
    privileges INT
);

CREATE TABLE article
(
    id_article INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nom VARCHAR(20),
    code_produit VARCHAR(20) NOT NULL UNIQUE,
    description VARCHAR(100)
);

CREATE TABLE entrepot
(
    id_entrepot INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nom VARCHAR(10) UNIQUE
);

CREATE TABLE allee
(
    id_allee INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    allee VARCHAR(1) UNIQUE
);

CREATE TABLE travee
(
    id_travee INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    travee VARCHAR(2) UNIQUE
);

CREATE TABLE niveau
(
    id_niveau INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    niveau VARCHAR(2) UNIQUE
);

CREATE TABLE alveole
(
    id_alveole INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    alveole VARCHAR(2) UNIQUE
);


CREATE TABLE entrepot_site
(
    id_site INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_allee INT  NOT NULL,
    id_travee INT NOT NULL,
    id_niveau INT NOT NULL,
    id_alveole INT NOT NULL,
    id_entrepot INT NOT NULL,
    FOREIGN KEY (id_entrepot) REFERENCES entrepot(id_entrepot),
    FOREIGN KEY (id_allee) REFERENCES allee(id_allee),
    FOREIGN KEY (id_travee) REFERENCES travee(id_travee),
    FOREIGN KEY (id_niveau) REFERENCES niveau(id_niveau),
    FOREIGN KEY (id_alveole) REFERENCES alveole(id_alveole)
    CONSTRAINT UC_pos UNIQUE (id_allee, id_travee, id_niveau,id_alveole)
);

CREATE TABLE stock
(
    id_stock INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    quantity INT NOT NULL,
    id_site INT NOT NULL UNIQUE,
    id_article INT NOT NULL,
    FOREIGN KEY (id_site) REFERENCES entrepot_site(id_site),
    FOREIGN KEY (id_article) REFERENCES article(id_article),
    constraint co_quantity check (quantity > 0)
);

CREATE TABLE `transactions` (
  `id_transaction` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `id_site` int(11) NOT NULL,
  `delta` int(11) NOT NULL,
  `estampille` datetime NOT NULL,
  PRIMARY KEY (`id_transaction`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_article` (`id_article`),
  KEY `id_site` (`id_site`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`),
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`),
  CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`id_site`) REFERENCES `entrepot_site` (`id_site`)
) 