<?php defined('BASEPATH') OR exit('No direct script access allowed');


abstract class Db_api extends REST_Controller
{
	
	protected $dbs = array();
	public $db = null;
	protected $dbh = null;
	protected $query = array();
	protected $custom_sql = array();	
	protected $instance;
	protected $ttl = 3600;
	protected $cache = array();
	protected $connections = array();

	public function __construct() {
		parent::__construct();
		
		//self::$instance = &$this;

		// Lets grab the config and get ready to party
		//$this->load->config('dbapi');

	}


	/**
	 * Register a new dataset
	 * @param string $name the dataset name
	 * @param array $args the dataset properties
	 */
	protected function register_db( $id = null, $args = array() ) {

		$defaults = array(
			'id' => $id, 
			'name' => null,
			'username' => 'root',
			'password' => 'root',
			'server' => 'localhost',
			'port' => 3306,
			'type' => 'mysql',
			'table_blacklist' => array(),
			'column_blacklist' => array(),
			'ttl' => $this->ttl,
		);

		$args = $this->shortcode_atts( $defaults, $args[$id] );

		$this->dbs[$id] = (object) $args;
		

		
	}
	
	/**
	 * Register a custom sql query
	 * @param string $name the query name
	 * @param array $args the query parameters
	 */
	protected function register_custom_sql( $name = null, $args = array() ) {

		$defaults = array(
			'parameters' => array(),
			'sql' => null,
		);

		$args = $this->shortcode_atts( $defaults, $args );

		$this->custom_sql[$name] = (object) $args;

	}	

	/**
	 * Retrieves a database and its properties
	 * @param string $db the DB slug (optional)
	 * @return array the database property array
	 */
	protected function get_db( $db = null ) {

		if ( $db == null && !is_null($this->db) ) {
			return $this->db;
		}

		if ( is_object( $db ) ) {
			$db = $db->name;
		}
				
		if ( !array_key_exists( $db, $this->dbs ) ) {
			$this->error( 'Invalid Database' );
		}

		return $this->dbs[$db];

	}

	/**
	 * Sets the current database
	 * @param string $db the db slug
	 * @return bool success/fail
	 */
	public function set_db( $db = null ) {

		$db = $this->get_db( $db );

		if ( !$db ) {
			return false;
		}
		
		$this->db = $db;

		return true;
		
	}



	/**
	 * Parses rewrite and actual query var and sanitizes
	 * @return array the query arg array
	 * @param $query (optional) a query other than the current query string
	 */
	public function parse_query( $query = null ) {

		$defaults = array(
			'db' => null,
			'table' => null,
			'order_by' => null,
			'direction' => 'ASC',
			'column' => null,
			'value' => null,
			'limit' => null,
			'page' => null,
			'format' => 'json',
			'callback' =>  null,
			'query' =>  null
		);


		if ( $query == null ) {
			$query = $_SERVER['QUERY_STRING'];
		}
		
		
		if (is_string($query)) {
			parse_str( $query, $parts );
		}		
		elseif (is_array($query)) {
			
			// if we have additional query segments in the query string, lets merge those together
			if(!empty($_SERVER['QUERY_STRING'])) {
				parse_str( $_SERVER['QUERY_STRING'], $additional_parts );
				
				// the URL path query segments take precedence over the query string (overwriting them just like overwriting defaults)
				$defaults = $this->shortcode_atts( $defaults, $additional_parts );
				$parts = $this->shortcode_atts( $defaults, $query ); 
				
			} else {
				$parts = $query;
			}
						
		} 	
		else {
			$parts = $query;
		}
		

		$parts = $this->shortcode_atts( $defaults, $parts );
		

		if ( $parts['db'] == null ) {
			$this->error( 'Must select a database' );
		}
		
		if ($parts['query'] == null) {
			
			if ( $parts['table'] == null) {
				$this->error( 'Must select a table' );
			}

			$db = $this->get_db( $parts['db'] );

			if ( in_array( $parts['table'], $db->table_blacklist ) ) {
				$this->error( 'Invalid table' );
			}

			if ( !in_array( $parts['direction'], array( 'ASC', 'DESC' ) ) ) {
				$parts['direction'] = null;
			}

			if ( !in_array( $parts['format'], array( 'html', 'xml', 'json' ) ) ) {
				$parts['format'] = null;
			}
			
		}

		return $parts;

	}

