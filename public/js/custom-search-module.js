/**
 * Custom Search Module - jQuery Implementation
 *
 * This replaces jQuery-QueryBuilder with a more flexible custom implementation
 * that allows for proper grouping with AND/OR operators
 */

// Initialize our search module when document is ready
$(document).ready(function () {
    const SearchModule = {
        // Configuration
        config: {
            container: '#search-builder',
            filtersProvider: getSavedSearchFilters,
            debug: false,
            operators: {
                string: ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with', 'is_null', 'is_not_null'],
                nullable_string: ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with', 'is_null', 'is_not_null'],
                select: ['equal', 'not_equal', 'is_null', 'is_not_null'],
                date: ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'is_null', 'is_not_null'],
                boolean: ['equal', 'not_equal']
            },
            translations: {
                operators: {
                    'equal': 'égal',
                    'not_equal': 'non égal',
                    'begins_with': 'commence par',
                    'not_begins_with': 'ne commence pas par',
                    'contains': 'contient',
                    'not_contains': 'ne contient pas',
                    'ends_with': 'finit par',
                    'not_ends_with': 'ne finit pas par',
                    'is_null': 'est vide',
                    'is_not_null': 'n\'est pas vide',
                    'less': 'inférieur',
                    'less_or_equal': 'inférieur ou égal',
                    'greater': 'supérieur',
                    'greater_or_equal': 'supérieur ou égal',
                    'between': 'entre'
                },
                buttons: {
                    addCriteria: "Ajouter un critère",
                    addGroup: "Ajouter un groupe",
                    delete: "Supprimer",
                    and: "ET",
                    or: "OU"
                }
            }
        },

        // State management
        state: {
            groupIdCounter: 0,
            ruleIdCounter: 0,
            groups: []
        },

        /**
         * Initialize the search module
         */
        /**
         * Initialize the search module
         */
        init: function () {
            const container = $(this.config.container);
            if (!container.length) return;

            // Initialize state with no root group
            this.state.groups = [];

            // Create container for groups
            container.html('<div class="groups-container"></div>');

            // Add initial group
            this.addGroup();

            // Add a global group button
            container.append(`
        <div class="text-center mt-3 mb-3">
            <button type="button" class="btn btn-success add-global-group">
                <i class="fas fa-plus"></i> ${this.config.translations.buttons.addGroup}
            </button>
        </div>
    `);

            // Bind event handlers
            this.bindEvents();

            // Initialize existing filters if provided
            if (typeof initialSearchFilters !== 'undefined' && initialSearchFilters) {
                this.setRules(initialSearchFilters);
            }
        },

        /**
         * Create root group that will contain all rules and subgroups
         */
        createRootGroup: function (container) {
            const rootGroupId = 'search-group-' + (this.state.groupIdCounter++);
            const rootGroup = this.createGroupElement(rootGroupId, true);
            container.append(rootGroup);

            // Initialize state
            this.state.groups.push({
                id: rootGroupId,
                operator: 'AND',
                rules: [],
                groups: []
            });

            // Add initial criteria
            this.addRule(rootGroupId);
        },

        /**
         * Create a new group element
         */
        /**
         * Create a new group element
         */
        createGroupElement: function (groupId) {
            const groupHtml = `
        <div class="search-group card mb-3" id="${groupId}" data-group-id="${groupId}">
            <div class="card-body">
                <div class="group-header d-flex align-items-center mb-3">
                    <div class="operator-selector btn-group me-auto">
                        <input type="radio" class="btn-check" name="${groupId}-operator" id="${groupId}-and" value="AND" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="${groupId}-and">${this.config.translations.buttons.and}</label>

                        <input type="radio" class="btn-check" name="${groupId}-operator" id="${groupId}-or" value="OR" autocomplete="off">
                        <label class="btn btn-outline-primary" for="${groupId}-or">${this.config.translations.buttons.or}</label>
                    </div>
                    <button type="button" class="btn btn-danger delete-group ms-2">
                        <i class="fas fa-times"></i> ${this.config.translations.buttons.delete}
                    </button>
                </div>
                <div class="rules-container">
                    <!-- Rules will be inserted here -->
                </div>
                <div class="group-footer mt-3">
                    <button type="button" class="btn btn-primary add-rule">
                        <i class="fas fa-plus"></i> ${this.config.translations.buttons.addCriteria}
                    </button>
                </div>
            </div>
        </div>
    `;

            return $(groupHtml);
        },

        /**
         * Add a new rule to the specified group
         */
        addRule: function (groupId) {
            const group = $(`#${groupId}`);
            const rulesContainer = group.find('.rules-container');
            const ruleId = 'search-rule-' + (this.state.ruleIdCounter++);

            // Create rule element
            const ruleElement = this.createRuleElement(ruleId);
            rulesContainer.append(ruleElement);

            // Update state
            const groupState = this.findGroupInState(groupId);
            if (groupState) {
                groupState.rules.push({
                    id: ruleId,
                    field: null,
                    operator: null,
                    value: null
                });
            }

            // Initialize the field selector
            this.initializeFieldSelector(ruleId);

            return ruleId;
        },

        /**
         * Create a rule element
         */
        createRuleElement: function (ruleId) {
            const ruleHtml = `
        <div class="search-rule row align-items-end mb-2" id="${ruleId}" data-rule-id="${ruleId}">
            <div class="col-md-3">
                <label class="form-label">Champ</label>
                <select class="form-select field-selector" name="${ruleId}-field">
                    <option value="">--- Sélectionner ---</option>
                </select>
            </div>
            <div class="col-md-3 operator-container">
                <label class="form-label">Opérateur</label>
                <select class="form-select operator-selector" name="${ruleId}-operator" disabled>
                    <option value="">--- Sélectionner ---</option>
                </select>
            </div>
            <div class="col-md-5 value-container">
                <label class="form-label">Valeur</label>
                <div class="value-input-container d-flex align-items-center">
                    <!-- Value input will be inserted here -->
                    <input type="text" class="form-control" name="${ruleId}-value" disabled>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger delete-rule">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

            return $(ruleHtml);
        },

        /**
         * Initialize the field selector with available filters
         */
        initializeFieldSelector: function (ruleId) {
            const filters = this.config.filtersProvider();
            const selector = $(`#${ruleId} .field-selector`);

            // Clear existing options, keeping the placeholder
            selector.find('option:not(:first)').remove();

            // Add filter options
            filters.forEach(filter => {
                if (filter.nested) {
                    // Create optgroup for nested fields using the label
                    const optgroup = $(`<optgroup label="${filter.label}"></optgroup>`);

                    filter.entries.forEach(entry => {
                        const fieldId = `${filter.nested}.${entry.id}`;
                        const option = $(`<option value="${fieldId}">${entry.label}</option>`);
                        // Store the original entry data as data attribute
                        option.data('field-config', entry);
                        option.data('nested-parent', filter.nested);
                        option.data('nested-label', filter.label);
                        // IMPORTANT: Store the related information from the parent filter
                        if (filter.related) {
                            option.data('nested-related', filter.related);
                        }
                        optgroup.append(option);
                    });

                    selector.append(optgroup);
                } else {
                    // Simple field
                    const option = $(`<option value="${filter.id}">${filter.label}</option>`);
                    option.data('field-config', filter);
                    selector.append(option);
                }
            });
        },
        /**
         * Add a new group at the same level as other groups
         */
        addGroup: function (addInitialRule = true) {
            const groupId = 'search-group-' + (this.state.groupIdCounter++);
            const groupElement = this.createGroupElement(groupId, false);

            // Append to the main container
            $(this.config.container).find('.groups-container').append(groupElement);

            // Update state
            this.state.groups.push({
                id: groupId,
                operator: 'AND',
                rules: [],
                groups: []
            });

            // Add initial rule only if requested
            if (addInitialRule) {
                this.addRule(groupId);
            }

            return groupId;
        },

        /**
         * Handle field selection change
         */
        handleFieldChange: function (ruleId, fieldId) {
            const rule = $(`#${ruleId}`);
            const operatorSelector = rule.find('.operator-selector');
            const valueContainer = rule.find('.value-container');
            const valueInputContainer = valueContainer.find('.value-input-container');

            // Reset existing value input
            valueInputContainer.empty();
            operatorSelector.empty();
            operatorSelector.prop('disabled', !fieldId);

            if (!fieldId) return;

            // Get the field configuration (either from nested or simple structure)
            const fieldOption = rule.find('.field-selector option:selected');
            const fieldConfig = fieldOption.data('field-config');
            const nestedParent = fieldOption.data('nested-parent');
            const nestedLabel = fieldOption.data('nested-label');
            const nestedRelated = fieldOption.data('nested-related'); // Get the related information

            if (!fieldConfig) return;

            // Update rule state with the selected field
            const ruleState = this.findRuleInState(ruleId);
            if (ruleState) {
                ruleState.field = fieldId; // This will be like "account_address.country_code"
                ruleState.operator = null;
                ruleState.value = null;
                ruleState.nested = nestedParent; // Store nested parent if applicable
                ruleState.nestedLabel = nestedLabel; // Store nested label for UI
                ruleState.nestedRelated = nestedRelated; // Store related information
            }

            // If this is a nested field, check if we need to create/update nested UI
            if (nestedParent) {
                this.handleNestedFieldUI(ruleId, nestedParent, nestedLabel);
            }

            // Populate operators based on the field type
            this.populateOperators(operatorSelector, fieldConfig);

            // Auto-select the first operator and trigger change
            const firstOperatorOption = operatorSelector.find('option[value!=""]').first();
            if (firstOperatorOption.length > 0) {
                operatorSelector.val(firstOperatorOption.val());
                operatorSelector.trigger('change');
            }
        },

        /**
         * Handle nested field UI grouping
         */
        handleNestedFieldUI: function (ruleId, nestedParent, nestedLabel) {
            const rule = $(`#${ruleId}`);
            const groupId = rule.closest('.search-group').data('group-id');

            // Check if the rule is already in the correct nested container
            const currentNestedContainer = rule.closest('.nested-field-container');
            const currentNestedParent = currentNestedContainer.length > 0 ? currentNestedContainer.data('nested-parent') : null;

            // If the rule is already in the correct nested container, don't move it
            if (currentNestedParent === nestedParent) {
                return;
            }

            // If the rule is in a different nested container, we need to move it
            const rulesContainer = rule.closest('.search-group').find('.rules-container');

            // Check if there's already a nested container for this parent in the group
            let nestedContainer = rulesContainer.find(`[data-nested-parent="${nestedParent}"]`);

            if (nestedContainer.length === 0) {
                // Create nested container
                nestedContainer = $(`
            <div class="nested-field-container border rounded p-3 mb-3" data-nested-parent="${nestedParent}">
                <div class="nested-header">
                    <h6 class="text-primary mb-2">${nestedLabel}</h6>
                </div>
                <div class="nested-rules-container">
                    <!-- Nested rules will go here -->
                </div>
            </div>
        `);

                // Append to the rules container
                rulesContainer.append(nestedContainer);
            }

            // Remove rule from its current container (if it was in a nested one)
            if (currentNestedContainer.length > 0) {
                // Check if the old container will be empty after removing this rule
                const remainingRules = currentNestedContainer.find('.nested-rule').not(rule);
                if (remainingRules.length === 0) {
                    // Remove the empty nested container
                    currentNestedContainer.remove();
                }
            }

            // Move the rule into the new nested container
            const nestedRulesContainer = nestedContainer.find('.nested-rules-container');
            nestedRulesContainer.append(rule);

            // Add styling to distinguish nested rules
            rule.addClass('nested-rule');
        },

        /**
         * Populate operator selector based on field type
         */
        populateOperators: function (operatorSelector, filter) {
            // Determine which operators to use
            let operatorsToUse = [];

            if (filter.operators) {
                operatorsToUse = filter.operators;
            } else {
                switch (filter.type) {
                    case 'string':
                        operatorsToUse = this.config.operators.string;
                        break;
                    case 'integer':
                    case 'double':
                    case 'number':
                        operatorsToUse = this.config.operators.select;
                        break;
                    case 'date':
                        operatorsToUse = this.config.operators.date;
                        break;
                    default:
                        operatorsToUse = this.config.operators.string;
                }
            }

            // Add operator options
            operatorsToUse.forEach(op => {
                const label = this.config.translations.operators[op] || op;
                operatorSelector.append(`<option value="${op}">${label}</option>`);
            });

            // Enable operator selector
            operatorSelector.prop('disabled', false);
        },

        /**
         * Handle operator selection change
         */
        handleOperatorChange: function (ruleId, operatorValue, isLoading = false) {
            const rule = $(`#${ruleId}`);
            const valueContainer = rule.find('.value-container');
            const valueInputContainer = valueContainer.find('.value-input-container');

            // Update rule state
            const ruleState = this.findRuleInState(ruleId);
            if (ruleState) {
                ruleState.operator = operatorValue;
                // Don't reset value if we're loading
                if (!isLoading) {
                    ruleState.value = null;
                }
            }

            // Clear existing value input
            valueInputContainer.empty();

            if (!operatorValue) return;

            // Check if this operator requires a value
            const noValueOperators = ['is_null', 'is_not_null'];
            if (noValueOperators.includes(operatorValue)) {
                // Hide the value container for operators that don't need values
                valueContainer.hide();
                return;
            }

            // Get field configuration
            const fieldOption = rule.find('.field-selector option:selected');
            const fieldConfig = fieldOption.data('field-config');

            if (!fieldConfig) return;

            // Create appropriate input based on field type and operator
            // Pass isLoading flag to skip auto-selection
            this.createValueInput(valueInputContainer, fieldConfig, operatorValue, isLoading);

            // Show value container
            valueContainer.show();
        },

        /**
         * Create an appropriate value input based on filter configuration
         */
        createValueInput: function (container, filter, operator, skipAutoSelect = false) {
            // Extract ruleId from container's parent rule element
            const ruleElement = container.closest('.search-rule');
            const ruleId = ruleElement.data('rule-id');
            const inputName = `${ruleId}-value`;
            let input = null;

            // Handle custom input function if provided
            if (typeof filter.input === 'function') {
                // For between operator with custom function, we need to handle it specially
                if (operator === 'between') {
                    // Create first input
                    const customInput1 = filter.input({id: filter.id}, inputName);
                    container.append(customInput1);

                    // Add separator
                    container.append('<span class="mx-2">et</span>');

                    // Create second input
                    const customInput2 = filter.input({id: filter.id}, `${inputName}_end`);
                    container.append(customInput2);

                    // Initialize flatpickr for both inputs
                    this.initializeFlatpickr(container);

                    // Initialize autocomplete if needed
                    this.initializeAutocomplete(container, ruleId, filter);

                    return;
                } else {
                    // Single input
                    const customInput = filter.input({id: filter.id}, inputName);
                    container.append(customInput);

                    // Initialize flatpickr if needed
                    this.initializeFlatpickr(container);

                    // Initialize autocomplete if needed
                    this.initializeAutocomplete(container, ruleId, filter);

                    return;
                }
            }

            // Create input element based on the filter type and input specification
            switch (filter.input) {
                case 'select':
                    input = $(`<select class="form-select" name="${inputName}"></select>`);

                    // Add options from filter values
                    if (filter.values) {
                        for (const [value, label] of Object.entries(filter.values)) {
                            input.append(`<option value="${value}">${label}</option>`);
                        }
                    }
                    break;

                case 'radio':
                    input = $('<div class="btn-group" role="group"></div>');

                    // Add radio options
                    if (filter.values) {
                        for (const [value, label] of Object.entries(filter.values)) {
                            const radioId = `${ruleId}-${filter.id}-${value}`;
                            input.append(`
                <input type="radio" class="btn-check" name="${inputName}" id="${radioId}" value="${value}" autocomplete="off">
                <label class="btn btn-outline-primary" for="${radioId}">${label}</label>
            `);
                        }
                    }
                    break;

                case 'number':
                    input = $(`<input type="number" class="form-control" name="${inputName}">`);
                    break;

                default:
                    // Default to text input
                    input = $(`<input type="text" class="form-control" name="${inputName}">`);

                    // For date type, use flatpickr
                    if (filter.type === 'date') {
                        input.attr('data-type', 'flatpickr');
                        input.addClass('query-builder-flatpickr');
                        input.attr('data-conf', 'allowInput=true;dateFormat=Y-m-d;altInput=true;altFormat=DD/MM/YYYY');
                    }
            }

            container.append(input);

            // Handle between operator which needs two inputs
            if (operator === 'between') {
                container.append('<span class="mx-2">et</span>');

                // Create second input based on first one
                let secondInput;
                if (input.is('select')) {
                    secondInput = input.clone();
                    secondInput.attr('name', `${inputName}_end`);
                } else {
                    secondInput = $(`<input type="${input.attr('type')}" class="${input.attr('class')}" name="${inputName}_end">`);

                    // Copy data attributes for special inputs like flatpickr
                    const dataAttributes = input.data();
                    for (const key in dataAttributes) {
                        secondInput.attr(`data-${key}`, dataAttributes[key]);
                    }
                }

                container.append(secondInput);
            }

            // Initialize flatpickr if needed
            this.initializeFlatpickr(container);

            // ONLY auto-select if we're not loading from saved data
            if (!skipAutoSelect) {
                // Trigger change event for select inputs to set initial value
                if (input && input.is('select') && input.find('option').length > 0) {
                    // Set the first option as selected but DON'T trigger change yet
                    input.val(input.find('option:first').val());
                    // Manually update the state without triggering change event
                    const ruleState = this.findRuleInState(ruleId);
                    if (ruleState) {
                        ruleState.value = input.val();
                    }
                }

                // Also handle radio buttons - select first one by default
                if (input && input.hasClass('btn-group') && input.find('input[type="radio"]').length > 0) {
                    const firstRadio = input.find('input[type="radio"]:first');
                    firstRadio.prop('checked', true);
                    // Manually update the state
                    const ruleState = this.findRuleInState(ruleId);
                    if (ruleState) {
                        ruleState.value = firstRadio.val();
                    }
                }
            }
        },

        /**
         * Initialize flatpickr for date inputs
         */
        initializeFlatpickr: function (container) {
            const inputs = container.find('[data-type="flatpickr"]').not('[data-flatpickr-initialized]');

            if (!inputs.length) return;

            inputs.each(function () {
                const input = $(this);
                const configStr = input.data('conf') || '';
                const config = {};

                // Parse configuration string
                if (configStr) {
                    const pairs = configStr.split(';');
                    for (const pair of pairs) {
                        if (!pair) continue;

                        const [key, value] = pair.split('=');
                        config[key] = value === 'true' ? true :
                            value === 'false' ? false :
                                value;
                    }
                }

                // Set default flatpickr options
                const options = {
                    locale: 'fr',
                    ...config
                };

                // Initialize flatpickr
                flatpickr(input[0], options);
                input.attr('data-flatpickr-initialized', 'true');
            });
        },

        /**
         * Initialize autocomplete for user inputs
         */
        initializeAutocomplete: function (container, ruleId, filter) {
            // Special handling for user search
            if (filter.id === 'created_by') {
                const autocompleteInput = container.find('[data-type="autocomplete"]');
                if (autocompleteInput.length) {
                    const uniqueId = autocompleteInput.attr('id');
                    const hiddenInput = container.find('.hidden-input');

                    // Initialize autocomplete (assuming there's a global function)
                    if (typeof initBsAutocomplete === 'function') {
                        initBsAutocomplete(uniqueId, {
                            actionName: 'searchUsers',
                            ajaxSelector: $('#modal-search-panel-ajax-form'),
                            onSelectCallback: function (item) {
                                hiddenInput.val(item.value);
                                hiddenInput.trigger('change');

                                // Update rule state
                                const ruleState = this.findRuleInState(ruleId);
                                if (ruleState) {
                                    ruleState.value = item.value;
                                }
                            }.bind(this)
                        });
                    }
                }
            }
        },

        /**
         * Handle value input change
         */
        handleValueChange: function (ruleId, value, event) {
            const ruleState = this.findRuleInState(ruleId);
            if (!ruleState) return;

            const $input = event ? $(event.target) : null;
            const inputName = $input ? $input.attr('name') : '';

            // Check if this is a "between" operator with two values
            if (inputName && inputName.endsWith('_end')) {
                // This is the second value for "between" operator
                if (!ruleState.value || typeof ruleState.value !== 'object') {
                    ruleState.value = {};
                }
                ruleState.value.end = value;
            } else if (ruleState.operator === 'between') {
                // This is the first value for "between" operator
                if (!ruleState.value || typeof ruleState.value !== 'object') {
                    ruleState.value = {};
                }
                ruleState.value.start = value;
            } else {
                // Single value
                ruleState.value = value;
            }
        },

        /**
         * Delete a rule from the UI and state
         */
        deleteRule: function (ruleId) {
            const rule = $(`#${ruleId}`);
            const groupId = rule.closest('.search-group').data('group-id');
            const nestedContainer = rule.closest('.nested-field-container');

            // Remove from UI
            rule.remove();

            // If this was in a nested container and it's now empty, remove the container
            if (nestedContainer.length && nestedContainer.find('.search-rule').length === 0) {
                nestedContainer.remove();
            }

            // Remove from state
            const groupState = this.findGroupInState(groupId);
            if (groupState) {
                const ruleIndex = groupState.rules.findIndex(r => r.id === ruleId);
                if (ruleIndex !== -1) {
                    groupState.rules.splice(ruleIndex, 1);
                }

                // If group is empty after deleting the rule, add a new rule
                if (groupState.rules.length === 0 && groupState.groups.length === 0) {
                    this.addRule(groupId);
                }
            }
        },

        /**
         * Delete a group from the UI and state
         */
        deleteGroup: function (groupId) {
            // Don't delete if it's the only group
            if (this.state.groups.length <= 1) {
                alert('Au moins un groupe est nécessaire');
                return;
            }

            const group = $(`#${groupId}`);

            // Remove from UI
            group.remove();

            // Remove from state
            const groupIndex = this.state.groups.findIndex(g => g.id === groupId);
            if (groupIndex !== -1) {
                this.state.groups.splice(groupIndex, 1);
            }
        },

        /**
         * Handle group operator change
         */
        handleOperatorGroupChange: function (groupId, operator) {
            // Update group state with the selected operator
            const groupState = this.findGroupInState(groupId);
            if (groupState) {
                groupState.operator = operator;
            }
        },

        /**
         * Bind all event handlers
         */
        /**
         * Bind all event handlers
         */
        /**
         * Bind all event handlers
         */
        bindEvents: function () {
            const $container = $(this.config.container);

            // Delegate events for dynamic elements

            // Add rule button
            $container.on('click', '.add-rule', (e) => {
                const groupId = $(e.target).closest('.search-group').data('group-id');
                this.addRule(groupId);
            });

            // Add group button (global)
            $container.on('click', '.add-global-group', () => {
                this.addGroup();
            });

            // Delete rule button
            $container.on('click', '.delete-rule', (e) => {
                const ruleId = $(e.target).closest('.search-rule').data('rule-id');
                this.deleteRule(ruleId);
            });

            // Delete group button
            $container.on('click', '.delete-group', (e) => {
                const groupId = $(e.target).closest('.search-group').data('group-id');
                this.deleteGroup(groupId);
            });

            // Field selector change
            $container.on('change', '.field-selector', (e) => {
                const ruleId = $(e.target).closest('.search-rule').data('rule-id');
                const fieldId = $(e.target).val();
                this.handleFieldChange(ruleId, fieldId);
            });

            // Operator selector change
            $container.on('change', '.operator-selector', (e) => {
                const ruleId = $(e.target).closest('.search-rule').data('rule-id');
                const operatorValue = $(e.target).val();
                this.handleOperatorChange(ruleId, operatorValue);
            });

            // Value input change
            $container.on('change', '.value-input-container input, .value-input-container select', (e) => {
                const ruleId = $(e.target).closest('.search-rule').data('rule-id');
                const value = $(e.target).val();
                this.handleValueChange(ruleId, value, e);
            });

            // Group operator change
            $container.on('change', 'input[type="radio"][name$="-operator"]', (e) => {
                const groupId = $(e.target).closest('.search-group').data('group-id');
                const operator = $(e.target).val();
                this.handleOperatorGroupChange(groupId, operator);
            });

            // Form submission
            $('#modal-search-panel-ajax-form').on('submit', (e) => {
                e.preventDefault();
                this.submitSearch();
            });

            // Submit button click
            $('#modal-search-panel-ajax-form .submit-btn').on('click', (e) => {
                e.preventDefault();

                // Reset name if save dialog exists
                if ($('#search_save_dialog_name').length) {
                    $('#search_save_dialog_name').val('');
                }

                this.submitSearch();
            });
        },

        /**
         * Submit the search form
         */
        /**
         * Submit the search form
         */
        submitSearch: function() {
            const rules = this.getRules();
            if (!rules) return;

            const serializedData = JSON.stringify(rules);
            const jAjaxSelector = $('#modal-search-panel-ajax-form');

            // Debug output if enabled
            if (this.config.debug) {
                console.log("SearchModule: Search data submitted:", rules);
                this.showDebugOutput(rules);
            }

            // Determine if we should prevent actual submission (debug mode)
            const shouldPreventSubmission = this.config.debug;

            if (typeof ajax === 'function') {
                ajax('action=storeSavedSearchInSession&event_id=' + $(this.config.container).data('event-id') + '&type=' + searchType + '&search_filters=' + serializedData, jAjaxSelector, {
                    successHandler: function(response) {
                        if (response.ok) {
                            if (shouldPreventSubmission) {
                                console.log("SearchModule: Search successful, but reload prevented (debug mode)");
                            } else {
                                // Normal behavior - reload page
                                if (typeof utils !== 'undefined' && typeof utils.reload === 'function') {
                                    utils.reload();
                                } else {
                                    window.location.reload();
                                }
                            }
                        }
                        return true;
                    },
                });
            } else {
                // Fallback if ajax function doesn't exist
                $.ajax({
                    url: jAjaxSelector.data('ajax'),
                    method: 'POST',
                    data: {
                        action: 'storeSavedSearchInSession',
                        type: searchType,
                        search_filters: serializedData
                    },
                    success: function(response) {
                        if (response.ok) {
                            if (shouldPreventSubmission) {
                                console.log("SearchModule: Search successful, but reload prevented (debug mode)");
                            } else {
                                // Normal behavior - reload page
                                window.location.reload();
                            }
                        }
                    }
                });
            }
        },

        /**
         * Get rules in the format expected by the backend
         */
        getRules: function () {
            if (this.state.groups.length === 0) return null;

            // For a single group, return it directly
            if (this.state.groups.length === 1) {
                return this.formatGroup(this.state.groups[0]);
            }

            // For multiple groups, simply return an array of groups
            // without forcing them into a parent group with OR condition
            const groups = this.state.groups.map(group => this.formatGroup(group));

            return {
                rules: groups
            };
        },

        /**
         * Format a group for submission (updated for nested)
         */
        /**
         * Format a group for submission (updated for nested)
         */
        formatGroup: function (group) {
            const result = {
                condition: group.operator,
                rules: []
            };

            // Group rules by nested parent
            const nestedGroups = {};
            const simpleRules = [];

            group.rules.forEach(rule => {
                if (rule.field && rule.operator) {
                    const formattedRule = {
                        id: rule.field,
                        operator: rule.operator
                    };

                    // Add value only if operator requires it
                    const noValueOperators = ['is_null', 'is_not_null'];
                    if (!noValueOperators.includes(rule.operator)) {
                        // Special handling for "between" operator
                        if (rule.operator === 'between' && rule.value && typeof rule.value === 'object') {
                            // Convert object {start: ..., end: ...} to array [start, end]
                            formattedRule.value = [rule.value.start || '', rule.value.end || ''];
                        } else {
                            formattedRule.value = rule.value;
                        }
                    }

                    // Get the original field configuration to preserve related and parse
                    const fieldOption = $(`[data-rule-id="${rule.id}"] .field-selector option:selected`);
                    const fieldConfig = fieldOption.data('field-config');

                    if (fieldConfig) {
                        // Include 'related' if it exists
                        if (fieldConfig.related) {
                            formattedRule.related = fieldConfig.related;
                        }

                        // Include 'parse' if it exists
                        if (fieldConfig.parse) {
                            formattedRule.parse = fieldConfig.parse;
                        }
                    }

                    // Check if this is a nested field
                    if (rule.nested) {
                        if (!nestedGroups[rule.nested]) {
                            nestedGroups[rule.nested] = {
                                condition: 'AND',
                                rules: []
                            };
                            // Store the related information for this nested group
                            if (rule.nestedRelated) {
                                nestedGroups[rule.nested].related = rule.nestedRelated;
                            }
                        }
                        nestedGroups[rule.nested].rules.push(formattedRule);
                    } else {
                        simpleRules.push(formattedRule);
                    }
                }
            });

            // Add simple rules first
            result.rules = result.rules.concat(simpleRules);

            // Add nested groups
            Object.keys(nestedGroups).forEach(nestedKey => {
                const nestedGroup = {
                    nested: nestedKey,
                    query: {
                        condition: nestedGroups[nestedKey].condition,
                        rules: nestedGroups[nestedKey].rules
                    }
                };

                // Include the related field if it exists
                if (nestedGroups[nestedKey].related) {
                    nestedGroup.related = nestedGroups[nestedKey].related;
                }

                result.rules.push(nestedGroup);
            });

            return result;
        },

        /**
         * Set rules from saved filters
         */
        setRules: function (savedRules) {
            console.log('SearchModule: Loading saved rules:', savedRules);

            // Clear existing groups
            $(this.config.container).find('.groups-container').empty();
            this.state.groups = [];

            // Handle empty or invalid rules
            if (!savedRules) {
                console.log('SearchModule: No saved rules to load');
                this.addGroup();
                return;
            }

            // If it's a direct rules array (from the new format)
            if (savedRules.rules && Array.isArray(savedRules.rules)) {
                // Check if it's multiple groups
                const hasMultipleGroups = savedRules.rules.some(rule => rule.condition && rule.rules);

                if (hasMultipleGroups) {
                    // Multiple groups
                    savedRules.rules.forEach(groupData => {
                        if (groupData.condition && groupData.rules) {
                            const groupId = this.addGroup(false);
                            const group = this.findGroupInState(groupId);
                            group.operator = groupData.condition;

                            // Set operator in UI
                            $(`#${groupId}-${groupData.condition.toLowerCase()}`).prop('checked', true);

                            // Set rules for this group
                            this.setGroupRules(groupId, groupData);
                        }
                    });
                } else {
                    // Single group with rules array - treat as single group
                    const groupId = this.addGroup(false);
                    const group = this.findGroupInState(groupId);
                    group.operator = 'AND'; // Default operator

                    // Set rules
                    this.setGroupRules(groupId, savedRules);
                }
            } else if (savedRules.condition && savedRules.rules) {
                // Old format - single group
                const groupId = this.addGroup(false);
                const group = this.findGroupInState(groupId);
                group.operator = savedRules.condition;

                // Set operator in UI
                $(`#${groupId}-${savedRules.condition.toLowerCase()}`).prop('checked', true);

                // Set rules
                this.setGroupRules(groupId, savedRules);
            } else {
                // No valid structure found
                console.log('SearchModule: Invalid saved rules structure');
                this.addGroup();
            }
        },
// Add a helper function to format dates
        formatDateForDisplay: function(dateStr) {
            if (!dateStr) return '';

            // Check if date is in yyyy-mm-dd format
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                const [year, month, day] = dateStr.split('-');
                return `${day}/${month}/${year}`;
            }

            // Return as-is if already in correct format or unrecognized
            return dateStr;
        },

// Update setGroupRules to properly set dates with correct format
        setGroupRules: function (groupId, groupData) {
            if (!groupData.rules || !groupData.rules.length) return;

            groupData.rules.forEach(ruleData => {
                if (ruleData.nested && ruleData.query) {
                    // Handle nested rule - store the related field for later use
                    const nestedRelated = ruleData.related || null;

                    ruleData.query.rules.forEach(nestedRule => {
                        const ruleId = this.addRule(groupId);

                        setTimeout(() => {
                            // When setting the field, we need to ensure the related data is preserved
                            const $fieldSelector = $(`#${ruleId} .field-selector`);
                            $fieldSelector.val(nestedRule.id).trigger('change');

                            // After setting the field, update the rule state with the related info
                            const ruleState = this.findRuleInState(ruleId);
                            if (ruleState && nestedRelated) {
                                ruleState.nestedRelated = nestedRelated;
                            }

                            setTimeout(() => {
                                $(`#${ruleId} .operator-selector`).val(nestedRule.operator).trigger('change');

                                setTimeout(() => {
                                    if (nestedRule.value !== undefined) {
                                        const $container = $(`#${ruleId} .value-input-container`);

                                        // Handle "between" operator with array values
                                        if (nestedRule.operator === 'between') {
                                            if (Array.isArray(nestedRule.value) && nestedRule.value.length === 2) {
                                                // Wait a bit more for flatpickr to initialize
                                                setTimeout(() => {
                                                    const $input1 = $container.find('input[name$="-value"]');
                                                    const $input2 = $container.find('input[name$="-value_end"]');

                                                    // Use flatpickr's setDate method if available
                                                    if ($input1[0] && $input1[0]._flatpickr) {
                                                        $input1[0]._flatpickr.setDate(nestedRule.value[0], true);
                                                    } else {
                                                        $input1.val(this.formatDateForDisplay(nestedRule.value[0]));
                                                    }

                                                    if ($input2[0] && $input2[0]._flatpickr) {
                                                        $input2[0]._flatpickr.setDate(nestedRule.value[1], true);
                                                    } else {
                                                        $input2.val(this.formatDateForDisplay(nestedRule.value[1]));
                                                    }
                                                }, 200);
                                            }
                                        } else {
                                            // Handle single date field
                                            const $input = $container.find('input, select').first();

                                            // Check if it's a date field
                                            if ($input.hasClass('query-builder-flatpickr') || $input.attr('data-type') === 'flatpickr') {
                                                setTimeout(() => {
                                                    if ($input[0] && $input[0]._flatpickr) {
                                                        $input[0]._flatpickr.setDate(nestedRule.value, true);
                                                    } else {
                                                        $input.val(this.formatDateForDisplay(nestedRule.value));
                                                    }
                                                }, 200);
                                            } else {
                                                $input.val(nestedRule.value).trigger('change');
                                            }
                                        }
                                    }
                                }, 100);
                            }, 100);
                        }, 50);
                    });
                } else {
                    // Handle simple rule
                    const ruleId = this.addRule(groupId);

                    setTimeout(() => {
                        $(`#${ruleId} .field-selector`).val(ruleData.id).trigger('change');

                        setTimeout(() => {
                            $(`#${ruleId} .operator-selector`).val(ruleData.operator).trigger('change');

                            setTimeout(() => {
                                if (ruleData.value !== undefined) {
                                    const $container = $(`#${ruleId} .value-input-container`);

                                    // Handle "between" operator
                                    if (ruleData.operator === 'between') {
                                        if (Array.isArray(ruleData.value) && ruleData.value.length === 2) {
                                            // Wait for flatpickr to initialize
                                            setTimeout(() => {
                                                const $input1 = $container.find('input[name$="-value"]');
                                                const $input2 = $container.find('input[name$="-value_end"]');

                                                // Use flatpickr's setDate method if available
                                                if ($input1[0] && $input1[0]._flatpickr) {
                                                    $input1[0]._flatpickr.setDate(ruleData.value[0], true);
                                                } else {
                                                    $input1.val(this.formatDateForDisplay(ruleData.value[0]));
                                                }

                                                if ($input2[0] && $input2[0]._flatpickr) {
                                                    $input2[0]._flatpickr.setDate(ruleData.value[1], true);
                                                } else {
                                                    $input2.val(this.formatDateForDisplay(ruleData.value[1]));
                                                }
                                            }, 200);
                                        }
                                    } else {
                                        // Handle single value
                                        const $input = $container.find('input, select').first();

                                        // Check if it's a date field
                                        if ($input.hasClass('query-builder-flatpickr') || $input.attr('data-type') === 'flatpickr') {
                                            setTimeout(() => {
                                                if ($input[0] && $input[0]._flatpickr) {
                                                    $input[0]._flatpickr.setDate(ruleData.value, true);
                                                } else {
                                                    $input.val(this.formatDateForDisplay(ruleData.value));
                                                }
                                            }, 200);
                                        } else {
                                            $input.val(ruleData.value).trigger('change');
                                        }
                                    }
                                }
                            }, 100);
                        }, 100);
                    }, 50);
                }
            });
        },

        /**
         * Find a group in state by ID
         */
        findGroupInState: function (groupId) {
            return this.state.groups.find(group => group.id === groupId) || null;
        },

        /**
         * Find a rule in state by ID
         */
        findRuleInState: function (ruleId) {
            // Recursive function to search for a rule
            const findRule = (groups) => {
                for (const group of groups) {
                    // Check rules in this group
                    for (const rule of group.rules) {
                        if (rule.id === ruleId) {
                            return rule;
                        }
                    }

                    // Check subgroups
                    if (group.groups.length) {
                        const found = findRule(group.groups);
                        if (found) {
                            return found;
                        }
                    }
                }

                return null;
            };

            return findRule(this.state.groups);
        },

        /**
         * Enable debug mode
         */
        enableDebug: function() {
            this.config.debug = true;
            console.log('SearchModule: Debug mode enabled');
        },
        /**
         * Disable debug mode
         */
        disableDebug: function() {
            this.config.debug = false;
            this.removeDebugOutput();
            console.log('SearchModule: Debug mode disabled');
        },

        /**
         * Toggle debug mode
         */
        toggleDebug: function() {
            if (this.config.debug) {
                this.disableDebug();
            } else {
                this.enableDebug();
            }
            return this.config.debug;
        },

        /**
         * Show debug output in UI
         */
        showDebugOutput: function(data) {
            if (!this.config.debug) return;

            if (!$('#debug-output').length) {
                $(this.config.container).after(`
            <div id="debug-output" class="mt-3 p-3 border bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Debug Output (Search Data):</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('#debug-output pre').toggle()">
                        Toggle
                    </button>
                </div>
                <pre class="mb-0" style="max-height: 300px; overflow-y: auto;"></pre>
            </div>
        `);
            }
            $('#debug-output pre').text(JSON.stringify(data, null, 2));
        },

        /**
         * Remove debug output from UI
         */
        removeDebugOutput: function() {
            $('#debug-output').remove();
        },

    };

// Replace the jQuery QueryBuilder with our custom implementation
    $('#builder').replaceWith('<div id="search-builder" class="mb-3"></div>');

// Initialize the search module
    SearchModule.init();
    // SearchModule.enableDebug();
});
