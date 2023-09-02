<?php   include("functions.php");	?>

<div class="content">
  <div class="jumbotron">
    <div class="container">
      <h1>Website Statistics</h1>
      <p>This page contains site stats, evidently! Want more? Please make suggestions!</p>
    </div>
  </div>
  <div class="container">
  <h2>Most sessions booked</h2>
  <table class="table-bordered">
    <tr><th>Who</th><th>Sessions booked</th></tr>
    <?php   echo most_sessions($session->logged_in); ?>
  </table>
  <br>

  <h2>Most sessions managed</h2>
  <table class="table-bordered">
    <tr><th>Who</th><th>Sessions managed</th></tr>
    <?php   echo most_managers($session->logged_in); ?>
  </table>
  <br>

  <?php   if($session->logged_in){ ?>
  <h2>Search for a user</h2>
      <div class="row">
        <div class="col-md-2">
          <label for="user">Enter the username</label>
          <input id="user" name="user" type="text" class="form-control">
          <a onclick='window.location.href="<?php   echo SITE_URL; ?>/user/info/index.php?user=" + document.getElementById("user").value;' class="btn btn-default">Search</a>
        </div>
      </div>
  <?php   } ?>
  <br>

  <h2>Page Loads</h2>
  <p>Site activity over the past 21 days (<strong><span style="color: #1B86E1;">total page loads</span>, <span style="color: #d438d2;">page loads from logged in users</span></strong>):</p>

<?php   include("graph.php");
  $myGraph = new nucleoGraph;
  $myGraph->construct(55, 800, 800, 'daily', true, 4, 60);
  $myGraph->setColors(1, '1B86E1', '71b8f4', '2d6290');
  $myGraph->setColors(2, 'd438d2', 'e96ae8', '9a2b99');
  page_loads();
  $myGraph->displayGraph();
?>

    </div>
  </div>
</div>
