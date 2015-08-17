<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <title>Tranquility</title>
	    <meta charset="utf-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
	    <meta http-equiv="Pragma" content="no-cache" />
	    <meta http-equiv="Expires" content="-1" />
	    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	
	    <!-- Google fonts -->
	    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet">
	
	    <!-- Bootstrap styling -->
	    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
	    <link href="/backend/css/styles.css" rel="stylesheet" />
	    <link href="/backend/css/login.css" rel="stylesheet" />
	
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
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</body>
</html>