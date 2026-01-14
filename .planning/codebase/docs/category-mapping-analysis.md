# Service Category Mapping Analysis
**Date**: 2026-01-01
**Purpose**: Correct service-to-category assignments in B2BCNC service catalog migration

## Category ID Mapping Reference

### Top-Level Categories
| ID | Name | Slug | Description |
|----|------|------|-------------|
| 1 | Phone | phone | Business telecommunications including VOIP, PBX, SIP, unified communications |
| 2 | Access Control | access-control | Physical security: door access, card readers, biometric authentication |
| 3 | Camera System | camera-system | Video surveillance: IP cameras, NVR, analytics, monitoring |
| 4 | Networking | networking | Network infrastructure: cabling, fiber, switches, routers, wireless |
| 5 | Managed IT | managed-it | IT support: monitoring, server management, backup, help desk |
| 6 | Consulting | consulting | Technology consulting: planning, cloud strategy, security, project management |

### Sub-Categories

#### Phone Sub-Categories (Parent: 1)
| ID | Name | Slug |
|----|------|------|
| 7 | VOIP Systems | voip-systems |
| 8 | PBX Solutions | pbx-solutions |
| 9 | Phone Services | phone-services |
| 10 | Unified Communications | unified-communications |
| 11 | Phone Hardware | phone-hardware |
| 12 | Call Center Solutions | call-center-solutions |

#### Access Control Sub-Categories (Parent: 2)
| ID | Name | Slug |
|----|------|------|
| 13 | Single Door Systems | single-door-systems |
| 14 | Multi-Door Systems | multi-door-systems |
| 15 | Card Readers & Credentials | card-readers-credentials |
| 16 | Biometric Access | biometric-access |
| 17 | Visitor Management | visitor-management |

#### Camera System Sub-Categories (Parent: 3)
| ID | Name | Slug |
|----|------|------|
| 18 | IP Camera Systems | ip-camera-systems |
| 19 | NVR Solutions | nvr-solutions |
| 20 | Video Analytics | video-analytics |
| 21 | Remote Monitoring | remote-monitoring |
| 22 | Mobile Surveillance | mobile-surveillance |

#### Networking Sub-Categories (Parent: 4)
| ID | Name | Slug |
|----|------|------|
| 23 | Structured Cabling | structured-cabling |
| 24 | Fiber Optic | fiber-optic |
| 25 | Network Infrastructure | network-infrastructure |
| 26 | Wireless Networks | wireless-networks |
| 27 | Network Security | network-security |
| 28 | Network Design | network-design |

#### Managed IT Sub-Categories (Parent: 5)
| ID | Name | Slug |
|----|------|------|
| 29 | Monitoring Services | monitoring-services |
| 30 | Server Management | server-management |
| 31 | Backup & Recovery | backup-recovery |
| 32 | Help Desk Support | help-desk-support |
| 33 | Patch Management | patch-management |
| 34 | Security Management | security-management |

#### Consulting Sub-Categories (Parent: 6)
| ID | Name | Slug |
|----|------|------|
| 35 | Infrastructure Planning | infrastructure-planning |
| 36 | Cloud Strategy | cloud-strategy |
| 37 | Security Assessment | security-assessment |
| 38 | Project Management | project-management |
| 39 | Technology Assessment | technology-assessment |
| 40 | Disaster Recovery Planning | disaster-recovery-planning |

## Miscategorized Services Analysis

### CRITICAL ISSUES: Wrong Parent Categories

#### Issue 1: Access Control Services in Phone Categories
**Current assignments:**
- "Access Control - Single Door System" (SKU: ACS-SD-001) → Category 11 (Phone Hardware)
- "Access Control - Single Door with Biometric Reader" (SKU: ACS-SD-BIO-001) → Category 11 (Phone Hardware)

**Problem**: Access control services are physical security systems, NOT phone hardware.

**Correct category**: 13 (Single Door Systems) - this is the proper sub-category under Access Control (parent 2)

**Reasoning**:
- Category 13 is literally named "Single Door Systems" and describes "Entry-level access control for single entry points"
- Category 11 (Phone Hardware) is for "Business phone equipment including desk phones, cordless systems, headsets"
- These are completely different domains

---

#### Issue 2: Multi-Door Access Control in Phone Categories
**Current assignments:**
- "Access Control - Multi-Door System (4-8 Doors)" (SKU: ACS-MD-4-8-001) → Category 12 (Call Center Solutions)
- "Access Control - Enterprise System (16+ Doors)" (SKU: ACS-ENT-001) → Category 12 (Call Center Solutions)

