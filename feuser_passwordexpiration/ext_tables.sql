CREATE TABLE fe_users (
	tx_feuserpasswordexpiration_last_password_change int(11) unsigned DEFAULT ALTER TABLE fe_users ADD lastPasswordChange int(11) unsigned DEFAULT CURRENT_TIMESTAMP NOT NULL; NOT NULL;
);