
</head>

<body>

  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="/">RestDB</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li class="active"><a href="/">Download</a></li>
            <li><a href="/">Developers</a></li>
            <li><a href="/">About</a></li>
          </ul>

	    <div class="pull-right">

				<?php

				if ($this->session->userdata('username')) {	
				?>
				
				
				
				<a class="btn btn-small btn-inverse" href="/dashboard"><i class="icon-list-alt icon-white"></i> Your APIs</a>
				
				<div class="btn-group">
					<a class="btn btn-small btn-inverse" href="/upload"><i class="icon-file icon-white"></i> Upload CSV</a>
					<a class="btn btn-small btn-inverse" href="/new"><i class="icon-hdd icon-white"></i> Connect Database</a>					
				</div>
				
				<div class="btn-group">
				    <a class="btn btn-small btn-inverse" href="/dashboard"><i class="icon-user icon-white"></i> <?php echo $this->session->userdata('name_full'); ?></a>
				    <a class="btn btn-small btn-inverse dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
				    <ul class="dropdown-menu">
					    <li><a href="/dashboard"><i class="icon-pencil"></i> Edit Profile</a></li>
					    <li><a href="/logout"><i class="icon-remove"></i> Logout</a></li>
				    </ul>
				</div>
	    	

				<?php } 
				else { ?>

					<a class="btn-auth btn-github" href="/login">Sign in with <b>GitHub</b></a>

				<?php } ?>
		
		</div>		
		
        </div><!--/.nav-collapse -->
      </div>
    </div>
  </div>

  <div class="container">