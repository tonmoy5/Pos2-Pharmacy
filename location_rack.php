<?php

//location_rack.php

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
    SELECT * FROM location_rack_msbs 
    ORDER BY location_rack_name ASC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_location_rack"]))
{
    $formdata = array();

    if(empty($_POST["location_rack_name"]))
    {
        $error .= '<li>Location Rack Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["location_rack_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['location_rack_name'] = trim($_POST["location_rack_name"]);
        }
    }

    if($error == '')
    {
        $object->query = "
        SELECT * FROM location_rack_msbs 
        WHERE location_rack_name = '".$formdata['location_rack_name']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Location Rack Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':location_rack_name'        =>  $formdata['location_rack_name'],
                ':location_rack_status'      =>  'Enable',
                ':location_rack_datetime'    =>  $object->now
            );

            $object->query = "
            INSERT INTO location_rack_msbs 
            (location_rack_name, location_rack_status, location_rack_datetime) 
            VALUES (:location_rack_name, :location_rack_status, :location_rack_datetime)
            ";

            $object->execute($data);

            header('location:location_rack.php?msg=add');
        }
    }
}

if(isset($_POST["edit_location_rack"]))
{
    $formdata = array();

    if(empty($_POST["location_rack_name"]))
    {
        $error .= '<li>Location Rack Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["location_rack_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['location_rack_name'] = trim($_POST["location_rack_name"]);
        }
    }

    if($error == '')
    {
        $location_rack_id = $object->convert_data(trim($_POST["location_rack_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM location_rack_msbs 
        WHERE location_rack_name = '".$formdata['location_rack_name']."' 
        AND location_rack_id != '".$location_rack_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Location Rack Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':location_rack_name'    =>  $formdata['location_rack_name'],
                ':location_rack_id'      =>  $location_rack_id
            );

            $object->query = "
            UPDATE location_rack_msbs 
            SET location_rack_name = :location_rack_name 
            WHERE location_rack_id = :location_rack_id
            ";

            $object->execute($data);

            header('location:location_rack.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $location_rack_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':location_rack_status'      =>  $status,
        ':location_rack_id'          =>  $location_rack_id
    );

    $object->query = "
    UPDATE location_rack_msbs 
    SET location_rack_status = :location_rack_status 
    WHERE location_rack_id = :location_rack_id
    ";

    $object->execute($data);

    header('location:location_rack.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Location Rack Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="category.php">Location Rack Management</a></li>
                                <li class="breadcrumb-item active">Add Location Rack</li>
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
                                            <i class="fas fa-user-plus"></i> Add New Location Rack
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="location_rack_name" type="text" placeholder="Enter Location Rack Name" name="location_rack_name" value="<?php if(isset($_POST["location_rack_name"])) echo $_POST["location_rack_name"]; ?>" />
                                                    <label for="location_rack_name">Location Rack Name</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_location_rack" class="btn btn-success" value="Add" />
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
                                $location_rack_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($location_rack_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM location_rack_msbs 
                                    WHERE location_rack_id = '$location_rack_id'
                                    ";

                                    $location_rack_result = $object->get_result();

                                    foreach($location_rack_result as $location_rack_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="location_rack.php">Location Rack Management</a></li>
                                    <li class="breadcrumb-item active">Edit Location Rack</li>
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
                                                <i class="fas fa-user-edit"></i> Edit Location Rack Details
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="location_rack_name" type="text" placeholder="Enter Location Rack Name" name="location_rack_name" value="<?php echo $location_rack_row["location_rack_name"]; ?>" />
                                                        <label for="location_rack_name">Location Rack Name</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="location_rack_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_location_rack" class="btn btn-primary" value="Edit" />
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
                                <li class="breadcrumb-item active">Location Rack Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Location Rack Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Location Rack Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Location Rack Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Location Rack Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Location Rack Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="location_rack.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>Location Rack Name</th>
                                                <th>Status</th>
                                                <th>Date & Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Location Rack Name</th>
                                                <th>Status</th>
                                                <th>Date & Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                        foreach($result as $row)
                                        {
                                            $location_rack_status = '';
                                            if($row["location_rack_status"] == 'Enable')
                                            {
                                                $location_rack_status = '<div class="badge bg-success">Enable</div>';
                                            }
                                            else
                                            {
                                                $location_rack_status = '<div class="badge bg-danger">Disable</div>';
                                            }
                                            echo '
                                            <tr>
                                                <td>'.$row["location_rack_name"].'</td>
                                                <td>'.$location_rack_status.'</td>
                                                <td>'.$row["location_rack_datetime"].'</td>
                                                <td>
                                                    <a href="location_rack.php?action=edit&code='.$object->convert_data($row["location_rack_id"]).'" class="btn btn-sm btn-primary">Edit</a>
                                                    <button type="button" name="delete_button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["location_rack_id"]).'`, `'.$row["location_rack_status"].'`); ">Delete</button>
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
                                if(confirm("Are you sure you want to "+new_status+" this Location Rack?"))
                                {
                                    window.location.href="location_rack.php?action=delete&code="+code+"&status="+new_status+"";
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