**Problem**: Multi-door access control systems are NOT call center technology.

**Correct category**: 14 (Multi-Door Systems) - this is the proper sub-category under Access Control (parent 2)

**Reasoning**:
- Category 14 is named "Multi-Door Systems" for "Enterprise access control managing multiple doors"
- Category 12 (Call Center Solutions) is for "Call center and contact center technology including ACD, IVR, call recording"
- Complete category mismatch

---

#### Issue 3: Card Reader Services in Biometric Category
**Current assignments:**
- "Card Reader Installation - Per Reader" (SKU: CR-INST-001) → Category 16 (Biometric Access)
- "Access Credentials - Bulk Provisioning (50+ Cards)" (SKU: AC-BULK-001) → Category 16 (Biometric Access)

**Problem**: Standard card readers and proximity cards are NOT biometric systems.

**Correct category**: 15 (Card Readers & Credentials) - dedicated category for this exact purpose

**Reasoning**:
- Category 15 is literally "Card Readers & Credentials" for "Access card readers, proximity cards, key fobs"
- Category 16 (Biometric Access) is for "Fingerprint, facial recognition, and biometric authentication"
- Card readers are standard RFID/proximity technology, not biometric

---

#### Issue 4: Visitor Management in Wrong Category
**Current assignment:**
- "Visitor Management System Installation" (SKU: VMS-001) → Category 15 (Card Readers & Credentials)

**Problem**: Minor misplacement - visitor management is a complete system, not just card readers.

**Correct category**: 17 (Visitor Management) - dedicated category exists

**Reasoning**:
- Category 17 exists specifically for "Visitor check-in systems, badge printing, and temporary access control"
- While visitor management may USE cards, it's a complete system with check-in software, badge printing, etc.

---

#### Issue 5: IP Camera Services in Wrong Sub-Categories
**Current assignments:**
- "IP Camera System - 4 Cameras (Starter)" (SKU: IPCAM-4-001) → Category 16 (Biometric Access)
- "IP Camera System - 8 Cameras (Professional)" (SKU: IPCAM-8-001) → Category 16 (Biometric Access)
- "IP Camera System - 16 Cameras (Advanced)" (SKU: IPCAM-16-001) → Category 16 (Biometric Access)
- "IP Camera System - Enterprise (32+ Cameras)" (SKU: IPCAM-ENT-001) → Category 16 (Biometric Access)
- "IP Camera Installation - Per Camera" (SKU: IPCAM-ADD-001) → Category 16 (Biometric Access)

**Problem**: IP camera systems have nothing to do with biometric access control.

**Correct category**: 18 (IP Camera Systems) - dedicated sub-category under Camera System (parent 3)

**Reasoning**:
- Category 18 is "IP Camera Systems" for "Network-based video surveillance cameras with PoE, HD/4K resolution"
- Category 16 (Biometric Access) is for fingerprint and facial recognition ACCESS CONTROL
- Cameras are surveillance, not access control

---

#### Issue 6: NVR Service Miscategorized
**Current assignment:**
- "NVR Setup & Configuration Service" (SKU: NVR-SETUP-001) → Category 17 (Visitor Management)

**Problem**: Network Video Recorders are camera recording systems, not visitor management.

**Correct category**: 19 (NVR Solutions) - dedicated sub-category under Camera System (parent 3)

**Reasoning**:
- Category 19 is "NVR Solutions" for "Network video recorder systems for IP camera storage, management"
- NVR is core camera system infrastructure, not visitor management

---

#### Issue 7: Video Analytics Miscategorized
**Current assignment:**
- "Video Analytics Platform Deployment" (SKU: VA-PLATFORM-001) → Category 18 (IP Camera Systems)

**Problem**: While related to cameras, video analytics is its own sophisticated category.

**Correct category**: 20 (Video Analytics) - dedicated sub-category exists

**Reasoning**:
- Category 20 exists specifically for "Intelligent video analysis including motion detection, people counting, license plate recognition"
- Analytics is a sophisticated layer above basic camera systems
- Dedicated category provides better service discovery

---

#### Issue 8: Professional Monitoring in Wrong Category
**Current assignment:**
- "Professional Video Monitoring Service (Monthly)" (SKU: VM-247-001) → Category 19 (NVR Solutions)

**Problem**: Live monitoring service is not the same as NVR hardware/software.

