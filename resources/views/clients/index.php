<?php

use TypeRocket\Database\Query;

function compareValues($value1, $operator, $value2)
{
    switch ($operator) {
        case '=':
            return $value1 == $value2;
        case '>':
            return $value1 > $value2;
        case '<':
            return $value1 < $value2;
        case '>=':
            return $value1 >= $value2;
        case '<=':
            return $value1 <= $value2;
        default:
            return false;
    }
}

$table = tr_table(\MakerMaker\Models\Client::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Mark as Active' => 'activate',
    'Mark as Inactive' => 'deactivate',
    'Mark as Prospects' => 'mark_prospects',
    'Mark as Onboarded' => 'mark_onboarded',
    'Update Last Contact' => 'update_last_contact',
    'Assign to User' => 'assign_user',
    'Set Priority' => 'set_priority',
    'Export Selected' => 'export'
]);

$table->setSearchColumns([
    'company_name' => 'Company Name',
    'legal_name' => 'Legal Name',
    'contact_firstname' => 'Contact First Name',
    'contact_lastname' => 'Contact Last Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'city' => 'City',
    'province' => 'Province',
    'industry' => 'Industry',
    'status' => 'Status'
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('client'); ?>

    <div class="tr-search-filters">
        <!-- Basic Company Information -->
        <div class="tr-filter-group">
            <label>Company Name:</label>
            <input type="text" name="company_name" class="tr-filter"
                value="<?php echo $_GET['company_name'] ?? ''; ?>"
                placeholder="Search Company Name">
        </div>

        <div class="tr-filter-group">
            <label>Legal Name:</label>
            <input type="text" name="legal_name" class="tr-filter"
                value="<?php echo $_GET['legal_name'] ?? ''; ?>"
                placeholder="Search Legal Name">
        </div>

        <!-- Contact Information -->
        <div class="tr-filter-group">
            <label>Contact First Name:</label>
            <input type="text" name="contact_firstname" class="tr-filter"
                value="<?php echo $_GET['contact_firstname'] ?? ''; ?>"
                placeholder="Search First Name">
        </div>

        <div class="tr-filter-group">
            <label>Contact Last Name:</label>
            <input type="text" name="contact_lastname" class="tr-filter"
                value="<?php echo $_GET['contact_lastname'] ?? ''; ?>"
                placeholder="Search Last Name">
        </div>

        <div class="tr-filter-group">
            <label>Email:</label>
            <input type="text" name="email" class="tr-filter"
                value="<?php echo $_GET['email'] ?? ''; ?>"
                placeholder="Search Email">
        </div>

        <div class="tr-filter-group">
            <label>Phone:</label>
            <input type="text" name="phone" class="tr-filter"
                value="<?php echo $_GET['phone'] ?? ''; ?>"
                placeholder="Search Phone">
        </div>

        <!-- Status and Priority -->
        <div class="tr-filter-group">
            <label>Status:</label>
            <select name="status" class="tr-filter">
                <option value="">All Statuses</option>
                <?php
                $statuses = ['prospect' => 'Prospect', 'active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'];
                outputSelectOptions($statuses, $_GET['status'] ?? '');
                ?>
            </select>
        </div>

        <div class="tr-filter-group">
            <label>Priority:</label>
            <select name="priority" class="tr-filter">
                <option value="">All Priorities</option>
                <?php
                $priorities = ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'critical' => 'Critical'];
                outputSelectOptions($priorities, $_GET['priority'] ?? '');
                ?>
            </select>
        </div>

        <!-- Location Information -->
        <div class="tr-filter-group">
            <label>Province:</label>
            <select name="province" class="tr-filter">
                <option value="">Select Province</option>
                <?php
                $provinces = ['ON', 'BC', 'AB', 'SK', 'MB', 'QC', 'NB', 'NS', 'PE', 'NL', 'YT', 'NT', 'NU'];
                outputSelectOptions($provinces, $_GET['province'] ?? '');
                ?>
            </select>
        </div>

        <div class="tr-filter-group">
            <label>City:</label>
            <input type="text" name="city" class="tr-filter"
                value="<?php echo $_GET['city'] ?? ''; ?>"
                placeholder="Enter city">
        </div>

        <!-- Business Information -->
        <div class="tr-filter-group">
            <label>Industry:</label>
            <input type="text" name="industry" class="tr-filter"
                value="<?php echo $_GET['industry'] ?? ''; ?>"
                placeholder="Search Industry">
        </div>

        <div class="tr-filter-group">
            <label>Company Size:</label>
            <select name="company_size" class="tr-filter">
                <option value="">All Sizes</option>
                <?php
                $sizes = ['1-10', '11-50', '51-200', '201-500', '500+'];
                outputSelectOptions($sizes, $_GET['company_size'] ?? '');
                ?>
            </select>
        </div>

        <div class="tr-filter-group">
            <label>Annual Revenue:</label>
            <select name="annual_revenue" class="tr-filter">
                <option value="">All Revenue Ranges</option>
                <?php
                $revenues = [
                    'under-1m' => 'Under $1M',
                    '1m-5m' => '$1M - $5M',
                    '5m-25m' => '$5M - $25M',
                    '25m-100m' => '$25M - $100M',
                    'over-100m' => 'Over $100M'
                ];
                outputSelectOptions($revenues, $_GET['annual_revenue'] ?? '');
                ?>
            </select>
        </div>

        <!-- Lead Source -->
        <div class="tr-filter-group">
            <label>Lead Source:</label>
            <select name="source" class="tr-filter">
                <option value="">All Sources</option>
                <?php
                $sources = [
                    'website' => 'Website',
                    'referral' => 'Referral',
                    'cold-call' => 'Cold Call',
                    'trade-show' => 'Trade Show',
                    'social-media' => 'Social Media',
                    'other' => 'Other'
                ];
                outputSelectOptions($sources, $_GET['source'] ?? '');
                ?>
            </select>
        </div>

        <!-- Assigned User -->
        <?php
        $userQuery = new Query();
        $users = $userQuery
            ->table('wp_users')
            ->select('ID', 'display_name')
            ->orderBy('display_name', 'ASC')
            ->get();
        ?>
        <div class="tr-filter-group">
            <label>Assigned To:</label>
            <select name="assigned_to" class="tr-filter">
                <option value="">All Users</option>
                <option value="0" <?= ($_GET['assigned_to'] ?? '') === '0' ? 'selected' : ''; ?>>Unassigned</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user->ID); ?>"
                        <?= ($_GET['assigned_to'] ?? '') === $user->ID ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($user->display_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Date Filters -->
        <div class="tr-filter-group">
            <label>Created Date Range:</label>
            <div class="tr-date-inputs">
                <input type="date" name="created_at_from" class="tr-filter"
                    value="<?php echo $_GET['created_at_from'] ?? ''; ?>"
                    placeholder="From">
                <input type="date" name="created_at_to" class="tr-filter"
                    value="<?php echo $_GET['created_at_to'] ?? ''; ?>"
                    placeholder="To">
            </div>
        </div>

        <div class="tr-filter-group">
            <label>Onboarded Date Range:</label>
            <div class="tr-date-inputs">
                <input type="date" name="onboarded_at_from" class="tr-filter"
                    value="<?php echo $_GET['onboarded_at_from'] ?? ''; ?>"
                    placeholder="From">
                <input type="date" name="onboarded_at_to" class="tr-filter"
                    value="<?php echo $_GET['onboarded_at_to'] ?? ''; ?>"
                    placeholder="To">
            </div>
        </div>

        <div class="tr-filter-group">
            <label>Last Contact Range:</label>
            <div class="tr-date-inputs">
                <input type="date" name="last_contact_from" class="tr-filter"
                    value="<?php echo $_GET['last_contact_from'] ?? ''; ?>"
                    placeholder="From">
                <input type="date" name="last_contact_to" class="tr-filter"
                    value="<?php echo $_GET['last_contact_to'] ?? ''; ?>"
                    placeholder="To">
            </div>
        </div>

        <!-- Onboarding Status -->
        <div class="tr-filter-group">
            <label>Onboarding Status:</label>
            <select name="onboarding_status" class="tr-filter">
                <option value="">All</option>
                <option value="onboarded" <?= ($_GET['onboarding_status'] ?? '') === 'onboarded' ? 'selected' : ''; ?>>Onboarded</option>
                <option value="not_onboarded" <?= ($_GET['onboarding_status'] ?? '') === 'not_onboarded' ? 'selected' : ''; ?>>Not Onboarded</option>
            </select>
        </div>

        <!-- Credit Limit Range -->
        <div class="tr-filter-group">
            <label>Credit Limit:</label>
            <div class="tr-range-inputs">
                <select name="credit_operator" class="tr-filter">
                    <?php
                    $operators = [
                        '=' => 'Equal to',
                        '<' => 'Less than',
                        '>' => 'Greater than',
                        '<=' => 'Less or equal',
                        '>=' => 'Greater or equal'
                    ];
                    outputSelectOptions($operators, $_GET['credit_operator'] ?? '');
                    ?>
                </select>
                <input type="number" step="0.01" name="credit_amount" class="tr-filter"
                    value="<?php echo $_GET['credit_amount'] ?? ''; ?>"
                    placeholder="Credit Amount">
            </div>
        </div>

    </div>
<?php
});

