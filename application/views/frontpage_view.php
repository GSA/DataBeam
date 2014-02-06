<?php include 'header_meta_inc_view.php';?>

<?php include 'header_inc_view.php';?>

<h1>Welcome to DataBeam</h1>
		
		<h2>DataBeam allows you to automatically turn any CSV or database into an API</h2>

		<h4 style="margin-top : 4em">Features</h4>
	    <ul>
			<li>Each new CSV or database connection creates a new REST API endpoint with output as json, jsonp, xml, csv, html, etc</li>
	        <li>Drag and drop CSV upload for CSV-to-API generation</li>				
			<li>Basic SQL "SELECT" statements can be used for each endpoint. Currently only read-only queries are included</li>
			<li>You can create custom SQL queries as new endpoints for more complex statements like JOINs across multiple tables.</li>			
			<li>You get auto-generated interactive documentation as a <a href="//swagger.wordnik.com/">Swagger compliant API</a></li>
			<li>You can auto-generate client libraries (with the Swagger schema and <a href="//github.com/wordnik/swagger-codegen">Swagger Codegen</a>)</li>
			<li>You get API access logs</li>
			<li>You get API key management (soon)</li>
			<li>oAuth for user management, currently only Github is used</li>			
	    </ul>

		<h4 style="margin-top : 2em">Requirements</h4>
		
		<p>Each instance is meant to be multiuser with Github for user authentication and you're free to use this instance by <a href="<?php echo base_url();?>login">logging in</a>, but it's also very easy to install your own copy with these basic requirements:</p>
        <ul>
            <li>PHP 5.3</li>
            <li>MySQL (for storing user data and connection settings)</li>
            <li>SQLite (for storing CSV data)</li>
            <li>Any <a href="//php.net/manual/en/pdo.drivers.php">PDO capable database</a> (for external database connections)</li>
        </ul>	
	
		
		<h4 style="margin-top : 2em">This project builds off of the following codebases:</h4>
        <ul>
            <li><a href="//github.com/project-open-data/db-to-api">DB to API</a></li>
            <li><a href="//github.com/philsturgeon/codeigniter-restserver">CI REST Server</a></li>
            <li><a href="//github.com/philsturgeon/codeigniter-oauth2">CI oAuth2</a></li>
            <li><a href="//github.com/blueimp/jQuery-File-Upload/">jQuery File Upload</a></li>
            <li><a href="//github.com/wordnik/swagger-ui">Swagger UI</a></li>
            <li><a href="//twitter.github.com/bootstrap/">Bootstrap</a></li>
        </ul>		
		
		<h4 style="margin-top : 2em">To Do</h4>
		<p>
            I'm starting to migrate issues to the <a href="//github.com/GSA/DataBeam/issues">Github Repo Issue
                tracker</a>
        </p>
    <ul>
        <li>Provide developer documentation for installation and contributions. Provide better user documentation and
            feature listing
        </li>
        <li>Implement better caching - it's barely implemented at all right now</li>
		    <li>Implement a UI for custom SQL queries - the backend for this is already in place</li>			
		    <li>Implement pagination by default and provide some configurable options per database</li>	
		    <li>Implement more robust CSV handling and even queuing for batch uploads</li>			
			<li>Implement API management features like API key provisioning. Backend for this mostly in place already</li>		
	        <li><del>Implement a real user interface, eg actual do some web design</del> - could still use more work</li>
		    <li><del>Implement a form for adding new databases. Backend functionality already works for this </del></li>
	        <li><del>Auto-generate <a href="//swagger.wordnik.com/">Swagger</a> schema and show interactive documentation. Allow user to edit some of this per database</del></li>
	    </ul>		

		<h4 style="margin-top : 2em">Who &amp; Where</h4>
		<ul>
			<li><a href="//github.com/philipashlock/DataBeam">Source Code</a> being hacked on by <a href="//twitter.com/philipashlock">Philip Ashlock</a></li>
		</ul>
		
		<h4 style="margin-top : 2em">Further Reading</h4>
		<ul>
		<li> For more advanced direct data interaction you might also be interested in <a href="//reclinejs.com">Recline.js</a>. Since it's a front-end layer Recline.js could also be included as part of the DataBeam UI.</li>
		<li> To automatically create web APIs for geospatial data you might be interested in <a href="//geoserver.org/display/GEOS/Welcome">GeoServer</a> or <a href="//github.com/CartoDB/cartodb20">CartoDB</a>
		</ul>		


		<?php include 'footer_inc_view.php';?>