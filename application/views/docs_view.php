<?php include 'header_meta_inc_view.php';?>


<link href='//fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'/>
<link href='<?php echo base_url();?>assets/css/hightlight.default.css' media='screen' rel='stylesheet' type='text/css'/>
<link href='<?php echo base_url();?>assets/css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
<script src='<?php echo base_url();?>assets/js/jquery-1.8.0.min.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/jquery.slideto.min.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/jquery.wiggle.min.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/jquery.ba-bbq.min.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/handlebars-1.0.rc.1.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/underscore-min.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/backbone-min.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/swagger.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/swagger-ui.js' type='text/javascript'></script>
<script src='<?php echo base_url();?>assets/js/highlight.7.3.pack.js' type='text/javascript'></script>


<style type="text/css">
       .swagger-ui-wrap {
           margin-left: auto;
           margin-right: auto;
       }

       .icon-btn {
           cursor: pointer;
       }

       #message-bar {
           min-height: 30px;
           text-align: center;
           padding-top: 10px;
       }

       .message-success {
           color: #89BF04;
       }

       .message-fail {
           color: #cc0000;
       }
   </style>


<?php
$discovery_url = 'http://'.$_SERVER["SERVER_NAME"].base_url() . $db['user_url'] . '/api-docs.json/' . $db['name_url'];
?>

   <script type="text/javascript">
       $(function () {
           window.swaggerUi = new SwaggerUi({
               discoveryUrl:"<?php echo $discovery_url; ?>",
               apiKey:"api-key",
               dom_id:"swagger-ui-container",
               supportHeaderParams: false,
               supportedSubmitMethods: ['get', 'post', 'put'],
               onComplete: function(swaggerApi, swaggerUi){
               	if(console) {
                       console.log("Loaded SwaggerUI")
                       console.log(swaggerApi);
                       console.log(swaggerUi);
                   }
                 $('pre code').each(function(i, e) {hljs.highlightBlock(e)});
               },
               onFailure: function(data) {
               	if(console) {
                       console.log("Unable to Load SwaggerUI");
                       console.log(data);
                   }
               },
               docExpansion: "full"
           });

           window.swaggerUi.load();
       });

   </script>

<?php include 'header_inc_view.php';?>



		

		<?php		
		echo '<h1>Docs for ' . $db['name_full'] . ' API</h1>';
				
		?>
		
		


		<div id="message-bar" class="swagger-ui-wrap">
		    &nbsp;
		</div>

		<div id="swagger-ui-container" class="swagger-ui-wrap">

		</div>
		
		<?php
				
		foreach ($tables as $table) {
			
			//echo "<a href=\"$table\">$table</a> <br>";
		}
		?>





<?php include 'footer_inc_view.php';?>