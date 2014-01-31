<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload extends CI_Controller {
	
    protected $path_img_upload_folder;
    protected $path_img_thumb_upload_folder;
    protected $path_url_img_upload_folder;
    protected $path_url_img_thumb_upload_folder;

    protected $delete_img_url;

  function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));

//Set relative Path with CI Constant
        $this->setPath_img_upload_folder("uploads");
        $this->setPath_img_thumb_upload_folder("assets/img/articles/thumbnails/");

        
//Delete img url
        $this->setDelete_img_url(base_url() . 'admin/deleteImage/');
 

//Set url img with Base_url()
        $this->setPath_url_img_upload_folder(base_url() . "assets/img/articles/");
        $this->setPath_url_img_thumb_upload_folder(base_url() . "assets/img/articles/thumbnails/");
  }


    public function index() {
		
		
		// if (empty($user)) 	$user = $this->input->get('user', TRUE);		

		if ($this->session->userdata('username')) {	
		     $this->load->view('upload_view');
		} else {
			redirect('login');
		}
		
		

   }

  

    public function upload_file() {
	
	
		if (!$this->session->userdata('username')) {
			redirect('login');
		}	
	
		function flatten_array(&$item, $key)
		{ $item = $item[0]; }		
		array_walk($_FILES['userfile'], 'flatten_array');	
		
        $name = $_FILES['userfile']['name'];
        $name = strtr($name, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $name = preg_replace('/([^.a-z0-9]+)/i', '_', $name);

		$_FILES['userfile']['name'] = $name;

		$name = reset(explode('.', $name));
		$db_hash = substr_replace(sha1(microtime(true)), '', 12);


       //Load the upload library, automatically will look for /config/uploads.php for config

	 // If we wanted to load the config stored in an array. 
	 //$this->config->load('upload', TRUE); // should now be available as an index in $this->config['upload']

        $this->load->library('upload');

		$this->load->helper('restdb'); // used for slugify	


       if ($this->do_upload()) {            

           $data = $this->upload->data();

           //Get info 
           $info = new stdClass();

		   	$info->username = $this->session->userdata('username');						
		   	$info->username_url = $this->session->userdata('username_url');
			
   			$info->name = $this->make_unique_name($name, $info->username_url); //Do a query to make sure that the dbname is unique, create unique name if needed
			$info->name_url = slugify($info->name);
			

			$info->api = base_url().$info->username_url . '/' . $info->name_url;
			
            $info->size = $data['file_size'];
            $info->type = $data['file_type'];
//          $info->url = $data['file_path'];

			// Parse CSV Data into an array. Todo: better to do db inserts line by line from csv, esp large files
			$parse_file = file_get_contents($data['full_path']);
			$csv = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $parse_file));
			
			// Add CSV data into db. First set db filepath
			$db_path = $this->config->item('sqlite_data_path') . $db_hash . '.db';

			// Check for an existing db file and create (touch) it if needed to initialize new file
			if(!file_exists($db_path)) touch($db_path);
			
			// Setting table columns. Just doing everything as text for now, but we could try to be smarter by typecast testing:
			// http://stackoverflow.com/questions/173498/using-php-to-take-the-first-line-of-a-csv-file-and-create-a-mysql-table-with-the
			$column_headers = null;
			foreach($csv[0] as $this_header) {	
				$this_header = strtolower(preg_replace('/([^a-z0-9]+)/i', '_', trim($this_header)));
				$column_headers .= "$this_header text, ";
			}
			
			// Remove column headings from array
			unset($csv[0]);
			
			// Remove trailing comma from string
			$column_headers = substr($column_headers, 0, strlen($column_headers) - 2);
			
			
			try {
				$dbh  = new PDO("sqlite:$db_path");

				$stm = "CREATE TABLE $info->name ($column_headers)";
				$dbh->exec($stm);

				foreach ($csv as $row) {
					
					$fields_insert = null;
					foreach($row as $column) {
						$fields_insert .= '"' . $column . '", ';
					}					
					
					// Remove trailing comma from string
					$fields_insert = substr($fields_insert, 0, strlen($fields_insert) - 2);
					
					
					$query="INSERT INTO $info->name VALUES (" . $fields_insert . ')';

					$dbh->exec($query);
				}

				// kill db connection
				$dbh = null;
			}	
			catch(PDOException $e) {
			 // Print PDOException message
			 echo $e->getMessage();
			}			
			
			// Everything below should be in a separate function, just doing it inline for now			
			//$this->save_db_connection()

			
				$data = array(
							'db_name'           => 	$info->name,
							'name_full'         => 	NULL,
							'name_url'          => 	$info->name_url,
							'name_hash'         => 	$db_hash,
							'description'       => 	NULL,
							'user_id'           => 	1,
							'user_url'          => 	$info->username_url,
							'db_username'       => 	NULL,
							'db_password'       => 	NULL,
							'db_server'         => 	NULL,
							'db_port'           => 	NULL,
							'local'             => 	1,
							'type'              => 	'sqlite',
							'table_blacklist'   => 	'',
							'column_blacklist'  => 	'',
						);
			
			$this->db->insert('db_connections', $data);	
			
		

			//return $this->db->get_where('db_connections', array('user_url' => $user_url, 'name_url' => $name_url));				
				
				
			// Save these details to MySQL! - $name, $column_headers, $unique_filename

           //Return JSON data
           if (IS_AJAX) {   //this is why we put this in the constants to pass only json data
                echo json_encode(array($info));
                //this has to be the only the only data returned or you will get an error.
                //if you don't give this a json array it will give you a Empty file upload result error
                //it you set this without the if(IS_AJAX)...else... you get ERROR:TRUE (my experience anyway)
            } else {   // so that this will still work if javascript is not enabled
                $file_data['upload_data'] = $this->upload->data();
                echo json_encode(array($info));
            }
        } else {

           // the display_errors() function wraps error messages in <p> by default and these html chars don't parse in
           // default view on the forum so either set them to blank, or decide how you want them to display.  null is passed.
            $error = array('error' => $this->upload->display_errors('',''));

            echo json_encode(array($error));
        }

       
  }


