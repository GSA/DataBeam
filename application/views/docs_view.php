<?php include 'header_meta_inc_view.php';?>


<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'/>
<link href='/assets/css/hightlight.default.css' media='screen' rel='stylesheet' type='text/css'/>
<link href='/assets/css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
<script src='/assets/js/jquery-1.8.0.min.js' type='text/javascript'></script>
<script src='/assets/js/jquery.slideto.min.js' type='text/javascript'></script>
<script src='/assets/js/jquery.wiggle.min.js' type='text/javascript'></script>
<script src='/assets/js/jquery.ba-bbq.min.js' type='text/javascript'></script>
<script src='/assets/js/handlebars-1.0.rc.1.js' type='text/javascript'></script>
<script src='/assets/js/underscore-min.js' type='text/javascript'></script>
<script src='/assets/js/backbone-min.js' type='text/javascript'></script>
<script src='/assets/js/swagger.js' type='text/javascript'></script>
<script src='/assets/js//swagger-ui.js' type='text/javascript'></script>
<script src='/lib/highlight.7.3.pack.js' type='text/javascript'></script>


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

   <script type="text/javascript">
       $(function () {
           window.swaggerUi = new SwaggerUi({
               discoveryUrl:"http://restdb.dev/philipashlock/api-docs.json",
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
               docExpansion: "none"
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