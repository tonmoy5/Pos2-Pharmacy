
<?php



$message = '';

$connect = '';

$base_url = '';

error_reporting(0);

if(file_exists('credential.inc'))
{
	include('credential.inc');
	try
	{
		$connect = new PDO("mysql:host=$gdb_host;dbname=$gdb_name", $gdb_user_name, $gdb_password);
		$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::ERRMODE_WARNING);

		$query = "
		SELECT * FROM user_msbs 
		WHERE user_type = 'Master' 
		AND user_status = 'Enable'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		if($statement->rowCount() > 0)
		{
			header('location:'.$gbase_url.'index.php');
		}
		else
		{
			$message = 'Set Up Master Account';
			$base_url = $gbase_url;
		}
	}
	catch(PDOException $e)
	{
		header('location:'.$gbase_url.'index.php');
	}
}
else
{
	header('location:'.$gbase_url.'index.php');
}

if(isset($_POST["submit"]))
{
	include('credential.inc');

	$connect = new PDO("mysql:host=$gdb_host;dbname=$gdb_name", $gdb_user_name, $gdb_password);

	$formdata = array();

	if(empty($_POST["user_name"]))
    {
        $error .= '<li>User Full Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["user_name"]))
        {
            $error .= '<li>Only letters, Numbers and Space allowed</li>';
        }
        else
        {
            $formdata['user_name'] = trim($_POST["user_name"]);
        }
    }

    if(empty($_POST["user_email"]))
    {
        $error .= '<li>User Email is required</li>';
    }
    else
    {
        if (!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL))
        {
            $error .= '<li>Invalid Email Address</li>';
        }
        else
        {
            $formdata['user_email'] = trim($_POST["user_email"]);
        }
    }

    if(empty($_POST["user_password"]))
    {
        $error .= '<li>Password is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["user_password"]))
        {
            $error .= '<li>Only letters, Numbers allowed</li>';
        }
        else
        {
            $formdata['user_password'] = trim($_POST["user_password"]);
        }
    }

    if($error == '')
    {
		$data = array(
			':user_name'		=>	$formdata["user_name"],
			':user_email'		=>  $formdata["user_email"],
			':user_password'	=>	$formdata["user_password"],
			':user_type'		=>	'Master',
			':user_status'		=>	'Enable',
			':user_created_on'	=>	date('Y-m-d H:i:s')
		);

		$query = "
		INSERT INTO user_msbs 
		(user_name, user_email, user_password, user_type, user_status, user_created_on) 
		VALUES (:user_name, :user_email, :user_password, :user_type, :user_status, :user_created_on)
		";

		$statement = $connect->prepare($query);
		$statement->execute($data);
		header('location:'.$base_url.'index.php');
	}
}

?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Set Up Master Account | Medical Store Management System in PHP</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
	    <style>
	    	.border-top { border-top: 1px solid #e5e5e5; }
			.border-bottom { border-bottom: 1px solid #e5e5e5; }

			.box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
	    </style>
	</head>
	<body class="bg-primary">
		<div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">

                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Set Up Master Account</h3></div>
                                    <div class="card-body">
                                        <?php
                                        if($error != '')
                                        {
                                            echo '<div class="alert alert-danger"><ul>'.$error.'</ul></div>';
                                        }
                                        ?>
                                        <form method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="user_name" type="text" name="user_name" placeholder="Enter User Name" value="<?php if(isset($_POST['user_name'])) echo $_POST['user_name']; ?>" />
                                                <label for="user_name">Master User Name</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="user_email" type="email" name="user_email" placeholder="Enter Master User Email Address" value="<?php if(isset($_POST['user_email'])) echo $_POST['user_email']; ?>" />
                                                <label for="user_email">Email Address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="user_password" type="password" name="user_password" placeholder="Enter Master User Login Password" value="<?php if(isset($_POST['user_password'])) echo $_POST['user_password']; ?>" />
                                                <label for="user_password">Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <input type="submit" name="submit" id="submit_button" class="btn btn-primary" value="submit" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Medical Store Management System in PHP <?php echo date('Y'); ?></div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>

	</body>
</html>