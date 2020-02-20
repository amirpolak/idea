<?php

	if(!function_exists("dmp")){
		function dmp($str){
			echo "<div align='left' dir='ltr;' style='direction:ltr;'>";
			echo "<pre>";
			print_r($str);
			echo "</pre>";
			echo "</div>";
		}
	}
