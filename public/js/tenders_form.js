$(document).ready(function() {
    // Initialize CKEditor
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('tender_description');
    } else {
        console.error('CKEditor is not loaded.');
    }

    // Initialize Select2
    $('.select2-checkbox').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Financial sub-tabs configuration
    const financialTabs = {
        'financial-statement': {
            tableId: 'financial-statement-table',
            fieldPrefix: 'financial_statements',
            existingYears: [],
            descending: false
        },
        'itr': {
            tableId: 'itr-table',
            fieldPrefix: 'financials[itr]',
            namePrefix: 'itr_years',
            specificField: 'itr',
            existingYears: [],
            descending: true
        },
        'turnover': {
            tableId: 'turnover-table',
            fieldPrefix: 'financials[turnover]',
            namePrefix: 'turnover_years',
            specificField: 'turnover',
            existingYears: [],
            descending: false
        },
        'networth': {
            tableId: 'networth-table',
            fieldPrefix: 'financials[net_worth]',
            namePrefix: 'networth_years',
            specificField: 'net_worth',
            existingYears: [],
            descending: false
        }
    };

    // Populate existing years for each financial sub-tab
    $.each(financialTabs, function(tabKey, tab) {
        $(`#${tab.tableId} [data-year]`).each(function() {
            const year = parseInt($(this).data('year'));
            if (!isNaN(year) && !tab.existingYears.includes(year)) {
                tab.existingYears.push(year);
            }
        });
        tab.existingYears.sort((a, b) => tab.descending ? b - a : a - b);
    });

    // Add row handlers
    $('#add-financial-statement-row').click(() => addFinancialRow('financial-statement'));
    $('#add-itr-row').click(() => addFinancialRow('itr'));
    $('#add-turnover-row').click(() => addFinancialRow('turnover'));
    $('#add-networth-row').click(() => addFinancialRow('networth'));

    function addFinancialRow(tabKey) {
        const tab = financialTabs[tabKey];
        const currentYear = new Date().getFullYear();
        const tempYearRange = 'temp-' + Math.random().toString(36).substr(2, 9);

        const $newRow = $(`
            <tr data-year-range="${tempYearRange}">
                <td>
                    <input type="number" class="form-control from-year" 
                           name="${tab.namePrefix || tab.fieldPrefix}[${tempYearRange}][from_year]" 
                           placeholder="From Year" 
                           min="2000" max="${currentYear + 1}" required>
                </td>
                <td>
                    <input type="number" class="form-control to-year" 
                           name="${tab.namePrefix || tab.fieldPrefix}[${tempYearRange}][to_year]" 
                           placeholder="To Year" 
                           min="2001" max="${currentYear + 2}" required>
                </td>
                ${tab.specificField ? getFieldCell(tab.fieldPrefix, tempYearRange, tab.specificField) : getStatementCells(tab.fieldPrefix, tempYearRange)}
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-financial-row">Remove</button></td>
            </tr>
        `);

        const $tbody = $(`#${tab.tableId} tbody`);
        tab.descending ? $tbody.prepend($newRow) : $tbody.append($newRow);
        $newRow.addClass('animate__animated animate__fadeIn');
    }

    function getStatementCells(fieldPrefix, yearRange) {
        return `
            <td><input type="checkbox" name="${fieldPrefix}[${yearRange}][revenue]"></td>
            <td><input type="checkbox" name="${fieldPrefix}[${yearRange}][expenses]"></td>
            <td><input type="checkbox" name="${fieldPrefix}[${yearRange}][profit_loss]"></td>
        `;
    }

    function getFieldCell(fieldPrefix, yearRange, fieldName) {
        if (fieldName === 'itr') {
            return `
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" 
                               name="${fieldPrefix}[${yearRange}]" value="1">
                    </div>
                </td>
            `;
        } else if (fieldName === 'net_worth') {
            return `
                <td>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="${fieldPrefix}[${yearRange}]" 
                                   value="positive">
                            <label class="form-check-label">Positive</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="${fieldPrefix}[${yearRange}]" 
                                   value="negative">
                            <label class="form-check-label">Negative</label>
                        </div>
                    </div>
                </td>
            `;
        } else {
            return `
                <td>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" class="form-control" 
                               name="${fieldPrefix}[${yearRange}]" 
                               placeholder="Enter amount">
                    </div>
                </td>
            `;
        }
    }

    function sortFinancialTable($table, descending) {
        const $tbody = $table.find('tbody');
        const $rows = $tbody.find('tr').get();

        $rows.sort((a, b) => {
            const aYear = parseInt($(a).find('.from-year').val()) || 0;
            const bYear = parseInt($(b).find('.from-year').val()) || 0;
            return descending ? bYear - aYear : aYear - bYear;
        });

        $.each($rows, (index, row) => $tbody.append(row));
    }

    $(document).on('click', '.remove-financial-row', function() {
        const $row = $(this).closest('tr');
        const tabKey = $row.closest('table').attr('id').replace('-table', '');
        const tab = financialTabs[tabKey];
        const year = parseInt($row.data('year'));

        $row.addClass('animate__animated animate__fadeOut');
        setTimeout(() => {
            if (year && tab.existingYears.includes(year)) {
                tab.existingYears = tab.existingYears.filter(y => y !== year);
                tab.existingYears.sort((a, b) => tab.descending ? b - a : a - b);
            }
            $row.remove();
        }, 500);
    });

    $(document).on('change', '.from-year', function() {
        const $row = $(this).closest('tr');
        const fromYear = parseInt($(this).val());
        const $toYearInput = $row.find('.to-year');
        const tabKey = $row.closest('table').attr('id').replace('-table', '');
        const tab = financialTabs[tabKey];

        if (!isNaN(fromYear)) {
            const toYear = fromYear + 1;
            $toYearInput.val(toYear);

            if (tab.existingYears.includes(fromYear)) {
                alert(`Year ${fromYear}-${toYear} already exists in the ${tabKey} table.`);
                $(this).val('');
                $toYearInput.val('');
                return;
            }

            const oldYear = parseInt($row.data('year'));
            if (oldYear && tab.existingYears.includes(oldYear)) {
                tab.existingYears = tab.existingYears.filter(y => y !== oldYear);
            }
            tab.existingYears.push(fromYear);
            tab.existingYears.sort((a, b) => tab.descending ? b - a : a - b);

            updateYearRange($row, fromYear, toYear);
            $row.data('year', fromYear);
            sortFinancialTable($row.closest('table'), tab.descending);
        }
    });

    $(document).on('change', '.to-year', function() {
        const $row = $(this).closest('tr');
        const fromYear = parseInt($row.find('.from-year').val());
        const toYear = parseInt($(this).val());

        if (!isNaN(fromYear) && !isNaN(toYear)) {
            if (toYear !== fromYear + 1) {
                alert('To Year must be exactly 1 year after From Year');
                $(this).val(fromYear + 1);
                updateYearRange($row, fromYear, fromYear + 1);
            } else {
                updateYearRange($row, fromYear, toYear);
            }
            sortFinancialTable($row.closest('table'), financialTabs[$row.closest('table').attr('id').replace('-table', '')].descending);
        }
    });

    function updateYearRange($row, fromYear, toYear) {
        const newYearRange = `${fromYear}-${toYear}`;
        const oldYearRange = $row.data('year-range');

        $row.find('[name]').each(function() {
            const oldName = $(this).attr('name');
            const newName = oldName.replace(oldYearRange, newYearRange);
            $(this).attr('name', newName);
        });

        $row.attr('data-year-range', newYearRange);
    }

    // Add more field handler
    // In your JavaScript file, update the addMoreField function:
window.addMoreField = function(type) {
    let containerId, html, fieldClass, inputName, placeholder;
    
    if (type === 'keyword') {
        containerId = 'keyword-container';
        fieldClass = 'keyword-field';
        inputName = 'keywords[]';
        placeholder = 'Enter keyword';
    } 
    else if (type === 'work-experience-keyword') {
        containerId = 'work-experience-keywords-container';
        fieldClass = 'work-experience-keyword-field';
        inputName = 'work_experience[work_exp_keywords][]';
        placeholder = 'Enter work experience keyword';
    } 
    else {
        console.error(`Unsupported field type: ${type}`);
        alert(`Error: Unsupported field type ${type}.`);
        return;
    }

    html = `
        <div class="${fieldClass} mb-2 animate__animated animate__fadeIn">
            <div class="input-group">
                <input type="text" class="form-control" name="${inputName}" placeholder="${placeholder}">
                <button type="button" class="btn btn-outline-danger remove-field">Remove</button>
            </div>
        </div>
    `;

    const container = document.getElementById(containerId);
    if (container) {
        container.insertAdjacentHTML('beforeend', html);
    } else {
        console.error(`Container with ID ${containerId} not found in DOM.`);
    }
};

    // Remove field handler
    window.removeField = function(element) {
        const field = element.closest('.keyword-field, .work-experience-field, .website-field, .certificate-field');
        if (field) {
            $(field).addClass('animate__animated animate__fadeOut');
            setTimeout(() => field.remove(), 500);
        } else {
            console.error('Field element not found for removal.');
        }
    };
});