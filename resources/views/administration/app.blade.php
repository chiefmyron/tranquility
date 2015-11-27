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
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        
        <!-- Linked fonts and icons -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.2.3/css/simple-line-icons.css" rel="stylesheet" type="text/css" />

        <!-- Bootstrap framework -->
        <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        
        <!-- Custom styles -->
        <link href="/backend/css/layout.css" rel="stylesheet" type="text/css" />
        <link href="/backend/css/colours/blue.css" rel="stylesheet" type="text/css" />
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    <!-- END HEAD -->
    </head>
    
    <body>
        
        <!-- Start page header -->
        <nav class="page-header navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="page-branding">
                    <button type="button" class="navbar-toggle collapsed pull-left visible-xs-block visible-sm-block" data-toggle="collapse" data-target="#sidebar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Tranquility</a>
                </div>
                

                
                <div class="page-actions hidden-sm hidden-xs">
                    <ul>
                        <li class="dropdown">
                            <a class="dropdown-toggle btn btn-primary" role="button" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" href="#">
                                <i class="fa fa-plus"></i> New <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Article</a></li>
                                <li><a href="#">Person</a></li>
                                <li><a href="#">Invoice</a></li>
                                <li><a href="#">Product</a></li>    
                            </ul>
                        </li>
                    </ul>
                </div>
                    
                <div class="page-top">
                    <!-- Header bar search form -->
                    <form class="search-form" action="" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search..." name="search" />
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div>
                    </form>
                    <!-- End of header bar search form -->
                    
                    <!-- Top level navigation icons -->
                    <div class="top-menu">

                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle dropdown-user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="/backend/images/user-avatar-default.png" class="img-circle" />
                                <span class="hidden-sm hidden-xs">Andrew Patterson <span class="caret"></span></span>
                            </button> 
                            <ul class="dropdown-menu">
                                <li><a href="#">Settings</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="{{ action('Administration\AuthController@logout') }}">{{ trans('administration.login_logout') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- End page header -->
        
        <!-- Separator between header and content -->
        <div class="clearfix"></div>
        
        <!-- Page container -->
        <div class="page-container">
            
            <!-- Start of sidebar menu -->
            <div class="page-sidebar-wrapper">
                <div id="sidebar" class="page-sidebar navbar-collapse collapse" aria-expaned="false">
                    <ul class="page-sidebar-menu">
                        <li class="nav-item start active">
                            <a href="#" class="nav-link">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li class="heading">
                            <h3>Website</h3>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="icon-settings"></i>
                                <span class="title">Pages</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="icon-settings"></i>
                                <span class="title">Pages</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>  
            <!-- End of sidebar menu -->
            
            <!-- Beginning of main page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="main-content">
                        <div class="row">
                            <!-- Toolbar -->
                            <div class="col-md-3 col-md-push-9">
                                <div id="toolbar-container">
                                    @yield('toolbar')  
                                </div>
                            </div>
                            <!-- End of toolbar -->
                            
                            <!-- Main content panel -->
                            <div class="col-md-9 col-md-pull-3">
                                
                                <!-- Page heading and subheading -->
                                @yield('heading')
                                @include('administration.errors.list')
                                
                                <!-- Breadcrumbs -->
                                @yield('breadcrumbs')
                                <hr />
                                
                                <div id="main-content-container">
                                    @yield('content')
                                </div>
                            </div>
                            <!-- End of main content panel -->
                        </div>
                        
                        <!-- End of row for main content and toolbar -->
                    </div>
                    
                </div>
            </div> 
        </div>
        
        <!-- Add JavaScript at the bottom to prevent page load blocking -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script src="/backend/js/core.js"></script>
        
    </body>
</html>   