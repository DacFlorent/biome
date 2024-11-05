INSERT INTO `User` (`ID`, `pseudo`, `CreatedAt`, `Points`, `Role`, `passwordHash`)
VALUES


INSERT INTO `FriendRequest` (`ID`, `Status`, `CreatedAt`, `IdReceiver`, `IdSender`)
VALUES

INSERT INTO `Theme` (`ID`, `Name`, `Color`, `IsActive`)
VALUES

INSERT INTO `Chapter` (`ID`, `Number`, `Title`, `IsActive`, `ThemeId`)
VALUES

INSERT INTO `Quiz` (`ID`, `Title`, `IsActive`, `Difficulty`, `ChapterId`)
VALUES

INSERT INTO `Question` (`ID`, `Content`, `IsActive`, `CorrectAnswer`, `Answer1`, `Answer2`, `Answer3`, `QuizId`)
VALUES

INSERT INTO `Mission` (`ID`, `Recurrence`, `Title`, `Content`, `Points`, `IsActive`)
VALUES

INSERT INTO `User_Mission` (`UserID`, `MissionID`)
VALUES