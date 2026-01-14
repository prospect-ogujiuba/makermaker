# Decision Categories for Handoffs

Standard decision categories and rationale patterns for consistent handoff documentation across all maker skills.

## Category: Data Modeling

Decisions about database structure and data storage strategy.

**Common Decisions:**
- Soft delete vs hard delete
- JSON columns vs separate tables
- Index strategy (composite, partial, covering)
- FK constraint behavior (cascade, restrict, set null)
- Column nullability
- Default values

**Good Rationale Examples:**
```yaml
- decision: "Soft delete via deleted_at column"
  rationale: "Business requirement: maintain audit history and allow record restoration"
  alternatives: ["Hard delete with archive table", "Hard delete without archive"]
  impact: ["Model needs SoftDeletes trait", "Queries must filter deleted records"]

- decision: "JSON metadata column for extensible attributes"
  rationale: "Technical flexibility: different service types need different custom fields without schema migrations"
  alternatives: ["EAV pattern", "Separate metadata tables per type"]
  impact: ["Model needs array cast", "Form needs dynamic key-value editor"]
```

**Bad Rationale Examples:**
```yaml
# Too vague
- decision: "Use soft delete"
  rationale: "It's better"

# No alternatives considered
- decision: "JSON column for metadata"
  rationale: "We need flexible storage"
```

---

## Category: Security

Decisions about access control, authentication, and data protection.

**Common Decisions:**
- Capability-based authorization
- Ownership checks (user can only edit own records)
- Admin override behavior
- Nonce validation for CSRF protection
- Input sanitization strategy
- Sensitive field handling

**Good Rationale Examples:**
```yaml
- decision: "Ownership-based access for non-admins"
  rationale: "Security consideration: users should only modify their own data; admins need full access for support"
  alternatives: ["Role-based only", "Team-based ownership", "No ownership restrictions"]
  impact: ["Controller filters queries by created_by", "Index shows ownership column"]

- decision: "REST write endpoints require nonce validation"
  rationale: "Security consideration: prevent CSRF attacks on state-changing operations"
  alternatives: ["JWT tokens", "API keys", "Session-only auth"]
  impact: ["Frontend must include nonce header", "Block components use wp_localize_script for nonce"]
```

**Bad Rationale Examples:**
```yaml
# Missing threat model
- decision: "Add authentication"
  rationale: "For security"

# No impact documented
- decision: "Require admin capability"
  rationale: "Only admins should access this"
```

---

## Category: Data Integrity

Decisions about validation, data quality, and consistency.

**Common Decisions:**
- Required vs optional fields
- Unique constraints and validation
- Conditional validation rules
- Sanitization function selection
- Cross-field validation
- Format validation (email, URL, phone)

**Good Rationale Examples:**
```yaml
- decision: "Conditional SKU validation - required only when has_sku feature enabled"
  rationale: "Business requirement: some service types are internal-only and don't need external identifiers"
  alternatives: ["Always required with auto-generation", "Always optional"]
  impact: ["Controller checks feature flag before validation", "Form conditionally shows SKU field"]

- decision: "wp_kses_post sanitization for description field"
  rationale: "Security consideration: allow safe HTML for rich content while preventing XSS"
  alternatives: ["sanitize_text_field (strips all HTML)", "No sanitization"]
  impact: ["Description supports formatting, links, images", "Output safe for direct rendering"]
```

**Bad Rationale Examples:**
```yaml
# No business context
- decision: "Make name required"
  rationale: "It should be required"

# Missing security rationale
- decision: "Use wp_kses_post"
  rationale: "For HTML content"
```

---

## Category: UX

Decisions about user experience, interface design, and usability.

**Common Decisions:**
- Field input types (text, wysiwyg, select, repeater)
- Default values and placeholders
- Auto-generation behavior (slug, SKU)
- Error message formatting
- Loading states and feedback
- Field grouping and layout

