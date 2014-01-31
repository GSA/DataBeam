<?php include 'header_meta_inc_view.php';?>



<?php 

$page = array('title' => 'Your APIs');

include 'header_inc_view.php';

?>
		
	

		<?php
		
		echo '<h1>' . $user['name_full'] . ' APIs</h1>';
				
		if (!empty($connections)) {
			
			echo '<table class="table table-striped">';
						
			foreach($connections as $connection) {
			
				$db_name = $connection['name_full'];
				$db_url = $connection['name_url'];
				$user_url 	= $connection['user_url'];
				
				$db_name = (empty($db_name)) ? $db_url : $db_name;				
			
			if($connection['local']) {
				//$db_url = 'local/' . $db_url;
				$icon = 'icon-file';
			} else {
				$icon = 'icon-hdd';
			}
                
				echo '<tr><td><i class="'.$icon.'"></i> <a href="'.base_url("$user_url/$db_url").'">'.$db_name.'</a> </td></tr>';
			}
			
			echo '</table>';
			
		} else {
?>
		<p>
			You haven't added any datasets yet. You can start by either <a href="<?php echo base_url();?>upload">Uploading a CSV</a> or <a href="<?php echo base_url();?>new">Connecting a Database</a>.
<?php
		}
		?>


<?php include 'footer_inc_view.php';?>