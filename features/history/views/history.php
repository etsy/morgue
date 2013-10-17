<!-- Postmortem History -->
<div class="row-fluid">
<legend style="margin-bottom:0px; border-bottom:none;">Postmortem History</legend>
<table class="table">
<tbody>
<?php 
foreach($event["history"] as $i => $h) {
    echo "<tr><td>" . Postmortem::humanize_history($h) . "</td></tr>";
} 
?>
</tbody>
</table>
</div>

<div class="row-fluid"><br/></div>
