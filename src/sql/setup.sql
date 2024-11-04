CREATE DATABASE `biome-db`;

USE `biome-db`;

CREATE USER 'biome-user' @'localhost' IDENTIFIED BY 'biome-password';

GRANT ALL PRIVILEGES ON `biome-db`.* TO 'biome-user'@'localhost';

-- Création de la table User
CREATE TABLE User
(
    ID        INT PRIMARY KEY AUTO_INCREMENT,
    Pseudo    VARCHAR(32) NOT NULL,
    CreatedAt DATE        NOT NULL,
    Points    INT     DEFAULT 0,
    Role      BOOLEAN DEFAULT FALSE -- FALSE pour utilisateur normal, TRUE pour admin
);

-- Création de la table FriendRequest
CREATE TABLE FriendRequest
(
    ID         INT PRIMARY KEY AUTO_INCREMENT,
    Status     SMALLINT NOT NULL, -- Valeur pour représenter le statut de la demande
    CreatedAt  DATE     NOT NULL,
    IdReceiver INT      NOT NULL,
    IdSender   INT      NOT NULL,
    FOREIGN KEY (IdReceiver) REFERENCES User (ID),
    FOREIGN KEY (IdSender) REFERENCES User (ID)
);

-- Création de la table Theme
CREATE TABLE Theme
(
    ID       INT PRIMARY KEY AUTO_INCREMENT,
    Name     VARCHAR(256) NOT NULL,
    Color    VARCHAR(24),
    IsActive BOOLEAN DEFAULT TRUE
);

-- Création de la table Chapter
CREATE TABLE Chapter
(
    ID       INT PRIMARY KEY AUTO_INCREMENT,
    Number   SMALLINT     NOT NULL,
    Title    VARCHAR(256) NOT NULL,
    IsActive BOOLEAN DEFAULT TRUE,
    ThemeId  INT,
    FOREIGN KEY (ThemeId) REFERENCES Theme (ID)
);

-- Création de la table Quiz
CREATE TABLE Quiz
(
    ID         INT PRIMARY KEY AUTO_INCREMENT,
    Title      VARCHAR(256) NOT NULL,
    IsActive   BOOLEAN DEFAULT TRUE,
    Difficulty SMALLINT     NOT NULL,
    ChapterId  INT,
    FOREIGN KEY (ChapterId) REFERENCES Chapter (ID)
);

-- Création de la table Question
CREATE TABLE Question
(
    ID            INT PRIMARY KEY AUTO_INCREMENT,
    Content       VARCHAR(256) NOT NULL,
    IsActive      BOOLEAN DEFAULT TRUE,
    CorrectAnswer VARCHAR(64)  NOT NULL,
    Answer1       VARCHAR(64),
    Answer2       VARCHAR(64),
    Answer3       VARCHAR(64),
    QuizId        INT,
    FOREIGN KEY (QuizId) REFERENCES Quiz (ID)
);

-- Création de la table Mission
CREATE TABLE Mission
(
    ID         INT PRIMARY KEY AUTO_INCREMENT,
    Recurrence SMALLINT     NOT NULL, -- 1 pour quotidien, 2 pour hebdomadaire, etc.
    Title      VARCHAR(256) NOT NULL,
    Content    VARCHAR(256),
    Points     INT          NOT NULL,
    IsActive   BOOLEAN DEFAULT TRUE
);

-- Table d'association entre User et Mission pour gérer les missions possédées par les utilisateurs
CREATE TABLE User_Mission
(
    UserID    INT,
    MissionID INT,
    PRIMARY KEY (UserID, MissionID),
    FOREIGN KEY (UserID) REFERENCES User (ID),
    FOREIGN KEY (MissionID) REFERENCES Mission (ID)
);