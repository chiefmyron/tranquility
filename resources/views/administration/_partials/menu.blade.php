            <div class="nav-container">
                <nav class="nav-main">
                    <div class="branding">
                        <button class="nav-toggle" type="button" data-toggle="collapse" data-target="#nav-toggleable-sm">
                            <span class="sr-only">Toggle nav</span>
                        </button>
                    
                        <a href="{{ action('Administration\HomeController@index') }}">Tranquility</a>
                    </div>
                    
                    <!-- Profile menu -->
                    <div class="profile-button">
                        <button type="button" class="btn dropdown-toggle dropdown-user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="/backend/images/user-avatar-default.png" class="img-circle" />
                            <span class="hidden-sm hidden-xs username">{{ $_currentUser->getDisplayName() }} <span class="caret"></span></span>
                        </button> 
                        <ul class="dropdown-menu">
                            <li><a href="{{ action('Administration\SettingsController@index') }}">{{ trans('administration.settings_heading_dashboard') }}</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ action('Administration\AuthController@logout') }}">{{ trans('administration.login_logout') }}</a></li>
                        </ul>
                    </div>

                    <div class="navigation collapse" id="nav-toggleable-sm">
                        <!-- Site wide search form -->
                        <form class="site-search">
                            <input class="form-control" type="text" placeholder="Search...">
                            <button type="submit" class="submit-btn">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </form>
                        
                        <!-- Navigation menu items -->
                        <div class="nav-items">
                            <ul class="nav nav-stacked">
                                <li class="heading">
                                    <h3>Website</h3>
                                </li>
                                <li class="nav-item pages{{ strtolower($active) == "pages" ? " active" : ""}}">
                                    <a href="#" class="nav-link">
                                        <i class="icon-doc"></i>
                                        <span class="title">Pages</span>
                                    </a>
                                </li>
                                <li class="nav-item images{{ strtolower($active) == "images" ? " active" : ""}}">
                                    <a href="#" class="nav-link">
                                        <i class="icon-picture"></i>
                                        <span class="title">Image gallery</span>
                                    </a>
                                </li>
                                <li class="heading">
                                    <h3>Customers</h3>
                                </li>
                                <li class="nav-item people{{ strtolower($active) == "people" ? " active" : ""}}">
                                    <a href="{{ action('Administration\PeopleController@index') }}" class="nav-link">
                                        <i class="icon-user"></i>
                                        <span class="title">{{ trans('administration.people_heading_people') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item accounts{{ strtolower($active) == "accounts" ? " active" : ""}}">
                                    <a href="#" class="nav-link">
                                        <i class="icon-briefcase"></i>
                                        <span class="title">Accounts</span>
                                    </a>
                                </li>
                                <li class="heading">
                                    <h3>Store</h3>
                                </li>
                                <li class="nav-item orders{{ strtolower($active) == "invoices" ? " active" : ""}}">
                                    <a href="#" class="nav-link">
                                        <i class="icon-settings"></i>
                                        <span class="title">Orders</span>
                                    </a>
                                </li>
                                <li class="nav-item invoices{{ strtolower($active) == "invoices" ? " active" : ""}}">
                                    <a href="#" class="nav-link">
                                        <i class="icon-settings"></i>
                                        <span class="title">Invoices</span>
                                    </a>
                                </li>
                                <li class="nav-item products{{ strtolower($active) == "invoices" ? " active" : ""}}">
                                    <a href="#" class="nav-link">
                                        <i class="icon-settings"></i>
                                        <span class="title">Products</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>