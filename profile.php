<?php

//user.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}


$object->query = "
    SELECT * FROM user_msbs 
    WHERE user_id = '".$_SESSION["user_id"]."'
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["edit_user"]))
{
    $formdata = array();

    if(empty($_POST["user_name"]))
    {
        $error .= '<li>User Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["user_name"]))
        {
            $error .= '<li>Only letters and white space allowed</li>';
        }
        else
        {
            $formdata['user_name'] = trim($_POST["user_name"]);
        }
    }

    if(empty($_POST["user_email"]))
    {
        $error .= '<li>Email Address is required</li>';
    }
    else
    {
        if(!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL))
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
        $formdata['user_password'] = trim($_POST["user_password"]);
    }

    if($error == '')
    {
        $user_id = $_SESSION["user_id"];

        $object->query = "
        SELECT * FROM user_msbs 
        WHERE user_email = '".$formdata['user_email']."' 
        AND user_id != '".$user_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Email Address Already Exists</li>';
        }
        else
        {
            $data = array(
                ':user_name'        =>  $formdata['user_name'],
                ':user_email'       =>  $formdata['user_email'],
                ':user_password'    =>  $formdata['user_password'],
                ':user_id'          =>  $user_id
            );

            $object->query = "
            UPDATE user_msbs 
            SET user_name = :user_name, 
            user_email = :user_email,
            user_password = :user_password 
            WHERE user_id = :user_id
            ";

            $object->execute($data);

            header('location:profile.php?msg=edit');
        }
    }
}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Profile</h1>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Profile</a></li>
                            </ol>
                            <div class="row">
                                <div class="col-md-6">
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }

                                if(isset($_GET["msg"]))
                                {
                                    if($_GET["msg"] == 'edit')
                                    {
                                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">User Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                    }
                                }

                                ?>
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-user-edit"></i> Edit Profile Details
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            foreach($result as $row)
                                            {
                                            ?>
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_name" type="text" placeholder="Enter User Name" name="user_name" value="<?php echo $row["user_name"]; ?>" />
                                                    <label for="user_name">User Name</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_email" type="text" placeholder="Enter User Email Address" name="user_email" value="<?php echo $row["user_email"]; ?>" />
                                                    <label for="user_email">Email Address</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_password" type="password" placeholder="Enter User Password" name="user_password" value="<?php echo $row["user_password"]; ?>" />
                                                    <label for="user_password">Password</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="edit_user" class="btn btn-primary" value="Edit" />
                                                </div>
                                            </form>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

<?php

include('footer.php');

?>