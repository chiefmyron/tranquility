/**
 * Main javascript function library for the BackOffice application
 */
define(['modules/controls', 'bootstrap', 'jquery'], function(controls) {
    var loggingEnabled = true;
    
    // Holds any typeahead instances for autocomplete controls
    var typeaheads = [];

    // Initialise module
    var init = function() {
        _log("init() started");

        // Get CSRF token for AJAX requests (@see https://laravel.com/docs/5.3/csrf#csrf-x-csrf-token) 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        _log("init() complete");
    }

    /**
     * Render dynamic / JS enabled controls
     */
    var renderCustomControls = function(context) {
        // If no context is provided, use the document
        if (context == 'undefined' || context == null) {
            context = document;
        }

        // Check for custom controls
        $(context).find('[data-custom-control]').each(function() {
            elementType = $(this).attr("data-custom-control");
            switch (elementType) {
                case 'tag-input':
                    controls.renderTagInput(this);
                    break;
                case 'entity-select-single':
                    controls.renderEntitySelect(this);
                    break;
                case 'date-input':

                    break;
            }
        });
    }

    /**
     * Attach handlers for common links, controls and custom events
     */
    var attachCommonHandlers = function() {
        // Hyperlinks
        _log('Attach event handler for hyperlink clicks');
        $('body').on('click', 'a.ajax', _xhrItemEventHandler)    // AJAX-enabled hyperlinks
                 .on('click', 'a.disabled', false);              // Disabled hyperlinks

        // AJAX form submission
        _log('Attach event handler for form submission');
        $('body').on('submit', 'form.ajax-submit', _xhrFormSubmitEventHandler);

        // Data table
        _log('Attach event handler for data table record select checkboxes');
        $('body').on('change', 'table.data-table input:checkbox.record-select', _dataTableRecordSelect)
                 .on('change', 'table.data-table input:checkbox.record-select-all', _dataTableRecordSelectAll);

        // Custom events
        _log('Attach handlers for custom application events');
        $('body').on('tql.element.refresh', _refreshControls);
    }

    /** 
     * Force display of the modal dialog
     */
    var displayDialog = function() {
        var modalContent = arguments[0];
        var size = arguments[1];
        
        // If modal content has been supplied, inject it now
        if ((typeof modalContent !== 'undefined') && (modalContent !== null) && (modalContent.length > 0))  {
            _updateElementContents($("#modal-dialog-container"), modalContent);
        }
        
        // Add class to change size of modal dialog
        $("#modal-dialog-container .modal-dialog").removeClass("modal-lg modal-sm");
        switch(size) {
            case "large":
                $("#modal-dialog-container .modal-dialog").addClass("modal-lg");
                break;
            case "small":
                $("#modal-dialog-container .modal-dialog").addClass("modal-sm");
                break;
        }
        $("#modal-dialog-container").modal('show');
        
        // Set autofocus for elements in dialog
        $("#modal-dialog-container").find('[autofocus]').focus();
    }

    /**
     * Force close the modal dialog
     */
    var closeDialog = function() {
        $("#modal-dialog-container").modal('hide');
        return false;
    }

    /**
     * Helper function to hide an element with the specified ID
     */
    var hideElement = function(target) {
        return $("#" + target).collapse('hide');
    }

    /**
     * Helper function to show an element with the specified ID
     */
    var showElement = function(target) {
        return $("#" + target).collapse('show');
    }

    /**
     * Event handler: Click for AJAX enabled links
     */
    function _xhrItemEventHandler(event) {
        // Prevent link navigation
        event.preventDefault();
        
        // Check that link is not disabled
        if ($(this).hasClass('disabled')) {
            return;
        }

        // Check if a preload target has been specified
        var preloadTarget = $(this).attr("data-ajax-preload-target");
        switch (preloadTarget) {
            case 'modal':
                // Open dialog immediately and show loading spinner
                response = _generateAjaxGenericResponse("#modal-dialog-container .modal-content", "<div class='modal-spinner'></div>", displayDialog);
                _callbackProcessAjaxResponse(response);
                break;
        }
        
        // Check if link is for an action that works on multiple selected items
        if ($(this).hasClass('multi-select')) {
            // Multiple items selected - use a POST request
            var selectedItems = _getSelectedListItems();
            _ajaxCall($(this).attr("href"), "POST", {id: selectedItems}, "json");
        } else {
            _ajaxCall($(this).attr("href"), "GET", {}, "json");
        }
    }

    /**
     * Event handler: Submission of AJAX enabled forms
     */
    function _xhrFormSubmitEventHandler(event) {
        // Prevent default form behaviour
        event.preventDefault();
        
        // Serialise form input and submit via AJAX
        var form = $(this).closest("form");
        var formValues = form.serialize();
        var formAction = form.attr("action");
        var formMethod = form.attr("method");
        return _ajaxCall(formAction, formMethod, formValues, "json");
    }

    /**
     * Event handler: Checkbox row selector for data tables
     */
    function _dataTableRecordSelect(event) {
        var table = $(this).parents('table.data-table');
        _changeTableActionButtonStatus(table);
    }

    /**
     * Event handler: 'Select all' row selector for data tables
     */
    function _dataTableRecordSelectAll(event) {
        var table = $(this).parents('table.data-table');
        $('tbody td input:checkbox.record-select', table).prop('checked', this.checked);
        _changeTableActionButtonStatus(table);
    }

    /**
     * Event handler: Render javascript-enabled controls
     */
    function _refreshControls(event) {
        // Get the container element
        container = event.target;
        _log("Refreshing bindings for controls inside element " + event.target);
        renderCustomControls(event.target);
    }

    /**
     * Event handler: Dialog load complete
     */
    function _eventDialogDisplayed(context, e) {
        console.log ("Event: Dialog displayed");
        
        
        // Attach tagging
        context.find('[data-role="tagsinput"]').each(function (element) {
            $(this).tagsInput({
                'height': 'auto',
                'width': '100%',
                'autocomplete_url': $(this).attr('data-autocomplete'),
                'delimiter': ','
            });
        });
    }

    /**
     * Private: Wrapper to handle AJAX calls to backend
     */
    function _ajaxCall(url, type, data, datatype) {
        _log("Performing " + type.toUpperCase() + " request to endpoint: " + url);

        // Perform AJAX call - handling of response will be done by generic callbacks
        $.ajax({
            url: url,
            type: type.toUpperCase(),
            dataType: datatype.toLowerCase(),
            async: true,
            data: data,
            success: _callbackAjaxSuccess,
            error: _callbackAjaxError
        });
    }

    /**
     * Callback: Generic processor for success message
     */
    function _callbackAjaxSuccess(data, textStatus, xhr) {
        var response = {
            content: [],
            messages: []
        };
        
        // Check that a response was received
        if (data === null) {
            // No response returned
            response = _generateAjaxGenericResponse("#modal-dialog-container .modal-content", "AJAX request did not return any data", displayDialog);
            return _callbackProcessAjaxResponse(response);
        }
        
        // Ensure response is an object
        if (typeof(data) !== 'object') {
            try {
                response = $.parseJSON(data);
            } catch (ex) {
                // Generate error message content for 
                response = _generateAjaxGenericResponse("#modal-dialog-container .modal-content", "Unable to parse AJAX response: " + ex.message, displayDialog);
                return _callbackProcessAjaxResponse(response);
            }
        }

        // Process response object
        return _callbackProcessAjaxResponse(data);
    }

    /**
     * Callback: Error handler for AJAX call
     */
    function _callbackAjaxError(xhr, textStatus, errorDetails) {
        _log("Error response received from AJAX request: " + textStatus + " [" + errorDetails + "]");

        // Handle error depending on the HTTP response code
        switch (xhr.status) {
            case 401:
            case 403:
                // Unauthorised access - display login dialog
                return ajaxCall("/administration/auth", "GET", {}, "json");
            case 500:
            default:
                // Server error - display message
                response = _generateAjaxGenericResponse("#modal-dialog-container .modal-content", xhr.responseText, displayDialog)
                return _callbackProcessAjaxResponse(response);
        }
    }

    /**
     * Callback: Process responses from a successful AJAX call to be backend.
     * Expects response to be a serialised Tranquility\View\AjaxResponse object
     */
    function _callbackProcessAjaxResponse(response) {
        // Update HTML areas with new content
        $.each(response.content, function(i, item) {
            // Replace HTML in specified element
            if (item.element != null) {
                _updateElementContents(item.element, item.content);
            }
            
            // If callback was included, execute it now
            if (item.callback != null) {
                // If callback is a function, call it directly
                if ($.isFunction(item.callback)) {
                    item.callback.apply(this, item.callbackArgs);
                } else {
                    // Attempt to execute function from its name
                    _executeFunctionByName(item.callback, window, item.callbackArgs);
                }
            }
        });

        // Remove any existing inline error messages
        $('div.alert-inline').slideUp().remove();
        
        // Display inline error messages
        $.each(response.messages, function(i, message) {
            if (message.fieldId != null) {
                // If message has not been pre-rendered, attempt to render it now
                if (message.html == null || message.html == 'undefined') {
                    message.html = '<div class="alert-inline alert-' + message.level + '" style="display: none;">' + message.text + '</div>'; 
                }

                // Add new inline error message (hidden by default)
                $("#" + message.fieldId).after(message.html);
                _log(message.text + '(Field ID: ' + message.fieldId + ' | Level: ' + message.level + ')');
            }
        });

        // Display all inline error messages
        $("div.alert-inline").slideDown();
    }

    /**
     * Private: Generate response object with a single content item and callback
     */
    function _generateAjaxGenericResponse(element, message, callback, callbackArgs) {
        var response = {
            content: []
        }

        var contentItem = {
            element: element,
            content: message,
            callback: callback,
            callbackArgs: callbackArgs
        }

        response.content.push(contentItem);
        return response;
    }

    /**
     * Private: Show or hide the actions for a data table, depending on 
     * whether any records are selected.
     */
    function _changeTableActionButtonStatus(table) {
        if ($('tbody td input.record-select', table).is(':checked')) {
            // Enable action buttons
            $('thead .table-action', table).removeClass('disabled');
            $('thead div.actions-container', table).slideDown(200);

            // Update count of selected items
            selectedItems = _getSelectedListItems();
            $("#item-selected-counter").html(selectedItems.length);
        } else {
            // Disable action buttons
            $('thead .table-action', table).addClass('disabled');
            $('thead div.actions-container', table).slideUp(200);

            // Uncheck the 'select all' checkbox
            $('tbody th .selectAll', table).prop('checked', false);
        }
    }
    
    /**
     * Private: Executes a function in the specified context
     * 
     * @link http://stackoverflow.com/questions/359788/how-to-execute-a-javascript-function-when-i-have-its-name-as-a-string
     */
    function _executeFunctionByName(functionName, context) {
        _log('Executing function "' + functionName + '" in context "' + context + '"');
        
        var args = arguments[2];
        var namespaces = functionName.split(".");
        var func = namespaces.pop();
        for(var i = 0; i < namespaces.length; i++) {
            context = context[namespaces[i]];
        }
        return context[func].apply(context, args);
    }

    /**
     * Private: Retrieve an array of all selected checkboxes with the specified name
     */
    function _getSelectedListItems(inputName) {
        if (inputName == "" || inputName == undefined) {
            inputName = "id";
        }
        
        var selectedItems = [];
        $('input:checkbox[name='+inputName+']:checked').each(function() {
            selectedItems.push($(this).val());
        });
        
        return selectedItems;
    }

    /**
     * Private: Updates the content of the specified element with new HTML
     * and fires the 'tql.element.refresh' event
     */
    function _updateElementContents(element, content) {
        _log('Updating contents of element "' + element + '"');
        $(element).html(content);
        $(element).trigger('tql.element.refresh');
    }

    /**
     * Private: Render process messages and inline error messages for forms
     * 
     * @var messages Array of message strings
     * @var target   [Optional] Element ID to display messages inside of
     */
    /*function displayMessages(messages, target) {
        var inline_container, container_div;
        
        // Determine container for messages
        var messageContainer = "#process-message-container"
        if ((typeof target !== 'undefined') && (target.length > 0))  {
            messageContainer = target;
        }
        
        // Remove any existing messages
        $("span.help-inline").slideUp().remove();
        if ($(messageContainer).is(":visible")) {
            $(messageContainer).slideUp();
        }
        
        // Generate HTML for messages
        var messageHtml = "";
        for (var i = 0; i < messages.length; i++) {
            
        }

        // Work out where we are displaying top-level (i.e. not field level) messages
        if (type == "dialog") {
            container_div = "#modal-message-container";
            
            // Remove inline error messages from dialog
            inline_container = "#modalDialog ";
            $(inline_container + "span.help-inline").slideUp();
            $(inline_container + "span.help-inline").remove();
        } else {
            container_div = "#message-container";
            
            // Remove all inline error messages
            inline_container = "";
            $("span.help-inline").slideUp();
            $("span.help-inline").remove();
        }
        
        // Clear any existing top-level messages
        if ($(container_div).is(":visible")) {
            $(container_div).slideUp();
        }
        
        // Loop through supplied messages and add to container
        var messageHtml = "";
        for (var i = 0; i < messages.length; i++) {
            if (messages[i].html.length > 0) {
                // HTML message present - add to top level message container
                messageHtml = messageHtml + messages[i].html;
            } else if (messages[i].fieldId != null) {
                // Field level validation messages display under form element
                var formElement = $(inline_container + " #" + messages[i].fieldId);
                formElement.after('<span class="help-inline" style="display: none;">' + messages[i].text + '</span>');
            }
        }
        
        // If there are messages, append them and display the container
        if (messageHtml.length > 0) {
            $(container_div).queue(function() {
                $(this).html(messageHtml);
                $(this).dequeue();
            });
            
            $(container_div).slideDown();
        }
        
        // Display any inline errors as well
        $(inline_container + "span.help-inline").slideDown();
    }*/

    /**
     * Private: Message logging
     */
    function _log(message) {
        if (loggingEnabled) {
            console.log('[CORE] ' + message);
        }
    }

    // Expose public functions
    return {
        init: init,
        attachCommonHandlers: attachCommonHandlers,
        renderCustomControls: renderCustomControls,
        displayDialog: displayDialog,
        closeDialog: closeDialog,
        showElement: showElement,
        hideElement: hideElement
    };
});



























