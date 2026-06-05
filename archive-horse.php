<?php get_header(); ?>

<div class="container">

  <h1>馬一覧</h1>

  <?php

  $horses = get_posts([
    'post_type' => 'horse',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'ID',
    'order' => 'DESC',
    'no_found_rows' => true,
  ]);

  $display_horses = [];

  if ($horses) {
    foreach ($horses as $horse) {

      $visibility = function_exists('sh4_get_horse_visibility')
        ? sh4_get_horse_visibility($horse->ID)
        : (get_field('horse_visibility', $horse->ID) ?: 'public');

      $can_view = function_exists('sh4_current_user_can_view_horse')
        ? sh4_current_user_can_view_horse($horse->ID)
        : ($visibility !== 'private');

      if (!$can_view) {
        continue;
      }

      $total_prize = get_field('total_prize', $horse->ID);

      if ($total_prize === '' || $total_prize === null) {
        $total_prize = function_exists('sh4_update_total_prize')
          ? sh4_update_total_prize($horse->ID)
          : 0;
      }

      $horse->sh4_total_prize = (int)$total_prize;
      $horse->sh4_visibility = $visibility;

      $display_horses[] = $horse;
    }
  }

  usort($display_horses, function($a, $b) {
    if ($a->sh4_total_prize === $b->sh4_total_prize) {
      return $b->ID <=> $a->ID;
    }

    return $b->sh4_total_prize <=> $a->sh4_total_prize;
  });

  $display_horses = array_slice($display_horses, 0, 100);

  ?>

  <div class="horse-list horse-list-compact">

  <?php if ($display_horses) : ?>

    <?php foreach ($display_horses as $horse) : ?>

      <?php
      $owner_id = intval(get_post_field('post_author', $horse->ID));
      $stable_name = function_exists('sh4_get_stable_name') ? sh4_get_stable_name($owner_id) : '名無し厩舎';
      $visibility = $horse->sh4_visibility;
      ?>

      <div class="horse-card horse-card-compact">

        <a href="<?php echo esc_url(get_permalink($horse->ID)); ?>" class="horse-card-title-link">
          <h3><?php echo esc_html($horse->post_title); ?></h3>
        </a>

        <div class="horse-card-prize">
          <?php echo esc_html(number_format($horse->sh4_total_prize)); ?>枚
        </div>

        <div class="horse-card-meta">
          <span class="horse-owner">厩舎：<?php echo esc_html($stable_name); ?></span>

          <?php if ($visibility === 'private') : ?>
            <span class="horse-visibility horse-visibility-private">非公開</span>
          <?php else : ?>
            <span class="horse-visibility horse-visibility-public">公開</span>
          <?php endif; ?>
        </div>

      </div>

    <?php endforeach; ?>

  <?php else : ?>

    <p>表示できる馬が登録されていません</p>

  <?php endif; ?>

  </div>

  <?php if (is_user_logged_in()) : ?>

    <p style="text-align:right;">
      <a href="<?php echo esc_url(home_url('/add-horse/')); ?>" class="add-btn">＋ 馬を追加</a>
    </p>

  <?php endif; ?>

</div>

<?php get_footer(); ?>
