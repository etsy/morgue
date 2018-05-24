<div class="row-fluid">
     <h2><?php echo $event["title"]; ?><small><?php echo $edited; ?></small></h2>
     <div class="span12">
     <h3>What Happened?</h3>
     <?php 
     if($history["summary"] === null){
         echo "<hr><h4>No Data Found</h4><br/>";
     } else {
         echo '<pre>' . $history["summary"] . '</pre>';
     }
     ?>
     <h3>Why were we surprised?</h3>
     <?php
     if($history["why_surprised"] === null){
         echo "<hr><h4>No Data Found</h4>";
     } else {
         echo '<pre>' . $history["why_surprised"] . '</pre>';
     }
     ?>
     </div>

    <h3>tl;dr</h3>
    <?php
    if($history["tldr"] === null){
        echo "<hr><h4>No Data Found</h4>";
    } else {
        echo '<pre>' . $history["tldr"] . '</pre>';
    }
    ?>
</div>
</div>
