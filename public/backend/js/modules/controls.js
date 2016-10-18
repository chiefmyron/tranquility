/**
 * Function library for attaching JS to custom control elements
 */
define(['tags', 'typeahead'], function(tags, typeahead) {
    var loggingEnabled = true;

    // Holds any typeahead instances for autocomplete controls
    var typeaheads = [];

    /**
     * Attach tag input to an element
     */
    function renderTagInput(element) {
        _log('Attaching tag control to element ' + element);
        $(element).tagsinput();
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