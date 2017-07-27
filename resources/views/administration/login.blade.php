<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <title>Tranquility</title>
	    <meta charset="utf-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
	    <meta http-equiv="Pragma" content="no-cache" />
	    <meta http-equiv="Expires" content="-1" />
	    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	
	    <!-- Linked fonts and icons -->
        @if(App::environment('local'))
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="/backend/css/local/bootstrap.3.3.5.min.css" rel="stylesheet" type="text/css" />
        <link href="/backend/css/local/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/backend/css/local/simple-line-icons.css" rel="stylesheet" type="text/css" />
        @else
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.2.3/css/simple-line-icons.css" rel="stylesheet" type="text/css" />
        <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        @endif
        
        <!-- Custom styles -->
        <link href="/backend/css/layout.css" rel="stylesheet" type="text/css" />
        <link href="/backend/css/login.css" rel="stylesheet" type="text/css" />
        <link href="/backend/css/colours/blue.css" rel="stylesheet" type="text/css" />
	
	    <!-- IE8 support -->
	    <!--[if lt IE9]>
	        <script src="https://oss.maxcdn.com/html5shiv/3.7.2./html5shiv.min.js"></script>
	        <script src="https://oss/maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
	</head>
	
	<body>
	    <div id="branding-container"></div>
	    <div class="login-panel">
	        @yield('content')
	    </div>
	
	    <!-- Add JavaScript at the bottom to prevent page load blocking -->
        @if(App::environment('local'))
        <script src="/backend/js/local/jquery.1.11.1.min.js"></script>
        <script src="/backend/js/local/bootstrap.3.3.5.min.js"></script>
        @else
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        @endif
	</body>
</html>