// Model filters
$table->addSearchModelFilter(function ($args, $model, $table) {
    // Basic Company Information Filters
    if (!empty($_GET['company_name'])) {
        $model->where('company_name', 'LIKE', '%' . $_GET['company_name'] . '%');
    }

    if (!empty($_GET['legal_name'])) {
        $model->where('legal_name', 'LIKE', '%' . $_GET['legal_name'] . '%');
    }

    // Contact Information Filters
    if (!empty($_GET['contact_firstname'])) {
        $model->where('contact_firstname', 'LIKE', '%' . $_GET['contact_firstname'] . '%');
    }

    if (!empty($_GET['contact_lastname'])) {
        $model->where('contact_lastname', 'LIKE', '%' . $_GET['contact_lastname'] . '%');
    }

    if (!empty($_GET['email'])) {
        $model->where('email', 'LIKE', '%' . $_GET['email'] . '%');
    }

    if (!empty($_GET['phone'])) {
        $model->where('phone', 'LIKE', '%' . $_GET['phone'] . '%');
    }

    // Status and Priority Filters
    if (!empty($_GET['status'])) {
        $model->where('status', '=', $_GET['status']);
    }

    if (!empty($_GET['priority'])) {
        $model->where('priority', '=', $_GET['priority']);
    }

    // Location Filters
    if (!empty($_GET['province'])) {
        $model->where('province', '=', $_GET['province']);
    }

    if (!empty($_GET['city'])) {
        $model->where('city', 'LIKE', '%' . $_GET['city'] . '%');
    }

    // Business Information Filters
    if (!empty($_GET['industry'])) {
        $model->where('industry', 'LIKE', '%' . $_GET['industry'] . '%');
    }

    if (!empty($_GET['company_size'])) {
        $model->where('company_size', '=', $_GET['company_size']);
    }

    if (!empty($_GET['annual_revenue'])) {
        $model->where('annual_revenue', '=', $_GET['annual_revenue']);
    }

    // Lead Source Filter
    if (!empty($_GET['source'])) {
        $model->where('source', '=', $_GET['source']);
    }

    // Assigned User Filter
    if (isset($_GET['assigned_to']) && $_GET['assigned_to'] !== '') {
        if ($_GET['assigned_to'] === '0') {
            $model->where('assigned_to', '=', null);
        } else {
            $model->where('assigned_to', '=', $_GET['assigned_to']);
        }
    }

    // Date Range Filters
    if (!empty($_GET['created_at_from'])) {
        $model->where('created_at', '>=', $_GET['created_at_from'] . ' 00:00:00');
    }

    if (!empty($_GET['created_at_to'])) {
        $model->where('created_at', '<=', $_GET['created_at_to'] . ' 23:59:59');
    }

    if (!empty($_GET['onboarded_at_from'])) {
        $model->where('onboarded_at', '>=', $_GET['onboarded_at_from'] . ' 00:00:00');
    }

    if (!empty($_GET['onboarded_at_to'])) {
        $model->where('onboarded_at', '<=', $_GET['onboarded_at_to'] . ' 23:59:59');
    }

    if (!empty($_GET['last_contact_from'])) {
        $model->where('last_contact_at', '>=', $_GET['last_contact_from'] . ' 00:00:00');
    }

    if (!empty($_GET['last_contact_to'])) {
        $model->where('last_contact_at', '<=', $_GET['last_contact_to'] . ' 23:59:59');
    }

    // Onboarding Status Filter
    if (!empty($_GET['onboarding_status'])) {
        if ($_GET['onboarding_status'] === 'onboarded') {
            $model->whereNotNull('onboarded_at');
        } elseif ($_GET['onboarding_status'] === 'not_onboarded') {
            $model->whereNull('onboarded_at');
        }
    }

    // Credit Limit Filter
    if (!empty($_GET['credit_amount']) && !empty($_GET['credit_operator'])) {
        $operator = $_GET['credit_operator'];
        $amount = $_GET['credit_amount'];
        $model->where('credit_limit', $operator, $amount);
    }

    return $args;
});

