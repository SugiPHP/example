CREATE TABLE links (id INTEGER NOT NULL, title VARCHAR(100), uri VARCHAR(255));
INSERT INTO links VALUES (1, 'home', '/');
INSERT INTO links VALUES (2, 'sign in', '/auth/login');
INSERT INTO links VALUES (3, 'sign up', '/auth/register');
CREATE TABLE auth_users
(
	user_id          INTEGER NOT NULL PRIMARY KEY,
	user_email       VARCHAR(255) NOT NULL,
	user_username    VARCHAR(100) NOT NULL UNIQUE,
	user_password    VARCHAR(60),
	user_state       INTEGER NOT NULL, -- 1 - active; 2 - inactive; 3 - blocked
	user_added       TIMESTAMP NOT NULL,
	login_attempts   INTEGER NOT NULL DEFAULT 0
);
