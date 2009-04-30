#
# Table structure for table 'tx_filerealurl_cache'
#
CREATE TABLE tx_filerealurl_cache (
	file_path varchar(255) DEFAULT '' NOT NULL,
	realurl_path_hash int(32) DEFAULT '0' NOT NULL,
	realurl_path varchar(255) DEFAULT '' NOT NULL,

	KEY realurl_path_hash (realurl_path_hash)
);