
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
  attachGlobalSearchHandlers();
});

// Every time a modal is shown, if it has an autofocus element, focus on it.
$('.modal').on('shown.bs.modal', function() {
  $(this).find('[autofocus]').focus();
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
    
    // Handle click event for enabled toolbar links
    $("#toolbar-container li.ajax a").on('click.toolbarEvent', function (e) {
        xhrItemEventHandler(this, e);
    });
    
    // Handle click event for links throughout the UI
    $("a.ajax").on('click.ajaxLinkEvent', function(e) {
        xhrItemEventHandler(this, e);    
    });
    
    // Handle click event for a 'select all' checkbox in a table
    $('table th .selectAll').on('click.selectAllCheckbox', function(e){
        var table = $(e.target).closest('table');
        $('td input:checkbox', table).prop('checked', this.checked);
        changeToolbarLinkStatus();
    });
    
    // Handle click event for any 'record selector' checkbox in a table
    $('table td input:checkbox.record-select').change(function () {
        changeToolbarLinkStatus();
    });
}

/**
 * Attach event handlers to search controls in administration page header
 */
function attachGlobalSearchHandlers() {
    // Handle click event for global search button in page header
    $('.page-header.navbar .search-form button').on('click.globalSearch', function(e) {
        $('.page-header.navbar form.search-form').addClass("open");
        $('.page-header.navbar .search-form input.form-control').focus();
    });
    $('.page-header.navbar .search-form input.form-control').on('focusout.globalSearch', function(e) {
        $('.page-header.navbar form.search-form').removeClass("open");
    });
    $('.page-header.navbar').on('mousedown', '.search-form.open button.submit', function(e) {
        e.preventDefault();
        e.stopPropagation(); 
        $(this).closest(".search-form").submit();
    });
}

/**
 * Event handler for AJAX enabled links
 */
function xhrItemEventHandler(context, e) {
    // Prevent link navigation
    e.preventDefault();
    
    // Check if link is for an action that works on multiple selected items
    var response;
    if ($(context).parent('li').hasClass('multi-select')) {
        // Multiple items selected - use a POST request
        var selectedItems = _getSelectedListItems();
        response = ajaxCall($(context).attr("href"), "POST", {id: selectedItems}, false, null, "json");
    } else {
        response = ajaxCall($(context).attr("href"), "GET", {}, false, null, "json");
    }
    
    // Process response based on HTTP status code
    processAjaxResponse(response);   
}

/**
 * Wrapper to handle AJAX calls to backend
 */
function ajaxCall( url, type, data, async, callback, datatype ) {
    // Ensure call type is in uppercase
    type = type.toUpperCase();
    datatype = datatype.toLowerCase();
    
    // Perform AJAX call
    var result = $.ajax({
        url: url,
        type: type,
        data: data,
        async: async,
        dataType: datatype,
        success: callback,
        error: _ajaxErrorHandler
    }); 
    
    // Determine what to do based on HTTP response code
    switch (result.status) {
        // Status OK
        case 200:
            // Parse responseText for a JSON object
            var response = {};
            try {
                response = jQuery.parseJSON(result.responseText);
            } catch (ex) {
                // Setup response as an exception
                response = {result: "exception", content: result.content};
            }
            return response;
        // Unauthorised request
        case 401:
            return ajaxCall("/administration/auth", "GET", {}, false, null, "json");
        // Server error
        case 500:
        default:
            // Display error in modal dialog
            $("#modal-dialog-container .modal-content").html(result.content);
            $("#modal-dialog-container").modal('show');
            return;
    }
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
            $("#" + item.element).html(item.content);
        }
        
        // If callback was included, execute it now
        if (item.callback != null) {
            executeFunctionByName(item.callback, window, item.callbackArgs);
        }
    });
    
    // Display inline error messages
    $.each(response.messages, function(i, message) {
        $("#" + message.fieldId).after('<span class="help-inline" style="display: none;">' + message.text + '</span>'); 
        if (message.fieldId != null) {
            console.log('[' + message.level + '] ' + message.text + '(Field ID: ' + message.fieldId + ')');
        }
    });
    $("span.help-inline").slideDown();
}

/**
 * Handles error scenarios from an AJAX call
 */
function _ajaxErrorHandler(xhr, ajaxOptions, errorDetails) {
    // If the error is a HTTP 403 error, display a timeout dialog
    if (xhr.status == 403) {
        // Display timeout dialog
        ajaxCall('/backoffice/auth/loginAjax', "get", {}, false, displayDialog, "json");
    } else {
        // Display generic error dialog
        xhr.content = xhr.responseText;
        displayDialog(xhr)
    }
}

/**
 * Enable toolbar links that interact with multiple selected items only if at least one item is selected
 */
function changeToolbarLinkStatus() {
    if ($('table td input.record-select').is(':checked')) {
        $("#toolbar-container li.multi-select").removeClass("disabled");
    } else {
        $("#toolbar-container li.multi-select").addClass("disabled");
        $('table th .selectAll').prop('checked', false);
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
}

/**
 * Force close the modal dialog
 */
function closeDialog() {
    $("#modal-dialog-container").modal('hide');
    return false;
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
    var response = ajaxCall(formAction, formMethod, formValues, false, null, "json");
    processAjaxResponse(response); 
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