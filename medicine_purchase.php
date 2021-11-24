<?php

//location_rack.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}

$where = '';

if(!$object->is_master_user())
{
    $where = "WHERE medicine_purchase_msbs.medicine_purchase_enter_by = '".$_SESSION["user_id"]."' ";
}

$object->query = "
    SELECT * FROM medicine_purchase_msbs 
    INNER JOIN medicine_msbs 
    ON medicine_msbs.medicine_id = medicine_purchase_msbs.medicine_id 
    INNER JOIN  supplier_msbs 
    ON  supplier_msbs.supplier_id = medicine_purchase_msbs.supplier_id 
    ".$where."
    ORDER BY medicine_purchase_msbs.medicine_purchase_id DESC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_medicine_purchase"]))
{
    $formdata = array();

    if(empty($_POST["medicine_id"]))
    {
        $error .= '<li>Medicine Name is required</li>';
    }
    else
    {
        $formdata['medicine_id'] = trim($_POST["medicine_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Supplier is required</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["medicine_batch_no"]))
    {
        $error .= '<li>Medicine Batch No. is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["medicine_batch_no"]))
        {
            $error .= '<li>Only letters and Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_batch_no'] = trim($_POST["medicine_batch_no"]);
        }
    }

    if(empty($_POST["medicine_purchase_qty"]))
    {
        $error .= '<li>Purchase Quantity is required</li>';
    }
    else
    {
        if (!preg_match("/^[0-9']*$/", $_POST["medicine_purchase_qty"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_purchase_qty'] = trim($_POST["medicine_purchase_qty"]);
        }
    }

    if(empty($_POST["medicine_purchase_price_per_unit"]))
    {
        $error .= '<li>Purchase Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_purchase_price_per_unit'] = trim($_POST["medicine_purchase_price_per_unit"]);
    }    

    /*if(empty($_POST["medicine_purchase_total_cost"]))
    {
        $error .= '<li>Purchase Total Cost is required</li>';
    }
    else
    {
        $formdata['medicine_purchase_total_cost'] = trim($_POST["medicine_purchase_total_cost"]);
    }*/

    if(empty($_POST["medicine_manufacture_month"]))
    {
        $error .= '<li>Manufacturing Month is required</li>';
    }
    else
    {
        $formdata['medicine_manufacture_month'] = trim($_POST["medicine_manufacture_month"]);
    }

    if(empty($_POST["medicine_manufacture_year"]))
    {
        $error .= '<li>Manufacturing Year is required</li>';
    }
    else
    {
        $formdata['medicine_manufacture_year'] = trim($_POST["medicine_manufacture_year"]);
    }

    if(empty($_POST["medicine_expired_month"]))
    {
        $error .= '<li>Expired Month is required</li>';
    }
    else
    {
        $formdata['medicine_expired_month'] = trim($_POST["medicine_expired_month"]);
    }

    if(empty($_POST["medicine_expired_year"]))
    {
        $error .= '<li>Expired Year is required</li>';
    }
    else
    {
        $formdata['medicine_expired_year'] = trim($_POST["medicine_expired_year"]);
    }

    if(empty($_POST["medicine_sale_price_per_unit"]))
    {
        $error .= '<li>Sale Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_sale_price_per_unit'] = trim($_POST["medicine_sale_price_per_unit"]);
    }

    if($error == '')
    {
        $total_cost = floatval($formdata['medicine_purchase_qty']) * floatval($formdata['medicine_purchase_price_per_unit']);
        $data = array(
            ':medicine_id'                      =>  $formdata['medicine_id'],
            ':supplier_id'                      =>  $formdata['supplier_id'],
            ':medicine_batch_no'                =>  $formdata['medicine_batch_no'],
            ':medicine_purchase_qty'            =>  $formdata['medicine_purchase_qty'], 
            ':available_quantity'               =>  $formdata['medicine_purchase_qty'], 
            ':medicine_purchase_price_per_unit' =>  $formdata['medicine_purchase_price_per_unit'],
            ':medicine_purchase_total_cost'     =>  $total_cost,
            ':medicine_manufacture_month'       =>  $formdata['medicine_manufacture_month'],
            ':medicine_manufacture_year'        =>  $formdata['medicine_manufacture_year'],
            ':medicine_expired_month'           =>  $formdata['medicine_expired_month'],
            ':medicine_expired_year'            =>  $formdata['medicine_expired_year'],
            ':medicine_sale_price_per_unit'     =>  $formdata['medicine_sale_price_per_unit'],
            ':medicine_purchase_enter_by'       =>  $_SESSION["user_id"],
            ':medicine_purchase_datetime'       =>  $object->now,
            ':medicine_purchase_status'         =>  'Enable'
        );

        $object->query = "
        INSERT INTO medicine_purchase_msbs 
        (medicine_id, supplier_id, medicine_batch_no, medicine_purchase_qty, available_quantity, medicine_purchase_price_per_unit, medicine_purchase_total_cost, medicine_manufacture_month, medicine_manufacture_year, medicine_expired_month, medicine_expired_year, medicine_sale_price_per_unit, medicine_purchase_enter_by, medicine_purchase_datetime, medicine_purchase_status) 
        VALUES (:medicine_id, :supplier_id, :medicine_batch_no, :medicine_purchase_qty, :available_quantity, :medicine_purchase_price_per_unit, :medicine_purchase_total_cost, :medicine_manufacture_month, :medicine_manufacture_year, :medicine_expired_month, :medicine_expired_year, :medicine_sale_price_per_unit, :medicine_purchase_enter_by, :medicine_purchase_datetime, :medicine_purchase_status)
            ";

        $object->execute($data);

        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity + ".$formdata['medicine_purchase_qty']." 
        WHERE medicine_id = '".$formdata['medicine_id']."'
        ";

        $object->execute();

        header('location:medicine_purchase.php?msg=add');
    }
}

if(isset($_POST["edit_medicine_purchase"]))
{
    $formdata = array();

    if(empty($_POST["medicine_id"]))
    {
        $error .= '<li>Medicine Name is required</li>';
    }
    else
    {
        $formdata['medicine_id'] = trim($_POST["medicine_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Supplier is required</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["medicine_batch_no"]))
    {
        $error .= '<li>Medicine Batch No. is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["medicine_batch_no"]))
        {
            $error .= '<li>Only letters and Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_batch_no'] = trim($_POST["medicine_batch_no"]);
        }
    }

    if(empty($_POST["medicine_purchase_qty"]))
    {
        $error .= '<li>Purchase Quantity is required</li>';
    }
    else
    {
        if (!preg_match("/^[0-9']*$/", $_POST["medicine_purchase_qty"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_purchase_qty'] = trim($_POST["medicine_purchase_qty"]);
        }
    }

    if(empty($_POST["medicine_purchase_price_per_unit"]))
    {
        $error .= '<li>Purchase Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_purchase_price_per_unit'] = trim($_POST["medicine_purchase_price_per_unit"]);
    }    

    /*if(empty($_POST["medicine_purchase_total_cost"]))
    {
        $error .= '<li>Purchase Total Cost is required</li>';
    }
    else
    {
        $formdata['medicine_purchase_total_cost'] = trim($_POST["medicine_purchase_total_cost"]);
    }*/

    if(empty($_POST["medicine_manufacture_month"]))
    {
        $error .= '<li>Manufacturing Month is required</li>';
    }
    else
    {
        $formdata['medicine_manufacture_month'] = trim($_POST["medicine_manufacture_month"]);
    }

    if(empty($_POST["medicine_manufacture_year"]))
    {
        $error .= '<li>Manufacturing Year is required</li>';
    }
    else
    {
        $formdata['medicine_manufacture_year'] = trim($_POST["medicine_manufacture_year"]);
    }

    if(empty($_POST["medicine_expired_month"]))
    {
        $error .= '<li>Expired Month is required</li>';
    }
    else
    {
        $formdata['medicine_expired_month'] = trim($_POST["medicine_expired_month"]);
    }

    if(empty($_POST["medicine_expired_year"]))
    {
        $error .= '<li>Expired Year is required</li>';
    }
    else
    {
        $formdata['medicine_expired_year'] = trim($_POST["medicine_expired_year"]);
    }

    if(empty($_POST["medicine_sale_price_per_unit"]))
    {
        $error .= '<li>Sale Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_sale_price_per_unit'] = trim($_POST["medicine_sale_price_per_unit"]);
    }

    if($error == '')
    {
        $medicine_purchase_id = $object->convert_data(trim($_POST["medicine_purchase_id"]), 'decrypt');

        $object->query = "
        SELECT medicine_purchase_qty FROM medicine_purchase_msbs 
        WHERE medicine_purchase_id = '".$medicine_purchase_id."'
        ";

        $temp_result = $object->get_result();

        $medicine_purchase_qty = 0;

        foreach($temp_result as $temp_row)
        {
            $medicine_purchase_qty = $temp_row["medicine_purchase_qty"];
        }

        $total_cost = floatval($formdata['medicine_purchase_qty']) * floatval($formdata['medicine_purchase_price_per_unit']);

        $data = array(
            ':medicine_id'                      =>  $formdata['medicine_id'],
            ':supplier_id'                      =>  $formdata['supplier_id'],
            ':medicine_batch_no'                =>  $formdata['medicine_batch_no'],
            ':medicine_purchase_qty'            =>  $formdata['medicine_purchase_qty'],
            ':available_quantity'               =>  $formdata['medicine_purchase_qty'], 
            ':medicine_purchase_price_per_unit' =>  $formdata['medicine_purchase_price_per_unit'],
            ':medicine_purchase_total_cost'     =>  $total_cost,
            ':medicine_manufacture_month'       =>  $formdata['medicine_manufacture_month'],
            ':medicine_manufacture_year'        =>  $formdata['medicine_manufacture_year'],
            ':medicine_expired_month'           =>  $formdata['medicine_expired_month'],
            ':medicine_expired_year'            =>  $formdata['medicine_expired_year'], 
            ':medicine_sale_price_per_unit'     =>  $formdata['medicine_sale_price_per_unit'],
            ':medicine_purchase_id'             =>  $medicine_purchase_id
        );

        $object->query = "
            UPDATE medicine_purchase_msbs 
            SET medicine_id = :medicine_id, 
            supplier_id = :supplier_id,
            medicine_batch_no = :medicine_batch_no, 
            medicine_purchase_qty = :medicine_purchase_qty, 
            available_quantity = :available_quantity, 
            medicine_purchase_price_per_unit = :medicine_purchase_price_per_unit, 
            medicine_purchase_total_cost = :medicine_purchase_total_cost, 
            medicine_manufacture_month = :medicine_manufacture_month, 
            medicine_manufacture_year = :medicine_manufacture_year, 
            medicine_expired_month = :medicine_expired_month, 
            medicine_expired_year = :medicine_expired_year, 
            medicine_sale_price_per_unit = :medicine_sale_price_per_unit  
            WHERE medicine_purchase_id = :medicine_purchase_id
            ";

        $object->execute($data);

        if($medicine_purchase_qty != $formdata['medicine_purchase_qty'])
        {
            $final_update_qty = 0;
            if($medicine_purchase_qty > $formdata['medicine_purchase_qty'])
            {
                $final_update_qty = $medicine_purchase_qty - $formdata['medicine_purchase_qty'];

                $object->query = "
                UPDATE medicine_msbs 
                SET medicine_available_quantity = medicine_available_quantity - ".$final_update_qty." 
                WHERE medicine_id = '".$formdata['medicine_id']."'
                ";
            }
            else
            {
                $final_update_qty = $formdata['medicine_purchase_qty'] - $medicine_purchase_qty;

                $object->query = "
                UPDATE medicine_msbs 
                SET medicine_available_quantity = medicine_available_quantity + ".$final_update_qty." 
                WHERE medicine_id = '".$formdata['medicine_id']."'
                ";
            }

            $object->execute();
        }

        header('location:medicine_purchase.php?msg=edit');
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $medicine_purchase_id = $object->convert_data(trim($_GET["code"]), 'decrypt');

    $medicine_id = $object->convert_data(trim($_GET["id"]), 'decrypt');

    $object->query = "
    SELECT medicine_purchase_qty FROM medicine_purchase_msbs 
    WHERE medicine_purchase_id = '".$medicine_purchase_id."'
    ";

    $temp_result = $object->get_result();

    $medicine_purchase_qty = 0;

    foreach($temp_result as $temp_row)
    {
        $medicine_purchase_qty = $temp_row["medicine_purchase_qty"];
    }

    $status = trim($_GET["status"]);
    $data = array(
        ':medicine_purchase_status'      =>  $status,
        ':medicine_purchase_id'          =>  $medicine_purchase_id
    );

    $object->query = "
    UPDATE medicine_purchase_msbs 
    SET medicine_purchase_status = :medicine_purchase_status 
    WHERE medicine_purchase_id = :medicine_purchase_id
    ";

    $object->execute($data);

    if($status == 'Disable')
    {
        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity - ".$medicine_purchase_qty." 
        WHERE medicine_id = '".$medicine_id."'
        ";
    }
    else
    {
        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity + ".$medicine_purchase_qty." 
        WHERE medicine_id = '".$medicine_id."'
        ";
    }

    $object->execute();

    header('location:medicine_purchase.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Medicine Purchase Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="medicine_purchase.php">Medicine Purchase Management</a></li>
                                <li class="breadcrumb-item active">Add Medicine Purchase</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> Add Medicine Purchase
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="medicine_id" class="form-control" id="medicine_id">
                                                        <?php echo $object->fill_medicine(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["medicine_id"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("medicine_id").value = "'.$_POST["medicine_id"].'"
                                                        </script>
                                                        ';
                                                    }

                                                    if(isset($_GET["medicine"]))
                                                    {
                                                        $medicine_id = $object->convert_data(trim($_GET["medicine"]), 'decrypt');
                                                        echo '
                                                        <script>
                                                        document.getElementById("medicine_id").value = "'.$medicine_id.'"
                                                        </script>
                                                        ';
                                                    }

                                                    ?>
                                                    <label for="medicine_id">Medicine Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="supplier_id" class="form-control" id="supplier_id">
                                                        <?php echo $object->fill_supplier(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["supplier_id"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("supplier_id").value = "'.$_POST["supplier_id"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    <label for="supplier_id">Supplier Name</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_purchase_qty" type="number" placeholder="Enter Quantity" name="medicine_purchase_qty" value="<?php if(isset($_POST["medicine_purchase_qty"])) echo $_POST["medicine_purchase_qty"]; ?>" />
                                                    <label for="medicine_purchase_qty">Medicine Quantity</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_purchase_price_per_unit" type="number" placeholder="Enter Purchase Price per Unit" name="medicine_purchase_price_per_unit" step=".01" value="<?php if(isset($_POST["medicine_purchase_price_per_unit"])) echo $_POST["medicine_purchase_price_per_unit"]; ?>" />
                                                    <label for="medicine_purchase_price_per_unit">Purchase Price per Unit</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="medicine_manufacture_month" class="form-control" id="medicine_manufacture_month">
                                                                <option value="">Select</option>
                                                                <option value="01">January</option>
                                                                <option value="02">February</option>
                                                                <option value="03">March</option>
                                                                <option value="04">April</option>
                                                                <option value="05">May</option>
                                                                <option value="06">June</option>
                                                                <option value="07">July</option>
                                                                <option value="08">August</option>
                                                                <option value="09">September</option>
                                                                <option value="10">October</option>
                                                                <option value="11">November</option>
                                                                <option value="12">December</option>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["medicine_manufacture_month"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("medicine_manufacture_month").value = "'.$_POST["medicine_manufacture_month"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="medicine_manufacture_month">Mfg. Month</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="medicine_manufacture_year" class="form-control" id="medicine_manufacture_year">
                                                                <option value="">Select</option>
                                                                <?php 
                                                                for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                {
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["medicine_manufacture_year"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("medicine_manufacture_year").value = "'.$_POST["medicine_manufacture_year"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="medicine_manufacture_year">Mfg. Year</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="medicine_expired_month" class="form-control" id="medicine_expired_month">
                                                                <option value="">Select</option>
                                                                <option value="01">January</option>
                                                                <option value="02">February</option>
                                                                <option value="03">March</option>
                                                                <option value="04">April</option>
                                                                <option value="05">May</option>
                                                                <option value="06">June</option>
                                                                <option value="07">July</option>
                                                                <option value="08">August</option>
                                                                <option value="09">September</option>
                                                                <option value="10">October</option>
                                                                <option value="11">November</option>
                                                                <option value="12">December</option>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["medicine_expired_month"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("medicine_expired_month").value = "'.$_POST["medicine_expired_month"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="medicine_expired_month">Expiry Month</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="medicine_expired_year" class="form-control" id="medicine_expired_year">
                                                                <option value="">Select</option>
                                                                <?php 
                                                                for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                {
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["medicine_expired_year"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("medicine_expired_year").value = "'.$_POST["medicine_expired_year"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="medicine_expired_year">Expiry Year</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_batch_no" type="text" placeholder="Enter Batch Number" name="medicine_batch_no" value="<?php if(isset($_POST["medicine_batch_no"])) echo $_POST["medicine_batch_no"]; ?>" />
                                                    <label for="medicine_batch_no">Medicine Batch No.</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_sale_price_per_unit" type="number" placeholder="Enter Sale Price per Unit" name="medicine_sale_price_per_unit" step=".01" value="<?php if(isset($_POST["medicine_sale_price_per_unit"])) echo $_POST["medicine_sale_price_per_unit"]; ?>" />
                                                    <label for="medicine_sale_price_per_unit">Sale Price per Unit</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="add_medicine_purchase" class="btn btn-success" value="Add" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $medicine_purchase_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($medicine_purchase_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM medicine_purchase_msbs 
                                    WHERE medicine_purchase_id = '$medicine_purchase_id'
                                    ";

                                    $medicine_purchase_result = $object->get_result();

                                    foreach($medicine_purchase_result as $medicine_purchase_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="medicine_purchase.php">Medicine Purchase Management</a></li>
                                    <li class="breadcrumb-item active">Edit Medicine Purchase Data</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Edit Medicine Purchase Data
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="medicine_id" class="form-control" id="medicine_id">
                                                        <?php echo $object->fill_medicine(); ?>
                                                        </select>
                                                        <label for="medicine_id">Medicine Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="supplier_id" class="form-control" id="supplier_id">
                                                        <?php echo $object->fill_supplier(); ?>
                                                        </select>
                                                        <label for="supplier_id">Supplier Name</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <div class="form-floating mb-3">
                                                            <input class="form-control" id="medicine_purchase_qty" type="number" placeholder="Enter Quantity" name="medicine_purchase_qty" value="<?php echo $medicine_purchase_row["medicine_purchase_qty"]; ?>" />
                                                            <label for="medicine_purchase_qty">Medicine Quantity</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="medicine_purchase_price_per_unit" type="number" placeholder="Enter Purchase Price per Unit" name="medicine_purchase_price_per_unit" step=".01" value="<?php echo $medicine_purchase_row["medicine_purchase_price_per_unit"]; ?>" />
                                                        <label for="medicine_purchase_price_per_unit">Purchase Price per Unit</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="medicine_manufacture_month" class="form-control" id="medicine_manufacture_month">
                                                                    <option value="">Select</option>
                                                                    <option value="01">January</option>
                                                                    <option value="02">February</option>
                                                                    <option value="03">March</option>
                                                                    <option value="04">April</option>
                                                                    <option value="05">May</option>
                                                                    <option value="06">June</option>
                                                                    <option value="07">July</option>
                                                                    <option value="08">August</option>
                                                                    <option value="09">September</option>
                                                                    <option value="10">October</option>
                                                                    <option value="11">November</option>
                                                                    <option value="12">December</option>
                                                                </select>
                                                                <label for="medicine_manufacture_month">Mfg. Month</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="medicine_manufacture_year" class="form-control" id="medicine_manufacture_year">
                                                                    <option value="">Select</option>
                                                                    <?php 
                                                                    for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                    {
                                                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="medicine_manufacture_year">Mfg. Year</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="medicine_expired_month" class="form-control" id="medicine_expired_month">
                                                                    <option value="">Select</option>
                                                                    <option value="01">January</option>
                                                                    <option value="02">February</option>
                                                                    <option value="03">March</option>
                                                                    <option value="04">April</option>
                                                                    <option value="05">May</option>
                                                                    <option value="06">June</option>
                                                                    <option value="07">July</option>
                                                                    <option value="08">August</option>
                                                                    <option value="09">September</option>
                                                                    <option value="10">October</option>
                                                                    <option value="11">November</option>
                                                                    <option value="12">December</option>
                                                                </select>
                                                                <label for="medicine_expired_month">Expiry Month</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="medicine_expired_year" class="form-control" id="medicine_expired_year">
                                                                    <option value="">Select</option>
                                                                    <?php 
                                                                    for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                    {
                                                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="medicine_expired_year">Expiry Year</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="medicine_batch_no" type="text" placeholder="Enter Batch Number" name="medicine_batch_no" value="<?php echo $medicine_purchase_row["medicine_batch_no"]; ?>" />
                                                        <label for="medicine_batch_no">Medicine Batch No.</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="medicine_sale_price_per_unit" type="number" placeholder="Enter Sale Price per Unit" name="medicine_sale_price_per_unit" step=".01" value="<?php echo $medicine_purchase_row["medicine_sale_price_per_unit"]; ?>" />
                                                        <label for="medicine_sale_price_per_unit">Sale Price per Unit</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="medicine_purchase_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_medicine_purchase" class="btn btn-primary" value="Edit" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <script>
                                document.getElementById('medicine_id').value = "<?php echo $medicine_purchase_row["medicine_id"]; ?>";
                                document.getElementById('supplier_id').value = "<?php echo $medicine_purchase_row["supplier_id"]; ?>";
                                document.getElementById('medicine_manufacture_month').value = "<?php echo $medicine_purchase_row["medicine_manufacture_month"]; ?>";
                                document.getElementById('medicine_manufacture_year').value = "<?php echo $medicine_purchase_row["medicine_manufacture_year"]; ?>";
                                document.getElementById('medicine_manufacture_month').value = "<?php echo $medicine_purchase_row["medicine_manufacture_month"]; ?>";
                                document.getElementById('medicine_expired_month').value = "<?php echo $medicine_purchase_row["medicine_expired_month"]; ?>";
                                document.getElementById('medicine_expired_year').value = "<?php echo $medicine_purchase_row["medicine_expired_year"]; ?>";
                                </script>
                                
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
                                <li class="breadcrumb-item active">Medicine Purchase Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Medicine Purchase Detail Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Medicine Purchase Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Medicine Purchase Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Medicine Purchase Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Medicine Purchase Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="medicine_purchase.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>Medicine Name</th>
                                                <th>Batch No.</th>
                                                <th>Supplier</th>
                                                <th>Quantity</th>
                                                <th>Available Qty.</th>
                                                <th>Price per Unit</th>
                                                <th>Total Cost</th>
                                                <th>Mfg. Date</th>
                                                <th>Expiry Date</th>
                                                <th>Sale Price</th>
                                                <th>Purchase Date</th>
                                                <th>Status</th>
                                                <!--<th>Added On</th>
                                                <th>Updated On</th>!-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Medicine Name</th>
                                                <th>Batch No.</th>
                                                <th>Supplier</th>
                                                <th>Quantity</th>
                                                <th>Available Qty.</th>
                                                <th>Price per Unit</th>
                                                <th>Total Cost</th>
                                                <th>Mfg. Date</th>
                                                <th>Expiry Date</th>
                                                <th>Sale Price</th>
                                                <th>Purchase Date</th>
                                                <th>Status</th>
                                                <!--<th>Added On</th>
                                                <th>Updated On</th>!-->
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                        foreach($result as $row)
                                        {
                                            $medicine_purchase_status = '';
                                            if($row["medicine_purchase_status"] == 'Enable')
                                            {
                                                $medicine_purchase_status = '<div class="badge bg-success">Enable</div>';
                                            }
                                            else
                                            {
                                                $medicine_purchase_status = '<div class="badge bg-danger">Disable</div>';
                                            }
                                            echo '
                                            <tr>
                                                <td>'.$row["medicine_name"].'</td>
                                                <td>'.$row["medicine_batch_no"].'</td>
                                                <td>'.$row["supplier_name"].'</td>
                                                <td>'.$row["medicine_purchase_qty"].'</td>
                                                <td>'.$row["available_quantity"].'</td>
                                                <td>'.$object->cur_sym . $row["medicine_purchase_price_per_unit"].'</td>
                                                <td>'.$object->cur_sym . $row["medicine_purchase_total_cost"].'</td>
                                                <td>'.$row["medicine_manufacture_month"].'/'.$row["medicine_manufacture_year"].'</td>
                                                <td>'.$row["medicine_expired_month"].'/'.$row["medicine_expired_year"].'</td>
                                                <td>'.$object->cur_sym . $row["medicine_sale_price_per_unit"].'</td>
                                                <td>'.$row["medicine_purchase_datetime"].'</td>
                                                <td>'.$medicine_purchase_status.'</td>
                                                <td>
                                                    <a href="medicine_purchase.php?action=edit&code='.$object->convert_data($row["medicine_purchase_id"]).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                    <button type="button" name="delete_button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["medicine_purchase_id"]).'`, `'.$row["medicine_purchase_status"].'`, `'.$object->convert_data($row["medicine_id"]).'`);"><i class="fas fa-times"></i></button>
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
                            

                            function delete_data(code, status, id)
                            {
                                var new_status = 'Enable';
                                if(status == 'Enable')
                                {
                                    new_status = 'Disable';
                                }
                                if(confirm("Are you sure you want to "+new_status+" this Medicine Purchase Details?"))
                                {
                                    window.location.href="medicine_purchase.php?action=delete&code="+code+"&status="+new_status+"&id="+id+"";
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