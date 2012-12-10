<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">

	<div id="body">
		
		
		<?php
		
		if ($this->session->userdata('username')) {	
		?>
		
		
		<a href="/upload">Upload a CSV</a> <br>
		<a href="/add">Add a Database</a> <br> <br>
		<a href="/logout">logout</a>		
		
		<?php		
		}
		?>

		<?php
		
		echo '<h1>' . $user['name_full'] . ' APIs</h1>';
				
		if (!empty($connections)) {
						
			foreach($connections as $connection) {
			
				$db_name = $connection['name_full'];
				$db_url = $connection['name_url'];
				$user_url 	= $connection['user_url'];
				
				$db_name = (empty($db_name)) ? $db_url : $db_name;				
				$db_url = ($connection['local']) ? 'local/' . $db_url : $db_url;			
			
				echo "<a href=\"/$user_url/$db_url\">$db_name</a> <br>";
			}
		}
		?>


	</div>

</div>

</body>
</html>