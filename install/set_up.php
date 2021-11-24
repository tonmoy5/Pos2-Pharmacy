
<?php

$message = '';

error_reporting(0);

if(file_exists('credential.inc'))
{
	include('credential.inc');
	try
	{
		$connect = new PDO("mysql:host=$gdb_host;dbname=$gdb_name", $gdb_user_name, $gdb_password);
		$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::ERRMODE_WARNING);
		
		header('location:'.$gbase_url.'admin/index.php');

	}
	catch(PDOException $e)
	{
		$message = 'Set Mysql Database Configuration Details';
	}
}
else
{
	$message = 'Set Mysql Database Configuration Details';
}


if(isset($_POST["submit"]))
{
	$formdata = array();

	if(empty($_POST["database_name"]))
    {
        $error .= '<li>Database Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["location_rack_name"]))
        {
            $error .= '<li>Only letters, Numbers allowed</li>';
        }
        else
        {
            $formdata['database_name'] = trim($_POST["database_name"]);
        }
    }

    if(empty($_POST["database_username"]))
    {
        $error .= '<li>Database Username is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["database_username"]))
        {
            $error .= '<li>Only letters, Numbers allowed</li>';
        }
        else
        {
            $formdata['database_username'] = trim($_POST["database_username"]);
        }
    }

    /*if(empty($_POST["database_password"]))
    {
        $error .= '<li>Password is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["database_password"]))
        {
            $error .= '<li>Only letters, Numbers allowed</li>';
        }
        else
        {
            $formdata['database_password'] = trim($_POST["database_password"]);
        }
    }*/

    if(empty($_POST["database_host"]))
    {
        $error .= '<li>Database Host is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["database_host"]))
        {
            $error .= '<li>Only letters, Numbers allowed</li>';
        }
        else
        {
            $formdata['database_host'] = trim($_POST["database_host"]);
        }
    }

    if(empty($_POST["base_url"]))
    {
        $error .= '<li>Base Url is required</li>';
    }
    else
    {
    	$formdata['base_url'] = trim($_POST["base_url"]);
        /*if(filter_var($_POST["base_url"], FILTER_VALIDATE_URL))
        {
            $error .= '<li>Invalid Base Url</li>';
        }
        else
        {
            $formdata['base_url'] = trim($_POST["base_url"]);
        }*/
    }

    if($error == '')
    {
		$string = '
		<?php 
			$gdb_name = "'.$formdata['database_name'].'";
			$gdb_user_name = "'.$formdata['database_username'].'";
			$gdb_password = "'.$formdata['database_password'].'";
			$gdb_host = "'.$formdata['database_host'].'";
			$gbase_url = "'.$formdata['base_url'].'";
		?>
		';

		if(file_put_contents('credential.inc', $string))
		{
			header('location:'.$formdata['base_url'].'/index.php');
		}
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
        <title>Set Up | Medical Store Management System in PHP</title>
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Set Up Medical Store Management System</h3></div>
                                    <div class="card-body">
                                        <?php
                                        if($error != '')
                                        {
                                            echo '<div class="alert alert-danger"><ul>'.$error.'</ul></div>';
                                        }
                                        ?>
                                        <form method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="database_name" type="text" name="database_name" placeholder="The Name of Database" value="<?php if(isset($_POST['database_name'])) echo $_POST['database_name']; ?>" />
                                                <label for="database_name">Name of MySQL Database</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="database_username" type="text" name="database_username" placeholder="Database Username" value="<?php if(isset($_POST['database_username'])) echo $_POST['database_username']; ?>" />
                                                <label for="database_username">Database Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="database_password" type="password" name="database_password" placeholder="Database Password" value="<?php if(isset($_POST['database_password'])) echo $_POST['database_password']; ?>" />
                                                <label for="database_password">Database Password</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="database_host" type="text" name="database_host" placeholder="Enter Your Database Host Name" value="<?php if(isset($_POST['database_host'])) echo $_POST['database_host']; ?>" />
                                                <label for="database_host">Your Database Host Name</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="base_url" type="text" name="base_url" placeholder="Define Base Url of Medical Store Management System" value="<?php if(isset($_POST['base_url'])) echo $_POST['base_url']; ?>" />
                                                <label for="base_url">Define Base Url of Medical Store Management System</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <input type="submit" name="submit" class="btn btn-primary" value="Submit" />
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