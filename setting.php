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
    SELECT * FROM store_msbs 
    LIMIT 1
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["submit"]))
{
    $formdata = array();

    if(empty($_POST["store_name"]))
    {
        $error .= '<li>Medical Store Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["store_name"]))
        {
            $error .= '<li>Only letters, Numbers and Space allowed</li>';
        }
        else
        {
            $formdata['store_name'] = trim($_POST["store_name"]);
        }
    }

    if(empty($_POST["store_address"]))
    {
        $error .= '<li>Medical Store is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["store_address"]))
        {
            $error .= '<li>Only letters, Numbers and Space allowed</li>';
        }
        else
        {
            $formdata['store_address'] = trim($_POST["store_address"]);
        }
    }

    if(empty($_POST["store_contact_no"]))
    {
        $error .= '<li>Medical Store Contact No. is required</li>';
    }
    else
    {
        if (!preg_match("/^[0-9']*$/", $_POST["store_contact_no"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['store_contact_no'] = trim($_POST["store_contact_no"]);
        }
    }

    if(empty($_POST["store_email_address"]))
    {
        $error .= '<li>Medical Store Email Address is required</li>';
    }
    else
    {
        if (!filter_var($_POST["store_email_address"], FILTER_VALIDATE_EMAIL))
        {
            $error .= '<li>Invalid Email Address</li>';
        }
        else
        {
            $formdata['store_email_address'] = trim($_POST["store_email_address"]);
        }
    }

    if(empty($_POST["store_timezone"]))
    {
        $error .= '<li>Timezone is required</li>';
    }
    else
    {
        $formdata['store_timezone'] = trim($_POST["store_timezone"]);
    }

    if(empty($_POST["store_currency"]))
    {
        $error .= '<li>Currency is required</li>';
    }
    else
    {
        $formdata['store_currency'] = trim($_POST["store_currency"]);
    }

    if($error == '')
    {
        $data = array(
            ':store_name'           =>  $formdata["store_name"],
            ':store_address'        =>  $formdata["store_address"],
            ':store_contact_no'     =>  $formdata["store_contact_no"],
            ':store_email_address'  =>  $formdata["store_email_address"],
            ':store_timezone'       =>  $formdata["store_timezone"],
            ':store_currency'       =>  $formdata["store_currency"],
            ':store_updated_on'     =>  date('Y-m-d H:i:s'),
            ':store_id'             =>  $_POST["store_id"]
        );

        $object->query = "
        UPDATE store_msbs 
        SET store_name = :store_name, 
        store_address = :store_address, 
        store_contact_no = :store_contact_no, 
        store_email_address = :store_email_address, 
        store_timezone = :store_timezone, 
        store_currency = :store_currency, 
        store_updated_on = :store_updated_on 
        WHERE store_id = :store_id
        ";

        $object->execute($data);

        $message = '<div class="alert alert-success">Data Successfully Change</div>';
    }
}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Setting</h1>

                            <?php
                            foreach($result as $row)
                            {
                            ?>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Setting</li>
                            </ol>
                            <div class="row">
                                <div class="col-md-6">
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                echo $message;
                                ?>
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-user-edit"></i> Edit Medical Store Details
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="store_name" type="text" name="store_name" placeholder="Enter Medical Store Name" value="<?php echo $row['store_name']; ?>" />
                                                    <label for="store_name">Medical Store Name</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <textarea class="form-control" id="store_address" name="store_address" placeholder="Enter Medical Store Address"><?php echo $row['store_address']; ?></textarea>
                                                    <label for="store_address">Medical Store Address</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="store_contact_no" type="text" name="store_contact_no" placeholder="Enter Contact No." value="<?php echo $row['store_contact_no']; ?>" />
                                                    <label for="store_contact_no">Medical Store Contact No.</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="store_email_address" type="text" name="store_email_address" placeholder="Enter Email Address" value="<?php echo $row['store_email_address']; ?>" />
                                                    <label for="store_email_address">Medical Store Email Address</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <select class="form-control" id="store_timezone" name="store_timezone">
                                                        <?php echo $object->Timezone_list(); ?>
                                                    </select>
                                                    <label for="store_timezone">Timezone</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <select class="form-control" id="store_currency" name="store_currency">
                                                        <?php echo $object->Currency_list(); ?>
                                                    </select>
                                                    <label for="store_timezone">Currenecy</label>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                    <input type="hidden" name="store_id" value="<?php echo $row['store_id']; ?>" />
                                                    <input type="submit" name="submit" id="submit_button" class="btn btn-primary" value="submit" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.getElementById('store_timezone').value = "<?php echo $row['store_timezone']; ?>";
                                    document.getElementById('store_currency').value = "<?php echo html_entity_decode($row['store_currency']); ?>";
                                </script>
                        <?php                        
                            }
                        ?>

                            </div>
                        </div>

<?php

include('footer.php');

?>