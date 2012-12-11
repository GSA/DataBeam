<?php include 'header_meta_inc_view.php';?>

<?php include 'header_inc_view.php';?>



<h1>Welcome to RestDB</h1>
		
		<h2>RestDB allows you to automatically turn any CSV or database into an API</h2>

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




		<?php include 'footer_inc_view.php';?>