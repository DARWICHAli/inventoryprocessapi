CREATE TABLE utilisateurs
(
    id_utilisateur INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nom VARCHAR(20),
    prenom VARCHAR(20),
    login VARCHAR(20),
    pass VARCHAR(20),
    privileges INT
);

CREATE TABLE article
(
    id_article INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nom VARCHAR(20),
    code_produit VARCHAR(10) NOT NULL,
    description VARCHAR(100)
);
CREATE TABLE entrepot
(
    id_entrepot INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nom VARCHAR(10)
);


CREATE TABLE entrepot_site
(
    id_site INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_allee INT,
    id_travee INT,
    id_niveau INT,
    id_alveole INT,
    id_entrepot INT,
    FOREIGN KEY (id_entrepot) REFERENCES entrepot(id_entrepot),
    FOREIGN KEY (id_allee) REFERENCES allee(id_allee),
    FOREIGN KEY (id_travee) REFERENCES travee(id_travee),
    FOREIGN KEY (id_niveau) REFERENCES niveau(id_niveau),
    FOREIGN KEY (id_alveole) REFERENCES alveole(id_alveole)
);

CREATE TABLE stocked
(
    quantity INT NOT NULL,
    id_site INT NOT NULL,
    id_article INT NOT NULL,
    FOREIGN KEY (id_site) REFERENCES entrepot_site(id_site),
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

CREATE TABLE allee
(
    id_allee INT PRIMARY NOT NULL,
    allee VARCHAR(4)
);

CREATE TABLE travee
(
    id_travee INT PRIMARY NOT NULL,
    travee VARCHAR(4)
);

CREATE TABLE niveau
(
    id_niveau INT PRIMARY NOT NULL,
    niveau VARCHAR(4)
);


CREATE TABLE alveole
(
    id_alveole INT PRIMARY NOT NULL,
    alveole VARCHAR(4)
);