//Function for the upload : return true/false
  public function do_upload() {

        if (!$this->upload->do_upload()) {

            return false;
        } else {
            //$data = array('upload_data' => $this->upload->data());

            return true;
        }
     }



private function make_unique_name($db_name, $username) {
	
			$count = 1;
			
	        while($this->check_unique_name($db_name, $username) > 0) {
				$db_name = $db_name . $count;
				$count++;
			}
	
	return $db_name;
}


private function check_unique_name($db_name, $username) {
	
	$query = $this->db->get_where('db_connections', array('db_name' => $db_name, 'user_url' => $username));
	
	return sizeof($query->row_array());	

}


public function deleteImage() {

        //Get the name in the url
        $file = $this->uri->segment(3);
        
        $success = unlink($this->getPath_img_upload_folder() . $file);
        $success_th = unlink($this->getPath_img_thumb_upload_folder() . $file);

        //info to see if it is doing what it is supposed to 
        $info = new stdClass();
        $info->sucess = $success;
        $info->path = $this->getPath_url_img_upload_folder() . $file;
        $info->file = is_file($this->getPath_img_upload_folder() . $file);
        if (IS_AJAX) {//I don't think it matters if this is set but good for error checking in the console/firebug
            echo json_encode(array($info));
        } else {     //here you will need to decide what you want to show for a successful delete
            var_dump($file);
        }
    }

    public function get_files() {

        $this->get_scan_files();
    }

    public function get_scan_files() {

        $file_name = isset($_REQUEST['file']) ?
                basename(stripslashes($_REQUEST['file'])) : null;
        if ($file_name) {
            $info = $this->get_file_object($file_name);
        } else {
            $info = $this->get_file_objects();
        }
        header('Content-type: application/json');
        echo json_encode($info);
    }

    protected function get_file_object($file_name) {
        $file_path = $this->getPath_img_upload_folder() . $file_name;
        if (is_file($file_path) && $file_name[0] !== '.') {

            $file = new stdClass();
            $file->name = $file_name;
            $file->size = filesize($file_path);
            $file->url = $this->getPath_url_img_upload_folder() . rawurlencode($file->name);
            $file->thumbnail_url = $this->getPath_url_img_thumb_upload_folder() . rawurlencode($file->name);
            //File name in the url to delete 
            $file->delete_url = $this->getDelete_img_url() . rawurlencode($file->name);
            $file->delete_type = 'DELETE';
            
            return $file;
        }
        return null;
    }

    protected function get_file_objects() {
        return array_values(array_filter(array_map(
             array($this, 'get_file_object'), scandir($this->getPath_img_upload_folder())
                   )));
    }


    public function getPath_img_upload_folder() {
        return $this->path_img_upload_folder;
    }

    public function setPath_img_upload_folder($path_img_upload_folder) {
        $this->path_img_upload_folder = $path_img_upload_folder;
    }

    public function getPath_img_thumb_upload_folder() {
        return $this->path_img_thumb_upload_folder;
    }

    public function setPath_img_thumb_upload_folder($path_img_thumb_upload_folder) {
        $this->path_img_thumb_upload_folder = $path_img_thumb_upload_folder;
    }

    public function getPath_url_img_upload_folder() {
        return $this->path_url_img_upload_folder;
    }

    public function setPath_url_img_upload_folder($path_url_img_upload_folder) {
        $this->path_url_img_upload_folder = $path_url_img_upload_folder;
    }

    public function getPath_url_img_thumb_upload_folder() {
        return $this->path_url_img_thumb_upload_folder;
    }

    public function setPath_url_img_thumb_upload_folder($path_url_img_thumb_upload_folder) {
        $this->path_url_img_thumb_upload_folder = $path_url_img_thumb_upload_folder;
    }

    public function getDelete_img_url() {
        return $this->delete_img_url;
    }

    public function setDelete_img_url($delete_img_url) {
        $this->delete_img_url = $delete_img_url;
    }



}

?>
