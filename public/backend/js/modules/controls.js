/**
 * Function library for attaching JS to custom control elements
 */
define(['selectize'], function() {
    var loggingEnabled = true;

    /**
     * Attach tag input to an element
     */
    function renderTagInput(element) {
        _log('Attaching tag control to element ' + element);
        var url = $(element).attr('data-custom-control-datasource');

        $(element).selectize({
            plugins: ['remove_button'],
            delimter: ',',
            valueField: 'label',
            labelField: 'label',
            searchField: 'label',
            create: function(input) {
                return {
                    'label': input
                }
            },
            render: {
                item: function(item, escape) {
                    return '<div>' + item.label + '</div>';
                },
                option: function(item, escape) {
                    return '<div>' + item.label + '</div>';
                }
            },
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        term: query
                    },
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res);
                    }
                });
            }
        });
    }

    /**
     * Attach single entity select to an element
     */
    function renderEntitySelect(element) {
        _log('Attaching "' + entityType + '" entity select control to element ' + element);

        // Get entity type
        var entityType = $(element).attr('data-custom-control-entity-type');
        var url = $(element).attr('data-custom-control-datasource');

        // Add the existing value
        existingValue = $(element).val();
        existingOptions = [];
        selectedItem = [];
        if (existingValue != '') {
            _log('Existing value found for element: ' + existingValue);
            values = existingValue.split(":");
            existingOptions.push({ id: values[0], label: values[1] });
            selectedItem.push(values[0]);
            $(element).val("");
        }

        $(element).selectize({
            maxItems: 1,
            valueField: 'id',
            labelField: 'label',
            searchField: 'label',
            create: false,
            options: existingOptions,
            items: selectedItem,
            render: {
                item: function(item, escape) {
                    return '<div>' + item.label + '</div>';
                },
                option: function(item, escape) {
                    return '<div>' + item.label + '</div>';
                }
            },
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        entity: entityType,
                        q: query
                    },
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res);
                    }
                });
            }
        });
    }

    /**
     * Private: Message logging
     */
    function _log(message) {
        if (loggingEnabled) {
            console.log('[CONTROLS] ' + message);
        }
    }

    // Expose public functions
    return {
        renderTagInput: renderTagInput,
        renderEntitySelect: renderEntitySelect
    };
});