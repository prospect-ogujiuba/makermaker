# MakerMaker Service Catalog Configuration Guide

**Version:** 1.0
**Last Updated:** December 20, 2025
**Audience:** Technical Administrators

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Understanding the Data Model](#understanding-the-data-model)
3. [Configuration Order](#configuration-order)
4. [Component Reference](#component-reference)
5. [Configuration Scenarios](#configuration-scenarios)
6. [Verification & Validation](#verification--validation)
7. [Troubleshooting](#troubleshooting)

---

## System Overview

### What is MakerMaker?

MakerMaker is a comprehensive service catalog system for WordPress that manages:

- **Services** - Core offerings you sell to customers
- **Pricing** - Flexible pricing models with tier-based pricing and currency support
- **Equipment** - Physical or virtual equipment required for service delivery
- **Deliverables** - Tangible outputs customers receive from services
- **Coverage Areas** - Geographic regions where services are available
- **Bundles** - Packaged combinations of services at discounted rates

### Key Business Concepts

**Service Catalog Structure:**
```
Service (e.g., "Website Design")
├─ Category: "Web Development"
├─ Type: "Project-Based"
├─ Complexity: "Advanced" (2.0x multiplier)
├─ Pricing: $5,000 (Standard Tier)
├─ Equipment: MacBook Pro, Adobe Creative Suite
├─ Deliverables: Wireframes, Mockups, Production Files
└─ Coverage: North America
```

**Why This Matters:**

Every service you configure must reference supporting data (categories, types, complexity levels). The database enforces this through **foreign key constraints** - attempting to create a Service without a valid Category will fail. This guide shows you the correct configuration order.

---

## Understanding the Data Model

### The Three-Tier Hierarchy

The system uses a three-tier data architecture:

```
TIER 1: LOOKUP TABLES (Configure First)
└─ Define categories, types, pricing models
   Used by: Everything else

TIER 2: BUSINESS OBJECTS (Configure Second)
└─ Services, Equipment, Deliverables, Coverage Areas
   Depends on: Lookup tables
   Used by: Junction tables

TIER 3: JUNCTION TABLES (Configure Last)
└─ Connect business objects together
   Depends on: Business objects
   Examples: Service Prices, Service Equipment, Bundle Items
```

### Why Order Matters: Foreign Key Constraints

The database uses **foreign key constraints** to maintain data integrity:

> **Foreign Key Constraint:** A database rule that says "Field X must reference a valid ID in Table Y"

**Example:**
```sql
Service.category_id → Must exist in Categories.id
```

If you try to create a Service with `category_id = 5` but no Category with `id = 5` exists, the database will reject it with an error like:

```
Cannot add or update a child row: a foreign key constraint fails
(`srvc_services`, CONSTRAINT `fk_service__category`
FOREIGN KEY (`category_id`) REFERENCES `srvc_categories` (`id`))
```

**The Solution:** Always create parent records (Categories) before child records (Services).

### Dependency Visualization

```
Complexity Levels ─┐
Pricing Models ────┤
Pricing Tiers ─────┤
Service Categories ┼──► SERVICES ──┐
Service Types ─────┘                │
                                    ├──► Service Prices
                                    ├──► Service Equipment
                                    ├──► Service Deliverables
Equipment ──────────────────────────┤
Deliverables ───────────────────────┤
Delivery Methods ───────────────────┤
Coverage Areas ─────────────────────┘
```

---

## Configuration Order

Follow this step-by-step configuration sequence to avoid dependency errors.

### Phase 1: Foundation Lookup Tables

These tables have NO dependencies - configure them first.

#### Step 1: Complexity Levels

**Location:** Services → Complexity Levels

**What it is:** Difficulty tiers that multiply the base price of services.

**Why create it now:** Services require a complexity level assignment. Complexity affects final pricing calculations.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Display name | "Basic", "Intermediate", "Advanced" |
| `level` | Number (0-255) | Yes | Numeric level (unique) | 1, 2, 3 |
| `price_multiplier` | Decimal | Yes | Price multiplier (0.0-99.9) | 1.0, 1.5, 2.0 |

**Real-world examples:**

```
Complexity Level: "Basic"
├─ Level: 1
├─ Multiplier: 1.0 (base price, no increase)
└─ Use case: Simple, routine services

Complexity Level: "Intermediate"
├─ Level: 2
├─ Multiplier: 1.5 (50% price increase)
└─ Use case: Moderate complexity requiring some expertise

Complexity Level: "Advanced"
├─ Level: 3
├─ Multiplier: 2.0 (100% price increase)
└─ Use case: Complex services requiring specialized skills

Complexity Level: "Expert"
├─ Level: 4
├─ Multiplier: 3.0 (200% price increase)
└─ Use case: Highly specialized, critical services
```

**Validation constraints:**
- `name` must be unique
- `level` must be unique (0-255)
- `price_multiplier` must be between 0.0 and 99.9

**Common mistakes:**
- Creating duplicate level numbers
- Using multipliers less than 1.0 (this reduces price - usually unintended)
- Forgetting that this multiplier applies AFTER pricing tier discounts

---

#### Step 2: Pricing Models

**Location:** Services → Pricing Models

**What it is:** How services are billed (hourly, fixed price, per user, etc.).

**Why create it now:** Service Prices require a pricing model to determine billing method.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Display name | "Hourly Rate" |
| `code` | Text | Yes | Unique identifier (slug) | "hourly" |
| `description` | Text | No | Explanation of model | "Billed per hour worked" |
| `is_time_based` | Boolean | Yes | Time-based billing? | Yes/No |

**Real-world examples:**

```
Pricing Model: "Hourly Rate"
├─ Code: hourly
├─ Time-based: Yes
└─ Use: Consulting, support, development services

Pricing Model: "Fixed Price"
├─ Code: fixed
├─ Time-based: No
└─ Use: Project-based work with defined scope

Pricing Model: "Per User/Month"
├─ Code: per_user_monthly
├─ Time-based: No
└─ Use: SaaS subscriptions, recurring services

Pricing Model: "Per Device"
├─ Code: per_device
├─ Time-based: No
└─ Use: Managed services, monitoring

Pricing Model: "Tiered Usage"
├─ Code: tiered_usage
├─ Time-based: No
└─ Use: Volume-based pricing (100 units, 500 units, etc.)
```

**Validation constraints:**
- `name` must be unique
- `code` must be unique (lowercase, no spaces)

**Common mistakes:**
- Using spaces in `code` field (use hyphens or underscores)
- Creating models you won't actually use (keep it simple initially)

---

#### Step 3: Pricing Tiers

**Location:** Services → Pricing Tiers

**What it is:** Customer segments with different pricing (small business, enterprise, etc.).

**Why create it now:** Service Prices are specific to a pricing tier - same service can have different prices for different customer tiers.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Display name | "Small Business" |
| `code` | Text | Yes | Unique identifier | "small_business" |
| `sort_order` | Number | Yes | Display order (0-255) | 1, 2, 3 |
| `discount_pct` | Decimal | Yes | Default discount (0-100%) | 0.00, 10.00, 15.00 |
| `min_volume` | Number | No | Minimum volume threshold | 1 |
| `max_volume` | Number | No | Maximum volume threshold | 50 |

**Real-world examples:**

```
Tier: "Startup"
├─ Code: startup
├─ Sort Order: 1
├─ Discount: 0% (full price)
├─ Volume: 1-10 users
└─ Use: New companies, small teams

Tier: "Small Business"
├─ Code: small_business
├─ Sort Order: 2
├─ Discount: 5%
├─ Volume: 11-50 users
└─ Use: Growing companies

Tier: "Enterprise"
├─ Code: enterprise
├─ Sort Order: 3
├─ Discount: 15%
├─ Volume: 51+ users
└─ Use: Large organizations

Tier: "Government"
├─ Code: government
├─ Sort Order: 4
├─ Discount: 10%
├─ Volume: No limit
└─ Use: Government contracts, public sector
```

**Validation constraints:**
- `name` must be unique
- `code` must be unique
- `discount_pct` must be 0-100
- If both `min_volume` and `max_volume` set, min must be ≤ max

**Common mistakes:**
- Overlapping volume ranges (can cause confusion)
- Forgetting that discounts stack with complexity multipliers
- Using negative discount percentages

---

#### Step 4: Currency Rates

**Location:** Services → Currency Rates

**What it is:** Exchange rates for multi-currency pricing.

**Why create it now:** Needed if you price services in multiple currencies. Optional if you only use one currency (e.g., CAD).

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `from_currency` | Text (3 chars) | Yes | Source currency code (ISO 4217) | "CAD" |
| `to_currency` | Text (3 chars) | Yes | Target currency code | "USD" |
| `exchange_rate` | Decimal | Yes | Conversion rate | 0.738000 |
| `effective_date` | Date | Yes | When rate became effective | 2025-12-20 |
| `source` | Text | No | Rate source | "manual", "bank_api" |

**Real-world examples:**

```
Currency Rate: CAD to USD
├─ From: CAD
├─ To: USD
├─ Rate: 0.738 (1 CAD = 0.738 USD)
├─ Effective: 2025-12-20
└─ Source: bank_api

Currency Rate: USD to EUR
├─ From: USD
├─ To: EUR
├─ Rate: 0.92
├─ Effective: 2025-12-20
└─ Source: manual
```

**Validation constraints:**
- Currency codes must be 3 uppercase letters (e.g., CAD, USD, EUR)
- `from_currency` cannot equal `to_currency`
- `exchange_rate` must be greater than 0
- Combination of `from_currency`, `to_currency`, `effective_date` must be unique

**Common mistakes:**
- Using lowercase currency codes (must be uppercase)
- Creating bidirectional rates (CAD→USD is enough; USD→CAD is calculated)
- Forgetting to update rates when they change (old rates remain valid for historical prices)

**When to skip:** If you only price services in one currency (e.g., only CAD), you can skip this entirely.

---

#### Step 5: Service Categories

**Location:** Services → Service Categories

**What it is:** Hierarchical organization of services (like folders for files).

**Why create it now:** Every Service must belong to a Category. Categories can be nested (parent/child relationships).

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `parent_id` | Select | No | Parent category (for sub-categories) | "Web Development" |
| `name` | Text | Yes | Display name | "WordPress Services" |
| `slug` | Text | Yes | URL-friendly identifier | "wordpress-services" |
| `icon` | Text | No | Icon class or name | "dashicons-wordpress" |
| `description` | Text | No | Category description | "WordPress development services" |
| `sort_order` | Number | Yes | Display order | 1, 2, 3 |
| `is_active` | Boolean | Yes | Visible/hidden | Yes |

**Real-world examples:**

```
Category: "Web Development" (Top-level)
├─ Parent: None
├─ Slug: web-development
├─ Sort Order: 1
├─ Children:
│   ├─ "WordPress Services"
│   ├─ "Custom Applications"
│   └─ "E-commerce"

Category: "WordPress Services" (Sub-category)
├─ Parent: Web Development
├─ Slug: wordpress-services
├─ Sort Order: 1
└─ Services: Theme Development, Plugin Development, Maintenance

Category: "Consulting" (Top-level)
├─ Parent: None
├─ Slug: consulting
├─ Sort Order: 2
└─ Children:
    ├─ "Technical Consulting"
    ├─ "Business Consulting"
    └─ "Strategy Consulting"
```

**Validation constraints:**
- `name` must be unique
- `slug` must be unique
- Cannot set `parent_id` to itself (category cannot be its own parent)
- `is_active` determines visibility in customer-facing interfaces

**Common mistakes:**
- Creating too many categories too soon (start simple, expand later)
- Circular parent relationships (A → B → A)
- Using spaces in slugs (use hyphens)

**Hierarchical note:** You can create parent categories first, then add child categories later. Or create all as top-level initially, then reorganize.

---

#### Step 6: Service Types

**Location:** Services → Service Types

**What it is:** Classification of how services are delivered (on-site, remote, hybrid).

**Why create it now:** Services require a type to define delivery characteristics and default durations.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Display name | "On-Site Project" |
| `code` | Text | Yes | Unique identifier | "onsite_project" |
| `description` | Text | No | Type description | "Requires on-site presence" |
| `requires_site_visit` | Boolean | Yes | On-site required? | Yes |
| `supports_remote` | Boolean | Yes | Can be done remotely? | No |
| `estimated_duration_hours` | Decimal | No | Default duration | 40.00 |

**Real-world examples:**

```
Service Type: "Remote Consulting"
├─ Code: remote_consulting
├─ Site Visit Required: No
├─ Supports Remote: Yes
├─ Estimated Duration: 2 hours
└─ Use: Phone/video consultations

Service Type: "On-Site Installation"
├─ Code: onsite_installation
├─ Site Visit Required: Yes
├─ Supports Remote: No
├─ Estimated Duration: 8 hours
└─ Use: Hardware installation, network setup

Service Type: "Hybrid Project"
├─ Code: hybrid_project
├─ Site Visit Required: Yes
├─ Supports Remote: Yes
├─ Estimated Duration: 160 hours
└─ Use: Projects with both remote and on-site work

Service Type: "Managed Service"
├─ Code: managed_service
├─ Site Visit Required: No
├─ Supports Remote: Yes
├─ Estimated Duration: null (ongoing)
└─ Use: Continuous monitoring, SaaS services
```

**Validation constraints:**
- `name` must be unique
- `code` must be unique
- `estimated_duration_hours` must be ≥ 0 (if set)
- Both `requires_site_visit` and `supports_remote` can be Yes (hybrid services)

**Common mistakes:**
- Setting both `requires_site_visit` and `supports_remote` to No (service can't be delivered)
- Using estimated duration for ongoing services (leave null)

---

### Phase 2: Business Objects

These depend on Phase 1 lookup tables.

#### Step 7: Services

**Location:** Services → Services

**What it is:** The core catalog items you sell to customers.

**Why create it now:** This is the heart of your catalog. Once services exist, you can attach pricing, equipment, deliverables, etc.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `sku` | Text | No | Stock keeping unit (auto-generated) | "WP-THEME-DEV" |
| `slug` | Text | Yes | URL identifier (auto-generated) | "wordpress-theme-development" |
| `name` | Text | Yes | Service name | "WordPress Theme Development" |
| `short_desc` | Text | Yes | Brief description (max 512 chars) | "Custom WordPress theme design and development" |
| `long_desc` | Text | No | Detailed description | Full HTML description |
| `category_id` | Select | Yes | Service category | "WordPress Services" |
| `service_type_id` | Select | Yes | Service type | "Remote Project" |
| `complexity_id` | Select | Yes | Complexity level | "Advanced" |
| `is_active` | Boolean | Yes | Available for sale? | Yes |
| `is_featured` | Boolean | Yes | Promoted on homepage? | No |
| `minimum_quantity` | Decimal | No | Minimum purchase quantity | 1.00 |
| `maximum_quantity` | Decimal | No | Maximum purchase quantity | null |
| `estimated_hours` | Decimal | No | Estimated effort | 80.00 |
| `skill_level` | Select | No | Required skill level | "advanced" |
| `metadata` | JSON | No | Additional properties | {"tags": ["php", "css"]} |

**Dependency requirements:**

> **Important:** Before creating a Service, you MUST have:
> - At least one Category (`category_id`)
> - At least one Service Type (`service_type_id`)
> - At least one Complexity Level (`complexity_id`)

**Real-world examples:**

```
Service: "WordPress Theme Development"
├─ SKU: WP-THEME-DEV (auto-generated from name)
├─ Category: WordPress Services
├─ Type: Remote Project
├─ Complexity: Advanced (2.0x multiplier)
├─ Estimated Hours: 80 hours
├─ Minimum Quantity: 1 theme
├─ Maximum Quantity: null (no limit)
├─ Skill Level: advanced
├─ Active: Yes
└─ Featured: No

Service: "SEO Consultation - Hourly"
├─ SKU: SEO-CONSULT-HR
├─ Category: Marketing Services
├─ Type: Remote Consulting
├─ Complexity: Intermediate (1.5x multiplier)
├─ Estimated Hours: 1 hour
├─ Minimum Quantity: 1 hour
├─ Maximum Quantity: 40 hours
├─ Skill Level: intermediate
├─ Active: Yes
└─ Featured: Yes

Service: "Server Setup & Configuration"
├─ SKU: SERVER-SETUP
├─ Category: Infrastructure
├─ Type: On-Site Installation
├─ Complexity: Expert (3.0x multiplier)
├─ Estimated Hours: 16 hours
├─ Minimum Quantity: 1 server
├─ Maximum Quantity: 10 servers
├─ Skill Level: expert
├─ Active: Yes
└─ Featured: No
```

**Validation constraints:**
- `sku` must be unique (if provided; can be auto-generated)
- `slug` must be unique
- `minimum_quantity` must be > 0 (if set)
- `maximum_quantity` must be ≥ `minimum_quantity` (if both set)
- `estimated_hours` must be > 0 (if set)
- `skill_level` options: entry, intermediate, advanced, expert, specialist

**Common mistakes:**
- Creating Services before Categories/Types/Complexity → Foreign key error
- Using minimum_quantity = 0 (validation rejects this)
- Forgetting that complexity_id affects final pricing

**Auto-code generation:** The system auto-generates `sku` and `slug` from `name` if not provided:
- SKU: uppercase, hyphens, from name (e.g., "WordPress Theme" → "WORDPRESS-THEME")
- Slug: lowercase, hyphens, from name (e.g., "WordPress Theme" → "wordpress-theme")

---

#### Step 8: Equipment

**Location:** Services → Equipment

**What it is:** Physical or virtual equipment required to deliver services.

**Why create it now:** Needed before associating equipment with services (Service Equipment junction).

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Equipment name | "MacBook Pro 16-inch" |
| `sku` | Text | Yes | Product identifier | "MBPRO16-2023" |
| `manufacturer` | Text | Yes | Equipment maker | "Apple" |
| `model` | Text | No | Model number | "A2485" |
| `category` | Text | No | Equipment category | "Laptops" |
| `unit_cost` | Decimal | No | Cost per unit | 2999.00 |
| `is_consumable` | Boolean | Yes | One-time use? | No |
| `specs` | JSON | No | Technical specifications | {"ram": "32GB", "storage": "1TB"} |

**Real-world examples:**

```
Equipment: "MacBook Pro 16-inch"
├─ SKU: MBPRO16-2023
├─ Manufacturer: Apple
├─ Model: A2485
├─ Category: Laptops
├─ Unit Cost: $2,999.00
├─ Consumable: No (reusable)
└─ Specs: {"ram": "32GB", "storage": "1TB SSD", "processor": "M2 Pro"}

Equipment: "Adobe Creative Cloud License"
├─ SKU: ADOBE-CC-ANNUAL
├─ Manufacturer: Adobe
├─ Model: null
├─ Category: Software Licenses
├─ Unit Cost: $599.88/year
├─ Consumable: Yes (annual renewal)
└─ Specs: {"license_type": "annual", "apps": "all"}

Equipment: "Network Cable - Cat6"
├─ SKU: CABLE-CAT6-100FT
├─ Manufacturer: Generic
├─ Model: null
├─ Category: Networking
├─ Unit Cost: $29.99
├─ Consumable: Yes (left on-site)
└─ Specs: {"length": "100ft", "type": "Cat6"}

Equipment: "Project Management Software"
├─ SKU: PM-SOFT-USER
├─ Manufacturer: Various
├─ Model: null
├─ Category: Software
├─ Unit Cost: $15.00/user/month
├─ Consumable: Yes (subscription)
└─ Specs: {"billing": "per_user_monthly"}
```

**Validation constraints:**
- `name` must be unique
- `sku` must be unique
- `unit_cost` must be ≥ 0 (if set)

**Common mistakes:**
- Confusing consumable vs. reusable:
  - Consumable: Used once, not returned (cables, licenses, materials)
  - Reusable: Equipment you own/reuse (laptops, tools, servers)
- Not tracking cost (makes it hard to calculate true service costs)

**Use cases:**
- Track equipment costs in service pricing
- Ensure required equipment is available before scheduling
- Bill customers for consumables

---

#### Step 9: Deliverables

**Location:** Services → Deliverables

**What it is:** Tangible outputs customers receive when service completes.

**Why create it now:** Needed before associating deliverables with services.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Deliverable name | "Functional Wireframes" |
| `description` | Text | Yes | What customer receives | "Interactive wireframes showing all user flows" |
| `deliverable_type` | Select | Yes | Output type | document, software, hardware, service, training, report |
| `template_path` | Text | No | File template location | "/templates/wireframes.sketch" |
| `estimated_effort_hours` | Decimal | No | Time to create | 8.00 |
| `requires_approval` | Boolean | Yes | Needs client sign-off? | Yes |

**Real-world examples:**

```
Deliverable: "Functional Wireframes"
├─ Type: document
├─ Description: Interactive wireframes showing all user flows and screens
├─ Template: /templates/wireframes/ux-template.sketch
├─ Effort: 8 hours
├─ Requires Approval: Yes
└─ Use: Web design projects

Deliverable: "Production WordPress Theme Files"
├─ Type: software
├─ Description: Complete theme package with PHP, CSS, JS, and documentation
├─ Template: /templates/wordpress/theme-boilerplate.zip
├─ Effort: 40 hours
├─ Requires Approval: No
└─ Use: WordPress theme development

Deliverable: "Monthly Analytics Report"
├─ Type: report
├─ Description: PDF report with traffic analysis, conversion metrics, and recommendations
├─ Template: /templates/reports/analytics-monthly.docx
├─ Effort: 2 hours
├─ Requires Approval: No
└─ Use: SEO/marketing services

Deliverable: "Training Session - WordPress Admin"
├─ Type: training
├─ Description: 2-hour live training on WordPress content management
├─ Template: null
├─ Effort: 2 hours
├─ Requires Approval: No
└─ Use: Client onboarding
```

**Validation constraints:**
- `estimated_effort_hours` must be > 0 (if set)
- `deliverable_type` options: document, software, hardware, service, training, report

**Common mistakes:**
- Creating deliverables too granular (every email is not a deliverable)
- Forgetting to set `requires_approval` for milestone deliverables
- Not estimating effort (makes service scoping harder)

---

#### Step 10: Delivery Methods

**Location:** Services → Delivery Methods

**What it is:** How services/deliverables are delivered to customers.

**Why create it now:** Needed before associating delivery methods with services.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Method name | "Email Delivery" |
| `code` | Text | Yes | Unique identifier | "email" |
| `description` | Text | No | Method description | "Files sent via secure email link" |
| `requires_site_access` | Boolean | Yes | On-site access needed? | No |
| `supports_remote` | Boolean | Yes | Can be done remotely? | Yes |
| `default_lead_time_days` | Number | Yes | Default delivery time | 0 |
| `default_sla_hours` | Number | No | Service level agreement | 24 |

**Real-world examples:**

```
Delivery Method: "Email Delivery"
├─ Code: email
├─ Site Access Required: No
├─ Supports Remote: Yes
├─ Lead Time: 0 days (instant)
├─ SLA: 24 hours (response time)
└─ Use: Digital deliverables (documents, files)

Delivery Method: "On-Site Installation"
├─ Code: onsite_install
├─ Site Access Required: Yes
├─ Supports Remote: No
├─ Lead Time: 5 days (scheduling)
├─ SLA: null
└─ Use: Hardware installation, network setup

Delivery Method: "Cloud Portal Access"
├─ Code: cloud_portal
├─ Site Access Required: No
├─ Supports Remote: Yes
├─ Lead Time: 0 days
├─ SLA: 1 hour
└─ Use: SaaS services, software access

Delivery Method: "Physical Shipment"
├─ Code: shipping
├─ Site Access Required: No
├─ Supports Remote: No
├─ Lead Time: 7 days
├─ SLA: null
└─ Use: Hardware, printed materials
```

**Validation constraints:**
- `name` must be unique
- `code` must be unique

**Common mistakes:**
- Creating too many similar methods (consolidate where possible)
- Not setting realistic lead times (causes customer expectation issues)

---

#### Step 11: Coverage Areas

**Location:** Services → Coverage Areas

**What it is:** Geographic regions where you offer services.

**Why create it now:** Needed before associating coverage with services.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Area name | "Greater Toronto Area" |
| `code` | Text | Yes | Unique identifier | "GTA" |
| `country_code` | Text (2 chars) | No | ISO country code | "CA" |
| `region_type` | Select | No | Area type | city, province, state, country, continent, global |
| `timezone` | Text | No | Timezone identifier | "America/Toronto" |
| `postal_code_pattern` | Text | No | Postal code regex pattern | "^M[0-9][A-Z]" |

**Real-world examples:**

```
Coverage Area: "Greater Toronto Area"
├─ Code: GTA
├─ Country: CA (Canada)
├─ Region Type: city
├─ Timezone: America/Toronto
├─ Postal Pattern: ^M[0-9][A-Z] (Toronto postal codes)
└─ Use: Local on-site services

Coverage Area: "Ontario"
├─ Code: ON
├─ Country: CA
├─ Region Type: province
├─ Timezone: America/Toronto
├─ Postal Pattern: null (entire province)
└─ Use: Regional services

Coverage Area: "North America"
├─ Code: NA
├─ Country: null (multi-country)
├─ Region Type: continent
├─ Timezone: null (multiple timezones)
├─ Postal Pattern: null
└─ Use: Remote consulting, SaaS

Coverage Area: "Global"
├─ Code: GLOBAL
├─ Country: null
├─ Region Type: global
├─ Timezone: null
├─ Postal Pattern: null
└─ Use: Worldwide remote services
```

**Validation constraints:**
- `code` must be unique
- `country_code` must be 2 uppercase letters (e.g., CA, US, UK)

**Common mistakes:**
- Using full country names in `country_code` (must be 2-letter ISO codes)
- Not setting timezone for local service areas (affects scheduling)

**When to skip:** If all services are remote/global, create one "Global" coverage area.

---

### Phase 3: Junction Tables (Connecting Everything)

These connect business objects together.

#### Step 12: Service Prices

**Location:** Services → Service Prices

**What it is:** Pricing for each service, specific to tier and model.

**Why create it now:** Services need prices to be sellable. One service can have multiple prices (different tiers, different models).

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `service_id` | Select | Yes | Service being priced | "WordPress Theme Development" |
| `pricing_tier_id` | Select | Yes | Customer tier | "Small Business" |
| `pricing_model_id` | Select | Yes | Billing method | "Fixed Price" |
| `currency` | Text (3 chars) | Yes | Currency code | "CAD" |
| `amount` | Decimal | No | Base price | 5000.00 |
| `unit` | Text | No | Pricing unit | null, "hour", "user", "device", "month" |
| `setup_fee` | Decimal | Yes | One-time fee | 0.00 |
| `valid_from` | DateTime | Yes | Price effective date | 2025-01-01 00:00:00 |
| `valid_to` | DateTime | No | Price expiration | null (indefinite) |
| `is_current` | Boolean | Yes | Active price? | Yes |
| `approval_status` | Select | Yes | Approval state | draft, pending, approved, rejected |

**Dependency requirements:**

> **Important:** Before creating a Service Price, you MUST have:
> - A Service (`service_id`)
> - A Pricing Tier (`pricing_tier_id`)
> - A Pricing Model (`pricing_model_id`)

**Real-world examples:**

```
Service Price: WordPress Theme Development - Small Business - Fixed
├─ Service: WordPress Theme Development
├─ Tier: Small Business (5% discount)
├─ Model: Fixed Price
├─ Amount: $5,000.00 (base price)
├─ Unit: null (one-time project)
├─ Setup Fee: $0.00
├─ Currency: CAD
├─ Valid From: 2025-01-01
├─ Valid To: null (ongoing)
├─ Current: Yes
├─ Approval: approved
└─ Final Price Calculation:
    Base: $5,000.00
    × Complexity (Advanced = 2.0x): $10,000.00
    - Tier Discount (5%): -$500.00
    = Final Price: $9,500.00

Service Price: SEO Consultation - Enterprise - Hourly
├─ Service: SEO Consultation
├─ Tier: Enterprise (15% discount)
├─ Model: Hourly Rate
├─ Amount: $150.00/hour
├─ Unit: hour
├─ Setup Fee: $0.00
├─ Currency: CAD
├─ Valid From: 2025-01-01
├─ Valid To: null
├─ Current: Yes
├─ Approval: approved
└─ Final Price Calculation (per hour):
    Base: $150.00
    × Complexity (Intermediate = 1.5x): $225.00
    - Tier Discount (15%): -$33.75
    = Final Hourly Rate: $191.25

Service Price: Managed WordPress Hosting - Startup - Monthly
├─ Service: Managed WordPress Hosting
├─ Tier: Startup
├─ Model: Per User/Month
├─ Amount: $99.00/month
├─ Unit: month
├─ Setup Fee: $199.00 (one-time)
├─ Currency: CAD
├─ Valid From: 2025-01-01
├─ Valid To: null
├─ Current: Yes
├─ Approval: approved
└─ First Month Bill: $99.00 + $199.00 setup = $298.00
```

**Pricing calculation formula:**

```
Final Price = ((Base Amount × Complexity Multiplier) - Tier Discount) + Setup Fee
```

**Validation constraints:**
- `amount` must be ≥ 0 (if set; can be null for quote-based services)
- `setup_fee` must be ≥ 0
- `valid_to` must be > `valid_from` (if both set)
- Combination of `service_id`, `pricing_tier_id`, `pricing_model_id`, `is_current=1` must be unique
  - Means: Only ONE current price per service/tier/model combination
- `currency` must be 3 uppercase letters

**Common mistakes:**
- Creating multiple `is_current=Yes` prices for same service/tier/model → Validation error
- Forgetting to set `approval_status = approved` (drafts won't show to customers)
- Not understanding price calculation (complexity multiplies first, then tier discount applies)
- Using `valid_to` for ongoing prices (leave null)

**Price history:** When changing prices, set old price `is_current=No` and create new price with `is_current=Yes`. This maintains price history.

---

#### Step 13: Service Equipment

**Location:** Services → Service Equipment

**What it is:** Associates equipment with services and tracks quantity requirements.

**Why create it now:** Documents what equipment is needed to deliver each service.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `service_id` | Select | Yes | Service requiring equipment | "WordPress Theme Development" |
| `equipment_id` | Select | Yes | Required equipment | "MacBook Pro 16-inch" |
| `required` | Boolean | Yes | Mandatory equipment? | Yes |
| `quantity` | Decimal | Yes | Quantity needed (1-10,000) | 1.000 |
| `quantity_unit` | Text | No | Unit of measure | "each", "hours", "licenses" |
| `cost_included` | Boolean | Yes | Cost included in service price? | Yes |

**Dependency requirements:**

> **Important:** Before creating Service Equipment, you MUST have:
> - A Service (`service_id`)
> - Equipment (`equipment_id`)

**Real-world examples:**

```
Service Equipment: WordPress Theme Development
├─ Service: WordPress Theme Development
├─ Equipment: MacBook Pro 16-inch
├─ Required: Yes
├─ Quantity: 1 each
├─ Cost Included: Yes (client doesn't pay for our laptop)
└─ Use: Track that we need a MacBook for this work

Service Equipment: WordPress Theme Development
├─ Service: WordPress Theme Development
├─ Equipment: Adobe Creative Cloud License
├─ Required: Yes
├─ Quantity: 1 license
├─ Cost Included: Yes
└─ Use: Design software needed for theme mockups

Service Equipment: Network Installation
├─ Service: Network Installation
├─ Equipment: Network Cable - Cat6
├─ Required: Yes
├─ Quantity: 500 feet
├─ Cost Included: No (billed to client separately)
└─ Use: Consumable materials used on-site

Service Equipment: SEO Consultation
├─ Service: SEO Consultation
├─ Equipment: SEO Analysis Tool License
├─ Required: No (optional)
├─ Quantity: 1 license
├─ Cost Included: Yes
└─ Use: Enhanced analysis available if we use the tool
```

**Validation constraints:**
- `quantity` must be 1-10,000
- Combination of `service_id` + `equipment_id` must be unique
  - Means: Cannot add same equipment to same service twice

**Common mistakes:**
- Setting `required=Yes` for optional equipment
- Not tracking consumables with `cost_included=No`
- Forgetting to update quantity when service scope changes

**Use cases:**
- Resource allocation (ensure equipment available before scheduling)
- Cost calculation (sum equipment costs for true service cost)
- Client invoicing (bill for consumables separately)

---

#### Step 14: Service Deliverables

**Location:** Services → Service Deliverables

**What it is:** Associates deliverables with services and defines delivery sequence.

**Why create it now:** Documents what customers receive when service completes.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `service_id` | Select | Yes | Service providing deliverable | "WordPress Theme Development" |
| `deliverable_id` | Select | Yes | Deliverable item | "Functional Wireframes" |
| `is_optional` | Boolean | Yes | Optional deliverable? | No |
| `sequence_order` | Number | No | Delivery order | 1, 2, 3 |

**Dependency requirements:**

> **Important:** Before creating Service Deliverables, you MUST have:
> - A Service (`service_id`)
> - Deliverables (`deliverable_id`)

**Real-world examples:**

```
Service: WordPress Theme Development
├─ Deliverable 1: Functional Wireframes
│   ├─ Optional: No
│   ├─ Sequence: 1 (first milestone)
│   └─ Requires Approval: Yes
├─ Deliverable 2: Visual Design Mockups
│   ├─ Optional: No
│   ├─ Sequence: 2 (after wireframes approved)
│   └─ Requires Approval: Yes
├─ Deliverable 3: Production WordPress Theme Files
│   ├─ Optional: No
│   ├─ Sequence: 3 (final delivery)
│   └─ Requires Approval: No
└─ Deliverable 4: Training Session - WordPress Admin
    ├─ Optional: Yes
    ├─ Sequence: 4 (post-launch)
    └─ Requires Approval: No

Service: SEO Consultation - Hourly
└─ Deliverable 1: Monthly Analytics Report
    ├─ Optional: Yes (only if client subscribes to monthly package)
    ├─ Sequence: null (recurring, no specific order)
    └─ Requires Approval: No
```

**Validation constraints:**
- Combination of `service_id` + `deliverable_id` must be unique

**Common mistakes:**
- Not setting sequence for milestone-based deliverables
- Marking critical deliverables as optional

**Use cases:**
- Project planning (what gets delivered when)
- Client expectations (clear deliverable list in proposals)
- Progress tracking (check off deliverables as they complete)

---

#### Step 15: Service Delivery

**Location:** Services → Service Delivery

**What it is:** Associates delivery methods with services and defines timing/costs.

**Why create it now:** Documents how services/deliverables reach customers.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `service_id` | Select | Yes | Service being delivered | "WordPress Theme Development" |
| `delivery_method_id` | Select | Yes | Delivery method | "Email Delivery" |
| `lead_time_days` | Number | Yes | Days until delivery | 0 |
| `sla_hours` | Number | No | Service level agreement | 24 |
| `surcharge` | Decimal | Yes | Additional cost | 0.00 |
| `is_default` | Boolean | Yes | Default method? | Yes |

**Dependency requirements:**

> **Important:** Before creating Service Delivery, you MUST have:
> - A Service (`service_id`)
> - A Delivery Method (`delivery_method_id`)

**Real-world examples:**

```
Service: WordPress Theme Development
├─ Delivery Method: Email Delivery (default)
│   ├─ Lead Time: 0 days (instant after completion)
│   ├─ SLA: 24 hours (response to questions)
│   ├─ Surcharge: $0.00
│   └─ Default: Yes
└─ Delivery Method: On-Site Handoff (optional)
    ├─ Lead Time: 5 days (scheduling required)
    ├─ SLA: null
    ├─ Surcharge: $500.00 (travel costs)
    └─ Default: No

Service: Server Setup & Configuration
├─ Delivery Method: On-Site Installation (default)
│   ├─ Lead Time: 7 days (scheduling and travel)
│   ├─ SLA: null
│   ├─ Surcharge: $0.00 (included in service price)
│   └─ Default: Yes
└─ Delivery Method: Remote Installation (optional)
    ├─ Lead Time: 2 days
    ├─ SLA: 48 hours
    ├─ Surcharge: -$200.00 (discount for no travel)
    └─ Default: No
```

**Validation constraints:**
- `surcharge` must be ≥ 0
- Combination of `service_id` + `delivery_method_id` must be unique

**Common mistakes:**
- Not setting a default method (customers need to know standard delivery)
- Negative surcharges for discounts (use positive for extra costs only)

**Use cases:**
- Customer choice (multiple delivery options)
- Pricing transparency (show delivery costs separately)
- Scheduling (lead times help set customer expectations)

---

#### Step 16: Service Coverage

**Location:** Services → Service Coverage

**What it is:** Associates coverage areas with services and defines location-based pricing adjustments.

**Why create it now:** Documents where services are available and any regional pricing differences.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `service_id` | Select | Yes | Service available in area | "Server Setup & Configuration" |
| `coverage_area_id` | Select | Yes | Geographic area | "Greater Toronto Area" |
| `delivery_surcharge` | Decimal | Yes | Regional surcharge | 0.00 |
| `lead_time_adjustment_days` | Number | Yes | Lead time modifier (+/-) | 0 |

**Dependency requirements:**

> **Important:** Before creating Service Coverage, you MUST have:
> - A Service (`service_id`)
> - A Coverage Area (`coverage_area_id`)

**Real-world examples:**

```
Service: Server Setup & Configuration
├─ Coverage: Greater Toronto Area (local)
│   ├─ Surcharge: $0.00 (base rate)
│   └─ Lead Time Adjustment: 0 days
├─ Coverage: Ontario (regional)
│   ├─ Surcharge: $250.00 (travel costs)
│   └─ Lead Time Adjustment: +2 days (travel time)
└─ Coverage: North America (long distance)
    ├─ Surcharge: $1,500.00 (travel + accommodation)
    └─ Lead Time Adjustment: +5 days

Service: Remote WordPress Consulting
├─ Coverage: Global
│   ├─ Surcharge: $0.00 (no travel)
│   └─ Lead Time Adjustment: 0 days
└─ (No other coverage areas needed - it's remote)

Service: On-Site Network Installation
├─ Coverage: Greater Toronto Area
│   ├─ Surcharge: $0.00
│   └─ Lead Time Adjustment: 0 days
└─ Service NOT available in other areas (don't create other coverage entries)
```

**Validation constraints:**
- `delivery_surcharge` must be ≥ 0
- Combination of `service_id` + `coverage_area_id` must be unique

**Common mistakes:**
- Creating coverage for unavailable areas (only create entries where service is offered)
- Forgetting surcharges for distant areas (travel costs add up)
- Not adjusting lead times for remote locations

**When to skip:** If service is global with no regional differences, create one "Global" coverage area with no surcharges.

---

#### Step 17: Service Addons

**Location:** Services → Service Addons

**What it is:** Optional or required add-on services that complement a main service.

**Why create it now:** Documents which services can/must be purchased together.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `service_id` | Select | Yes | Main service | "WordPress Theme Development" |
| `addon_service_id` | Select | Yes | Add-on service | "SEO Optimization" |
| `required` | Boolean | Yes | Mandatory add-on? | No |
| `min_qty` | Decimal | Yes | Minimum quantity | 0.000 |
| `max_qty` | Decimal | No | Maximum quantity | null |
| `default_qty` | Decimal | Yes | Default quantity | 1.000 |
| `sort_order` | Number | Yes | Display order | 1, 2, 3 |

**Dependency requirements:**

> **Important:** Before creating Service Addons, you MUST have:
> - A main Service (`service_id`)
> - An add-on Service (`addon_service_id`)
> - Both services must be different (cannot add service to itself)

**Real-world examples:**

```
Service: WordPress Theme Development
├─ Addon: SEO Optimization (optional)
│   ├─ Required: No
│   ├─ Min Qty: 0 (can skip)
│   ├─ Max Qty: 1 (one-time service)
│   ├─ Default Qty: 1 (suggested)
│   └─ Sort Order: 1
├─ Addon: Content Writing - 10 Pages (optional)
│   ├─ Required: No
│   ├─ Min Qty: 0
│   ├─ Max Qty: 5 (max 50 pages)
│   ├─ Default Qty: 1
│   └─ Sort Order: 2
└─ Addon: Training Session - WordPress Admin (required)
    ├─ Required: Yes (mandatory for all clients)
    ├─ Min Qty: 1
    ├─ Max Qty: 3 (additional sessions available)
    ├─ Default Qty: 1
    └─ Sort Order: 3

Service: Website Hosting - Monthly
└─ Addon: Daily Backups (optional)
    ├─ Required: No
    ├─ Min Qty: 0
    ├─ Max Qty: 1
    ├─ Default Qty: 0 (opt-in)
    └─ Sort Order: 1
```

**Validation constraints:**
- `min_qty` must be ≥ 0
- `max_qty` must be ≥ `min_qty` (if set)
- `default_qty` must be between `min_qty` and `max_qty`
- `service_id` cannot equal `addon_service_id` (service cannot be addon to itself)
- Combination of `service_id` + `addon_service_id` must be unique

**Common mistakes:**
- Creating circular dependencies (A requires B, B requires A)
- Setting `required=Yes` with `min_qty=0` (contradictory)
- Not setting max_qty for unlimited add-ons (causes scoping issues)

**Use cases:**
- Upselling (suggest related services during checkout)
- Package requirements (mandatory add-ons for compliance)
- Custom quotes (let customers configure service packages)

---

#### Step 18: Service Relationships

**Location:** Services → Service Relationships

**What it is:** Defines relationships between services (prerequisites, dependencies, incompatibilities).

**Why create it now:** Documents service dependencies and conflicts.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `service_id` | Select | Yes | Primary service | "WordPress Plugin Development" |
| `related_service_id` | Select | Yes | Related service | "WordPress Theme Development" |
| `relation_type` | Select | Yes | Relationship type | See options below |
| `strength` | Number (1-10) | Yes | Relationship strength | 5 |
| `notes` | Text | No | Relationship explanation | "Plugin requires custom theme integration" |

**Relation Types:**

| Type | Meaning | Example |
|------|---------|---------|
| `prerequisite` | Must be purchased before this service | "WordPress Setup" → "WordPress Theme Development" |
| `dependency` | This service depends on related service | "Plugin Development" → "WordPress Core" |
| `incompatible_with` | Cannot be purchased together | "Windows Hosting" ↔ "macOS Software Installation" |
| `substitute_for` | Can replace related service | "Cloud Hosting" ↔ "On-Premise Hosting" |
| `complements` | Works well together (recommendation) | "SEO Optimization" ↔ "Content Writing" |
| `replaces` | Newer version of related service | "WordPress 6.x Theme" → "WordPress 5.x Theme" |
| `requires` | Mandatory related service | "E-commerce Plugin" → "SSL Certificate" |
| `enables` | Related service unlocks functionality | "Advanced Hosting" → "Redis Caching" |
| `conflicts_with` | Technical conflict (both can't run simultaneously) | "PHP 7.4" ↔ "PHP 8.1" |

**Dependency requirements:**

> **Important:** Before creating Service Relationships, you MUST have:
> - A primary Service (`service_id`)
> - A related Service (`related_service_id`)
> - Both services must be different

**Real-world examples:**

```
Relationship: WordPress Plugin Development → WordPress Setup
├─ Type: prerequisite
├─ Strength: 10 (critical)
├─ Notes: "Must have WordPress installed before plugin development"
└─ Effect: System warns customers to purchase WordPress Setup first

Relationship: E-commerce Plugin → SSL Certificate
├─ Type: requires
├─ Strength: 10 (critical)
├─ Notes: "E-commerce requires secure connection"
└─ Effect: SSL automatically added to cart with E-commerce Plugin

Relationship: SEO Optimization ↔ Content Writing
├─ Type: complements
├─ Strength: 7 (strong recommendation)
├─ Notes: "SEO-optimized content drives better results"
└─ Effect: System suggests Content Writing when SEO added to cart

Relationship: Cloud Hosting ↔ On-Premise Hosting
├─ Type: incompatible_with
├─ Strength: 10 (critical)
├─ Notes: "Cannot host in both cloud and on-premise simultaneously"
└─ Effect: System prevents both in same order

Relationship: WordPress 6.x Theme → WordPress 5.x Theme
├─ Type: replaces
├─ Strength: 9
├─ Notes: "Newer theme version with enhanced features"
└─ Effect: System suggests upgrade to existing customers

Relationship: Advanced Hosting → Basic Hosting
├─ Type: substitute_for
├─ Strength: 5
├─ Notes: "Advanced hosting includes all basic features plus extras"
└─ Effect: System allows choosing either (not both)
```

**Validation constraints:**
- `strength` must be 1-10
- `service_id` cannot equal `related_service_id`
- Combination of `service_id` + `related_service_id` + `relation_type` must be unique

**Common mistakes:**
- Using wrong relation type (prerequisite vs. requires - prerequisite is temporal, requires is technical)
- Setting strength too low for critical relationships
- Creating bidirectional incompatibilities (if A incompatible with B, B automatically incompatible with A)

**Use cases:**
- Guided selling (prevent incompatible purchases)
- Upselling (suggest complementary services)
- Order validation (ensure prerequisites purchased)
- Product migration (replace old services with new versions)

---

#### Step 19: Service Bundles

**Location:** Services → Service Bundles

**What it is:** Packaged combinations of services offered at discounted rates.

**Why create it now:** Creates value packages for customers.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | Text | Yes | Bundle name | "Complete WordPress Solution" |
| `slug` | Text | Yes | URL identifier (auto-generated) | "complete-wordpress-solution" |
| `short_desc` | Text | No | Brief description | "Everything you need to launch your WordPress site" |
| `long_desc` | Text | No | Detailed description | Full HTML description |
| `bundle_type` | Select | Yes | Bundle classification | package, collection, suite, solution |
| `total_discount_pct` | Decimal | Yes | Bundle-wide discount (0-100%) | 15.00 |
| `is_active` | Boolean | Yes | Available for sale? | Yes |
| `valid_from` | Date | No | Bundle start date | 2025-01-01 |
| `valid_to` | Date | No | Bundle end date | null (ongoing) |

**Bundle Types:**

| Type | Use Case | Example |
|------|----------|---------|
| `package` | Fixed set of services bundled | "Startup Website Package" |
| `collection` | Curated group of related services | "WordPress Security Collection" |
| `suite` | Comprehensive solution | "Full-Service WordPress Suite" |
| `solution` | Complete end-to-end offering | "E-commerce Launch Solution" |

**Real-world examples:**

```
Bundle: "Complete WordPress Solution"
├─ Slug: complete-wordpress-solution
├─ Type: solution
├─ Total Discount: 15%
├─ Valid: 2025-01-01 → null (ongoing)
├─ Active: Yes
├─ Includes (see Bundle Items):
│   ├─ WordPress Setup
│   ├─ Custom Theme Development
│   ├─ 5 Essential Plugins
│   ├─ SEO Optimization
│   └─ 3 Months Maintenance
└─ Pricing: $8,500 (regularly $10,000, save $1,500)

Bundle: "Holiday Special - Website Refresh"
├─ Slug: holiday-website-refresh
├─ Type: package
├─ Total Discount: 25% (seasonal promotion)
├─ Valid: 2025-12-01 → 2025-12-31 (limited time)
├─ Active: Yes
└─ Includes:
    ├─ Design Refresh
    ├─ Performance Optimization
    └─ Security Audit

Bundle: "WordPress Security Collection"
├─ Slug: wordpress-security-collection
├─ Type: collection
├─ Total Discount: 10%
├─ Valid: null → null (always available)
├─ Active: Yes
└─ Includes:
    ├─ Security Audit
    ├─ Firewall Setup
    ├─ Malware Scanning
    ├─ SSL Certificate
    └─ Monthly Security Monitoring
```

**Validation constraints:**
- `slug` must be unique
- `total_discount_pct` must be 0-100
- `valid_to` must be ≥ `valid_from` (if both set)

**Common mistakes:**
- Setting `total_discount_pct` too high (ensure bundle is still profitable)
- Creating bundles without checking service compatibility
- Forgetting to set `valid_to` for limited-time offers

**Next step:** After creating Bundle, add services via Bundle Items (Step 20).

---

#### Step 20: Bundle Items

**Location:** Services → Bundle Items

**What it is:** Individual services included in a bundle.

**Why create it now:** Bundles need services to be sellable packages.

**Fields to configure:**

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `bundle_id` | Select | Yes | Parent bundle | "Complete WordPress Solution" |
| `service_id` | Select | Yes | Included service | "WordPress Setup" |
| `quantity` | Decimal | Yes | Service quantity | 1.000 |
| `discount_pct` | Decimal | Yes | Item-specific discount (0-100%) | 0.00 |
| `is_optional` | Boolean | Yes | Customer can remove? | No |
| `sort_order` | Number | Yes | Display order | 1, 2, 3 |

**Dependency requirements:**

> **Important:** Before creating Bundle Items, you MUST have:
> - A Bundle (`bundle_id`)
> - Services to include (`service_id`)

**Real-world examples:**

```
Bundle: "Complete WordPress Solution" ($8,500 total)
├─ Item 1: WordPress Setup
│   ├─ Quantity: 1
│   ├─ Item Discount: 0% (no additional discount)
│   ├─ Optional: No (required)
│   └─ Sort Order: 1
├─ Item 2: Custom Theme Development
│   ├─ Quantity: 1
│   ├─ Item Discount: 0%
│   ├─ Optional: No
│   └─ Sort Order: 2
├─ Item 3: Essential Plugins
│   ├─ Quantity: 5
│   ├─ Item Discount: 10% (extra discount on plugins)
│   ├─ Optional: No
│   └─ Sort Order: 3
├─ Item 4: SEO Optimization
│   ├─ Quantity: 1
│   ├─ Item Discount: 0%
│   ├─ Optional: Yes (customer can remove and save $500)
│   └─ Sort Order: 4
└─ Item 5: 3 Months Maintenance
    ├─ Quantity: 3 (months)
    ├─ Item Discount: 5%
    ├─ Optional: Yes
    └─ Sort Order: 5

Bundle Pricing Calculation:
├─ Bundle Base Price: $10,000 (sum of all items at regular prices)
├─ Bundle Discount: 15% → -$1,500
├─ Item-specific Discounts: -$150 (plugins extra 10%)
└─ Bundle Final Price: $8,350
```

**Discount stacking:**

```
Bundle discount + Item discount = Total savings

Example:
Service Regular Price: $1,000
× Bundle Discount (15%): -$150 → $850
× Item Discount (10%): -$100 → $750
= Final Item Price in Bundle: $750
```

**Validation constraints:**
- `quantity` must be > 0
- `discount_pct` must be 0-100
- Combination of `bundle_id` + `service_id` must be unique

**Common mistakes:**
- Confusing bundle discount vs. item discount (both stack)
- Setting all items optional (bundle needs core required items)
- Not ordering items logically (confuses customers)

**Use cases:**
- Fixed bundles (all required items, one price)
- Configurable bundles (customer chooses optional items)
- Promotional bundles (heavy discounts for limited time)

---

## Configuration Scenarios

### Scenario 1: Simple Hourly Consulting Service

**Business Need:** Offer SEO consulting at $150/hour for small businesses.

**Configuration Steps:**

1. **Complexity Levels** (if not exists):
   ```
   Name: "Intermediate"
   Level: 2
   Multiplier: 1.5
   ```

2. **Pricing Models** (if not exists):
   ```
   Name: "Hourly Rate"
   Code: "hourly"
   Time-based: Yes
   ```

3. **Pricing Tiers** (if not exists):
   ```
   Name: "Small Business"
   Code: "small_business"
   Sort Order: 1
   Discount: 5%
   ```

4. **Service Categories** (if not exists):
   ```
   Name: "Marketing Services"
   Slug: "marketing-services"
   Sort Order: 1
   Active: Yes
   ```

5. **Service Types** (if not exists):
   ```
   Name: "Remote Consulting"
   Code: "remote_consulting"
   Site Visit Required: No
   Supports Remote: Yes
   Estimated Duration: 1 hour
   ```

6. **Service**:
   ```
   Name: "SEO Consultation - Hourly"
   Category: Marketing Services
   Type: Remote Consulting
   Complexity: Intermediate (1.5x)
   Estimated Hours: 1
   Active: Yes
   ```

7. **Service Price**:
   ```
   Service: SEO Consultation - Hourly
   Tier: Small Business
   Model: Hourly Rate
   Amount: $100.00 (base rate)
   Unit: hour
   Currency: CAD
   Approval: approved
   Current: Yes
   ```

**Final Calculation:**
```
Base Rate: $100/hour
× Complexity (1.5x): $150/hour
- Tier Discount (5%): -$7.50/hour
= Customer Pays: $142.50/hour
```

---

### Scenario 2: Complex Project Service with Equipment and Deliverables

**Business Need:** WordPress theme development requiring specific equipment and delivering multiple outputs.

**Configuration Steps:**

1. **Use Steps 1-6 from Scenario 1** (Complexity, Pricing Models, Tiers, Categories, Types)

2. **Service**:
   ```
   Name: "WordPress Theme Development"
   Category: Web Development
   Type: Remote Project
   Complexity: Advanced (2.0x)
   Estimated Hours: 80
   Minimum Quantity: 1 theme
   Active: Yes
   ```

3. **Equipment** (create all required equipment):
   ```
   Equipment 1:
   ├─ Name: "MacBook Pro 16-inch"
   ├─ SKU: "MBPRO16-2023"
   ├─ Manufacturer: "Apple"
   ├─ Unit Cost: $2,999.00
   └─ Consumable: No

   Equipment 2:
   ├─ Name: "Adobe Creative Cloud License"
   ├─ SKU: "ADOBE-CC-ANNUAL"
   ├─ Manufacturer: "Adobe"
   ├─ Unit Cost: $599.88/year
   └─ Consumable: Yes
   ```

4. **Service Equipment** (associate equipment):
   ```
   Service Equipment 1:
   ├─ Service: WordPress Theme Development
   ├─ Equipment: MacBook Pro 16-inch
   ├─ Required: Yes
   ├─ Quantity: 1
   ├─ Cost Included: Yes

   Service Equipment 2:
   ├─ Service: WordPress Theme Development
   ├─ Equipment: Adobe Creative Cloud License
   ├─ Required: Yes
   ├─ Quantity: 1
   └─ Cost Included: Yes
   ```

5. **Deliverables** (create all deliverables):
   ```
   Deliverable 1:
   ├─ Name: "Functional Wireframes"
   ├─ Type: document
   ├─ Effort: 8 hours
   └─ Requires Approval: Yes

   Deliverable 2:
   ├─ Name: "Visual Design Mockups"
   ├─ Type: document
   ├─ Effort: 16 hours
   └─ Requires Approval: Yes

   Deliverable 3:
   ├─ Name: "Production WordPress Theme Files"
   ├─ Type: software
   ├─ Effort: 40 hours
   └─ Requires Approval: No

   Deliverable 4:
   ├─ Name: "Training Session - WordPress Admin"
   ├─ Type: training
   ├─ Effort: 2 hours
   └─ Requires Approval: No
   ```

6. **Service Deliverables** (associate deliverables):
   ```
   Service Deliverable 1:
   ├─ Service: WordPress Theme Development
   ├─ Deliverable: Functional Wireframes
   ├─ Optional: No
   └─ Sequence: 1

   Service Deliverable 2:
   ├─ Service: WordPress Theme Development
   ├─ Deliverable: Visual Design Mockups
   ├─ Optional: No
   └─ Sequence: 2

   Service Deliverable 3:
   ├─ Service: WordPress Theme Development
   ├─ Deliverable: Production WordPress Theme Files
   ├─ Optional: No
   └─ Sequence: 3

   Service Deliverable 4:
   ├─ Service: WordPress Theme Development
   ├─ Deliverable: Training Session - WordPress Admin
   ├─ Optional: Yes
   └─ Sequence: 4
   ```

7. **Service Price**:
   ```
   Service: WordPress Theme Development
   Tier: Small Business
   Model: Fixed Price
   Amount: $2,500.00 (base price)
   Unit: null
   Setup Fee: $0.00
   Currency: CAD
   Approval: approved
   Current: Yes
   ```

**Final Calculation:**
```
Base Price: $2,500
× Complexity (2.0x): $5,000
- Tier Discount (5%): -$250
= Customer Pays: $4,750

Equipment Cost (internal, not billed):
├─ MacBook Pro: $2,999 (amortized)
├─ Adobe CC: $599.88/year
└─ Total Equipment: $3,598.88 (cost to deliver service)

Deliverables Included:
├─ Wireframes (8 hours)
├─ Mockups (16 hours)
├─ Production Files (40 hours)
└─ Optional Training (2 hours, additional charge)
```

---

### Scenario 3: Service Bundle with Multiple Tiers

**Business Need:** Offer a complete WordPress solution bundle with discounts.

**Configuration Steps:**

1. **Complete Scenario 2** (creates all required services)

2. **Create Additional Services** (for bundle):
   ```
   Service: WordPress Setup
   ├─ Category: Web Development
   ├─ Type: Remote Project
   ├─ Complexity: Basic (1.0x)
   ├─ Price: $500 (base)
   └─ Final: $475 (after tier discount)

   Service: SEO Optimization
   ├─ Category: Marketing Services
   ├─ Type: Remote Project
   ├─ Complexity: Intermediate (1.5x)
   ├─ Price: $750 (base)
   └─ Final: $1,125 → $1,069 (after complexity and tier)

   Service: WordPress Maintenance - Monthly
   ├─ Category: Support Services
   ├─ Type: Managed Service
   ├─ Complexity: Basic (1.0x)
   ├─ Price: $199/month (base)
   └─ Final: $189/month (after tier discount)
   ```

3. **Service Bundle**:
   ```
   Name: "Complete WordPress Solution"
   Slug: "complete-wordpress-solution"
   Type: solution
   Total Discount: 15%
   Valid From: 2025-01-01
   Valid To: null (ongoing)
   Active: Yes
   ```

4. **Bundle Items**:
   ```
   Item 1: WordPress Setup
   ├─ Quantity: 1
   ├─ Item Discount: 0%
   ├─ Optional: No
   └─ Sort Order: 1

   Item 2: WordPress Theme Development
   ├─ Quantity: 1
   ├─ Item Discount: 0%
   ├─ Optional: No
   └─ Sort Order: 2

   Item 3: SEO Optimization
   ├─ Quantity: 1
   ├─ Item Discount: 0%
   ├─ Optional: Yes (customer choice)
   └─ Sort Order: 3

   Item 4: WordPress Maintenance - Monthly
   ├─ Quantity: 3 (months)
   ├─ Item Discount: 5% (extra discount)
   ├─ Optional: Yes
   └─ Sort Order: 4
   ```

5. **Bundle Pricing Calculation**:
   ```
   Individual Services (Small Business Tier):
   ├─ WordPress Setup: $475
   ├─ Theme Development: $4,750
   ├─ SEO Optimization: $1,069 (optional)
   ├─ Maintenance (3mo): $567 (optional)
   └─ Total Regular: $6,861

   Bundle Pricing:
   ├─ Base Total: $6,861
   ├─ Bundle Discount (15%): -$1,029
   ├─ Item Discount (Maintenance 5%): -$28
   └─ Bundle Price: $5,804

   Customer Saves: $1,057 (15.4%)
   ```

**Bundle Variations:**

```
Minimum Bundle (required items only):
├─ WordPress Setup
├─ Theme Development
└─ Price: $4,449 (vs. $5,225 separately)

Full Bundle (all items):
├─ WordPress Setup
├─ Theme Development
├─ SEO Optimization
├─ 3 Months Maintenance
└─ Price: $5,804 (vs. $6,861 separately)
```

---

## Verification & Validation

### Pre-Launch Checklist

Before making your catalog live, verify:

#### Phase 1: Foundation Data

- [ ] **Complexity Levels**
  - [ ] At least one complexity level exists
  - [ ] Multipliers are reasonable (1.0 - 3.0 range)
  - [ ] Levels are numbered sequentially (1, 2, 3...)

- [ ] **Pricing Models**
  - [ ] At least one pricing model exists
  - [ ] Codes are lowercase, no spaces
  - [ ] `is_time_based` correctly set for hourly/time models

- [ ] **Pricing Tiers**
  - [ ] At least one tier exists (can start with just "Standard")
  - [ ] Sort orders are logical (smaller tiers = lower numbers)
  - [ ] Discount percentages don't exceed 50% (unless intentional)

- [ ] **Service Categories**
  - [ ] At least one category exists
  - [ ] Category hierarchy makes sense (no circular parents)
  - [ ] Active categories visible in admin

- [ ] **Service Types**
  - [ ] At least one type exists
  - [ ] Site visit / remote flags align with business model
  - [ ] Estimated durations set for time-based services

#### Phase 2: Business Objects

- [ ] **Services**
  - [ ] Each service has valid category, type, complexity
  - [ ] SKUs are unique
  - [ ] Active services have at least one price
  - [ ] Minimum/maximum quantities logical
  - [ ] Estimated hours set (for scoping and quoting)

- [ ] **Service Prices**
  - [ ] Each active service has ≥ 1 approved, current price
  - [ ] No duplicate current prices (same service/tier/model)
  - [ ] Amounts are positive (or null for quote-based)
  - [ ] Currency codes valid (CAD, USD, etc.)
  - [ ] Approval status = "approved" for live prices

- [ ] **Equipment** (optional)
  - [ ] All required equipment created
  - [ ] SKUs unique
  - [ ] Consumable flag correct
  - [ ] Unit costs tracked (for profitability analysis)

- [ ] **Deliverables** (optional)
  - [ ] All promised deliverables created
  - [ ] Effort hours estimated
  - [ ] Approval flags set for milestone deliverables

- [ ] **Delivery Methods** (optional)
  - [ ] At least one method exists
  - [ ] Lead times realistic
  - [ ] Codes unique

- [ ] **Coverage Areas** (optional)
  - [ ] All service areas created
  - [ ] Country codes valid (2-letter ISO)
  - [ ] Timezones set for scheduling

#### Phase 3: Junction Tables

- [ ] **Service Equipment**
  - [ ] All service equipment requirements documented
  - [ ] Required flag correct
  - [ ] Cost included flag accurate

- [ ] **Service Deliverables**
  - [ ] All service deliverables associated
  - [ ] Sequence orders logical
  - [ ] Optional flag correct

- [ ] **Service Delivery**
  - [ ] Each service has ≥ 1 delivery method
  - [ ] Default method set
  - [ ] Surcharges reasonable

- [ ] **Service Coverage**
  - [ ] Each service has coverage areas defined
  - [ ] Surcharges set for distant areas
  - [ ] Lead time adjustments realistic

- [ ] **Service Addons** (optional)
  - [ ] No circular addon dependencies
  - [ ] Required addons have min_qty ≥ 1
  - [ ] Optional addons have min_qty = 0

- [ ] **Service Relationships** (optional)
  - [ ] Prerequisites correctly configured
  - [ ] Incompatibilities bidirectional
  - [ ] Relationship strengths accurate

- [ ] **Service Bundles** (optional)
  - [ ] Bundle discounts profitable
  - [ ] Bundle items added
  - [ ] Required vs. optional items clear
  - [ ] Validity dates set for promotions

### Data Integrity Checks

Run these queries in WordPress admin or database tool to verify:

#### 1. Orphaned Service Prices
```
Find: Service Prices referencing deleted Services
Expected: None
```

#### 2. Services Without Prices
```
Find: Active Services with no current, approved price
Expected: None (unless quote-based)
```

#### 3. Missing Dependencies
```
Find: Services without Category, Type, or Complexity
Expected: None (impossible due to foreign keys, but worth checking)
```

#### 4. Duplicate Current Prices
```
Find: Multiple current prices for same Service/Tier/Model
Expected: None
```

#### 5. Invalid Price Ranges
```
Find: Prices with valid_to < valid_from
Expected: None
```

#### 6. Circular Category Parents
```
Find: Categories where parent_id = id
Expected: None (triggers prevent this)
```

#### 7. Inactive Categories with Active Services
```
Find: Active Services in inactive Categories
Expected: Decision needed (should services inherit category status?)
```

### Business Logic Validation

#### Pricing Validation

For each service, verify final pricing calculation:

```
Example Service: "WordPress Theme Development"
├─ Base Price: $2,500
├─ Complexity: Advanced (2.0x) → $5,000
├─ Tier: Small Business (5% discount) → -$250
└─ FINAL: $4,750

Manual Check:
1. Look up base price in Service Prices
2. Look up complexity multiplier
3. Look up tier discount
4. Calculate: (Base × Complexity) - (Result × Tier Discount %)
5. Verify matches expected selling price
```

#### Equipment Cost Validation

For services with equipment, calculate true cost:

```
Service: "WordPress Theme Development"
├─ Selling Price: $4,750
├─ Equipment Costs:
│   ├─ MacBook Pro: $2,999 (amortized over 100 projects = $30/project)
│   └─ Adobe CC: $599.88/year (amortized over 50 projects = $12/project)
├─ Equipment Cost per Project: $42
├─ Labor Cost (80 hours × $50/hour): $4,000
├─ Total Cost: $4,042
└─ Profit: $708 (14.9% margin)

Check: Is this margin acceptable for business?
```

#### Deliverable Coverage

For each service, verify all deliverables documented:

```
Service: "WordPress Theme Development"
Expected Deliverables:
├─ Wireframes ✓ (documented)
├─ Mockups ✓ (documented)
├─ Production Files ✓ (documented)
├─ Training ✓ (optional, documented)
└─ Documentation ✗ (MISSING - add this!)
```

### Common Data Issues

#### Issue 1: Foreign Key Constraint Error

**Symptom:**
```
Cannot add or update a child row: a foreign key constraint fails
(`srvc_services`, CONSTRAINT `fk_service__category`...)
```

**Cause:** Attempting to create Service with `category_id` that doesn't exist.

**Fix:**
1. Go to Service Categories
2. Create the category first
3. Note the category ID
4. Return to Services and select that category

#### Issue 2: Duplicate Current Price

**Symptom:**
```
Duplicate entry for key 'uq_service_price__current_pricing'
```

**Cause:** Trying to create second current price for same service/tier/model combination.

**Fix:**
1. Go to Service Prices
2. Find existing current price for this service/tier/model
3. Set old price `is_current = No`
4. Create new price with `is_current = Yes`

#### Issue 3: Invalid Quantity Range

**Symptom:**
```
Check constraint 'chk_service__valid_quantity_range' is violated
```

**Cause:** `maximum_quantity` < `minimum_quantity`

**Fix:**
1. Edit Service
2. Either:
   - Increase `maximum_quantity` to ≥ `minimum_quantity`
   - OR decrease `minimum_quantity`
   - OR set `maximum_quantity = null` (no limit)

#### Issue 4: Circular Category Parent

**Symptom:**
```
Error: parent_id cannot equal id
```

**Cause:** Trying to make category its own parent.

**Fix:**
1. Edit Service Category
2. Set `parent_id = null` (make it top-level)
3. OR select different parent category

#### Issue 5: Service Cannot Be Own Addon

**Symptom:**
```
Service cannot be an addon to itself
```

**Cause:** Setting `service_id = addon_service_id` in Service Addons.

**Fix:**
1. Edit Service Addon
2. Select different addon service
3. Verify `service_id ≠ addon_service_id`

---

## Troubleshooting

### Symptom: "Cannot create Service - category constraint fails"

**Diagnosis:**
```
Error: Cannot add or update a child row:
a foreign key constraint fails (`srvc_services`,
CONSTRAINT `fk_service__category` FOREIGN KEY (`category_id`)
REFERENCES `srvc_categories` (`id`))
```

**Cause:** Selected category doesn't exist in database.

**Resolution:**
1. Verify category exists: Go to Services → Service Categories
2. If category missing: Create category first, then return to service creation
3. If category exists: Note the category ID, ensure it matches what you're selecting

**Prevention:** Always create Categories before Services.

---

### Symptom: "Cannot delete Category - still in use"

**Diagnosis:**
```
Error: Cannot delete or update a parent row:
a foreign key constraint fails
```

**Cause:** Services still reference this category.

**Resolution:**
1. Go to Services → Services
2. Filter by the category you want to delete
3. For each service:
   - Either: Change service to different category
   - Or: Delete service (if truly no longer needed)
4. After no services reference category, deletion will work

**Prevention:** Before deleting categories, check for dependent services.

---

### Symptom: "Service has no price"

**Diagnosis:** Service appears in admin but not on customer-facing pages.

**Cause:** No current, approved price exists for this service.

**Resolution:**
1. Go to Services → Service Prices
2. Filter by the service
3. Check if price exists:
   - **If no price:** Create one (see Step 12)
   - **If price exists:** Verify `is_current = Yes` and `approval_status = approved`

**Prevention:** Always create at least one approved price per active service.

---

### Symptom: "Pricing calculation seems wrong"

**Diagnosis:** Customer quoted price doesn't match expected price.

**Cause:** Misunderstanding of pricing formula.

**Resolution:**

**Review pricing formula:**
```
Final Price = ((Base Amount × Complexity Multiplier) - Tier Discount) + Setup Fee
```

**Example debug:**
```
Service: WordPress Theme Development
├─ Base: $2,500 (from Service Price)
├─ Complexity: Advanced = 2.0x (from Complexity Level)
├─ Tier: Small Business = 5% discount (from Pricing Tier)
└─ Calculation:
    Step 1: $2,500 × 2.0 = $5,000
    Step 2: $5,000 × 5% = $250 discount
    Step 3: $5,000 - $250 = $4,750
    Step 4: $4,750 + $0 setup fee = $4,750 FINAL
```

**Common mistakes:**
- Applying tier discount before complexity multiplier (wrong order)
- Forgetting complexity multiplier entirely
- Using wrong pricing tier

**Prevention:** Document pricing examples for common services.

---

### Symptom: "Bundle price doesn't add up"

**Diagnosis:** Bundle total doesn't match sum of individual services.

**Cause:** Confusion about bundle vs. item discounts.

**Resolution:**

**Review bundle pricing logic:**
```
1. Calculate each item's regular price (with complexity and tier)
2. Apply item-specific discount (if any)
3. Sum all items
4. Apply bundle-wide discount
```

**Example debug:**
```
Bundle: Complete WordPress Solution
Items:
├─ WordPress Setup: $475 regular
│   ├─ Item discount: 0%
│   └─ Price in bundle (before bundle discount): $475
├─ Theme Development: $4,750 regular
│   ├─ Item discount: 0%
│   └─ Price in bundle (before bundle discount): $4,750
├─ Maintenance (3mo): $567 regular
│   ├─ Item discount: 5% → -$28
│   └─ Price in bundle (before bundle discount): $539
└─ Subtotal: $5,764

Bundle Discount:
├─ Subtotal: $5,764
├─ Bundle discount: 15% → -$865
└─ FINAL BUNDLE PRICE: $4,899

Verification:
Individual: $5,792
Bundle: $4,899
Savings: $893 (15.4%)
```

**Prevention:** Use bundle pricing calculator or spreadsheet to verify before publishing.

---

### Symptom: "Equipment cost not reflected in service price"

**Diagnosis:** Equipment costs seem high, but service price unchanged.

**Cause:** Equipment costs are tracked separately from service prices.

**Resolution:**

**Understanding equipment costs:**

Equipment costs are for **internal tracking only** - they don't automatically adjust service prices.

**Purpose of equipment cost tracking:**
1. **Profitability analysis** - Know true cost of delivering service
2. **Resource planning** - Ensure equipment available before booking
3. **Pricing decisions** - Inform future price increases

**Example:**
```
Service: WordPress Theme Development
├─ Selling Price: $4,750 (manually set in Service Price)
├─ Equipment Cost: $42/project (MacBook + Adobe CC amortized)
├─ Labor Cost: $4,000 (80 hours × $50/hour)
├─ Total Cost: $4,042
└─ Profit: $708 (14.9%)

Equipment cost is NOT deducted from selling price.
It's used to calculate profit margin.
```

**If equipment cost should be billed separately:**
- Set `cost_included = No` in Service Equipment
- Add equipment as separate line item on invoice

**Prevention:** Understand equipment costs are informational, not automatic price adjustments.

---

### Symptom: "Customer can't purchase service in their area"

**Diagnosis:** Service shows as unavailable for customer's location.

**Cause:** No Service Coverage entry for customer's area.

**Resolution:**
1. Go to Services → Service Coverage
2. Check if service has coverage for customer's area:
   - **If no coverage:** Create Service Coverage entry (see Step 16)
   - **If coverage exists:** Verify coverage area matches customer's location

**Example:**
```
Service: Server Setup & Configuration
Coverage Areas:
├─ Greater Toronto Area ✓
├─ Ontario ✓
└─ North America ✗ (MISSING - add if needed)

Customer in Vancouver (BC):
├─ Not in GTA ✗
├─ Not in Ontario ✗
└─ Service unavailable for this customer

Fix: Add "Canada" or "North America" coverage area
```

**Prevention:** Define broad coverage areas (e.g., "Global") for remote services.

---

### Symptom: "Service relationships not enforcing prerequisites"

**Diagnosis:** Customers can purchase Service B without prerequisite Service A.

**Cause:** Relationship type wrong or relationship not created.

**Resolution:**

**Review relationship types:**
- **`prerequisite`** - Must purchase A before B (temporal)
- **`requires`** - B cannot function without A (technical dependency)

**Check relationship exists:**
1. Go to Services → Service Relationships
2. Filter by primary service
3. Verify relationship exists with correct type:
   - Service: Plugin Development
   - Related Service: WordPress Setup
   - Type: prerequisite
   - Strength: 10 (critical)

**If relationship exists but not enforcing:**
- Check relationship `strength` ≥ 8 (high priority)
- Verify application logic implements prerequisite checking
- May require custom code to enforce in checkout flow

**Prevention:** Document all service dependencies during service creation.

---

### Getting Help

If you encounter issues not covered in this guide:

1. **Check database logs** - Look for constraint violation messages (tell you exactly what's wrong)
2. **Review CLAUDE.md** - Technical documentation in plugin directory
3. **Check WordPress debug log** - May contain validation error details
4. **Contact support** - Provide:
   - Error message (complete text)
   - Steps you took before error
   - Screenshots of configuration screens
   - Service/Category/Equipment names involved

---

## Appendix: Field Reference

### Complete Field List by Table

#### Complexity Levels

| Field | Type | Required | Validation | Default |
|-------|------|----------|------------|---------|
| `name` | varchar(64) | Yes | Unique | - |
| `level` | tinyint (0-255) | Yes | Unique, 0-255 | 0 |
| `price_multiplier` | decimal(3,1) | Yes | 0.0-99.9 | 1.0 |

#### Pricing Models

| Field | Type | Required | Validation | Default |
|-------|------|----------|------------|---------|
| `name` | varchar(64) | Yes | Unique | - |
| `code` | varchar(64) | Yes | Unique | - |
| `description` | varchar(255) | No | - | NULL |
| `is_time_based` | boolean | Yes | - | 0 (No) |

#### Pricing Tiers

| Field | Type | Required | Validation | Default |
|-------|------|----------|------------|---------|
| `name` | varchar(64) | Yes | Unique | - |
| `code` | varchar(64) | Yes | Unique | - |
| `sort_order` | tinyint (0-255) | Yes | - | 0 |
| `discount_pct` | decimal(5,2) | Yes | 0-100 | 0.00 |
| `min_volume` | int unsigned | No | ≤ max_volume | NULL |
| `max_volume` | int unsigned | No | ≥ min_volume | NULL |

#### Service Categories

| Field | Type | Required | Validation | Default |
|-------|------|----------|------------|---------|
| `parent_id` | bigint(20) | No | Exists in categories, ≠ id | NULL |
| `name` | varchar(64) | Yes | Unique | - |
| `slug` | varchar(64) | Yes | Unique | - |
| `icon` | varchar(32) | No | - | NULL |
| `description` | text | No | - | NULL |
| `sort_order` | int unsigned | Yes | - | 0 |
| `is_active` | boolean | Yes | - | 1 (Yes) |

#### Service Types

| Field | Type | Required | Validation | Default |
|-------|------|----------|------------|---------|
| `name` | varchar(64) | Yes | Unique | - |
| `code` | varchar(64) | Yes | Unique | - |
| `description` | text | No | - | NULL |
| `requires_site_visit` | boolean | Yes | - | 0 (No) |
| `supports_remote` | boolean | Yes | - | 0 (No) |
| `estimated_duration_hours` | decimal(6,2) | No | ≥ 0 | NULL |

#### Services

| Field | Type | Required | Validation | Default |
|-------|------|----------|------------|---------|
| `sku` | varchar(64) | No | Unique | NULL (auto) |
| `slug` | varchar(64) | Yes | Unique | (auto) |
| `name` | varchar(64) | Yes | - | - |
| `short_desc` | varchar(512) | Yes | - | - |
| `long_desc` | text | No | - | NULL |
| `category_id` | bigint(20) | Yes | Exists in categories | - |
| `service_type_id` | bigint(20) | Yes | Exists in service_types | - |
| `complexity_id` | bigint(20) | Yes | Exists in complexity_levels | - |
| `is_active` | boolean | Yes | - | 1 (Yes) |
| `is_featured` | boolean | Yes | - | 0 (No) |
| `minimum_quantity` | decimal(8,2) | No | > 0 | NULL |
| `maximum_quantity` | decimal(8,2) | No | ≥ minimum_quantity | NULL |
| `estimated_hours` | decimal(6,2) | No | > 0 | NULL |
| `skill_level` | enum | No | entry, intermediate, advanced, expert, specialist | NULL |
| `metadata` | json | No | - | NULL |

#### Service Prices

| Field | Type | Required | Validation | Default |
|-------|------|----------|------------|---------|
| `service_id` | bigint(20) | Yes | Exists in services | - |
| `pricing_tier_id` | bigint(20) | Yes | Exists in pricing_tiers | - |
| `pricing_model_id` | bigint(20) | Yes | Exists in pricing_models | - |
| `currency` | char(3) | Yes | 3 uppercase letters | CAD |
| `amount` | decimal(12,2) | No | ≥ 0 | NULL |
| `unit` | varchar(32) | No | - | NULL |
| `setup_fee` | decimal(12,2) | Yes | ≥ 0 | 0.00 |
| `valid_from` | datetime | Yes | - | current_timestamp |
| `valid_to` | datetime | No | > valid_from | NULL |
| `is_current` | boolean | Yes | Unique per service/tier/model | 1 (Yes) |
| `approval_status` | enum | Yes | draft, pending, approved, rejected | draft |

---

## Quick Reference: Configuration Order

**Phase 1 - Foundation (No Dependencies):**
1. Complexity Levels
2. Pricing Models
3. Pricing Tiers
4. Currency Rates (optional)
5. Service Categories
6. Service Types

**Phase 2 - Business Objects (Depend on Phase 1):**
7. Services
8. Equipment
9. Deliverables
10. Delivery Methods
11. Coverage Areas

**Phase 3 - Junction Tables (Depend on Phase 2):**
12. Service Prices
13. Service Equipment
14. Service Deliverables
15. Service Delivery
16. Service Coverage
17. Service Addons
18. Service Relationships
19. Service Bundles
20. Bundle Items

**Remember:** Each phase depends on the previous. Complete Phase 1 entirely before starting Phase 2.

---

**End of Configuration Guide**

For technical implementation details, see `CLAUDE.md` in the plugin directory.
For database schema documentation, see `database/migrations/` directory.
