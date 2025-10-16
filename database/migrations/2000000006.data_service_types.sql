-- Description:
-- >>> Up >>>
INSERT INTO `{!!prefix!!}srvc_service_types`
(`name`, `code`, `description`, `requires_site_visit`, `supports_remote`, `estimated_duration_hours`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
('Installation Services', 'installation_services', 'On-site installation and setup of hardware, software, and systems', 1, 0, 8.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Maintenance & Support', 'maintenance_support', 'Ongoing maintenance, troubleshooting, and technical support services', 0, 1, 2.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Consulting Services', 'consulting_services', 'Strategic IT consulting and advisory services for business optimization', 0, 1, 4.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Training Services', 'training_services', 'User training and education on systems, software, and best practices', 1, 1, 6.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Configuration Services', 'configuration_services', 'System configuration, setup, and customization to meet specific requirements', 0, 1, 4.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Repair Services', 'repair_services', 'Hardware and system repair services to restore functionality', 1, 0, 3.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Upgrade Services', 'upgrade_services', 'System upgrades, migrations, and enhancement services', 1, 1, 6.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Design & Planning', 'design_planning', 'System architecture design and project planning services', 0, 1, 12.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Monitoring Services', 'monitoring_services', '24/7 system monitoring and proactive alerting services', 0, 1, 1.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Emergency Services', 'emergency_services', 'Urgent response and emergency technical support services', 1, 1, 2.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Managed Services', 'managed_services', 'Comprehensive managed IT services and ongoing system management', 0, 1, 4.00, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Cloud Migration Services', 'cloud_migration_services', 'Migration of systems and data to cloud platforms', 0, 1, 16.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Data Backup & Recovery', 'data_backup_recovery', 'Data backup solutions and disaster recovery services', 1, 1, 8.00, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Security Audits', 'security_audits', 'Comprehensive security assessments and vulnerability testing', 1, 1, 24.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),
('Compliance Services', 'compliance_services', 'Regulatory compliance assessment and implementation services', 0, 1, 8.00, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Network Assessments', 'network_assessments', 'Network infrastructure evaluation and performance analysis', 1, 1, 12.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Performance Optimization', 'performance_optimization', 'System performance tuning and optimization services', 0, 1, 6.00, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Integration Services', 'integration_services', 'System integration and API development services', 0, 1, 16.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Custom Development', 'custom_development', 'Bespoke software and application development services', 0, 1, 40.00, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Project Management', 'project_management', 'IT project coordination and management services', 0, 1, 8.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Proactive Health Checks', 'proactive_health_checks', 'Regular system health monitoring and preventive maintenance', 1, 1, 4.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('License Management', 'license_management', 'Software license tracking, compliance, and optimization services', 0, 1, 2.00, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Vendor Coordination', 'vendor_coordination', 'Third-party vendor management and coordination services', 0, 1, 3.00, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('IT Roadmap & Strategy', 'it_roadmap_strategy', 'Long-term IT strategic planning and roadmap development', 0, 1, 20.00, '2025-08-28 23:57:07', NOW(), NULL, 2, 2);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_service_types`;