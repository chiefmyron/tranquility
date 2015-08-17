<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>Tranquility</title>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
				
	</head>
	
	<body>
		<div class="container">

			@yield('content')
		</div>
		
		@yield('footer')
		
		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script>
			$("#flash-overlay-modal").modal();	
		</script>
	</body>
</html>