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
                        return $.map(list, function(cityname) {
                            return { name: cityname }; 
                        });
                    }
                }
            });
            tags.initialize();
            typeaheads['tags'] = tags;
        }
        
        $(element).tagsinput({
            tagClass: '',
            typeaheadjs: {
                name: 'tags',
                displayKey: 'name',
                valueKey: 'name',
                source: typeaheads['tags'].ttAdapter(),
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
        renderTagInput: renderTagInput
    };
});