**Good Rationale Examples:**
```yaml
- decision: "WYSIWYG editor for description field"
  rationale: "User experience: services need rich content with formatting, links, and embedded media"
  alternatives: ["Plain textarea", "Markdown editor", "Block editor"]
  impact: ["Form uses wp_editor component", "Frontend renders with wp_kses_post"]

- decision: "Auto-generate slug from name on blur"
  rationale: "User experience: reduce manual work while allowing customization"
  alternatives: ["Manual slug entry only", "Server-side generation on save"]
  impact: ["Form includes JavaScript for slug generation", "Slug field editable but auto-populated"]
```

**Bad Rationale Examples:**
```yaml
# No user benefit explained
- decision: "Use dropdown for type"
  rationale: "Dropdowns are standard"

# Missing alternatives
- decision: "Show 10 items per page"
  rationale: "10 is a good number"
```

---

## Category: Performance

Decisions about speed, efficiency, and resource usage.

**Common Decisions:**
- Eager loading defaults
- Index selection for common queries
- Caching strategy
- Pagination limits
- Query optimization
- Lazy loading vs eager loading

**Good Rationale Examples:**
```yaml
- decision: "Eager load type relationship by default"
  rationale: "Performance optimization: type name is displayed in every list view and detail view"
  alternatives: ["Lazy load on demand", "Denormalize type_name onto model"]
  impact: ["N+1 queries prevented", "Controller doesn't need explicit with() calls"]

- decision: "Composite index on (type_id, is_active)"
  rationale: "Performance optimization: most common query pattern is filtering active items by type"
  alternatives: ["Separate indexes on each column", "No index (table scan)"]
  impact: ["Index used for type+status filter queries", "Slight write overhead acceptable"]
```

**Bad Rationale Examples:**
```yaml
# No performance justification
- decision: "Add an index"
  rationale: "Indexes are good"

# Missing query pattern context
- decision: "Eager load all relationships"
  rationale: "To be safe"
```

---

## Category: Integration

Decisions about system integration, API design, and helper usage.

**Common Decisions:**
- Helper class selection
- REST endpoint design
- Event/hook integration
- Third-party service connections
- Response format standardization

**Good Rationale Examples:**
```yaml
- decision: "Use RestHelper::formatResponse for all API responses"
  rationale: "Technical consistency: frontend components expect {success, data, message} structure"
  alternatives: ["Raw JSON responses", "WP_REST_Response only"]
  impact: ["All REST methods return consistent format", "Error handling standardized"]

- decision: "AutoCodeHelper for SKU generation"
  rationale: "Technical consistency: centralized SKU format and uniqueness checking across all entities"
  alternatives: ["Inline UUID generation", "Manual entry only"]
  impact: ["SKU format consistent across entities", "Form shows SKU as readonly post-creation"]
```

**Bad Rationale Examples:**
```yaml
# No consistency benefit explained
- decision: "Use the helper"
  rationale: "It's available"

# Missing downstream impact
- decision: "Return JSON"
  rationale: "For the API"
```

---

## Rationale Patterns

Standard phrases for documenting "why" decisions were made:

| Pattern | Use When |
|---------|----------|
| "Business requirement: [specific need]" | Decision driven by stakeholder/business need |
| "Performance optimization: [what improves]" | Decision improves speed/efficiency |
| "Security consideration: [threat mitigated]" | Decision addresses security concern |
| "User experience: [UX improvement]" | Decision improves usability |
| "Technical constraint: [limitation addressed]" | Decision works around technical limitation |
| "Technical consistency: [pattern followed]" | Decision maintains codebase consistency |
| "Technical flexibility: [future-proofing]" | Decision enables future changes |

---

## Decision Documentation Checklist

When documenting a decision in any handoff:

- [ ] **Decision**: Clear statement of what was decided
- [ ] **Rationale**: Why this choice was made (use patterns above)
- [ ] **Alternatives**: What other options were considered
- [ ] **Impact**: What downstream stages need to know/do differently

**Minimum viable decision:**
```yaml
- decision: "[Clear statement]"
  rationale: "[Pattern]: [specific reason]"
  alternatives: ["[Option 1]", "[Option 2]"]
  impact: ["[Effect on downstream stage]"]
```
