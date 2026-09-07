-- Structure of the Draagvlak database.
--
-- Run this with "composer db:setup"; that script creates the database itself
-- and then executes this file. Everything is InnoDB with utf8mb4, so foreign
-- keys work and a name with an accent or an emoji is stored correctly.
--
-- Four tables. A user has contacts, a contact sends messages, and every change
-- to a score is written down as an event instead of only overwriting a number.

CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(80)  NOT NULL,
    email           VARCHAR(190) NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    score           TINYINT UNSIGNED NOT NULL DEFAULT 69,
    has_plus        BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- One account per address. The check lives here as well as in the code,
    -- because two people can submit the form at the same second.
    UNIQUE KEY users_email_unique (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contacts (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id              INT UNSIGNED NOT NULL,
    name                 VARCHAR(80)  NOT NULL,
    relation             VARCHAR(80)  NOT NULL DEFAULT '',
    score                TINYINT UNSIGNED NOT NULL DEFAULT 60,
    is_emergency_contact BOOLEAN      NOT NULL DEFAULT FALSE,
    lists_you            BOOLEAN      NOT NULL DEFAULT TRUE,
    note                 VARCHAR(255) NOT NULL DEFAULT '',
    removed_at           DATETIME     NULL DEFAULT NULL,
    created_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY contacts_user_id_index (user_id),
    CONSTRAINT contacts_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id                INT UNSIGNED NOT NULL,
    contact_id             INT UNSIGNED NOT NULL,
    body                   TEXT         NOT NULL,
    received_at            DATETIME     NOT NULL,
    -- Seconds the participant gets to answer in time. Counting starts at
    -- seen_at, not at received_at, so the pressure is real in every session.
    respond_within_seconds SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    seen_at                DATETIME     NULL DEFAULT NULL,
    handled_at             DATETIME     NULL DEFAULT NULL,
    outcome                ENUM('answered', 'postponed', 'automatic', 'removed') NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY messages_user_id_index (user_id),
    KEY messages_contact_id_index (contact_id),
    CONSTRAINT messages_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT messages_contact_id_foreign FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS score_events (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    delta      TINYINT      NOT NULL,
    reason     VARCHAR(120) NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY score_events_user_id_index (user_id),
    CONSTRAINT score_events_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
