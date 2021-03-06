<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Character set -->
        <meta charset="utf-8" />
        
        <!-- Site title and metadata -->
        <title>Tranquility</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        
        <!-- Linked fonts and icons -->
        @if(App::environment('local'))
        <meta name="use-local-resources" content="true" />
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="/backend/css/local/bootstrap.3.3.6.min.css" rel="stylesheet" type="text/css" />
        <link href="/backend/css/local/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/backend/css/local/simple-line-icons.css" rel="stylesheet" type="text/css" />
        @else
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.2.3/css/simple-line-icons.css" rel="stylesheet" type="text/css" />
        <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        @endif
        
        <!-- Custom styles -->
        <link href="/backend/css/layout.css" rel="stylesheet" type="text/css" />
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    <!-- END HEAD -->
    </head>
    
    <body>
        <div class="admin-container">
            <div class="fluid-container">
                <!-- Start navigation  -->
                @yield('menu')  
                <!-- End navigation --> 

                <!-- Start breadcrumbs -->
                @yield('breadcrumbs')
                <!-- End breadcrumbs -->
                
                <!-- Beginning of main page content -->
                <div class="content-container">
                    
                    
                    <!-- Page heading and subheading -->
                    <header id="page-header">
                        @yield('heading')

                        <div id="extra-headers">
                            @yield('actionButton')
                        </div>
                    </header>
                    
                    <!-- Process messages -->
                    @include('administration._partials.errors', ['messages' => Session::pull('messages')])
                    
                    <div id="main-content-container">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Container for modal dialog -->
        <div id="modal-dialog-container" class="modal fade" tabindex="-1" role="dialog" aria-labelledBy="modal-dialog-title">
            <div class="modal-dialog" role="document">
                <div id="modal-content" class="modal-content"></div>
            </div>
        </div>
        <!-- End of modal dialog -->
        
        <!-- Add JavaScript at the bottom to prevent page load blocking -->
        <script src="/backend/js/lib/require.js" data-main="/backend/js/app"></script>
    </body>
</html>   