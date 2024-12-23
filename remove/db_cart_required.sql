

CREATE TABLE `db_cart_orders` (
  `id` int(11) NOT NULL auto_increment,
  `customer` int(11) NOT NULL default '0',
  `order_date` date NOT NULL default '0000-00-00',
  `processed_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `open` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

INSERT INTO `db_cart_orders` VALUES (1, 99, '2005-08-04', '2005-08-04 13:44:16', 'n');

# --------------------------------------------------------

CREATE TABLE `db_cart_rows` (
  `id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL default '0',
  `product_id` varchar(20) NOT NULL default '0',
  `product_name` varchar(100) NOT NULL default '',
  `price` decimal(6,2) NOT NULL default '0.00',
  `vat_perc` decimal(3,1) NOT NULL default '000.0',
  `quantity` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=16 ;

INSERT INTO `db_cart_rows` VALUES (1, 1, '15210100', 'Mirror set', '16.00', '19.0', 2);
INSERT INTO `db_cart_rows` VALUES (2, 1, '15060600', 'GSM-Holder', '14.00', '19.0', 1);

# --------------------------------------------------------

CREATE TABLE `db_cart_shipment` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `address` varchar(50) NOT NULL default '',
  `postal_code` varchar(10) NOT NULL default '',
  `place` varchar(50) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `message` text NOT NULL,
  PRIMARY KEY  (`order_id`)
) TYPE=MyISAM COMMENT='Table with shipment information';

INSERT INTO `db_cart_shipment` VALUES (1, 'Olaf Lederer', 'Hoofdstraat 1', '1000 AA', 'some place in the Netherlands', 'The Netherlands', '');
