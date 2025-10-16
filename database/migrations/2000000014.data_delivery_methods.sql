-- Description:
-- >>> Up >>>
INSERT INTO `{!!prefix!!}srvc_delivery_methods`
(`name`, `code`, `description`, `requires_site_access`, `supports_remote`, `default_lead_time_days`, `default_sla_hours`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
('On-Site Installation', 'on-site-installation', 'Technician visits customer location to perform full installation and configuration. Includes testing and basic training.', 1, 0, 3, 8, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Remote Configuration', 'remote-configuration', 'Complete setup performed remotely via VPN or remote access tools. Requires customer to have equipment physically installed.', 0, 1, 1, 4, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Hybrid (On-site + Remote)', 'hybrid-on-site-remote', 'Initial on-site installation followed by remote configuration and optimization. Combines benefits of both approaches.', 1, 1, 2, 6, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Client Self-Install', 'client-self-install', 'Customer performs installation using provided documentation and support hotline. Equipment ships pre-configured when possible.', 0, 1, 0, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('White Glove Service', 'white-glove', 'Premium full-service installation with project management, custom configuration, extended testing, and comprehensive training.', 1, 0, 5, 24, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Pickup', 'pickup', 'Customer picks up equipment from our location. Basic configuration assistance available on-site.', 0, 0, 0, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Shipping/Delivery', 'shipping-delivery', 'Equipment shipped directly to customer location via standard or expedited carrier. No installation included.', 0, 0, 2, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('In-Store Service', 'in-store-service', 'Service performed at our retail location. Customer drops off equipment or receives service in our workshop.', 0, 0, 1, 24, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Drop-Ship Partner', 'drop-ship-partner', 'Equipment ships directly from manufacturer or distributor to customer. We coordinate and provide remote support.', 0, 1, 5, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Scheduled Maintenance Window', 'scheduled-maintenance-window', 'Service performed during pre-approved off-hours maintenance windows to minimize business disruption. Requires advance scheduling.', 1, 0, 14, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Break-Fix On-Demand', 'break-fix-on-demand', 'Reactive service dispatched when issues occur. No scheduled maintenance. Technician arrives within agreed response time.', 1, 0, 0, 8, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Cloud-Based Provisioning', 'cloud-based-provisioning', 'Services delivered entirely through cloud platforms. No physical installation required. Access via web portal or API.', 0, 1, 0, 2, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Vendor Direct Fulfillment', 'vendor-direct-fulfillment', 'We coordinate directly with equipment vendors for specialized installation. Manufacturer-certified technicians perform work.', 1, 0, 10, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Turnkey Project Delivery', 'turnkey-project-delivery', 'Complete end-to-end project including design, procurement, installation, testing, and cutover. Single point of contact.', 1, 1, 30, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Swing Shift Service', 'swing-shift-service', 'Installation performed during evening or overnight hours to avoid disrupting business operations. Premium scheduling option.', 1, 0, 5, 12, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Site Survey & Pre-Staging', 'site-survey-pre-staging', 'Initial site assessment followed by equipment pre-configuration in our lab before final on-site installation.', 1, 0, 7, 16, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Multi-Site Rollout', 'multi-site-rollout', 'Coordinated deployment across multiple locations using standardized procedures. Includes project management and reporting.', 1, 1, 21, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_delivery_methods`;