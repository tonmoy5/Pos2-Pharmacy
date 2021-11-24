<?php

//user.php

include('class/db.php');

$object = new db();

if (!$object->is_login()) {
    header('location:login.php');
}

if (!$object->is_master_user()) {
    header('location:index.php');
}

$object->query = "
    SELECT * FROM supplier_msbs 
    ORDER BY supplier_name ASC
";

$result = $object->get_result();

$message = '';

$error = '';

if (isset($_POST["add_supplier"])) {
    $formdata = array();

    if (empty($_POST["supplier_name"])) {
        $error .= '<li>Supplier Name is required</li>';
    } else {
        $formdata['supplier_name'] = $object->clean_input($_POST["supplier_name"]);
    }

    if (empty($_POST["supplier_address"])) {
        $error .= '<li>Supplier Address is required</li>';
    } else {
        $formdata['supplier_address'] = $object->clean_input($_POST["supplier_address"]);
    }

    if (empty($_POST["supplier_contact_no"])) {
        $error .= '<li>Supplier Contact Number is required</li>';
    } else {
        $formdata['supplier_contact_no'] = $object->clean_input($_POST["supplier_contact_no"]);
        // if(!preg_match('/^[0-9]{10}+$/', $_POST["supplier_contact_no"]))
        // {
        //     $error .= '<li>Invalid Supplier Contact Number</li>';
        // }
        // else
        // {
        // }
    }

    if (empty($_POST["supplier_email"])) {
        $error .= '<li>Supplier Email is required</li>';
    } else {
        if (!filter_var($_POST["supplier_email"], FILTER_VALIDATE_EMAIL)) {
            $error .= '<li>Invalid Supplier Email Address</li>';
        } else {
            $formdata['supplier_email'] = trim($_POST["supplier_email"]);
        }
    }

    if ($error == '') {
        $object->query = "
        SELECT * FROM supplier_msbs 
        WHERE supplier_email = '" . $formdata['supplier_email'] . "'
        ";

        $object->execute();

        if ($object->row_count() > 0) {
            $error = '<li>Supplier Already Exists</li>';
        } else {
            $data = array(
                ':supplier_name'        =>  $formdata['supplier_name'],
                ':supplier_address'     =>  $formdata['supplier_address'],
                ':supplier_contact_no'  =>  $formdata['supplier_contact_no'],
                ':supplier_email'       =>  $formdata['supplier_email'],
                ':supplier_status'      =>  'Enable',
                ':supplier_datetime'    =>  $object->now
            );

            $object->query = "
            INSERT INTO supplier_msbs 
            (supplier_name, supplier_address, supplier_contact_no, supplier_email, supplier_status, supplier_datetime) 
            VALUES (:supplier_name, :supplier_address, :supplier_contact_no, :supplier_email, :supplier_status, :supplier_datetime)
            ";

            $object->execute($data);

            header('location:supplier.php?msg=add');
        }
    }
}