**Correct category**: 21 (Remote Monitoring) - dedicated category for monitoring services

**Reasoning**:
- Category 21 is "Remote Monitoring" for "24/7 professional monitoring services with alarm response"
- This is a managed SERVICE, not NVR equipment/software
- NVR is technology; remote monitoring is an ongoing service

---

#### Issue 9: Structured Cabling in Wrong Categories
**Current assignments:**
- "CAT6 Structured Cabling - Per Run" (SKU: CAT6-RUN-001) → Category 21 (Remote Monitoring)
- "CAT6A Structured Cabling - Per Run" (SKU: CAT6A-RUN-001) → Category 21 (Remote Monitoring)
- "Structured Cabling Infrastructure Project" (SKU: SC-INFRA-001) → Category 21 (Remote Monitoring)

**Problem**: Network cabling has nothing to do with video monitoring services.

**Correct category**: 23 (Structured Cabling) - dedicated sub-category under Networking (parent 4)

**Reasoning**:
- Category 23 is "Structured Cabling" for "Professional CAT5e, CAT6, CAT6A network cabling installation"
- Category 21 (Remote Monitoring) is for "24/7 professional monitoring services" for CAMERAS
- Cabling is core networking infrastructure, not monitoring

---

#### Issue 10: Fiber Optic Miscategorized
**Current assignment:**
- "Fiber Optic Installation - Per Run" (SKU: FIBER-RUN-001) → Category 22 (Mobile Surveillance)

**Problem**: Fiber optic cabling is NOT mobile surveillance.

**Correct category**: 24 (Fiber Optic) - dedicated sub-category under Networking (parent 4)

**Reasoning**:
- Category 24 is "Fiber Optic" for "Single-mode and multi-mode fiber optic cable installation"
- Category 22 (Mobile Surveillance) is for "Mobile app access, cloud recording, and remote camera viewing"
- Fiber is networking infrastructure, not surveillance apps

---

#### Issue 11: Network Infrastructure Services Miscategorized
**Current assignments:**
- "Network Switch Deployment & Configuration" (SKU: NET-SW-001) → Category 23 (Structured Cabling)
- "Network Router & Firewall Deployment" (SKU: NET-RTR-FW-001) → Category 23 (Structured Cabling)

**Problem**: Switches, routers, and firewalls are active network equipment, not passive cabling.

**Correct category**: 25 (Network Infrastructure) - dedicated sub-category for active equipment

**Reasoning**:
- Category 25 is "Network Infrastructure" for "Enterprise switches, routers, firewalls, and core network equipment"
- Category 23 (Structured Cabling) is for passive copper cabling
- Active vs passive network components need separation

---

#### Issue 12: Wireless Services Miscategorized
**Current assignments:**
- "Wireless Access Point Installation - Per AP" (SKU: WAP-INST-001) → Category 24 (Fiber Optic)
- "Wireless Network Design & Site Survey" (SKU: WIFI-DESIGN-001) → Category 24 (Fiber Optic)

**Problem**: Wireless networking is not fiber optic cabling.

**Correct category**: 26 (Wireless Networks) - dedicated sub-category under Networking (parent 4)

**Reasoning**:
- Category 26 is "Wireless Networks" for "Enterprise WiFi access points, wireless controllers"
- Wireless and fiber are completely different networking technologies

---

#### Issue 13: Network Security Services Miscategorized
**Current assignments:**
- "Network Security Audit & Assessment" (SKU: SEC-AUDIT-001) → Category 25 (Network Infrastructure)
- "Next-Generation Firewall Configuration" (SKU: NGFW-CONFIG-001) → Category 25 (Network Infrastructure)
- "VPN Setup & Configuration" (SKU: VPN-SETUP-001) → Category 25 (Network Infrastructure)

**Problem**: Security services deserve their own category separate from general infrastructure.

**Correct category**: 27 (Network Security) - dedicated sub-category exists

**Reasoning**:
- Category 27 is "Network Security" for "Firewalls, VPNs, intrusion prevention, web filtering"
- While security uses infrastructure, it's a specialized domain requiring dedicated category
- Better service discovery and alignment with business focus

---

#### Issue 14: Network Design Service Miscategorized
**Current assignment:**
- "Network Infrastructure Assessment & Roadmap" (SKU: NET-ASSESS-001) → Category 26 (Wireless Networks)

**Problem**: Comprehensive network assessment is not wireless-specific.

**Correct category**: 28 (Network Design) - dedicated sub-category for design/planning services

