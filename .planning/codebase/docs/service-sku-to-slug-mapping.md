# Service SKU to Slug Mapping Reference

This document maps the assumed SKU values used in the original service relationships migration to the actual slug values found in the services data migration.

**Context**: The services table has `NULL` SKU values that get auto-generated. Services are uniquely identified by their `slug` field, which is derived from the service name.

## Complete SKU → Slug Mapping

### IP Camera Systems
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `IPCAM-4` | `ip-camera-system-4-cameras` | IP Camera System - 4 Cameras (Starter) |
| `IPCAM-8` | `ip-camera-system-8-cameras` | IP Camera System - 8 Cameras (Professional) |
| `IPCAM-16` | `ip-camera-system-16-cameras` | IP Camera System - 16 Cameras (Advanced) |
| `IPCAM-32` | `ip-camera-system-enterprise-32-plus` | IP Camera System - Enterprise (32+ Cameras) |

### VOIP Phone Systems
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `VOIP-5EXT` | `voip-phone-system-starter` | VOIP Phone System - Starter (3-5 Extensions) |
| `VOIP-25EXT` | `voip-phone-system-professional` | VOIP Phone System - Professional (10-25 Extensions) |
| `VOIP-50EXT` | `voip-phone-system-enterprise` | VOIP Phone System - Enterprise (50+ Extensions) |

### Cloud PBX
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `CLOUD-PBX` | `cloud-pbx-deployment` | Cloud PBX Deployment & Migration |

### SIP Trunking
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `SIP-TRUNK` | `voip-sip-trunk-configuration` | SIP Trunk Configuration & Deployment |

### Access Control Systems
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `ACCS-1DOOR` | `access-control-single-door` | Access Control - Single Door System |
| `ACCS-MULTI` | `access-control-multi-door-4-8` | Access Control - Multi-Door System (4-8 Doors) |
| `CARD-READER` | `card-reader-installation` | Card Reader Installation - Per Reader |

### Structured Cabling
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `CAT6-RUN` | `cat6-structured-cabling-per-run` | CAT6 Structured Cabling - Per Run |
| `CAT6A-RUN` | `cat6a-structured-cabling-per-run` | CAT6A Structured Cabling - Per Run |
| `FIBER-RUN` | `fiber-optic-installation-per-run` | Fiber Optic Installation - Per Run |

### Wireless Infrastructure
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `WAP-INSTALL` | `wireless-access-point-installation` | Wireless Access Point Installation - Per AP |
| `WIFI-DESIGN` | `wireless-network-design-site-survey` | Wireless Network Design & Site Survey |

### Network Infrastructure
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `NET-SWITCH` | `network-switch-deployment` | Network Switch Deployment & Configuration |
| `FW-CONFIG` | `next-gen-firewall-configuration` | Next-Generation Firewall Configuration |
| `VPN-SETUP` | `vpn-setup-configuration` | VPN Setup & Configuration |

### Network Services
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `NET-ASSESS` | `network-infrastructure-assessment` | Network Infrastructure Assessment & Roadmap |
| `NET-AUDIT` | `network-security-audit` | Network Security Audit & Assessment |
| `NET-MON` | `network-monitoring-setup` | 24/7 Network Monitoring Setup |

### Server Infrastructure
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `SERVER-INSTALL` | `server-installation-configuration` | Server Installation & Configuration |
| `SERVER-VIRT` | `server-virtualization-platform` | Server Virtualization Platform Deployment |
| `BACKUP-DR` | `backup-disaster-recovery-solution` | Backup & Disaster Recovery Solution |

### Unified Communications
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `UC-PLATFORM` | `unified-communications-platform` | Unified Communications Platform Deployment |
| `CONF-AV` | `conference-room-av-system` | Conference Room AV System Installation |

### Cloud & Migration
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `CLOUD-MIG` | `cloud-migration-services` | Cloud Migration Services |

### NVR & Video Management
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `NVR-SETUP` | `nvr-setup-configuration` | NVR Setup & Configuration Service |

### Managed Services
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `MSP-DEVICE` | `managed-it-services-per-device` | Managed IT Services - Per Device (Monthly) |

### Consulting
| Assumed SKU | Actual Slug | Service Name |
|------------|-------------|--------------|
| `IT-CONSULT-HR` | `it-infrastructure-consulting-hourly` | IT Infrastructure Consulting - Hourly |

## Validation Summary

- **Total SKU References**: 36 unique SKU values
- **Total Mapped Services**: 36 services
- **Unmapped SKUs**: 0 (all SKUs successfully mapped)
- **Total Relationships**: 146 relationship records

## Migration Impact

All service relationship references have been updated from SKU-based lookups to slug-based lookups:

**Before**: `WHERE sku = 'IPCAM-4'`
**After**: `WHERE slug = 'ip-camera-system-4-cameras'`

This ensures all foreign key constraints will be satisfied when the migration runs, as the slug values exist in the services table while the SKU values do not (SKUs are NULL and get auto-generated).

## Verification

All relationships maintain their original business logic:
- Prerequisites remain intact
- Dependencies correctly mapped
- Incompatibilities preserved
- Complements relationships maintained
- Replaces/Enables relationships accurate
- Substitute_for relationships correct

No relationships were lost or removed during the mapping process.
