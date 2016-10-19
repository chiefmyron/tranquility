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

        $(element).on('itemAdded', function(event) {
            if ($(this).tagsinput('items').length > 0) {
                console.log($(this));
                console.log($(this).siblings().find('input.tt-input'));
                $(this).parent().find('input.tt-input').hide();
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