	/**
	 * Establish a database connection
	 * @param string $db the database slug
	 * @return object the PDO object
	 * @todo support port #s and test on each database
	 */
	protected function &connect( $db ) {



		if ( is_object( $db ) ) {
			$db = $db->id;
		}
		
		// check for existing connection
		if ( isset( $this->connections[$db] ) ) {
			return $this->connections[$db];
		}
			
		$db = $this->get_db( $db );

		try {
			if ($db->type == 'mysql') {
				$dbh = new PDO( "mysql:host={$db->server};dbname={$db->name}", $db->username, $db->password );
			}
			elseif ($db->type == 'pgsql') {
				$dbh = new PDO( "pgsql:host={$db->server};dbname={$db->name}", $db->username, $db->password );
			}
			elseif ($db->type == 'mssql') {
				$dbh = new PDO( "sqlsrv:Server={$db->server};Database={$db->name}", $db->username, $db->password );
			}
			elseif ($db->type == 'sqlite') {
				$dbh = new PDO( "sqlite:/{$db->name}" );
			}
			elseif ($db->type == 'oracle') {
				$dbh = new PDO( "oci:dbname={$db->name}" );
			}
			elseif ($db->type == 'ibm') {
				// May require a specified port number as per http://php.net/manual/en/ref.pdo-ibm.connection.php.
				$dbh = new PDO( "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$db->name};HOSTNAME={$db->server};PROTOCOL=TCPIP;", $db->username, $db->password );
			}
			elseif ( ($db->type == 'firebird') || ($db->type == 'interbase') ){
				$dbh = new PDO( "firebird:dbname={$db->name};host={$db->server}" );
			}
			elseif ($db->type == '4D') {
				$dbh = new PDO( "4D:host={$db->server}", $db->username, $db->password );
			}
			elseif ($db->type == 'informix') {
				$dbh = new PDO( "informix:host={$db->server}; database={$db->name}; server={$db->server}", $db->username, $db->password );
			}
			else {
				$this->error('Unknown database type.');
			}
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}

		// cache
		$this->connections[$db->name] = &$dbh;
		
		return $dbh;

	}

	/**
	 * Verify a table exists, used to sanitize queries
	 * @param string $query_table the table being queried
	 * @param string $db the database to check
	 * @param return bool true if table exists, otherwise false
	 */
	protected function verify_table( $query_table, $db = null ) {
		
		$tables = $this->cache_get( $this->get_db( $db )->name . '_tables' );

		if ( !$tables  ) {
		
			$tables = $this->allowed_tables($db);
			
			//var_dump($tables);
				
		}
		
		return in_array( $query_table, $tables );
		
	}
	
	
	/**
	 * Return list of tables we're allowed to access
	 */	
	public function allowed_tables($db = null) {

		$dbh = &$this->connect( $db );
		try { 
				
							
			if ($this->get_db( $db )->type == 'sqlite') {
				$stmt = $dbh->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");					
			} else {
				$stmt = $dbh->query( 'SHOW TABLES' );
			}
			
			
		} catch( PDOException $e ) {
			echo $e->getMessage();
		}
		
		$tables = array();
		while ( $table = $stmt->fetch() ) {
			$tables[] = $table[0];
		}


		// remove any blacklisted tables
		if(!empty($this->dbs[$db]->table_blacklist)) {
			
			$tables = array_diff($tables, $this->dbs[$db]->table_blacklist);
					
		}
						
		return $tables;
		
	}
	
	
	
	

	/**
	 * Returns an array of all columns in a table
	 * @param string $table the table to check
	 * @param string $db the database to check
	 * @return array an array of the column names
	 */
	public function get_columns( $table, $db = null ) {

		if ( !$this->verify_table( $table, $db ) ) {
			return false;
		}
			
		$key = $this->get_db( $db )->name . '.' . $table . '_columns';
		
		if ( $cache = $this->cache_get( $key ) ) {
			return $cache;
		}
			
		$dbh = &$this->connect( $db );
		
		try {
			
			if ($this->get_db( $db )->type == 'sqlite') {
				$q = $dbh->prepare( "PRAGMA table_info([$table])" );								
				$q->execute();
				$columns = $q->fetchAll(PDO::FETCH_COLUMN, 1);				

			} else {
				$q = $dbh->prepare( "DESCRIBE $table" );				
				$q->execute();
				$columns = $q->fetchAll(PDO::FETCH_COLUMN);				
			}

			
		} catch( PDOException $e ) {
			echo $e->getMessage();
		}

		$cache_ttl = ( isset( $this->db->ttl) ) ? $this->db->ttl : $this->ttl;
		$this->cache_set( $key, $columns, $cache_ttl );
		return $columns;
	}

	/**
	 * Verify a column exists
	 * @param string $column the column to check
	 * @param string $table the table to check
	 * @param string $db (optional) the db to check
	 * @retrun bool true if exists, otherwise false
	 */
	protected function verify_column( $column, $table, $db = null ) {

		$columns = $this->get_columns( $table, $db );
		return in_array( $column, $columns );

	}

	/**
	 * Returns the first column in a table
	 * @param string $table the table 
	 * @param string $db the datbase slug
	 * @return string the column name
	 */
	function get_first_column( $table, $db = null ) {

		return reset( $this->get_columns( $table, $db ) );

	}

