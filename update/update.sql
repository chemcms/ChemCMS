-- 2017-08-13 20:39 增加化学-产品库存更改日志表
CREATE TABLE `cmf_chem_product_inventory_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pack_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '包装规格 id',
  `is_in` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为入库;0:出库;1:入库',
  `inventory_change` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存更改',
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `create_time` int(10) unsigned NOT NULL,
  `category_name` varchar(30) NOT NULL DEFAULT '' COMMENT '库存更新类型名',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT=' 化学-产品库存更改日志表';

-- 2017-08-16 10:22 增加化学-产品库存更改日志表
CREATE TABLE `cmf_chem_stock_out` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:未出库;0:已经出库',
  `category_name` varchar(30) NOT NULL DEFAULT '' COMMENT '库存更新类型名',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '出库类型名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='产品出库表';

-- 2017-08-16 10:22 增加化学-产品库存更改日志表
CREATE TABLE `cmf_chem_stock_out_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pack_id` int(11) NOT NULL DEFAULT '0' COMMENT '包装规格 id',
  `quantity` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `stock_out_id` int(11) NOT NULL DEFAULT '0' COMMENT '出库单 id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


ALTER TABLE `cmf_chem_stock_out_item` ADD `create_time` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间' AFTER `stock_out_id`;

-- 2017-08019 10:45
ALTER TABLE `cmf_chem_stock_out` ADD `stock_out_sn` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '出库单号' AFTER `remark`;
ALTER TABLE `cmf_chem_stock_out_item` ADD `stock_out_sn` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '出库单号' AFTER `create_time`;