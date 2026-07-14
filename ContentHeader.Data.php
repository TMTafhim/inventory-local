<?php
$app_page_title = (!empty($page_title) && $page_title == 'BoXinfo') ? 'Dashboard' : str_replace("_", " ", $page_title);
$app_page_context = !empty($MenuName) ? $MenuName : 'Home';
?>
  <div class="content-header app-page-head">
      <div class="container-fluid">
        <div class="app-page-titlebar">
          <div class="app-page-titlemark" aria-hidden="true">
            <i class="fas fa-layer-group"></i>
          </div>
          <div class="app-page-titlecopy">
            <div class="app-page-kicker">
              <span><?php echo $app_page_context; ?></span>
              <i class="fas fa-chevron-right"></i>
              <span><?php echo $app_page_title; ?></span>
            </div>
            <h1 class="m-0"><?php echo $app_page_title; ?></h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>