**Reasoning**:
- Category 28 is "Network Design" for "Network architecture planning, site surveys, capacity planning"
- This is strategic planning across ALL network domains, not just wireless
- Infrastructure assessment covers wired, wireless, security, etc.

---

#### Issue 15: Monitoring Services Miscategorized
**Current assignment:**
- "24/7 Network Monitoring Setup" (SKU: MON-247-001) → Category 27 (Network Security)

**Problem**: While monitoring helps security, it's broader IT operations management.

**Correct category**: 29 (Monitoring Services) - dedicated sub-category under Managed IT (parent 5)

**Reasoning**:
- Category 29 is "Monitoring Services" for "24/7 proactive system monitoring, alerting, performance tracking"
- Monitoring covers infrastructure health, not just security events
- Better aligned with Managed IT operational services

---

#### Issue 16: Server Services Miscategorized
**Current assignments:**
- "Server Installation & Configuration" (SKU: SRV-INSTALL-001) → Category 28 (Network Design)
- "Server Virtualization Platform Deployment" (SKU: VM-PLATFORM-001) → Category 28 (Network Design)

**Problem**: Server deployment is NOT network design.

**Correct category**: 30 (Server Management) - dedicated sub-category under Managed IT (parent 5)

**Reasoning**:
- Category 30 is "Server Management" for "Server installation, configuration, patching, maintenance"
- Network design is planning/architecture; server deployment is implementation
- Server management is core Managed IT service

---

#### Issue 17: Backup Service Miscategorized
**Current assignment:**
- "Backup & Disaster Recovery Solution" (SKU: BKP-DR-001) → Category 29 (Monitoring Services)

**Problem**: Backup/DR is not the same as monitoring.

**Correct category**: 31 (Backup & Recovery) - dedicated sub-category exists

**Reasoning**:
- Category 31 is "Backup & Recovery" for "Data backup, disaster recovery planning, offsite replication"
- Monitoring tracks system health; backup protects data
- Separate operational domains

---

#### Issue 18: Managed IT Service Miscategorized
**Current assignment:**
- "Managed IT Services - Per Device (Monthly)" (SKU: MIT-DEVICE-001) → Category 30 (Server Management)

**Problem**: Comprehensive managed IT is broader than just server management.

**Correct category**: 32 (Help Desk Support) - best fit for all-inclusive managed services

**Reasoning**:
- This service includes "unlimited help desk support" as primary user-facing component
- Category 32 is "Help Desk Support" for "End-user technical support, ticketing systems, remote assistance"
- While service includes servers, the primary value is help desk + comprehensive support
- Alternative: could argue for staying in 30, but help desk is the differentiator from server-only services

---

#### Issue 19: Consulting Services Miscategorized
**Current assignments:**
- "IT Infrastructure Consulting - Hourly" (SKU: CONSULT-HOUR-001) → Category 33 (Patch Management)

**Problem**: Strategic consulting is NOT patch management.

**Correct category**: 35 (Infrastructure Planning) - dedicated consulting sub-category

**Reasoning**:
- Category 35 is "Infrastructure Planning" for "IT infrastructure assessment, technology roadmapping"
- Category 33 (Patch Management) is for "Automated software updates, security patching" - operational task
- Consulting is strategic planning, not tactical operations

---

#### Issue 20: Cloud Services Miscategorized
**Current assignments:**
- "Cloud Migration Services" (SKU: CLOUD-MIG-001) → Category 34 (Security Management)
- "Hybrid Cloud Architecture Design" (SKU: CLOUD-HYBRID-001) → Category 34 (Security Management)

**Problem**: Cloud migration/architecture is NOT security management.

**Correct category**: 36 (Cloud Strategy) - dedicated consulting sub-category exists

**Reasoning**:
- Category 36 is "Cloud Strategy" for "Cloud migration planning, hybrid infrastructure design"
- Category 34 (Security Management) is for "Antivirus, anti-malware, endpoint protection" - operational security
- Cloud strategy is strategic consulting domain

---

#### Issue 21: Security Program Development Miscategorized
**Current assignment:**
- "Cybersecurity Program Development" (SKU: SEC-PROG-001) → Category 35 (Infrastructure Planning)

**Problem**: While strategic, cybersecurity program is not infrastructure planning.

**Correct category**: 37 (Security Assessment) - dedicated consulting sub-category for security

**Reasoning**:
- Category 37 is "Security Assessment" for "Network security audits, vulnerability assessments, penetration testing"
- Cybersecurity program development includes assessments, policy development, etc.
- Better aligned with security consulting than general infrastructure

