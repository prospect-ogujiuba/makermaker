-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_equipment`;
CREATE TABLE `{!!prefix!!}srvc_equipment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(64) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `manufacturer` varchar(128) DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Equipment specifications as JSON object' CHECK (json_valid(`specs`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_equipment__sku` (`sku`),
  KEY `idx_equipment__name` (`name`),
  KEY `idx_equipment__manufacturer` (`manufacturer`),
  KEY `idx_equipment__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Equipment catalog items';

INSERT INTO `{!!prefix!!}srvc_equipment` (`id`, `sku`, `name`, `manufacturer`, `specs`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'VOIP-PBX-001',	'VoIP PBX System',	'Cisco',	'{\"model\": \"UC560\", \"ports\": 24, \"users\": 50, \"features\": [\"SIP\", \"H.323\", \"PoE\"]}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(2,	'VOIP-PHONE-001',	'IP Desk Phone',	'Cisco',	'{\"model\": \"CP-7841\", \"lines\": 4, \"display\": \"3.5 inch\", \"power\": \"PoE\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(3,	'VOIP-PHONE-002',	'Executive IP Phone',	'Cisco',	'{\"model\": \"CP-8851\", \"lines\": 5, \"display\": \"5 inch color\", \"power\": \"PoE+\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(4,	'VOIP-PHONE-003',	'Cordless IP Phone',	'Cisco',	'{\"model\": \"CP-8821\", \"type\": \"wireless\", \"battery\": \"Li-Ion\", \"range\": \"150m\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(5,	'VOIP-GW-001',	'VoIP Gateway',	'AudioCodes',	'{\"model\": \"MP-114\", \"fxs_ports\": 4, \"protocols\": [\"SIP\", \"H.323\"]}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(6,	'NET-SW-001',	'Managed Switch 24-Port',	'Cisco',	'{\"model\": \"SG350-28\", \"ports\": 24, \"uplinks\": 4, \"poe\": true, \"power\": \"185W\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(7,	'NET-SW-002',	'Core Switch 48-Port',	'Cisco',	'{\"model\": \"SG500-52P\", \"ports\": 48, \"uplinks\": 4, \"poe_plus\": true, \"power\": \"370W\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(8,	'NET-RT-001',	'Business Router',	'Cisco',	'{\"model\": \"RV340\", \"wan_ports\": 2, \"lan_ports\": 4, \"vpn\": \"50 tunnels\", \"throughput\": \"500Mbps\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(9,	'NET-AP-001',	'Wireless Access Point',	'Cisco',	'{\"model\": \"WAP371\", \"standard\": \"802.11ac\", \"antennas\": 4, \"power\": \"PoE+\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(10,	'NET-AP-002',	'High-Density Access Point',	'Cisco',	'{\"model\": \"WAP581\", \"standard\": \"802.11ac Wave 2\", \"mu_mimo\": true, \"power\": \"PoE++\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(11,	'CBL-CAT6-001',	'Cat6 Cable - 1000ft',	'Belden',	'{\"category\": \"Cat6\", \"length\": \"1000ft\", \"conductor\": \"23AWG\", \"jacket\": \"CMR\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(12,	'CBL-CAT6A-001',	'Cat6A Cable - 1000ft',	'Belden',	'{\"category\": \"Cat6A\", \"length\": \"1000ft\", \"conductor\": \"23AWG\", \"jacket\": \"CMR\", \"shielded\": true}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(13,	'CBL-FIBER-001',	'Fiber Optic Cable SM',	'Corning',	'{\"type\": \"Single Mode\", \"count\": 12, \"length\": \"1000ft\", \"jacket\": \"OFNR\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(14,	'CBL-PATCH-001',	'Cat6 Patch Cable',	'Panduit',	'{\"category\": \"Cat6\", \"length\": \"3ft\", \"color\": \"blue\", \"connectors\": \"RJ45\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(15,	'SEC-CAM-001',	'IP Security Camera',	'Axis',	'{\"model\": \"P3245-LVE\", \"resolution\": \"1920x1080\", \"night_vision\": true, \"poe_plus\": true}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(16,	'SEC-CAM-002',	'PTZ Security Camera',	'Axis',	'{\"model\": \"P5635-E\", \"resolution\": \"1920x1080\", \"zoom\": \"32x\", \"outdoor\": true}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(17,	'SEC-NVR-001',	'16-Channel NVR',	'Axis',	'{\"model\": \"S2216\", \"channels\": 16, \"storage\": \"16TB\", \"recording\": \"H.264/H.265\"}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(18,	'SEC-AC-001',	'Access Control Panel',	'HID',	'{\"model\": \"VertX V2000\", \"doors\": 2, \"readers\": 4, \"users\": 50000}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(19,	'SEC-AC-002',	'Card Reader',	'HID',	'{\"model\": \"R40\", \"technology\": \"125kHz\", \"format\": \"Wiegand\", \"led\": true}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(20,	'SEC-AC-003',	'Keypad Reader',	'HID',	'{\"model\": \"RPK40\", \"technology\": \"125kHz + PIN\", \"keys\": 12, \"backlit\": true}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_equipment`;