// Configure table columns
$table->setColumns([
    'company_name' => [
        'label' => 'Company Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
        'callback' => function ($value, $item) {
            return sprintf(
                '<a href="/wp-admin/admin.php?page=client_edit&route_args[0]=%d"><strong>%s</strong></a>',
                $item->id,
                htmlspecialchars($value)
            );
        }
    ],
    'contact_name' => [
        'label' => 'Primary Contact',
        'callback' => function ($value, $item) {
            $name = trim($item->contact_firstname . ' ' . $item->contact_lastname);
            $title = $item->contact_title ? ' - ' . $item->contact_title : '';
            return htmlspecialchars($name . $title);
        }
    ],
    'email' => [
        'label' => 'Email',
        'sort' => true,
        'callback' => function ($value) {
            return $value ? '<a href="mailto:' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</a>' : 'N/A';
        }
    ],
    'phone' => [
        'label' => 'Phone',
        'sort' => true,
        'callback' => function ($value) {
            if ($value) {
                $formatted = preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $value);
                return '<a href="tel:' . htmlspecialchars($value) . '">' . htmlspecialchars($formatted) . '</a>';
            }
            return 'N/A';
        }
    ],
    'location' => [
        'label' => 'Location',
        'callback' => function ($value, $item) {
            return htmlspecialchars($item->city . ', ' . $item->province);
        }
    ],
    'status' => [
        'label' => 'Status',
        'sort' => true,
        'callback' => function ($value) {
            $classes = [
                'prospect' => 'tr-warning',
                'active' => 'tr-check',
                'inactive' => 'tr-no-data',
                'suspended' => 'tr-no-data'
            ];
            $class = $classes[$value] ?? '';
            return sprintf(
                '<span class="tr-table-tag %s">%s</span>',
                $class,
                ucfirst($value)
            );
        }
    ],
    'priority' => [
        'label' => 'Priority',
        'sort' => true,
        'callback' => function ($value) {
            $classes = [
                'low' => '',
                'normal' => 'tr-table-tag',
                'high' => 'tr-warning',
                'critical' => 'tr-no-data'
            ];
            $class = $classes[$value] ?? '';
            return sprintf(
                '<span class="tr-table-tag %s">%s</span>',
                $class,
                ucfirst($value)
            );
        }
    ],
    'assigned_to' => [
        'label' => 'Assigned To',
        'callback' => function ($value) {
            if ($value) {
                $user = get_userdata($value);
                return $user ? htmlspecialchars($user->display_name) : 'Unknown User';
            }
            return '<span class="tr-no-data">Unassigned</span>';
        }
    ],
    'last_contact_at' => [
        'label' => 'Last Contact',
        'sort' => true,
        'callback' => function ($value, $item) {
            if ($value) {
                $days = floor((time() - strtotime($value)) / (60 * 60 * 24));
                $class = $days > 30 ? 'tr-warning' : ($days > 60 ? 'tr-no-data' : '');
                return sprintf(
                    '<span class="tr-table-tag %s">%d days ago</span>',
                    $class,
                    $days
                );
            }
            return '<span class="tr-no-data">Never</span>';
        }
    ],
    'onboarded_at' => [
        'label' => 'Onboarded',
        'sort' => true,
        'callback' => function ($value) {
            if ($value) {
                return '<span class="tr-check">Yes</span><br><small>' . date('M d, Y', strtotime($value)) . '</small>';
            }
            return '<span class="tr-no-data">No</span>';
        }
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true,
        'callback' => function ($value) {
            return date('M d, Y', strtotime($value));
        }
    ]
], 'id');

$table->render();