CREATE TABLE additional_imap (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
  iid integer NOT NULL REFERENCES identities(identity_id) ON DELETE CASCADE ON UPDATE CASCADE,
  username varchar(256) DEFAULT NULL,
  password text,
  server varchar(256) DEFAULT NULL,
  enabled smallint NOT NULL DEFAULT 0,
  label text,
  preferences text
);

CREATE TABLE additional_imap_hosts (
  id serial PRIMARY KEY,
  domain varchar(255) NOT NULL,
  host varchar(255) DEFAULT NULL,
  ts timestamp NOT NULL
);

CREATE TABLE cache_tables (
  id serial NOT NULL,
  suffix varchar(255) NOT NULL,
  ts integer NOT NULL,
  PRIMARY KEY (id)
);

CREATE INDEX ix_additional_imap_user_id ON additional_imap(user_id);
CREATE INDEX ix_additional_imap_iid ON additional_imap(iid);
