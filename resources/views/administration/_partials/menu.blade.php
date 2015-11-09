		<nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Tranquility</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <!-- Left aligned navbar items -->
                    <ul class="nav navbar-nav">
                        <li><a href="#">People</a></li>
                        <li><a href="#">Website</a></li>
                        <li><a href="#">Store</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                    
                    <!-- Right aligned navbar items -->
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a class="dropdown-toggle" role="button" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" href="#">
                                <img width="20" height="20" src="/backend/images/user-avatar-default.png" class="img-circle" />
                                <span class="hidden-sm"> Andrew Patterson </span><span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Settings</a></li>
                                <li><a href="{{ action('Administration\AuthController@logout') }}">{{ trans('administration.login_logout') }}</a></li>
                            </ul>
                        </li>
                    </ul>
                    
                    <form class="navbar-form navbar-right">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </nav>