	/**
	 * Build and execute the main database query
	 * @param array $query the database query ASSUMES SANITIZED
	 * @return array an array of results
	 */
	public function query( $query, $db = null ) {

		$key = md5( serialize( $query ) . $this->get_db( $db )->name );
		
		if ( $cache = $this->cache_get( $key ) ) {
			return $cache;
		}

		try {

			$dbh = &$this->connect( $db );

			// sanitize table name
			if ( !$this->verify_table( $query['table'] ) && empty($query['query'])) {
				$this->error( 'Invalid Table' );
			}
			

			// santize column name
			if ( $query['column'] ) {
				if ( !$this->verify_column( $query['column'], $query['table'] ) ) {
					$query['column'] = null;
				}
		  	}

			if (!empty($query['query']) && !empty($this->custom_sql[$query['query']])) {

				$query_name = $query['query'];
				parse_str( $_SERVER['QUERY_STRING'], $custom_vars );
			
				foreach ($this->custom_sql[$query_name]->parameters as $parameter) {
					$search = '{[' . $parameter . ']}';
					$replace = $custom_vars[$parameter];
					$this->custom_sql[$query_name]->sql = str_replace($search, $replace, $this->custom_sql[$query_name]->sql);
				}

				$sql = $this->custom_sql[$query_name]->sql;

				$sth = $dbh->prepare( $sql );
				$sth->execute();

			}		
			else {

			  $sql = 'SELECT * FROM ' . $query['table'];

				if ( $query['value'] && $query['column'] == null ) {
					$query['column'] = $this->get_first_column( $query['table'] );
				}

				if ( $query['value'] && $query['column'] ) {
					$sql .= " WHERE `{$query['table']}`.`{$query['column']}` = :value";
				}

				if ( $query['order_by'] && $query['direction'] ) {

					if ( !$this->verify_column( $query['order_by'], $query['table'] ) ) {
						return false;
					}

					$sql .= " ORDER BY `{$query['table']}`.`{$query['order_by']}` {$query['direction']}";

				}

				if ( $query['limit'] ) {
					$sql .= " LIMIT " . (int) $query['limit'];
				}
				
				if ( $query['page'] && $query['limit'] ) {
					$offset = $query['limit'] * ($query['page'] - 1);
					$sql .= " OFFSET " . (int) $offset;
				}				
				
				
				$sth = $dbh->prepare( $sql );
				
				if ($query['value']) {
					$sth->bindParam( ':value', $query['value'] );
				}
				
				$sth->execute();
			}	

			$results = $sth->fetchAll( PDO::FETCH_OBJ );

			$results = $this->sanitize_results( $results );

		} catch( PDOException $e ) {
			echo $e->getMessage();
		}
		
		$cache_ttl = ( isset( $this->db->ttl) ) ? $this->db->ttl : $this->ttl;
		 
		$this->cache_set( $key, $results, $cache_ttl );
		
		return $results;

	}

	/**
	 * Remove any blacklisted columns from the data set.
	 */
	protected function sanitize_results( $results, $db = null ) {

		$db = $this->get_db( $db );

		if ( empty( $db->column_blacklist ) ) {
			return $results;
		}

		foreach ( $results as $ID => $result ) {

			foreach ( $db->column_blacklist as $column ) {
				unset( $results[ $ID ][ $column] );
			}

		}

		return $results;

	}

	/**
	 * Halt the program with an "Internal server error" and the specified message.
	 */
	protected function error( $error, $code = '500' ) {
		$this->http_response_code( $code );
		die( $error );
		return false;

	}

	/**
	 * Output JSON encoded data.
	 * @todo Support JSONP, with callback filtering.
	 */
	function render_json( $data, $query ) {

		header('Content-type: application/json');
		$output = json_encode( $data );
		
		// Prepare a JSONP callback.
		$callback = $this->jsonp_callback_filter( $query['callback'] );

		// Only send back JSONP if that's appropriate for the request.
		if ( $callback ) {
			echo "{$callback}($output);";
			return;
		}

		// If not JSONP, send back the data.
		echo $output;

	}
	
	/**
	 * Prevent malicious callbacks from being used in JSONP requests.
	 */
	protected function jsonp_callback_filter( $callback ) {

		// As per <http://stackoverflow.com/a/10900911/1082542>.
		if ( preg_match( '/[^0-9a-zA-Z\$_]|^(abstract|boolean|break|byte|case|catch|char|class|const|continue|debugger|default|delete|do|double|else|enum|export|extends|false|final|finally|float|for|function|goto|if|implements|import|in|instanceof|int|interface|long|native|new|null|package|private|protected|public|return|short|static|super|switch|synchronized|this|throw|throws|transient|true|try|typeof|var|volatile|void|while|with|NaN|Infinity|undefined)$/', $callback) ) {
			return false;
		}

		return $callback;

	}

