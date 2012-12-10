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
	<link rel="stylesheet" href="/assets/css/auth-buttons.css">
	
</head>
<body>

<div id="container">
	<h1>Welcome to RESTdb!</h1>

	<div id="body">
		<p><a class="btn-auth btn-github large" href="/login">Sign in with <b>GitHub</b></a></p>        
		
		<p>RESTdb allows you to automatically turn any CSV or database into an API</p>

		<h4 style="margin-top : 4em">Details</h4>
	    <ul>
			<li><a href="https://github.com/philipashlock/RESTdb">Source Code</a> being hacked on by <a href="http://twitter.com/philipashlock">Philip Ashlock</a></li>			
	        <li>File Upload widget with multiple file selection, drag&amp;drop support, progress bars and preview images for jQuery.</li>
	        <li>Front-end can be used with any server-side platform (PHP, Python, Ruby on Rails, Java, Node.js, Go etc.) that supports standard HTML form file uploads. </li>		
			<li>Current backend utilizes SQLite for each csv uploaded, DB-to-API for arbitrary queries using GET, and CI REST Server for API logging, 
				key management, format transformations and other common API features.
			</li>
	    </ul>


		
		<h4>This project builds off of the following codebases:</h4>
        <ul class="nav">
            <li><a href="https://github.com/blueimp/jQuery-File-Upload/">jQuery File Upload</a></li>
            <li><a href="https://github.com/project-open-data/db-to-api">DB to API</a></li>
            <li><a href="https://github.com/philsturgeon/codeigniter-restserver">CI REST Server</a></li>
        </ul>		
		
		<h4>To Do</h4>
	    <ul>
	        <li>Implement a real user interface, eg actual do some web design</li>
			<li>Provide developer documentation for installation and contributions. Provide better user documentation and feature listing</li>	
		    <li>Implement a form for adding new databases. Backend functionality already works for this </li>
		    <li>Implement pagination by default and provide some configurable options per database</li>	
			<li>Implement API management features like API key provisioning. Backend for this mostly in place already</li>		
	        <li>Auto-generate <a href="http://swagger.wordnik.com/">Swagger</a> schema and show interactive documentation. Allow user to edit some of this per database</li>
	    </ul>		
		
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>