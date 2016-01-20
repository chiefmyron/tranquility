
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

// Every time a modal is shown, if it has an autofocus element, focus on it.
$('.modal').on('shown.bs.modal', function() {
  $(this).find('[autofocus]').focus();
});

// Attach event handlers to toolbar links
function attachCommonHandlers() {
    // Clear any existing handlers
    $("#toolbar-container li.ajax a").off('click.toolbarEvent');
    $("table th .selectAll").off('click.selectAllCheckbox');
    $(".page-header.navbar .search-form button").off('click.globalSearch');
    
    // Handle click event for enabled toolbar links
    $("#toolbar-container li.ajax a").on('click.toolbarEvent', function (e) {
        toolbarItemEventHandler(this, e)
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

function toolbarItemEventHandler(context, e) {
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

function processAjaxResponse(response) {
    // Check HTTP response code
    
    // TODO: Error handling / 403 handling
    
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
}

// Enable toolbar links that interact with multiple selected items only if at least one item is selected
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
    var args = [].slice.call(arguments).splice(2);
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
function displayDialog(modalContent) {
    // If modal content has been supplied, inject it now
    if ((typeof modalContent !== 'undefined') && (modalContent !== null) && (modalContent.length > 0))  {
		$("#modal-dialog-container").html(modalContent);
	}
    
    // Display dialog and attach default submit event handler
    /*$("#modal-dialog-container button.ajax-submit").on("click.dialogSubmit", function (e) {
        defaultDialogSubmitEventHandler(this, e);
    });*/
    $("#modal-dialog-container form.ajax-submit").on("submit.dialogSubmit", function (e) {
        defaultDialogSubmitEventHandler(this, e);
    });
    $("#modal-dialog-container").modal('show');
}

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
 * Force close the modal dialog
 */
function closeDialog() {
    $("#modal-dialog-container").modal('hide');
    return false;
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
 * Used to display dialogs (e.g. options dialogs, etc...)
 * 
 * serviceResponse: see library/Tranquility/ServiceResponse.php for structure
 * type: Type of dialog to display. Valid types are "wide", "dialog".   
 */
//function displayDialog(serviceResponse) {
    // If type is not defined, set default to "wide"
    /*if (type === null || type === undefined) {
        type = "wide";
    }*/
    /*
    // Display dialog contents
    if (serviceResponse.content == undefined) {
        dialogContent = "";
    } else if (serviceResponse.content.dialog == undefined) {
        dialogContent = serviceResponse.content;
    } else {
        dialogContent = serviceResponse.content.dialog;
    }
    if (dialogContent != "" && dialogContent != null) {
        //$('#modalDialog').removeClass('wide dialog').addClass('wide');
        $('#modalDialog .modal-content').html(dialogContent);
        $('#modalDialog').modal();
    }

    // If there are any messages in the service response, display them now
    if (serviceResponse.messages != undefined && serviceResponse.messages != null && serviceResponse.messages.length > 0) {
        displayMessages(serviceResponse.messageTarget, serviceResponse.messages);
    }*/
//}





function getSelectedCheckboxValues( element_name ) {
    var value_array = [];
    $('input:checkbox[name=' + element_name + ']:checked').each(function (i) {
        value_array[i] = $(this).val();
    });

    return value_array;
}

// Wrapper for JQuery $.ajax() call
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

function attachDatePicker() {
    if ($(".date-input").length > 0) {
        $(".date-input").datepicker();
    }
}

function attachTabHandler() {
    $('ul.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
}



// Retrieves form inputs and returns as an array
function _extractFormValues(formId) {
    var inputs = {};
    $("form#" + formId + " :input").each(function() {
        var inputType = $(this).attr('type');
        
        switch (inputType) {
            case 'checkbox':
                if ($(this).is(':checked')) {
                    inputs[this.name] = 1;
                } else {
                    inputs[this.name] = 0;
                }
                break;
            case 'radio':
                // Escape square brackets
                var elementName = this.name;
                elementName = elementName.replace("[", "\\[");
                elementName = elementName.replace("]", "\\]");
                inputs[this.name] = $('input:radio[name=' + elementName + ']:checked').val();
                break;
            default:
                inputs[this.name] = $(this).val();
                break;
        }
        
    });
    
    return inputs;
}

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