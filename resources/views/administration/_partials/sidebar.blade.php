<?php if (!isset($active)) { $active = ''; } ?>                
                
                <div id="sidebar" class="page-sidebar navbar-collapse collapse" aria-expaned="false">
                    <ul class="page-sidebar-menu">
                        <li class="nav-item start{{ strtolower($active) == "dashboard" ? " active" : ""}}">
                            <a href="{{ action('Administration\HomeController@index') }}" class="nav-link">
                                <i class="icon-home"></i>
                                <span class="title">{{ trans('administration.common_dashboard') }}</span>
                            </a>
                        </li>
                        <li class="heading">
                            <h3>Website</h3>
                        </li>
                        <li class="nav-item{{ strtolower($active) == "pages" ? " active" : ""}}">
                            <a href="#" class="nav-link">
                                <i class="icon-doc"></i>
                                <span class="title">Pages</span>
                            </a>
                        </li>
                        <li class="nav-item{{ strtolower($active) == "images" ? " active" : ""}}">
                            <a href="#" class="nav-link">
                                <i class="icon-picture"></i>
                                <span class="title">Image gallery</span>
                            </a>
                        </li>
                        <li class="heading">
                            <h3>Customers</h3>
                        </li>
                        <li class="nav-item{{ strtolower($active) == "people" ? " active" : ""}}">
                            <a href="{{ action('Administration\PeopleController@index') }}" class="nav-link">
                                <i class="icon-user"></i>
                                <span class="title">{{ trans('administration.people_heading_people') }}</span>
                            </a>
                        </li>
                        <li class="nav-item{{ strtolower($active) == "accounts" ? " active" : ""}}">
                            <a href="#" class="nav-link">
                                <i class="icon-briefcase"></i>
                                <span class="title">Accounts</span>
                            </a>
                        </li>
                        <li class="nav-item{{ strtolower($active) == "invoices" ? " active" : ""}}">
                            <a href="#" class="nav-link">
                                <i class="icon-settings"></i>
                                <span class="title">Invoices</span>
                            </a>
                        </li>
                    </ul>
                </div>