
/**
 * Main javascript function library for the BackOffice application
 *
 * Relies on jQuery being loaded beforehand
 *
 * @version $Id$
 * @package Backoffice
 * @author Andrew Patterson <patto@live.com.au>
 */

// AJAX setup 
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

// Initial page setup
$(document).ready(function() {
    attachCommonHandlers();
});

/**
 * Attach and/or refresh event handlers to common administration page elements 
 * (e.g. toolbar, AJAX enalbed links, checkboxes in data tables).
 */
function attachCommonHandlers() {
    // Clear any existing handlers
    $("#toolbar-container li.ajax a").off('click.toolbarEvent');
    $("a.ajax").off('click.ajaxLinkEvent');
    $("table th .selectAll").off('click.selectAllCheckbox');
    $(".page-header.navbar .search-form button").off('click.globalSearch');
    
    // Handle click event for links throughout the UI
    $("a.ajax").on('click.ajaxLinkEvent', function(e) {
        if (!($(this).hasClass('disabled'))) {
            xhrItemEventHandler(this, e);    
        }
    });
    
    // Handle click event for a 'select all' checkbox in a table
    $('table th .selectAll').on('click.selectAllCheckbox', function(e){
        var table = $(e.target).closest('table');
        $('tbody td input:checkbox', table).prop('checked', this.checked);
        changeTableActionButtonStatus(table);
    });
    
    // Handle click event for any 'record selector' checkbox in a table
    $('table td input:checkbox.record-select').change(function () {
        changeTableActionButtonStatus();
    });
    
    // Handle disabled hyperlinks
    $("a.disabled").on('click.disabledHyperlinkEvent', function (e) {
        e.preventDefault();
    });

    // Handle dialog events
    $('.modal').on('tql.modal.displayed', function (e) {
        _eventDialogDisplayed($(this), e);
    });
}

/**
 * Event handler for AJAX enabled links
 */
function xhrItemEventHandler(context, e) {
    // Prevent link navigation
    e.preventDefault();
    
    // Check that link is not disabled
    if ($(context).hasClass('disabled') || $(context).parent('li').hasClass('disabled')) {
        e.preventDefault();
        return;
    }

    // Check if a preload target has been specified
    var preloadTarget = $(context).attr("data-ajax-preload-target");
    switch (preloadTarget) {
        case 'modal':
            // Open dialog immediately and show loading spinner
            response = _generateAjaxGenericResponse("#modal-dialog-container .modal-content", "<div class='modal-spinner'></div>", displayDialog);
            processAjaxResponse(response);
            break;
    }
    
    // Check if link is for an action that works on multiple selected items
    var response;
    if ($(context).parent('li').hasClass('multi-select')) {
        // Multiple items selected - use a POST request
        var selectedItems = _getSelectedListItems();
        ajaxCall($(context).attr("href"), "POST", {id: selectedItems}, "json");
    } else {
        ajaxCall($(context).attr("href"), "GET", {}, "json");
    }
}

/**
 * Wrapper to handle AJAX calls to backend
 */
function ajaxCall(url, type, data, datatype) {
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
        return processAjaxResponse(response);
    }
    
    // Ensure response is an object
    if (typeof(data) !== 'object') {
        try {
            response = $.parseJSON(data);
        } catch (ex) {
            // Generate error message content for 
            response = _generateAjaxGenericResponse("#modal-dialog-container .modal-content", "Unable to parse AJAX response: " + ex.message, displayDialog);
            return processAjaxResponse(response);
        }
    }

    // Process response object
    return processAjaxResponse(data);
}

/**
 * Callback: Error handler for AJAX call
 */
function _callbackAjaxError(xhr, textStatus, errorDetails) {
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
            return processAjaxResponse(response);
    }
}

/**
 * Utility: Generate response object with a single content item and callback
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
 * Callback function to process responses from a successful AJAX call to be backend.
 * Expects response to be a serialised Tranquility\View\AjaxResponse object
 */
