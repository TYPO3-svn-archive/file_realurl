#
# Table structure for table 'tx_filerealurl_cache'
#
CREATE TABLE tx_filerealurl_cache (
	pid int(11) DEFAULT '0' NOT NULL,
	file_path_hash bigint(20) DEFAULT '0' NOT NULL,
	file_path varchar(255) DEFAULT '' NOT NULL,
	realurl_path_hash bigint(20) DEFAULT '0' NOT NULL,
	realurl_path varchar(255) DEFAULT '' NOT NULL,

	KEY source_hash (pid,file_path_hash),
	KEY realurl_path_hash (realurl_path_hash)
);