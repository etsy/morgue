<div class="row-fluid">
     <legend>Search Results</legend>
     <div class="span12">
      <?php
     if(count($results) == 0) {
         echo '<h3>No Results</h3>';
     } else {
         echo '<table class="table table-striped">
               <thead><tr><th>Title</th><th>Created</th></tr></thead>';
         foreach ($results as $result) {
             echo "<tr>";
             echo "<td><a href=/events/$result[id]>$result[title]</a></td>";
             echo "<td>$result[created]</td>";
             echo "</tr>";
         }
     }
      ?>
      </table>
  </div>
</div>