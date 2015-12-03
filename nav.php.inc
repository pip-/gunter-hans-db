		<?php
include("../secure/db.php");

if($_SERVER['HTTPS'] != 'on') {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
        ?>
		<nav class="navbar navbar-default">
		  <div class="container-fluid">
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" aria-expanded="false"></button>
		      <a class="navbar-brand" href="index.php">Gunter Hans</a>
		    </div>

		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav">
		        <li class="<?php echo $isSaleSearch ?>"><a href="sale_search.php">Sale Search</a></li>
		        <li class="<?php echo $isForcasting ?>"><a href="forecasting.php">Forecasting</a></li>
		        <li class="dropdown <?php echo $isDiscount ?>">
		          <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Discount <span class="caret"></span></a>
		          <ul class="dropdown-menu">
		            <li><a href="discount_redeemed.php">Discount Redeemed</a></li>
		            <li><a href="discount_sales.php">Discount vs Sales</a></li>
		            <li><a href="discount_dep.php">Departments and Categories</a></li>
		          </ul>
		        </li>
		        <li class="dropdown <?php echo $isTips ?>">
		          <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Tips <span class="caret"></span></a>
		          <ul class="dropdown-menu">
		            <li><a href="tips_search.php">Tips Search</a></li>
		            <li><a href="tips_employee.php">Tips per Employee</a></li>
		            <li><a href="tips_average.php">Average Tips</a></li>
		          </ul>
		        </li>
		        <li class="dropdown <?php echo $isAdvanced ?>"><a href="advanced.php">Advanced Report</a></li>
                <li class="dropdown <?php echo $isUpload ?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Upload<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="parse_transaction.php">Basic Transaction</a></li>
                        <li><a href="parse_detailed_transaction.php">Detailed Transaction</a></li>
                    </ul>
                </li>
		      </ul>
		      <?php
		      	if (isset($_SESSION['username'])) {
		      		$template = '
		      			<ul class="nav navbar-nav navbar-right">
		        			<li class="dropdown">
					          	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">%s <span class="caret"></span></a>
					          	<ul class="dropdown-menu">
						            <li><a href="index.php?logout=1">Logout</a></li>
					          	</ul>
				        	</li>
		      			</ul>';
		      		echo sprintf($template, substr($_SESSION['username'], 0, -1));
		      	} else {
		      		echo '
		      			<ul class="nav navbar-nav navbar-right">
		        			<li><a href="index.php">Login</a></li>
		      			</ul>';
		      	}
		      ?>
		    </div>
		  </div>
		</nav>