	/**
	 * Output data as an HTML table.
	 */
	function render_html( $data ) {

  	require_once( dirname( __FILE__ ) . '/bootstrap/header.html' );

  	//err out if no results
		if ( empty( $data ) ) {
		  echo "No results found";
		  return;
		}
		
		//page title
		echo "<h1>Results</h1>";
		
		//render table headings
		echo "<table class='table table-striped'>\n<thead>\n<tr>\n";

		foreach ( array_keys( get_object_vars( reset( $data ) ) ) as $heading ) {
  		echo "\t<th>$heading</th>\n";
		}
		
		echo "</tr>\n</thead>\n";
		
		//loop data and render
		foreach ( $data as $row ) {
  		
  		echo "<tr>\n";
  		
  		foreach ( $row as $cell ) {
    		
    		echo "\t<td>$cell</td>\n";
    		
  		}
  		
  		echo "</tr>";
  		
		}
		
		echo "</table>";
		
  	require_once( dirname( __FILE__ ) . '/bootstrap/footer.html' );		
		
	}

	/**
	 * Output data as XML.
	 */
	protected function render_xml( $data ) {

		header ("Content-Type:text/xml");  
		$xml = new SimpleXMLElement( '<results></results>' );
		$xml = $this->object_to_xml( $data, $xml );
		echo $this->tidy_xml( $xml );
		
	}

	/**
	 * Recusively travserses through an array to propegate SimpleXML objects
	 * @param array $array the array to parse
	 * @param object $xml the Simple XML object (must be at least a single empty node)
	 * @return object the Simple XML object (with array objects added)
	 */
	protected function object_to_xml( $array, $xml ) {
	
		//array of keys that will be treated as attributes, not children
		$attributes = array( 'id' );
	
		//recursively loop through each item
		foreach ( $array as $key => $value ) {
	
			//if this is a numbered array,
			//grab the parent node to determine the node name
			if ( is_numeric( $key ) )
				$key = 'result';
	
			//if this is an attribute, treat as an attribute
			if ( in_array( $key, $attributes ) ) {
				$xml->addAttribute( $key, $value );
	
				//if this value is an object or array, add a child node and treat recursively
			} else if ( is_object( $value ) || is_array( $value ) ) {
					$child = $xml->addChild(  $key );
					$child = $this->object_to_xml( $value, $child );
	
					//simple key/value child pair
				} else {
				$xml->addChild( $key, $value );
			}
	
		}
	
		return $xml;
	
	}
	
	/**
	 * Clean up XML domdocument formatting and return as string
	 */
	protected function tidy_xml( $xml ) {
  	
	   $dom = new DOMDocument();
	   $dom->preserveWhiteSpace = false;
	   $dom->formatOutput = true;
	   $dom->loadXML( $xml->asXML() );
	   return $dom->saveXML();
  	
	}

	/**
	 * Retrieve data from Alternative PHP Cache (APC).
	 */
	protected function cache_get( $key ) {
		
		if ( !extension_loaded('apc') || (ini_get('apc.enabled') != 1) ) {
			if ( isset( $this->cache[ $key ] ) ) {
				return $this->cache[ $key ];
			}
		}
		else {
			return apc_fetch( $key );
		}

		return false;

	}

	/**
	 * Store data in Alternative PHP Cache (APC).
	 */
	protected function cache_set( $key, $value, $ttl = null ) {

		if ( $ttl == null ) {
			$ttl = ( isset( $this->db->ttl) ) ? $this->db->ttl : $this->ttl;
		}

		$key = 'db_api_' . $key;

		if ( extension_loaded('apc') && (ini_get('apc.enabled') == 1) ) {
			return apc_store( $key, $value, $ttl );
		}

		$this->cache[$key] = $value;


	}
	
	
	/**
	 * Combine user attributes with known attributes and fill in defaults when needed.
	 *
	 * The pairs should be considered to be all of the attributes which are
	 * supported by the caller and given as a list. The returned attributes will
	 * only contain the attributes in the $pairs list.
	 *
	 * If the $atts list has unsupported attributes, then they will be ignored and
	 * removed from the final returned list.
	 *
	 * @since 2.5
	 *
	 * @param array $pairs Entire list of supported attributes and their defaults.
	 * @param array $atts User defined attributes in shortcode tag.
	 * @return array Combined and filtered attribute list.
	 */
	public function shortcode_atts($pairs, $atts) {
		$atts = (array)$atts;
		$out = array();
		foreach($pairs as $name => $default) {
			if ( array_key_exists($name, $atts) ) {
				$out[$name] = $atts[$name];
			}
			else {
				$out[$name] = $default;
			}
		}
		return $out;
	}	
	
	
    public function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }	
	


}