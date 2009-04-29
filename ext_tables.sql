#
# Table structure for table 'tx_filerealurl_cache'
#
CREATE TABLE tx_filerealurl_cache (
	file_path_hash int(16) UNSIGNED DEFAULT '0' NOT NULL,
	file_path varchar(255) DEFAULT '' NOT NULL,
	realurl_path_hash int(16) UNSIGNED DEFAULT '0' NOT NULL,
	realurl_path varchar(255) DEFAULT '' NOT NULL,

	KEY file_path_hash (file_path_hash),
	KEY realurl_path_hash (realurl_path_hash)
);