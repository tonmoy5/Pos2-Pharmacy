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
    SELECT * FROM category_msbs 
    ORDER BY category_name ASC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_category"]))
{
    $formdata = array();

    if(empty($_POST["category_name"]))
    {
        $error .= '<li>Category Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["category_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['category_name'] = trim($_POST["category_name"]);
        }
    }

    if($error == '')
    {
        $object->query = "
        SELECT * FROM category_msbs 
        WHERE category_name = '".$formdata['category_name']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Category Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':category_name'        =>  $formdata['category_name'],
                ':category_status'      =>  'Enable',
                ':category_datetime'    =>  $object->now
            );

            $object->query = "
            INSERT INTO category_msbs 
            (category_name, category_status, category_datetime) 
            VALUES (:category_name, :category_status, :category_datetime)
            ";

            $object->execute($data);

            header('location:category.php?msg=add');
        }
    }
}

if(isset($_POST["edit_category"]))
{
    $formdata = array();

    if(empty($_POST["category_name"]))
    {
        $error .= '<li>Category Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["category_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['category_name'] = trim($_POST["category_name"]);
        }
    }

    if($error == '')
    {
        $category_id = $object->convert_data(trim($_POST["category_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM category_msbs 
        WHERE category_name = '".$formdata['category_name']."' 
        AND category_id != '".$category_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Category Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':category_name'    =>  $formdata['category_name'],
                ':category_id'      =>  $category_id
            );

            $object->query = "
            UPDATE category_msbs 
            SET category_name = :category_name 
            WHERE category_id = :category_id
            ";

            $object->execute($data);

            header('location:category.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $category_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':category_status'      =>  $status,
        ':category_id'          =>  $category_id
    );

    $object->query = "
    UPDATE category_msbs 
    SET category_status = :category_status 
    WHERE category_id = :category_id
    ";

    $object->execute($data);

    header('location:category.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Category Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="category.php">Category Management</a></li>
                                <li class="breadcrumb-item active">Add Category</li>
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
                                            <i class="fas fa-user-plus"></i> Add New Category
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="category_name" type="text" placeholder="Enter Category Name" name="category_name" value="<?php if(isset($_POST["category_name"])) echo $_POST["category_name"]; ?>" />
                                                    <label for="category_name">Category Name</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_category" class="btn btn-success" value="Add" />
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
                                $category_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($category_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM category_msbs 
                                    WHERE category_id = '$category_id'
                                    ";

                                    $category_result = $object->get_result();

                                    foreach($category_result as $category_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="category.php">Category Management</a></li>
                                    <li class="breadcrumb-item active">Edit Category</li>
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
                                                <i class="fas fa-user-edit"></i> Edit Category Details
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="category_name" type="text" placeholder="Enter Category Name" name="category_name" value="<?php echo $category_row["category_name"]; ?>" />
                                                        <label for="category_name">Category Name</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="category_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_category" class="btn btn-primary" value="Edit" />
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
                                <li class="breadcrumb-item active">Category Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Category Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Category Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="category.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>Category Name</th>
                                                <th>Status</th>
                                                <th>Date & Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Category Name</th>
                                                <th>Status</th>
                                                <th>Date & Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                        foreach($result as $row)
                                        {
                                            $category_status = '';
                                            if($row["category_status"] == 'Enable')
                                            {
                                                $category_status = '<div class="badge bg-success">Enable</div>';
                                            }
                                            else
                                            {
                                                $category_status = '<div class="badge bg-danger">Disable</div>';
                                            }
                                            echo '
                                            <tr>
                                                <td>'.$row["category_name"].'</td>
                                                <td>'.$category_status.'</td>
                                                <td>'.$row["category_datetime"].'</td>
                                                <td>
                                                    <a href="category.php?action=edit&code='.$object->convert_data($row["category_id"]).'" class="btn btn-sm btn-primary">Edit</a>
                                                    <button type="button" name="delete_button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["category_id"]).'`, `'.$row["category_status"].'`); ">Delete</button>
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
                                if(confirm("Are you sure you want to "+new_status+" this Category?"))
                                {
                                    window.location.href="category.php?action=delete&code="+code+"&status="+new_status+"";
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