<?php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
	header('location:login.php');
}

if(!$object->is_master_user())
{
    header('location:medicine_purchase.php');
}

include('header.php');

$object->query = "
    SELECT * FROM medicine_msbs 
    INNER JOIN category_msbs 
    ON category_msbs.category_id = medicine_msbs.medicine_category 
    INNER JOIN  medicine_manufacuter_company_msbs 
    ON  medicine_manufacuter_company_msbs.medicine_manufacuter_company_id = medicine_msbs.medicine_manufactured_by 
    INNER JOIN location_rack_msbs 
    ON location_rack_msbs.location_rack_id = medicine_msbs.medicine_location_rack 
    WHERE medicine_status = 'Enable' 
    AND medicine_available_quantity < medicine_pack_qty 
    ORDER BY medicine_msbs.medicine_name ASC
";

$medicine_result = $object->get_result();

?>

							<div class="container-fluid px-4">
		                        <h1 class="mt-4">Dashboard</h1>
		                        <ol class="breadcrumb mb-4">
		                            <li class="breadcrumb-item active">Dashboard</li>
		                        </ol>
		                        <div class="row">
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-primary text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->Get_total_no_of_medicine(); ?></h2>
		                                    	<h5 class="text-center">In Stock Medicine</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-warning text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->Count_outstock_medicine(); ?></h2>
		                                    	<h5 class="text-center">Out of Stock Medicine</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-danger text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->cur_sym . number_format(floatval($object->Get_total_medicine_purchase()), 2, '.', ','); ?></h2>
		                                    	<h5 class="text-center">Total Purchase</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-success text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->cur_sym . number_format(floatval($object->Get_total_medicine_sale()), 2, '.', ','); ?></h2>
		                                    	<h5 class="text-center">Total Sale</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            
		                        </div>

		                        <div class="row">
		                            <div class="col-xl-6">
		                                <div class="card mb-4">
		                                    <div class="card-header">
		                                        <i class="fas fa-chart-area me-1"></i>
		                                        Sale Status
		                                    </div>
		                                    <div class="card-body"><canvas id="saleChart" width="100%" height="40"></canvas></div>
		                                </div>
		                            </div>
		                            <div class="col-xl-6">
		                                <div class="card mb-4">
		                                    <div class="card-header">
		                                        <i class="fas fa-chart-bar me-1"></i>
		                                        No. of Medicine
		                                    </div>
		                                    <div class="card-body"><canvas id="stockChart" width="100%" height="40"></canvas></div>
		                                </div>
		                            </div>
		                        </div>

		                        <div class="card mb-4">
		                            <div class="card-header">
		                                <i class="fas fa-table me-1"></i>
		                                List of Out of Stock Medicine
		                            </div>
		                            <div class="card-body">
		                                <table id="datatablesSimple">
		                                    <thead>
		                                        <tr>
		                                            <th>Medicine Name</th>
	                                                <th>Company</th>
	                                                <th>Pack Detail</th>
	                                                <th>Available Quantity</th>
	                                                <th>Location Rack</th>
	                                                <th>Status</th>
	                                                <th>Added On</th>
	                                                <th>Updated On</th>
	                                                <th>Action</th>
		                                        </tr>
		                                    </thead>
		                                    <tfoot>
		                                        <tr>
		                                            <th>Medicine Name</th>
	                                                <th>Company</th>
	                                                <th>Pack Detail</th>
	                                                <th>Available Quantity</th>
	                                                <th>Location Rack</th>
	                                                <th>Status</th>
	                                                <th>Added On</th>
	                                                <th>Updated On</th>
	                                                <th>Action</th>
		                                        </tr>
		                                    </tfoot>
		                                    <tbody>
		                                    <?php

	                                        foreach($medicine_result as $row)
	                                        {
	                                            $medicine_status = '';
	                                            if($row["medicine_status"] == 'Enable')
	                                            {
	                                                $medicine_status = '<div class="badge bg-success">Enable</div>';
	                                            }
	                                            else
	                                            {
	                                                $medicine_status = '<div class="badge bg-danger">Disable</div>';
	                                            }
	                                            echo '
	                                            <tr>
	                                                <td>'.$row["medicine_name"].'</td>
	                                                <td>'.$row["company_name"].'</td>
	                                                <td>'.$row["medicine_pack_qty"].' '.$row["category_name"].'</td>
	                                                <td>'.$row["medicine_available_quantity"].'</td>
	                                                <td>'.$row["location_rack_name"].'</td>
	                                                <td>'.$medicine_status.'</td>
	                                                <td>'.$row["medicine_add_datetime"].'</td>
	                                                <td>'.$row["medicine_update_datetime"].'</td>
	                                                <td>
	                                                    <a href="medicine_purchase.php?action=add&code='.$object->convert_data("add").'&medicine='.$object->convert_data($row["medicine_id"]).'" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Purchase</a>
	                                                </td>
	                                            </tr>
	                                            ';
	                                        }

		                                    ?>
		                                    </tbody>
		                                </table>
		                            </div>

		                    </div>

<?php

include('footer.php');

?>
<?php 

$area_chart_data = $object->Get_last_fifteen_day_date();

$sale_data = $object->Get_last_fifteen_day_medicine_sale_data();

$month_data = $object->Get_last_six_month_name();

$stock_data = $object->Get_last_six_month_medicine_stock_data();



?>
<script>

// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Area Chart Example
var ctx = document.getElementById("saleChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?php echo json_encode(array_reverse($area_chart_data['month_date'])); ?>,
    datasets: [{
      label: "Medicine Sale",
      lineTension: 0.3,
      backgroundColor: "rgba(2,117,216,0.2)",
      borderColor: "rgba(2,117,216,1)",
      pointRadius: 5,
      pointBackgroundColor: "rgba(2,117,216,1)",
      pointBorderColor: "rgba(255,255,255,0.8)",
      pointHoverRadius: 5,
      pointHoverBackgroundColor: "rgba(2,117,216,1)",
      pointHitRadius: 50,
      pointBorderWidth: 2,
      data: <?php echo json_encode(array_reverse($sale_data)); ?>,
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          maxTicksLimit: 5
        },
        gridLines: {
          color: "rgba(0, 0, 0, .125)",
        }
      }],
    },
    legend: {
      display: false
    }
  }
});

var ctx1 = document.getElementById("stockChart");
var myLineChart1 = new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: <?php echo json_encode(array_reverse($month_data['month_name'])); ?>,
    datasets: [{
      label: "No of Medicine",
      backgroundColor: "rgba(2,117,216,1)",
      borderColor: "rgba(2,117,216,1)",
      data: <?php echo json_encode($stock_data); ?>,
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 6
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5
        },
        gridLines: {
          display: true
        }
      }],
    },
    legend: {
      display: false
    }
  }
});

</script>