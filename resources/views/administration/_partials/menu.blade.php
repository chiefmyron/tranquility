        <nav class="page-header navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="page-branding">
                    <button type="button" class="navbar-toggle collapsed pull-left visible-xs-block visible-sm-block" data-toggle="collapse" data-target="#sidebar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ action('Administration\HomeController@index') }}">Tranquility</a>
                </div>
                
                <div class="page-actions hidden-sm hidden-xs">
                    <ul>
                        <li class="dropdown">
                            <a class="dropdown-toggle btn btn-primary" role="button" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" href="#">
                                <i class="fa fa-plus"></i> New <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Article</a></li>
                                <li><a href="{{ action('Administration\PeopleController@create') }}">{{ trans('administration.people_heading_person') }}</a></li>
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
                            <input type="text" class="form-control" placeholder="{{ trans('administration.search_heading_search') }}..." name="search" />
                            <span class="input-group-btn">
                                <button class="btn btn-default submit" type="button"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div>
                    </form>
                    <!-- End of header bar search form -->
                    
                    <!-- Top level navigation icons -->
                    <div class="top-menu">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle dropdown-user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="/backend/images/user-avatar-default.png" class="img-circle" />
                                <span class="hidden-sm hidden-xs">{{ $_currentUser->getDisplayName() }} <span class="caret"></span></span>
                            </button> 
                            <ul class="dropdown-menu">
                                <li><a href="{{ action('Administration\SettingsController@index') }}">{{ trans('administration.settings_heading_dashboard') }}</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="{{ action('Administration\AuthController@logout') }}">{{ trans('administration.login_logout') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>