---

#### Issue 22: Project Management Miscategorized
**Current assignment:**
- "Technology Project Management Services" (SKU: PM-TECH-001) → Category 36 (Cloud Strategy)

**Problem**: Project management is not cloud-specific.

**Correct category**: 38 (Project Management) - dedicated consulting sub-category exists

**Reasoning**:
- Category 38 is "Project Management" for "Technology project planning, execution oversight, vendor coordination"
- This service manages ANY technology project, not just cloud
- Dedicated category provides clear service positioning

---

## Summary Statistics

### Total Services Reviewed: 58

### Miscategorized Services: 47 (81% miscategorization rate!)

### Services Correctly Categorized: 11
1. VOIP Phone System - Starter → Category 7 (VOIP Systems) ✓
2. VOIP Phone System - Professional → Category 7 (VOIP Systems) ✓
3. VOIP Phone System - Enterprise → Category 7 (VOIP Systems) ✓
4. SIP Trunk Configuration → Category 9 (Phone Services) ✓
5. Cloud PBX Deployment → Category 8 (PBX Solutions) ✓
6. On-Premise PBX Installation → Category 8 (PBX Solutions) ✓
7. Conference Room AV System → Category 10 (Unified Communications) ✓
8. Unified Communications Platform → Category 10 (Unified Communications) ✓
9. Microsoft Teams Voice Deployment → Category 10 (Unified Communications) ✓
10. Call Center - Basic Setup → Category 12 (Call Center Solutions) ✓
11. SIP trunk is correctly in Phone Services ✓

### Category Correction Summary by Domain

**Access Control Services (6 services):**
- 2 services need: Category 11 → 13 (Single Door Systems)
- 2 services need: Category 12 → 14 (Multi-Door Systems)
- 2 services need: Category 16 → 15 (Card Readers & Credentials)
- 1 service needs: Category 15 → 17 (Visitor Management)

**Camera System Services (8 services):**
- 5 services need: Category 16 → 18 (IP Camera Systems)
- 1 service needs: Category 17 → 19 (NVR Solutions)
- 1 service needs: Category 18 → 20 (Video Analytics)
- 1 service needs: Category 19 → 21 (Remote Monitoring)

**Networking Services (14 services):**
- 3 services need: Category 21 → 23 (Structured Cabling)
- 1 service needs: Category 22 → 24 (Fiber Optic)
- 2 services need: Category 23 → 25 (Network Infrastructure)
- 2 services need: Category 24 → 26 (Wireless Networks)
- 3 services need: Category 25 → 27 (Network Security)
- 1 service needs: Category 26 → 28 (Network Design)
- 1 service needs: Category 27 → 29 (Monitoring Services)

**Managed IT Services (4 services):**
- 2 services need: Category 28 → 30 (Server Management)
- 1 service needs: Category 29 → 31 (Backup & Recovery)
- 1 service needs: Category 30 → 32 (Help Desk Support)

**Consulting Services (5 services):**
- 1 service needs: Category 33 → 35 (Infrastructure Planning)
- 2 services need: Category 34 → 36 (Cloud Strategy)
- 1 service needs: Category 35 → 37 (Security Assessment)
- 1 service needs: Category 36 → 38 (Project Management)

## Root Cause Analysis

The miscategorization appears systematic rather than random:

1. **Sequential ID Confusion**: Services appear to have been assigned category IDs sequentially or using wrong offsets
   - Access control services assigned to phone categories (11, 12 instead of 13, 14)
   - Camera services assigned to access control categories (16, 17 instead of 18, 19)
   - Network services shifted by consistent offsets

2. **Pattern**: Almost all services are off by approximately the same number of category positions, suggesting:
   - Possible spreadsheet/calculation error during data preparation
   - Category IDs may have been renumbered after initial service assignments
   - Bulk import with wrong category ID mapping

3. **Impact**: This severely impacts:
   - Service catalog navigation and discovery
   - Customer experience finding relevant services
   - Sales team ability to present service offerings
   - SEO and site structure
   - Reporting and analytics by category

## Recommendations

1. **Immediate**: Apply all corrections in migration file
2. **Testing**: After applying corrections, verify in admin UI that services appear under correct categories
3. **Process**: Establish category ID reference document for future service additions
4. **Validation**: Add migration validation checking category_id foreign key constraints
5. **Documentation**: Update service catalog documentation with correct category structure
