<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header">
            <!-- This is for the sidebar toggle which is visible on mobile only -->
            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                <i class="ti-menu ti-close"></i>
            </a>
            <!-- ============================================================== -->
            <!-- Logo -->
            <!-- ============================================================== -->
            <a class="navbar-brand" href="<?=base_url();?>" style="padding-top: 10px;">
                <!-- Logo icon -->
                <b class="logo-icon">
                    <!-- Dark Logo icon 
                    <img src="<?=base_url()?>assets/images/icon.png" alt="homepage" class="dark-logo" style="width:100%;" />-->
                    <!-- Light Logo icon 
                    <img src="<?=base_url()?>assets/images/icon.png" alt="homepage" class="light-logo" style="width:100%;"  />-->
                </b>
                <!--End Logo icon -->
                <!-- Logo text -->
                <span class="logo-text">
                   <!-- Dark Logo icon -->
                    <img src="<?=base_url()?>assets/images/logo_text.png" alt="homepage" class="dark-logo" style="width:70%;" />
                    <!-- Light Logo icon -->
                    <img src="<?=base_url()?>assets/images/logo_text.png" alt="homepage" class="light-logo" style="width:70%;"  />
                </span>
            </a>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Toggle which is visible on mobile only -->
            <!-- ============================================================== -->
            <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ti-more"></i>
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse collapse" id="navbarSupportedContent">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav float-left mr-auto">
                <li class="nav-item d-none d-md-block">
                    <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar">
                        <i class="sl-icon-menu font-20"></i>
                    </a>
                </li>
				<li class="nav-item d-none d-md-block text-facebook font-20 font-bold" style="line-height:45px;"><?=(!empty($headData->pageTitle)) ? $headData->pageTitle : SITENAME?></li>
                <!-- ============================================================== -->
            </ul>
            <!-- ============================================================== -->
            <!-- Right side toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav float-right">

                <!--End Customizer Panel -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="<?=base_url()?>assets/images/users/user_default.png" alt="user" class="rounded-circle" width="31">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                        <span class="with-arrow">
                            <span class="bg-primary"></span>
                        </span>
                        <div class="d-flex no-block align-items-center p-15 bg-primary text-white m-b-10">
                            <div class="">
                                <img src="<?=base_url()?>assets/images/users/user_default.png" alt="user" class="img-circle" width="60">
                            </div>
                            <div class="m-l-10">
                                <h4 class="m-b-0"><?=$this->session->userdata('emp_name')?></h4>
                                <p class=" m-b-0"><?=$this->session->userdata('roleName')?></p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#change-psw"><i class="ti-key m-r-5 m-l-5"></i> Change Password</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url('logout')?>"><i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
                    </div>
                </li>
                <!-- User profile and search -->
            </ul>
        </div>
    </nav>
</header>