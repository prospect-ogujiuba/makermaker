-- Description:
-- >>> Up >>>
-- Service Bundles with enhanced schema fields
INSERT INTO `{!!prefix!!}srvc_service_bundles` 
(`name`, `slug`, `short_desc`, `long_desc`, `bundle_type`, `total_discount_pct`, `is_active`, `valid_from`, `valid_to`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES

-- Small Business Packages
('Small Office Complete', 'small-office-complete', 'Complete IT solution for small offices (5-15 employees)', 'Comprehensive IT infrastructure package including network setup, security cameras, VoIP system, and professional installation. Perfect for small businesses looking for an all-in-one solution with ongoing support.', 'package', 15.00, 1, '2025-01-01', '2025-12-31', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('Medium Business Package', 'medium-business-package', 'Comprehensive IT infrastructure for growing businesses (15-50 employees)', 'Enterprise-grade infrastructure scaled for mid-sized organizations. Includes redundant network equipment, advanced security features, cloud integration, and managed services with 24/7 monitoring.', 'package', 20.00, 1, '2025-01-01', '2025-12-31', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

-- Security Solutions
('Enterprise Security Suite', 'enterprise-security-suite', 'Complete security solution with cameras, access control, and monitoring', 'Full-spectrum physical security including IP cameras with AI analytics, biometric access control, intrusion detection, and integrated monitoring dashboard with mobile alerts.', 'suite', 18.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('Retail Security Package', 'retail-security-package', '8-16 IP cameras, NVR, POS-view zones, and alerting for storefronts', 'Tailored for retail environments with loss prevention focus. Includes POS integration, customer counting, heat mapping, and cloud backup of critical footage.', 'package', 12.00, 1, '2025-01-01', '2026-12-31', '2025-08-28 23:57:07', NOW(), NULL, 2, 2),

-- Network Solutions
('Network Infrastructure Bundle', 'network-infrastructure-bundle', 'Complete network setup with cabling, equipment, and wireless', 'End-to-end network implementation from structured cabling to wireless access points. Includes network design, equipment procurement, installation, testing, and documentation.', 'package', 10.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('Network Refresh Essentials', 'network-refresh-essentials', 'Core/edge switch refresh, VLAN redesign, and PoE budgeting with cutover plan', 'Modernize aging network infrastructure with minimal downtime. Includes capacity planning, migration strategy, after-hours implementation, and post-cutover validation.', 'solution', 15.00, 1, '2025-03-01', '2025-09-30', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),

-- Communication Solutions
('Communication Package', 'communication-package', 'VoIP system with professional installation and training', 'Modern cloud-based or on-premise VoIP deployment with auto-attendant, voicemail-to-email, mobile apps, and quality of service tuning for crystal-clear calls.', 'package', 12.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('VoIP Launch FastTrack', 'voip-launch-fasttrack', '50-150 user VoIP deployment with number porting, IVR, training, and QoS tuning', 'Rapid VoIP rollout for mid-sized organizations. Includes parallel system testing, user training sessions, number porting coordination, and 30-day hypercare support.', 'solution', 18.00, 1, '2025-01-01', '2025-12-31', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),

('Conference Room Pro', 'conference-room-pro', 'UC room system with 4K display, PTZ camera, ceiling mic array, and Teams/Zoom config', 'Transform meeting spaces into professional collaboration hubs. Includes touch panel control, wireless presentation, calendar integration, and multi-platform video conferencing support.', 'package', 10.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),

-- Managed Services
('Managed Services Starter', 'managed-services-starter', 'Proactive monitoring, patching, and helpdesk for up to 25 endpoints', 'Entry-level managed services with remote monitoring and management, monthly patching windows, business hours helpdesk, and quarterly business reviews.', 'package', 25.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),

('Managed Services Growth', 'managed-services-growth', 'MSP bundle with endpoint management, backup, and quarterly reviews (25-150 endpoints)', 'Comprehensive managed services for growing organizations. Includes 24/7 monitoring, unlimited helpdesk, cloud backup, security patching, and dedicated account manager.', 'suite', 30.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),

-- Cloud & Migration
('Cloud Readiness Kit', 'cloud-readiness-kit', 'Assessment, migration plan, and pilot workload to AWS/Azure/GCP', 'Structured cloud adoption journey starting with application assessment, TCO analysis, architecture design, and proof-of-concept migration for low-risk workload.', 'solution', 15.00, 1, '2025-01-01', '2025-12-31', '2025-08-28 23:57:07', NOW(), NULL, 1, 1),

-- Disaster Recovery & Backup
('Disaster Recovery & Backup', 'dr-backup-suite', 'Cloud backup, on-prem NAS, and DR runbook with quarterly restore tests', 'Business continuity assurance with automated cloud replication, local backup appliance, documented recovery procedures, and validated quarterly disaster recovery drills.', 'suite', 20.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),

-- Remote Work Solutions
('Secure Remote Work', 'secure-remote-work', 'VPN, MFA, endpoint hardening, and collaboration enablement for hybrid teams', 'Enable secure anywhere-work with zero-trust network access, multi-factor authentication, endpoint detection and response, and collaboration platform rollout.', 'solution', 15.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

-- Industry-Specific Packages
('Warehouse Ops Bundle', 'warehouse-ops-bundle', 'Industrial Wi-Fi, handheld scanner network, and camera coverage for aisles/docks', 'Ruggedized infrastructure for logistics operations including industrial-grade wireless, barcode scanner integration, temperature monitoring, and loading dock cameras.', 'package', 12.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),

('Access Control Upgrade', 'access-control-upgrade', '4-12 door controllers, readers, credentials, and scheduling with HR integration', 'Modern access control replacing legacy key systems. Includes badge management system, automated provisioning from HR system, visitor management, and detailed audit trails.', 'solution', 10.00, 1, '2025-01-01', '2025-12-31', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),

('Office Move IT Pack', 'office-move-it-pack', 'Structured cabling, rack setup, ISP cutover, and desktop moves in one weekend', 'Turnkey office relocation IT services with pre-move site survey, parallel infrastructure buildout, cutover coordination, and post-move optimization.', 'package', 8.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('Branch-in-a-Box', 'branch-in-a-box', 'Pre-staged router, switch, APs, and VoIP with zero-touch deployment for new sites', 'Rapid branch office deployment with pre-configured equipment, remote activation, centralized management integration, and on-site assist if needed.', 'solution', 10.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),

-- Compliance & Security
('Compliance Readiness (PCI/HIPAA)', 'compliance-readiness', 'Gap assessment, policy pack, hardening baseline, and evidence collection templates', 'Structured compliance program kickstart with vulnerability scanning, penetration testing, policy documentation, staff training, and ongoing audit support.', 'suite', 18.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('PenTest & Remediation', 'pentest-remediation', 'External + internal penetration test with prioritized remediation sprints', 'Offensive security assessment identifying exploitable vulnerabilities followed by guided remediation with retesting to validate fixes.', 'solution', 15.00, 1, '2025-01-01', '2025-12-31', '2025-08-28 23:57:07', NOW(), NULL, 2, 2),

-- Smart Building & IoT
('Smart Building Starter', 'smart-building-starter', 'IP cameras, access control, and environmental sensors with single-pane monitoring', 'Converged building systems on unified IP network with integrated dashboard for security, HVAC, lighting, and occupancy analytics.', 'package', 12.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),

-- Vertical Market Solutions
('Education Campus Kit', 'education-campus-kit', 'High-density Wi-Fi, content filtering, lab imaging, and classroom AV basics', 'Purpose-built for K-12 and higher education with CIPA-compliant filtering, classroom management tools, device provisioning, and digital signage.', 'suite', 20.00, 1, '2025-06-01', '2025-08-31', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('Healthcare Clinic IT', 'healthcare-clinic-it', 'Secure EHR-ready network, segmented Wi-Fi, IP cameras, and backup with encryption', 'HIPAA-compliant infrastructure with network segmentation, encrypted backup, BAA-covered cloud services, and audit logging for patient data access.', 'suite', 18.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),

('Construction Site Rapid-Deploy', 'construction-site-rapid', 'Temporary site network with LTE backup, cameras, and access control in rugged enclosures', 'Job site connectivity and security in harsh environments. Weather-resistant equipment, cellular failover, time-lapse cameras, and equipment theft deterrence.', 'package', 10.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),

-- Executive & Specialty
('Executive Protection IT', 'executive-protection-it', 'Hardened endpoints, secure comms, travel kits, and managed identity monitoring', 'C-suite security hardening with anti-phishing training, secure mobile devices, VPN travel kits, dark web monitoring, and executive impersonation alerts.', 'solution', 15.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),

-- Infrastructure Projects
('Data Center Mini-Refresh', 'datacenter-mini-refresh', 'Rack consolidation, UPS refresh, virtualization upgrade, and monitoring overhaul', 'Server room modernization without full data center buildout. Includes power conditioning, cooling optimization, hypervisor upgrades, and infrastructure monitoring.', 'solution', 15.00, 1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),

('Cabling Turnkey (Cat6A)', 'cabling-turnkey-cat6a', 'Design, install, certify Cat6A with patch panels and labeling up to 96 drops', 'Structured cabling project from blueprints to certified installation. Includes pathway planning, cable pulling, termination, testing, and as-built documentation.', 'package', 8.00,  1, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),

('Video Analytics Suite', 'video-analytics-suite', 'CCTV upgrade with AI analytics (line-crossing, object detection, LPR) and alerting', 'Transform existing cameras into intelligent security system with computer vision, automated alerts, searchable video database, and predictive analytics.', 'suite', 12.00, 1, '2025-01-01', '2025-12-31', '2025-08-28 23:57:07', NOW(), NULL, 1, 2);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_service_bundles`;