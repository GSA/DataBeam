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
				
		<form action="/add" method="post">

			<label for="name_full">Dataset Name</label>
			<input type="text" name="name_full" id="name_full">
			
			<br>
			
			<label for="description">Dataset Description</label>
			<input type="text" name="description" id="description">
			
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

			<label for="db_username">Database Username</label>
			<input type="text" name="db_username" id="db_username">

			<br>

			<label for="db_password">Database Password</label>
			<input type="password" name="db_password" id="db_password">
			
			<br>			
			
			<label for="table_blacklist">Table Blacklist (comma separated)</label>
			<input type="text" name="table_blacklist" id="table_blacklist">

			<br>

			<label for="column_blacklist">Column Blacklist (comma separated)</label>
			<input type="text" name="column_blacklist" id="column_blacklist">

			<br>

			<input type="submit" value="Submit">
			
		</form>
			
			
			
		
		
	</div>

</div>

</body>
</html>