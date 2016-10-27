// Set up require.js
require.config({
    shim : {
        "bootstrap" : { "deps": ['jquery'] },
        "selectize" : { "deps": ['jquery'] },
        //"tagsinput": { "deps": ['bootstrap', 'bloodhound', 'typeahead'] },
        //"typeahead": { 
        //    "deps": ['jquery'], 
        //    "init": function ($) { return require.s.contexts._.registry['typeahead.js'].factory( $ ); } // @see https://github.com/twitter/typeahead.js/issues/1211
        //},
    },    
    paths: {
        "jquery": "https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min",
        "bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min",
        //"typeahead": "lib/typeahead.0.11.1.min",
        //"bloodhound": "lib/bloodhound.0.11.1.min",
        //"tagsinput": "lib/bootstrap-tagsinput.0.8.0.min",
        "selectize": "lib/selectize.0.12.14.min"
    }
});

// Load modules
require(['modules/core'], function(core) {
    core.init();
    
    // TODO: Don't pollute global namespace!
    window["core"] = core;

    // Attach event handlers to the page
    $(document).ready(function() {
        core.attachCommonHandlers();
        core.renderCustomControls();
    });
});