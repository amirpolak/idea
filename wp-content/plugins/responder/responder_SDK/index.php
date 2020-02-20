<?php
	# include the libraries needed to make the REST API requests
	include 'OAuth.php';
	include 'responder_sdk.php';
	
	
	################################################################################
	# Tokens; should fill with the tokens acquired from the responder support team #
	################################################################################
	
	# represents you as a client (the connection to responder)
	$client_key = '6FF503AEA0B1E24E72583C99B0DD471A';
	$client_secret = '72818D716ABC9AF0A959696576F418B0';

	# represents the user in responder
	$user_key = '';
	$user_secret = '';

	
	# create the responder request instance
	$responder = new ResponderOAuth($client_key, $client_secret, $user_key, $user_secret);
	
	
	#############################
	# Examples of list requests #
	#############################
	
	# get all lists
	//$response = $responder->http_request('lists', 'get');
	
	# get 15 lists starting from list number 3
	// $response = $responder->http_request('lists?offset=3&limit=15', 'get');
	
	# creating a list
	// $post_data = array(
		// 'info' => json_encode(
			// array(
				// 'DESCRIPTION' => 'test list',
				// 'SENDER_NAME' => 'your name',
				// 'SENDER_EMAIL' => 'example@email.com',
				// 'SENDER_ADDRESS' => 'your physical address'
			// )
		// )
	// );
	// $response = $responder->http_request('lists', 'post', $post_data);
	
	# updating a list
	// $post_data = array(
		// 'info' => json_encode(
			// array(
				// 'DESCRIPTION' => 'my new name list'
			// )
		// )
	// );
	// $list_id = 0; /* returned when creating a list [using POST] or reading a list [using GET] */
	// $response = $responder->http_request("lists/{$list_id}", 'put', $post_data);
	
	# deleting a list
	// $list_id = 0;
	// $response = $responder->http_request("lists/{$list_id}", 'delete');
	
	
	####################################
	# Examples of subscribers requests #
	####################################
	
	# get all subscribers of a list
	// $list_id = 0;
	// $response = $responder->http_request("lists/{$list_id}/subscribers", 'get');
	
	# creating a subscriber in a list
	// $list_id = 0;
	// $post_data = array(
		// 'subscribers' => json_encode(
			// array(
				// array(
					// 'EMAIL' => 'email@example.com',
					// 'NAME' => 'example name'
				// )
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/subscribers", 'post', $post_data);
	
	# updating a subscriber of a list
	// $list_id = 0;
	// $post_data = array(
		// 'subscribers' => json_encode(
			// array(
				// array(
					// 'IDENTIFIER' => 'email@example.com', /* can be either an email address or the ID of the subscriber */
					// 'EMAIL' => 'newemail@example.com'
				// )
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/subscribers", 'put', $post_data);
	
	# deleting a subscriber from a list
	// $list_id = 0;
	// $post_data = array(
		// 'subscribers' => json_encode(
			// array(
				// /* can be either an email address or the ID of the subscriber */
				// /* array('ID' => 0), */
				// array('EMAIL' => 'email@example.com')
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/subscribers", 'delete', $post_data);
	
	
	########################################
	# Examples of personal fields requests #
	########################################
	
	# get all personal fields of a list
	// $list_id = 60 ;
	// $response = $responder->http_request("lists/{$list_id}/personal_fields", 'get');
	
	# creating a personal field in a list
	// $list_id = 0;
	// $post_data = array(
		// 'personal_fields' => json_encode(
			// array(
				// array(
					// 'NAME' => 'Example field'
				// )
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/personal_fields", 'post', $post_data);
	
	# updating a personal field of a list
	// $list_id = 0;
	// $post_data = array(
		// 'personal_fields' => json_encode(
			// array(
				// array(
					// 'ID' => 0,
					// 'NAME' => 'New field name'
				// )
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/personal_fields", 'put', $post_data);
	
	# deleting a personal field from a list
	// $list_id = 0;
	// $post_data = array(
		// 'personal_fields' => json_encode(
			// array(
				// array('ID' => 0)
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/personal_fields", 'delete', $post_data);
	
	
	##############################
	# Examples of views requests #
	##############################
	
	# get all views of a list
	// $list_id = 0;
	// $response = $responder->http_request("lists/{$list_id}/views", 'get');
	
	# creating a view in a list
	// $list_id = 0;
	// $post_data = array(
		// 'views' => json_encode(
			// array(
				// array('NAME' => 'example view')
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/views", 'post', $post_data);
	
	# updating a view of a list
	// $list_id = 0;
	// $post_data = array(
		// 'views' => json_encode(
			// array(
				// array(
					// 'ID' => 0,
					// 'NAME' => 'new name'
				// )
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/views", 'put', $post_data);
	
	# deleting a view from a list
	// $list_id = 0;
	// $post_data = array(
		// 'views' => json_encode(
			// array(
				// array('ID' => 0)
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/views", 'delete', $post_data);
	
	
	###############################################
	# Examples of subscribers of a views requests #
	###############################################
	
	# get all subscribers of a view
	// $list_id = 0;
	// $view_id = 0;
	// $response = $responder->http_request("lists/{$list_id}/views/{$view_id}/subscribers", 'get');
	
	# add a subscriber to a view
	// $list_id = 0;
	// $view_id = 0;
	// $post_data = array(
		// 'subscribers' => json_encode(
			// array(
				// /* can be either an email address or the ID of the subscriber */
				// /* array('ID' => 0), */
				// array('EMAIL' => 'email@example.com')
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/views/{$view_id}/subscribers", 'post', $post_data);
	
	# removing a subscriber from a view
	// $list_id = 0;
	// $view_id = 0;
	// $post_data = array(
		// 'subscribers' => json_encode(
			// array(
				// /* can be either an email address or the ID of the subscriber */
				// /* array('ID' => 0), */
				// array('EMAIL' => 'email@example.com')
			// )
		// )
	// );
	// $response = $responder->http_request("lists/{$list_id}/views/{$view_id}/subscribers", 'delete', $post_data);
	
	$json_response = json_decode($response);

    /*foreach ($json_response as $list){
        echo "<table style='overflow:hidden; border:1px solid gray;'>";
        foreach ($list as $semlist){
            echo "<tr>";
            foreach ($semlist as $key => $value){
                echo "<td>".$value."</td>";
            }
            "</td>";
        }
        echo "</table>";
        break;
    }*/
	echo '<pre>';
	# print the response
	if ($json_response) {
		print_r($json_response);
	} else {
		print_r($response);
	}

	echo '</pre>';
?>