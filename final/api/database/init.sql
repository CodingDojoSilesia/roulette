DROP TABLE IF EXISTS bet;
DROP TABLE IF EXISTS player;
CREATE TABLE players (
    id INTEGER PRIMARY KEY,
    hashname TEXT NOT NULL UNIQUE,
    chips INTEGER NOT NULL
);
CREATE TABLE bets (
    id INTEGER PRIMARY KEY,
    playerId INTEGER NOT NULL,
    betIndex INTEGER NOT NULL,
    chips INTEGER NOT NULL,
    isCompleted INTEGER NOT NULL DEFAULT 0,
    spinNumber INTEGER,
    FOREIGN KEY (playerId) REFERENCES player (playerId) ON DELETE CASCADE ON UPDATE CASCADE
);
