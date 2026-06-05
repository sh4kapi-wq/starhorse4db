<?php get_template_part('_g3/footer'); ?>

<div class="global-bottom-links">
  <a href="<?php echo esc_url(home_url('/')); ?>">🏠 トップ</a>
  <a href="<?php echo esc_url(home_url('/horse/')); ?>">🐎 馬一覧</a>

  <?php if (is_user_logged_in()) : ?>
    <a href="<?php echo esc_url(home_url('/mypage/')); ?>">👤 マイページ</a>
  <?php else : ?>
    <a href="<?php echo esc_url(function_exists('sh4_get_login_page_url') ? sh4_get_login_page_url(home_url('/mypage/')) : home_url('/login/')); ?>">🔐 ログイン</a>
  <?php endif; ?>
</div>
