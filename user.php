<?php

//user.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}

if(!$object->is_master_user())
{
    header('location:index.php');
}

$object->query = "
    SELECT * FROM user_msbs 
    ORDER BY user_id DESC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_user"]))
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
        $object->query = "
        SELECT * FROM user_msbs 
        WHERE user_email = '".$formdata['user_email']."'
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
                ':user_type'        =>  'User',
                ':user_status'      =>  'Enable',
                ':user_created_on'  =>  $object->now
            );

            $object->query = "
            INSERT INTO user_msbs 
            (user_name, user_email, user_password, user_type, user_status, user_created_on) 
            VALUES (:user_name, :user_email, :user_password, :user_type, :user_status, :user_created_on)
            ";

            $object->execute($data);

            header('location:user.php?msg=add');
        }
    }
}

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
        $user_id = $object->convert_data(trim($_POST["user_id"]), 'decrypt');

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

            header('location:user.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $user_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':user_status'      =>  $status,
        ':user_id'          =>  $user_id
    );

    $object->query = "
    UPDATE user_msbs 
    SET user_status = :user_status 
    WHERE user_id = :user_id
    ";

    $object->execute($data);

    header('location:user.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">User Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="user.php">User Management</a></li>
                                <li class="breadcrumb-item active">Add User</li>
                            </ol>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    if(isset($error) && $error != '')
                                    {
                                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                    }
                                    ?>
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-user-plus"></i> Add New User
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_name" type="text" placeholder="Enter User Name" name="user_name" value="<?php if(isset($_POST["user_name"])) echo $_POST["user_name"]; ?>" />
                                                    <label for="user_name">User Name</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_email" type="text" placeholder="Enter User Email Address" name="user_email" value="<?php if(isset($_POST["user_email"])) echo $_POST["user_email"]; ?>" />
                                                    <label for="user_email">Email Address</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_password" type="password" placeholder="Enter User Password" name="user_password" value="<?php if(isset($_POST["user_password"])) echo $_POST["user_password"]; ?>" />
                                                    <label for="user_password">Password</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_user" class="btn btn-success" value="Add" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $user_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($user_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM user_msbs 
                                    WHERE user_id = '$user_id'
                                    ";

                                    $user_result = $object->get_result();

                                    foreach($user_result as $user_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="user.php">User Management</a></li>
                                    <li class="breadcrumb-item active">Edit User</li>
                                </ol>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                        if(isset($error) && $error != '')
                                        {
                                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                        }
                                        ?>
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-user-edit"></i> Edit User Details
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="user_name" type="text" placeholder="Enter User Name" name="user_name" value="<?php echo $user_row["user_name"]; ?>" />
                                                        <label for="user_name">User Name</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="user_email" type="text" placeholder="Enter User Email Address" name="user_email" value="<?php echo $user_row["user_email"]; ?>" />
                                                        <label for="user_email">Email Address</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="user_password" type="password" placeholder="Enter User Password" name="user_password" value="<?php echo $user_row["user_password"]; ?>" />
                                                        <label for="user_password">Password</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="user_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_user" class="btn btn-primary" value="Edit" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                else
                                {
                                    echo '<div class="alert alert-info">Something Went Wrong</div>';
                                }                                
                            }
                            else
                            {
                                echo '<div class="alert alert-info">Something Went Wrong</div>';
                            }
                        ?>

                        <?php
                        }
                        else
                        {
                        ?>
                        
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">User Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New User Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">User Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">User Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">User Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> User Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="user.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>User Name</th>
                                                <th>User Email Address</th>
                                                <th>Password</th>
                                                <th>User Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>User Name</th>
                                                <th>User Email Address</th>
                                                <th>Password</th>
                                                <th>User Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                        foreach($result as $row)
                                        {
                                            $user_type = '';
                                            $user_status = '';
                                            if($row["user_type"] == 'Master')
                                            {
                                                $user_type = '<div class="badge bg-info">Master</div>';
                                            }
                                            else
                                            {
                                                $user_type = '<div class="badge bg-warning">Sub User</div>';
                                            }
                                            if($row["user_status"] == 'Enable')
                                            {
                                                $user_status = '<div class="badge bg-success">Enable</div>';
                                            }
                                            else
                                            {
                                                $user_status = '<div class="badge bg-danger">Disable</div>';
                                            }
                                            echo '
                                            <tr>
                                                <td>'.$row["user_name"].'</td>
                                                <td>'.$row["user_email"].'</td>
                                                <td>'.$row["user_password"].'</td>
                                                <td>'.$user_type.'</td>
                                                <td>'.$user_status.'</td>
                                                <td>
                                                    <a href="user.php?action=edit&code='.$object->convert_data($row["user_id"]).'" class="btn btn-sm btn-primary">Edit</a>
                                                    <button type="button" name="delete_button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["user_id"]).'`, `'.$row["user_status"].'`); ">Delete</button>
                                                </td>
                                            </tr>
                                            ';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <script>
                            

                            function delete_data(code, status)
                            {
                                var new_status = 'Enable';
                                if(status == 'Enable')
                                {
                                    new_status = 'Disable';
                                }
                                if(confirm("Are you sure you want to "+new_status+" this User?"))
                                {
                                    window.location.href="user.php?action=delete&code="+code+"&status="+new_status+"";
                                }
                            }

                            </script>
                        <?php
                        }
                        ?>

                        </div>

<?php

include('footer.php');

?>