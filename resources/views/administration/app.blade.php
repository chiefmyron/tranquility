<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!-- Favicon -->
	
	
	<!-- Site title -->
	<title>Tranquility</title>
	
	<!-- Google fonts -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet">
	
	<!-- Bootstrap styling -->
	<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/backend/css/styles.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div id="wrapper">

        <!-- Navigation -->
        @include('administration._partials.menu')

        <div id="content-wrapper" class="container">
            <!-- Breadcrumbs -->
            @yield('breadcrumbs')
            
            <!-- Row for main content and toolbar -->
            <div class="row">
                
                <!-- Toolbar -->
                <div id="toolbar-container" class="col-md-3 col-md-push-9">
                    @yield('toolbar')  
                </div>
                <!-- End of toolbar -->
                
                <!-- Main content panel -->
                <div id="main-content-container" class="col-md-9 col-md-pull-3">
                    @yield('content')
                </div>
                <!-- End of main content panel -->
                
            </div>
            <!-- End of row for main content and toolbar -->
        </div>
        <!-- /#content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Add JavaScript at the bottom to prevent page load blocking -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/backend/js/core.js"></script>

</body>

</html>