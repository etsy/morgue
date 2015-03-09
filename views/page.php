<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="$csrf_token" />
    <meta name="csrf-param" content="_csrf" />
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="/assets/css/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="/assets/css/dropzone.css">
    <link rel="stylesheet" href="/assets/css/chosen.css" />
    <link rel="stylesheet" href="/assets/css/image_sizing.css" />
    <link rel="stylesheet" href="/assets/css/morgue.css" />

    <title><?php echo isset($page_title) ? htmlentities($page_title) : 'Morgue' ?></title>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js"></script>
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
