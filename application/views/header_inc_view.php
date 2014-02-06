<body>

<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="<?php echo base_url();?>">DataBeam</a>
            <div class="nav-collapse collapse">
                <ul class="nav">

                    <?php $page_title = (!empty($page['title'])) ? $page['title'] : null?>

                    <li<?php if ($page_title == 'Download') echo ' class="active"' ?>><a
                            href="https://github.com/GSA/DataBeam/archive/master.zip">Download</a></li>
                    <li<?php if ($page_title == 'Developers') echo ' class="active"' ?>><a
                            href="https://github.com/GSA/DataBeam/">Developers</a></li>
                    <li<?php if($page_title == 'About') echo ' class="active"'?>><a href="<?php echo base_url();?>">About</a></li>
                </ul>

                <div class="pull-right">

                    <?php

                    if ($this->session->userdata('username')) {
                        ?>



                        <a class="btn btn-small btn-inverse<?php if($page_title == 'Your APIs') echo ' active'?>" href="<?php echo base_url();?>dashboard"><i class="icon-list-alt icon-white"></i> Your APIs</a>

                        <div class="btn-group">
                            <a class="btn btn-small btn-inverse<?php if($page_title == 'Upload a CSV') echo ' active'?>" href="<?php echo base_url();?>upload"><i class="icon-file icon-white"></i> Upload CSV</a>
                            <a class="btn btn-small btn-inverse<?php if($page_title == 'Connect a Database') echo ' active'?>" href="<?php echo base_url();?>new"><i class="icon-hdd icon-white"></i> Connect Database</a>
                        </div>

                        <div class="btn-group">
                            <a class="btn btn-small btn-inverse" href="<?php echo base_url();?>dashboard"><i class="icon-user icon-white"></i> <?php echo $this->session->userdata('name_full'); ?></a>
                            <a class="btn btn-small btn-inverse dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url();?>dashboard"><i class="icon-pencil"></i> Edit Profile</a></li>
                                <li><a href="<?php echo base_url();?>logout"><i class="icon-remove"></i> Logout</a></li>
                            </ul>
                        </div>


                    <?php }
                    else { ?>

                        <a class="btn-auth btn-github" href="<?php echo base_url();?>login">Sign in with <b>GitHub</b></a>

                    <?php } ?>

                </div>

            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>

<div class="container">
    <div class="alert">
        This instance of DataBeam is currently being provided as a demo for testing purposes only. Until otherwise indicated, APIs created using this tool should only be expected to exist temporarily.
    </div>