function processAjaxResponse(response) {
    // Update HTML areas with new content
    $.each(response.content, function(i, item) {
        // Replace HTML in specified element
        if (item.element != null) {
            $(item.element).html(item.content);
        }
        
        // If callback was included, execute it now
        if (item.callback != null) {
            // If callback is a function, call it directly
            if ($.isFunction(item.callback)) {
                item.callback.apply(this, item.callbackArgs);
            } else {
                // Attempt to execute function from its name
                executeFunctionByName(item.callback, window, item.callbackArgs);
            }
        }
    });
    
    // Display inline error messages
    $.each(response.messages, function(i, message) {
        $(message.fieldId).after('<span class="help-inline" style="display: none;">' + message.text + '</span>'); 
        if (message.fieldId != null) {
            console.log('[' + message.level + '] ' + message.text + '(Field ID: ' + message.fieldId + ')');
        }
    });
    $("span.help-inline").slideDown();
}

/**
 * Enable toolbar links that interact with multiple selected items only if at least one item is selected
 */
function changeTableActionButtonStatus(table) {
    if ($('tbody td input.record-select', table).is(':checked')) {
        // Enable action buttons
        $('thead .table-action', table).removeClass('disabled');
        $('thead tr.actions div', table).slideDown();
    } else {
        // Disable action buttons
        $('thead .table-action', table).addClass('disabled');
        $('thead tr.actions div', table).slideUp();

        // Uncheck the 'select all' checkbox
        $('tbody th .selectAll', table).prop('checked', false);
    }
}

/**
 * Executes a function in the specified context
 * 
 * @link http://stackoverflow.com/questions/359788/how-to-execute-a-javascript-function-when-i-have-its-name-as-a-string
 */
function executeFunctionByName(functionName, context) {
    //var args = [].slice.call(arguments).splice(2);
    
    var args = arguments[2];
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for(var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    return context[func].apply(context, args);
}

/** 
 * Force display of the modal dialog
 */
function displayDialog() {
    var modalContent = arguments[0];
    var size = arguments[1];
    
    // If modal content has been supplied, inject it now
    if ((typeof modalContent !== 'undefined') && (modalContent !== null) && (modalContent.length > 0))  {
		$("#modal-dialog-container").html(modalContent);
	}
    
    // Display dialog and attach default submit event handler
    $("#modal-dialog-container form.ajax-submit").off("submit.dialogSubmit")
    $("#modal-dialog-container form.ajax-submit").on("submit.dialogSubmit", function (e) {
        defaultDialogSubmitEventHandler(this, e);
    });
    
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

    // Trigger event
    $('.modal').trigger('tql.modal.displayed');
}

/**
 * Force close the modal dialog
 */
function closeDialog() {
    $("#modal-dialog-container").modal('hide');
    return false;
}

/**
 * Event handler: Dialog load complete
 */
function _eventDialogDisplayed(context, e) {
    console.log ("Event: Dialog displayed");
    // Set autofocus for elements in dialog
    context.find('[autofocus]').focus();
    
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
 * Event handler for default dialog form submission action
 */
function defaultDialogSubmitEventHandler(context, e) {
    // Prevent default form behaviour
    e.preventDefault();
    
    // Serialise form input and submit via AJAX
    var form = $(context).closest("form");
    var formValues = form.serialize();
    var formAction = form.attr("action");
    var formMethod = form.attr("method");
    return ajaxCall(formAction, formMethod, formValues, "json");
}

/**
 * Helper function to hide an element with the specified ID
 */
function hideElement(target) {
    $("#" + target).collapse('hide');
}

/**
 * Helper function to show an element with the specified ID
 */
function showElement(target) {
    $("#" + target).collapse('show');
}

/**
 * Display process messages
 * 
 * @var messages Array of message strings
 * @var target   [Optional] Element ID to display messages inside of
 */
function displayMessages(messages, target) {
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
}

/**
 * Attach datepicker control to input fields
 */
function attachDatePicker() {
    if ($(".date-input").length > 0) {
        $(".date-input").datepicker();
    }
}

/**
 * Retrieve an array of all selected checkboxes with the specified name
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