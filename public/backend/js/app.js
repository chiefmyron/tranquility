// Set up require.js
require.config({
    shim : {
        "bootstrap" : { "deps" :['jquery'] }
    },    
    paths: {
        "jquery": "https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min",
        "bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min",
    }
});

// Load modules
require(['jquery', 'bootstrap', 'modules/core'], function(jquery, bootstrap, core) {
    core.init();
    
    // TODO: Don't pollute global namespace!
    window["core"] = core;

    // Attach event handlers to the page
    $(document).ready(function() {
        core.attachCommonHandlers();
        core.renderControls();
    });
});