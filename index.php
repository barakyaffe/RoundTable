<?php

/*
 if (!isset($_SERVER['PHP_AUTH_USER'])) {
 
        header("WWW-Authenticate: Basic realm=\"Private Area\"");
        header("HTTP/1.0 401 Unauthorized");
        print "Sorry - you need valid credentials to be granted access!\n";
        exit;
    } else {
        if (($_SERVER['PHP_AUTH_USER'] == 'barak') && ($_SERVER['PHP_AUTH_PW'] == 'yaffe')) {
            print "Welcome to the private area!";
        } else {
            header("WWW-Authenticate: Basic realm=\"Private Area\"");
            header("HTTP/1.0 401 Unauthorized");
            print "Sorry - you need valid credentials to be granted access!\n";
            exit;
        }
    }
*/
include_once 'includes/globals.php';
//include_once 'includes/auth.php';

//establishing parameters
if (@($_GET['pID'])) {
    $pID = (int)$_GET['pID'];
    $whereStr = "WHERE `pixel_id` LIKE '%$pID%'";
} else {
    $whereStr = "WHERE `pixel_id` LIKE '%%'";
}
if (@($_GET['from'])) {
    $from      = $_GET['from'];
    $dateFrom  = date('Y-m-d', strtotime(str_replace('/', '-', $from)));
    $dateFrom .= ' 00:00:00';
    $whereStr .= " AND `timestamp` >= '$dateFrom'";
}
if (@($_GET['to'])) {
    $to = $_GET['to'];
    $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $to)));
    $dateTo .= ' 23:59:59';
    $whereStr .= " AND `timestamp` <= '$dateTo'";
}

//check1
//var_dump($whereStr);

//set page number variable
$pn = (@$_GET['pn']) ? (int)$_GET['pn'] : 1;
//set amount of result per page as variable
$rpp = (@$_GET['rpp']) && $_GET['rpp'] <= 50  ? (int)$_GET['rpp'] : 5;
//check2
//var_dump($rpp);

//get the total records for this search
$counterSql = "SELECT COUNT(*) AS total FROM `pixels_activities` $whereStr";
$result = $dbCon->query($counterSql);
$record = $result->fetch_assoc();
$totalRows = $record['total'];
//check3
//var_dump($totalRows);

//calculate total pages
$totalPages = ceil($totalRows / $rpp);
//check4
//var_dump($totalPages);

//where LIMIT start
$start = ($pn > 1) ? ($pn * $rpp) - $rpp : 0;
//check5
//var_dump($start);

//set the query
$sql = "SELECT * FROM `pixels_activities` $whereStr LIMIT $start, $rpp";
//send the query to the DB
$result = $dbCon->query($sql);
if (!$result) {
    die('Query failed: ' . $dbCon->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="social art gallery">
    <meta name="author" content="Barak yaffe">
    <title>Round Table</title>
    <!--Generic css-->
    <link href="media/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="media/css/footable.bootstrap.css" rel="stylesheet"/>
    <link href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" >
    <!--My css-->
    <link href="media/css/mystyle.css" type="text/css" rel="stylesheet"/>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<div class="container">
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="sr-only">Show and Hide the Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">
                    <img src="media/images/NewLogo1.png" class="logo"/>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <form class="navbar-form navbar-left" role="form">
                    <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-search"></span>
                    </span>
                        <input type="search" id="pID" name="pID" class="form-control input-sm"
                               placeholder="Insert ID number"
                               data-toggle="tooltip" data-placement="bottom"
                               title="This field is for Pixel ID search"/>
                    </div>
                    <div class="input-group date">
                        <label for="from" hidden></label>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                        <input type="text" name="from" id="from" class="form-control input-sm "
                               placeholder="Start date"/>
                    </div>
                    <div class="input-group date">
                        <label for="to" hidden></label>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                        <input type="text" id="to" name="to" class="form-control input-sm" placeholder="End date"/>
                    </div>
                    <div class="input-group">
                        <label for="rpp" hidden></label>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-list"></span>
                    </span>
                        <input type="number" name="rpp" id="rpp" class="form-control input-sm"
                               min="10" max="50" step="10"  placeholder="Results" data-toggle="tooltip" data-placement="bottom"
                               title="Choose number of Results per page">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <!--build the Table structure-->
    <div class="container table-responsive">
        <?php if ($result->num_rows > 0) {
              echo '<table class="footable table table-striped toggle-circle-arrow breakpoint-all">';
              echo '<thead>';
              echo '<tr>';
              echo '<th>Pixel ID</th>';
              echo '<th>Time Stamp</th>';
              echo '<th data-breakpoints="xs">Platform</th>';
              echo '<th data-breakpoints="xs">Referral</th>';
              echo '<th data-breakpoints="xs">Response Code</th>';
              echo '<th data-breakpoints="xs">Response</th>';
              echo '</tr>';
              echo '</thead>';
              echo '<tbody>';?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['pixel_id'] ?></td>
                    <td><?= date('d/m/y h:i:s', strtotime($row['timestamp'])) ?></td>
                    <td><?= $row['platform'] ?></td>
                    <td data-toggle="tooltip" data-placement="bottom"
                        title="<?= substr($row['pixel'], 0, 60) ?>"><?= substr($row['pixel'], 0, 30) ?>...</td>
                    <td><?= $row['response_code'] ?></td>
                    <td><?= $row['response'] ?></td>
                </tr>

            <?php endwhile ?>

                </tbody>
            </table>
            <?php } else {
                    echo "0 results";
                  }
            $dbCon->close();
            ?>
        
    <!--Build the Paging-->
    <nav>
        <ul class="pager">
    <?php
    //generating the "previous"
    $last = $totalPages;
    if ($last != 1 && $pn > 1) {
    $previous = $pn - 1; ?>
            <li class="previous"><a href="?pn=<?=$previous?>&rpp=<?php echo $rpp; ?>"><span aria-hidden="true">&larr;</span> Older</a></li>
    <?php } else{
    echo "<li>no previous page</li>";}?>
    <?php
    //generating the "Next"
    if ($pn != $last) {
    $next = $pn + 1;
    ?>
            <li class="next"><a href="?pn=<?=$next?>&rpp=<?php echo $rpp; ?>">Newer <span aria-hidden="true">&rarr;</span></a></li>
    <?php } else{
        echo "<li>no next page</li>";}
    ?>
        </ul>
    </nav>
    </div>
    <footer>
        <div class="row">
            <div class="col-lg-12 text-center">
                <p>Copyright &copy; B.Yaffe 2015</p>
            </div>
        </div>
    </footer>

</div>

<!-- jQuery and Bootstrap JS -->
<script src="media/js/jquery-1.11.3.min.js"></script>
<script src="media/js/bootstrap.min.js"></script>
<script src="media/js/jQeuryUI.js"></script>
<script src="media/js/datePicker.js"></script>
<script src="media/js/footable.js"></script>

<script>
    jQuery(function ($) {
        $('.table').footable();
    });
    $(document).ready(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
</script>

</body>
</html>