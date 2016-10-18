// Set up require.js
require.config({
    shim : {
        "bootstrap" : { "deps" :['jquery'] },
        "tags": { "deps":['bootstrap'] }
    },    
    paths: {
        "jquery": "https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min",
        "bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min",
        "typeahead": "lib/bloodhound.0.11.1.min",
        "tags": "lib/bootstrap-tagsinput.0.8.0.min"
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