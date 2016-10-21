/**
 * Function library for attaching JS to custom control elements
 */
define(['tagsinput', 'typeahead', 'bloodhound'], function() {
    var loggingEnabled = true;

    // Holds any typeahead instances for autocomplete controls
    var typeaheads = [];

    /**
     * Attach tag input to an element
     */
    function renderTagInput(element) {
        _log('Attaching tag control to element ' + element);

        // Create typeahead instance for existing tag lookup
        if (!('tags' in typeaheads)) {
            _log('Creating new Bloodhound instance for "tags"');
            var url = $(element).attr('data-custom-control-datasource');
            var tags = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: (url + '?term=%QUERY'),
                    wildcard: '%QUERY',
                    filter: function(list) {
                        return $.map(list, function(tagname) {
                            return { name: tagname }; 
                        });
                    }
                }
            });
            tags.initialize();
            typeaheads['tags'] = tags;
        }
        
        $(element).tagsinput({
            tagClass: '',
            allowDuplicates: false,
            typeaheadjs: {
                name: 'tags',
                displayKey: 'name',
                valueKey: 'name',
                source: typeaheads['tags'].ttAdapter(),
            }
        });
    }

    /**
     * Attach single entity select to an element
     */
    function renderEntitySelect(element) {
        // Get entity type
        entityType = $(element).attr('data-custom-control-entity-type');
        _log('Attaching "' + entityType + '" entity select control to element ' + element);

        // Create typeahead instance for existing tag lookup
        if (!(entityType in typeaheads)) {
            _log('Creating new Bloodhound instance for "' + entityType + '"');
            var url = $(element).attr('data-custom-control-datasource');
            var entities = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: (url + '?entity=' + entityType + '&q=%QUERY'),
                    wildcard: '%QUERY',
                }
            });
            entities.initialize();
            typeaheads[entityType] = entities;
        }

        // Render control
        existingValue = $(element).val();
        $(element).tagsinput({
            tagClass: 'single-select',
            itemValue: 'id',
            itemText: 'label',
            maxTags: 1,
            typeaheadjs: {
                name: $(element).attr('name') + '-entity-select',
                displayKey: 'label',
                source: typeaheads[entityType].ttAdapter(),
            }
        });
        $(element).siblings('div.bootstrap-tagsinput').addClass('entity-select-single');

        $(element).on('itemAdded', function(event) {
            if ($(this).tagsinput('items').length > 0) {
                $(this).parent().find('.twitter-typeahead').hide();
            }
        });

        $(element).on('itemRemoved', function(event) {
            if ($(this).tagsinput('items').length <= 0) {
                $(this).parent().find('.twitter-typeahead').show();
            }
        });

        // Add the existing value
        if (existingValue != '') {
            _log('Existing value found for element: ' + existingValue);
            values = existingValue.split(":");
            $(element).tagsinput('add', { id: values[0], label: values[1] });
        }
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