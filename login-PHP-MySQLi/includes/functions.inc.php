<?php
	function emptyInputSignup($fullname, $email, $username, $password, $passwordRepeat)
	{
		$result = false;
		if( empty($fullname) || empty($email) || empty($username) || empty($password) || empty($passwordRepeat) )
		{
			$result = true;
		}
		else
		{
			$result = false;
		}
		return $result;
	}
	
	function emptyInputLogin($username, $password)
	{
		$result = false;
		if( empty($username) || empty($password) )
		{
			$result = true;
		}
		else
		{
			$result = false;
		}
		return $result;
	}
	
	function invalidUsername($username)
	{
		$result = false;
		if( !preg_match("/^[a-zA-Z0-9]*$/", $username) )
		{
			$result = true;
		}
		else
		{
			$result = false;
		}
		return $result;
	}
	
	function invalidEmail($email)
	{
		$result = false;
		// typed ! as we search for error first .. so if the function retun not true (invalid email) then result = ture >> error and same with ohter functions.
		if( !filter_var($email, FILTER_VALIDATE_EMAIL) )
		{
			$result = true;
		}
		else
		{
			$result = false;
		}
		return $result;
	}
	
	function passwordsMatch($password, $passwordRepeat)
	{
		$result = false;
		if( $password !== $passwordRepeat )
		{
			$result = true;
		}
		else
		{
			$result = false;
		}
		return $result;
	}
	
	function usernameExists($conn, $username)
	{
		$sql = "SELECT * FROM users WHERE usersUsername = ?;";
		
		// intializing a new prepared statement using the fun mysqli_stmt_init and take the $sql .. do not trust user input 
		$stmt = mysqli_stmt_init($conn);
		// and here we check also if there are any error when connecting to the database.
		if( !mysqli_stmt_prepare($stmt, $sql) )
		{
			header("location: ../signup.php?error=stmtfailed");
			exit();
		}
		// s >> for one string as it is only the username.
		// third param is the data submited by user which it is the username
		mysqli_stmt_bind_param($stmt, "s", $username);
		mysqli_stmt_execute($stmt);
		
		$resultData = mysqli_stmt_get_result($stmt);
		
		mysqli_stmt_close($stmt);
		
		if( $row = mysqli_fetch_assoc($resultData) )
		{
			return $row;
		}
		else
		{
			return false;
		}
	}
	
	function emailExists($conn, $email)
	{
		$sql = "SELECT * FROM users WHERE usersEmail = ?;";
		
		$stmt = mysqli_stmt_init($conn);
		
		if( !mysqli_stmt_prepare($stmt, $sql) )
		{
			header("location: ../signup.php?error=stmtfailed");
			exit();
		}
		
		mysqli_stmt_bind_param($stmt, "s", $email);
		mysqli_stmt_execute($stmt);
		
		$resultData = mysqli_stmt_get_result($stmt);
		
		mysqli_stmt_close($stmt);
		
		if( $row = mysqli_fetch_assoc($resultData) )
		{
			return $row;
		}
		else
		{
			return false;
		}
	}
	
	function createUser($conn, $fullname, $email, $username, $password)
	{
		$sql = "INSERT INTO users (usersUsername, usersEmail, usersPassword, usersRealname) VALUES (?, ?, ?, ?);";
		
		$stmt = mysqli_stmt_init($conn);
		
		if ( !mysqli_stmt_prepare($stmt, $sql) )
		{
			header("location: ../signup.php?error=stmtfailed");
			exit();
		}
		
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		
		mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashedPassword, $fullname);
		
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		header("location: ../signup.php?error=none");
		exit();
	}
	
	function loginUser($conn, $username, $password)
	{
		$usernameExists = usernameExists($conn, $username);
		
		if ($usernameExists === false)
		{
			header("location: ../login.php?error=wronglogin");
			exit();
		}
		
		$hashedPassword = $usernameExists["usersPassword"];
		$checkPassword = password_verify($password, $hashedPassword);
		
		if ( $checkPassword === false )
		{
			header("location: ../login.php?error=wronglogin");
			exit();
		}
		else if ( $checkPassword === true )
		{
			session_start();
			$_SESSION["userid"  ] = $usernameExists["usersId"      ];
			$_SESSION["username"] = $usernameExists["usersUsername"];
			$_SESSION["realname"] = $usernameExists["usersRealname"];
			header("location: ../index.php");
			exit();
		}
	}
