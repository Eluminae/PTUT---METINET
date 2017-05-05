INSERT INTO `wc_identity` (`id`, `last_name`, `first_name`, `email`) VALUES
('b7f5d7fc-1966-499a-a32d-cfbca4b849c8',	'Degoule',	'Jean-Patrick',	'admin@admin.fr');
INSERT INTO `wc_administrator` (`id`, `identity_id`, `password`, `salt`, `role`) VALUES
('7b22fa94-0ac7-4c36-a4c7-192f29616ae1',	'b7f5d7fc-1966-499a-a32d-cfbca4b849c8',	'$2y$13$CbHjwHEMNQxyzGxZuIIto.mY3hM5aryyrq1yyKgOrX7ckoxRFUgz.',	'aeee0839-1844-4eec-9781-3c7e1f89a494',	'ROLE_ADMIN');