if (isset($_POST["edit_supplier"])) {
    $formdata = array();

    if (empty($_POST["supplier_name"])) {
        $error .= '<li>Supplier Name is required</li>';
    } else {
        $formdata['supplier_name'] = $object->clean_input($_POST["supplier_name"]);
    }

    if (empty($_POST["supplier_address"])) {
        $error .= '<li>Supplier Address is required</li>';
    } else {
        $formdata['supplier_address'] = $object->clean_input($_POST["supplier_address"]);
    }

    if (empty($_POST["supplier_contact_no"])) {
        $error .= '<li>Supplier Contact Number is required</li>';
    } else {
        if (!preg_match('/^[0-9]{10}+$/', $_POST["supplier_contact_no"])) {
            $error .= '<li>Invalid Supplier Contact Number</li>';
        } else {
            $formdata['supplier_contact_no'] = $object->clean_input($_POST["supplier_contact_no"]);
        }
    }

    if (empty($_POST["supplier_email"])) {
        $error .= '<li>Supplier Email is required</li>';
    } else {
        if (!filter_var($_POST["supplier_email"], FILTER_VALIDATE_EMAIL)) {
            $error .= '<li>Invalid Supplier Email Address</li>';
        } else {
            $formdata['supplier_email'] = trim($_POST["supplier_email"]);
        }
    }

    if ($error == '') {
        $supplier_id = $object->convert_data(trim($_POST["supplier_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM supplier_msbs 
        WHERE supplier_email = '" . $formdata['supplier_email'] . "' 
        AND supplier_id != '" . $supplier_id . "'
        ";

        $object->execute();

        if ($object->row_count() > 0) {
            $error = '<li>Supplier Already Exists</li>';
        } else {
            $data = array(
                ':supplier_name'        =>  $formdata['supplier_name'],
                ':supplier_address'     =>  $formdata['supplier_address'],
                ':supplier_contact_no'  =>  $formdata['supplier_contact_no'],
                ':supplier_email'       =>  $formdata['supplier_email'],
                ':supplier_id'          =>  $supplier_id
            );

            $object->query = "
            UPDATE supplier_msbs 
            SET supplier_name = :supplier_name, 
            supplier_address = :supplier_address, 
            supplier_contact_no = :supplier_contact_no, 
            supplier_email = :supplier_email 
            WHERE supplier_id = :supplier_id
            ";

            $object->execute($data);

            header('location:supplier.php?msg=edit');
        }
    }
}


if (isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete') {
    $supplier_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':supplier_status'      =>  $status,
        ':supplier_id'          =>  $supplier_id
    );

    $object->query = "
    UPDATE supplier_msbs 
    SET supplier_status = :supplier_status 
    WHERE supplier_id = :supplier_id
    ";

    $object->execute($data);

    header('location:supplier.php?msg=' . strtolower($status) . '');
}


include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Supplier Management</h1>

    <?php
    if (isset($_GET["action"], $_GET["code"])) {
        if ($_GET["action"] == 'add') {
    ?>

            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="supplier.php">Supplier Management</a></li>
                <li class="breadcrumb-item active">Add New Supplier</li>
            </ol>
            <div class="row">
                <div class="col-md-6">
                    <?php
                    if (isset($error) && $error != '') {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    }
                    ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user-plus"></i> Add New Supplier
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="supplier_name" type="text" placeholder="Enter Supplier Name" name="supplier_name" value="<?php if (isset($_POST["supplier_name"])) echo $_POST["supplier_name"]; ?>" />
                                    <label for="supplier_name">Supplier Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="supplier_address" placeholder="Enter Supplier Address" name="supplier_address"><?php if (isset($_POST["supplier_address"])) echo $_POST["supplier_address"]; ?></textarea>
                                    <label for="supplier_address">Address</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="supplier_contact_no" type="text" placeholder="Enter Supplier Contact Number" name="supplier_contact_no" value="<?php if (isset($_POST["supplier_contact_no"])) echo $_POST["supplier_contact_no"]; ?>" />
                                    <label for="supplier_contact_no">Contact No.</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="supplier_email" type="text" placeholder="Enter Supplier Email Address" name="supplier_email" value="<?php if (isset($_POST["supplier_email"])) echo $_POST["supplier_email"]; ?>" />
                                    <label for="supplier_email">Email Address</label>
                                </div>
                                <div class="mt-4 mb-0">
                                    <input type="submit" name="add_supplier" class="btn btn-success" value="Add" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else if ($_GET["action"] == 'edit') {
            $supplier_id = $object->convert_data(trim($_GET["code"]), 'decrypt');

            if ($supplier_id > 0) {
                $object->query = "
                                    SELECT * FROM supplier_msbs 
                                    WHERE supplier_id = '$supplier_id'
                                    ";

                $supplier_result = $object->get_result();

                foreach ($supplier_result as $supplier_row) {
            ?>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="supplier.php">Supplier Management</a></li>
                        <li class="breadcrumb-item active">Edit Supplier Data</li>
                    </ol>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            if (isset($error) && $error != '') {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-edit"></i> Edit Supplier Data
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="supplier_name" type="text" placeholder="Enter Supplier Name" name="supplier_name" value="<?php echo $supplier_row["supplier_name"]; ?>" />
                                            <label for="supplier_name">Supplier Name</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" id="supplier_address" placeholder="Enter Supplier Address" name="supplier_address"><?php echo $supplier_row["supplier_address"]; ?></textarea>
                                            <label for="supplier_address">Address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="supplier_contact_no" type="text" placeholder="Enter Supplier Contact Number" name="supplier_contact_no" value="<?php echo $supplier_row["supplier_contact_no"]; ?>" />
                                            <label for="supplier_contact_no">Contact No.</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="supplier_email" type="text" placeholder="Enter Supplier Email Address" name="supplier_email" value="<?php echo $supplier_row["supplier_email"]; ?>" />
                                            <label for="supplier_email">Email Address</label>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="hidden" name="supplier_id" value="<?php echo trim($_GET["code"]); ?>" />
                                            <input type="submit" name="edit_supplier" class="btn btn-primary" value="Edit" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
        <?php
                }
            } else {
                echo '<div class="alert alert-info">Something Went Wrong</div>';
            }
        } else {
            echo '<div class="alert alert-info">Something Went Wrong</div>';
        }
        ?>

    <?php
    } else {
    ?>

        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Suplier Management</li>
        </ol>

        <?php

        if (isset($_GET["msg"])) {
            if ($_GET["msg"] == 'add') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Suplier Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            if ($_GET["msg"] == 'edit') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Supplier Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            if ($_GET["msg"] == 'disable') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Supplier Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            if ($_GET["msg"] == 'enable') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Supplier Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }

        ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Supplier Management
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="supplier.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        foreach ($result as $row) {
                            $supplier_status = '';
                            if ($row["supplier_status"] == 'Enable') {
                                $supplier_status = '<div class="badge bg-success">Enable</div>';
                            } else {
                                $supplier_status = '<div class="badge bg-danger">Disable</div>';
                            }
                            echo '
                                            <tr>
                                                <td>' . html_entity_decode($row["supplier_name"]) . '</td>
                                                <td>' . html_entity_decode($row["supplier_address"]) . '</td>
                                                <td>' . $row["supplier_contact_no"] . '</td>
                                                <td>' . $row["supplier_email"] . '</td>
                                                <td>' . $supplier_status . '</td>
                                                <td>' . $row["supplier_datetime"] . '</td>
                                                <td>
                                                    <a href="supplier.php?action=edit&code=' . $object->convert_data($row["supplier_id"]) . '" class="btn btn-sm btn-primary">Edit</a>
                                                    <button type="button" name="delete_button" class="btn btn-danger btn-sm" onclick="delete_data(`' . $object->convert_data($row["supplier_id"]) . '`, `' . $row["supplier_status"] . '`); ">Delete</button>
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
            function delete_data(code, status) {
                var new_status = 'Enable';
                if (status == 'Enable') {
                    new_status = 'Disable';
                }
                if (confirm("Are you sure you want to " + new_status + " this Location Rack?")) {
                    window.location.href = "supplier.php?action=delete&code=" + code + "&status=" + new_status + "";
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