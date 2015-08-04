
<div class="navbar">
  <div class="navbar-inner" id="header">
    <div class="container">
      <div id="ascii_logo"><a class="brand" href="<?php echo ($content === 'frontpage') ? '#' : '/' ?>"> __    __  ______  ______  ______  __  __  ______         _____
/\ \-./  \/\  __ \/\  == \/\  ___\/\ \/\ \/\  ___\      //     \
\ \ \-./\ \ \ \/\ \ \  __<\ \ \__ \ \ \_\ \ \  __\     || R I P |
 \ \_\ \ \_\ \_____\ \_\ \_\ \_____\ \_____\ \_____\   ||       |
  \/_/  \/_/\/_____/\/_/ /_/\/_____/\/_____/\/_____/ ,.||,.,,.,.|,.</a></div>
        <div>
      <ul class="nav">
<?php foreach (Configuration::get_navbar_features() as $navbar_feature) { 
/*
 * For each feature that declares navbar, we will draw a link in the navbar.
 * If the feature defines custom_js_assets we will load the script here too.
 */ ?>
		<li class="<?php echo $navbar_feature['name'] ?>">
			<a  class="nav_link" href="<?php echo ($content === '/'.$navbar_feature['name']) ? '#' : '/'.$navbar_feature['name'] ?>"><?php echo ucfirst($navbar_feature['name']) ?></a><?php
			if (isset($navbar_feature['custom_js_assets'])) {
				if (!is_array($navbar_feature['custom_js_assets'])) {
					$js_assets = array($navbar_feature['custom_js_assets']);
				} else {
					$js_assets = $navbar_feature['custom_js_assets'];
				}
				foreach ($js_assets as $js_asset) {
                                    if (strpos($js_asset, "https://") === false && strpos($js_asset, "http://") === false) {
					echo "<script type=\"text/javascript\" src=\"/features/{$navbar_feature['name']}/js/{$js_asset}\"></script>"; 
                                    } else {
					echo "<script type=\"text/javascript\" src=\"{$js_asset}\"></script>";                                         
                                    }
                                }
			}
?></li>
<?php } ?>

      </ul>
          <div id="search_div">
              <input id="search_field" type="text" placeholder="Search">
          </div>  
          <script type="text/javascript" src="/features/search/js/search.js"></script>
      </div>
    </div>
  </div>
</div>
