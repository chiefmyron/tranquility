// Setup resources for offline access
if (document.getElementsByName("use-local-resources").length > 0) {
    paths = {
        "jquery": "lib/jquery.1.12.1.min",
        "bootstrap": "lib/bootstrap.3.3.6.min",
        "selectize": "lib/selectize.0.12.14.min"
    };
} else {
    paths = {
        "jquery": "https://ajax.googleapis.com/ajax/libs/jquery/1.12.1/jquery.min",
        "bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min",
        "selectize": "lib/selectize.0.12.14.min"
    };
}

// Set up require.js
require.config({
    shim : {
        "bootstrap" : { "deps": ['jquery'] },
        "selectize" : { "deps": ['jquery'] },
    },    
    paths: paths
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