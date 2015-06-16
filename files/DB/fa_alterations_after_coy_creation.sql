
-------------------12 june 2015-------------TDS GL for customer payment

INSERT INTO `connectncabs`.`1_sys_prefs` (`name`, `category`, `type`, `length`, `value`) VALUES ('sales_tds_act', 'glsetup.customer', 'varchar', '15', ''),('purchase_tds_act', 'glsetup.purchase', 'varchar', '15', '');


ALTER TABLE `1_debtor_trans`  ADD `ov_tds` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_discount`;

ALTER TABLE `1_supp_trans`  ADD `ov_tds` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_discount`
-----------------------------------------------------------------------
