<?php
	$dbServer = "localhost";
	$dbUsername = "root";
	$dbPassword = "";
	$dbName = "phpproject01";
	
	$conn = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName);
	
	if(!$conn)
	{
		die( "Connection failed: " . mysqli_connect_error() );
	}



	// the die funciton terminates the execution of a script and outputs a message to the user.
	// It can be used to handle errors or unexpected situations in a program.