<?php include 'header_meta_inc_view.php';?>

<?php 

$page = array('title' => 'Connect a Database');

include 'header_inc_view.php';?>


<h1>Connect a Database</h1>

				
		<form action="/new" method="post">

			<label for="name_full">Dataset Name</label>
			<input type="text" name="name_full" id="name_full">
			
			<br>
			
			<label for="description">Dataset Description</label>
			<input type="text" name="description" id="description">			
			
			<br>

			<label for="type">Database Type</label>
			<select name="type" id="type">
				<option value="">Select a Database Type</option>

				<option value="mysql">MySQL</option>
				<option value="pgsql">PostgresSQL</option>
				<option value="mssql">Microsoft SQL</option>
				<option value="sqlite">SQLite</option>
				<option value="oracle">Oracle</option>
				<option value="ibm">IBM</option>
				<option value="firebird">Firebird</option>
				<option value="interbase">Interbase</option>
				<option value="4D">4D</option>
				<option value="informix">Informix</option>
			</select>			
			
			<br>			
			
			<label for="db_name">Database Name</label>
			<input type="text" name="db_name" id="db_name">

			<br>

			<label for="db_server">Database Server</label>
			<input type="text" name="db_server" id="db_server">

			<br>

			<label for="db_port">Database Port</label>
			<input type="text" name="db_port" id="db_port">

			<br>

			<label for="db_username">Database Username</label>
			<input type="text" name="db_username" id="db_username">

			<br>

			<label for="db_password">Database Password</label>
			<input type="password" name="db_password" id="db_password" autocomplete="off">
			
			<br>			
			
			<label for="table_blacklist">Table Blacklist (comma separated)</label>
			<input type="text" name="table_blacklist" id="table_blacklist">

			<br>

			<label for="column_blacklist">Column Blacklist (comma separated)</label>
			<input type="text" name="column_blacklist" id="column_blacklist">

			<br>

			<input type="submit" value="Submit">
			
		</form>
			
			
	
<?php include 'footer_inc_view.php';?>