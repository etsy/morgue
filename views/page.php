<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="$csrf_token" />
    <meta name="csrf-param" content="_csrf" />
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="/assets/css/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="/assets/css/chosen.css" />
    <link rel="stylesheet" href="/assets/css/image_sizing.css" />
    <link rel="stylesheet" href="/assets/css/morgue.css" />

    <title><?php echo isset($page_title) ? htmlentities($page_title) : 'Morgue' ?></title>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.6.0/underscore-min.js"></script>
    <?php
        $config = Configuration::get_configuration();
        echo '<script type="text/javascript">';
        echo 'if (window.MORGUE === undefined) {';
        echo '  window.MORGUE = {};';
        echo '}';
        echo 'MORGUE.date_format="', $config['date_format_front'], '";';
        echo 'MORGUE.show_24_hours=', var_export($config['show24hours'], true) ,';';
        echo 'MORGUE.weekstart=', var_export($config['weekstart'], true) ,';';
        echo '</script>';
    ?>

  </head>
  <body>
    <?php include __DIR__.'/header.php' ?>

    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span9">
<?php
    // include our $content view if we can find it
    $incpath = stream_resolve_include_path($content .".php");
    if ($incpath !== false) {
        include $incpath;
    } else {
        echo "Could not find $content";
    }
?>
        </div>
        <div class="span3">
          <?php if($show_sidebar == true) {
              include __DIR__.'/sidebar.php';
          } ?>
          <?php include __DIR__.'/timezone.php' ?>
        </div>
      </div>
    </div>

    <?php include __DIR__.'/footer.php' ?>
  </body>
</html>
