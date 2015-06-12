
-------------------12 june 2015-------------TDS GL for customer payment
INSERT INTO `0_chart_master` (`account_code`, `account_code2`, `account_name`, `account_type`, `inactive`) VALUES ('2391', '', 'Tax Deducted at Source', '4', '0');

INSERT INTO `connectncabs`.`1_sys_prefs` (`name`, `category`, `type`, `length`, `value`) VALUES ('tds_act', 'glsetup.customer', 'varchar', '15', '2391');
-----------------------------------------------------------------------
