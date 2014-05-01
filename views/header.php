

<div class="navbar">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="<?php echo ($content === 'frontpage') ? '#' : '/' ?>">Post Mortem Keeper</a>
        <div>
      <ul class="nav">
<?php foreach (Configuration::get_navbar_features() as $navbar_feature) { ?>
        <li>
            <a  href="<?php echo ($content === '/'.$navbar_feature['name']) ? '#' : '/'.$navbar_feature['name'] ?>"><?php echo ucfirst($navbar_feature['name']) ?></a>
        </li>
<?php } ?>
      </ul>
        </div>
    </div>
  </div>
</div>
