<?php get_header(); ?>

<div class="top-wrap">

  <!-- =========================
    タイトル
  ========================= -->
  <h1 class="top-title">スターホースDB</h1>
  <p class="top-sub">馬の戦績を管理するデータベース</p>


  <!-- =========================
    新着馬
  ========================= -->
  <h2>新着馬</h2>

  <div class="horse-list">

    <?php
    $horses = get_posts([
      'post_type' => 'horse',
      'posts_per_page' => 50,
      'orderby' => 'date',
      'order' => 'DESC'
    ]);

    $display_count = 0;

    foreach ($horses as $horse) {
      $visibility = function_exists('sh4_get_horse_visibility')
        ? sh4_get_horse_visibility($horse->ID)
        : (get_field('horse_visibility', $horse->ID) ?: 'public');

      if ($visibility === 'private') {
        continue;
      }

      $display_count++;

      if ($display_count > 5) {
        break;
      }

      echo '<div class="horse-card">';
      echo '<a href="'.esc_url(get_permalink($horse->ID)).'">';
      echo esc_html($horse->post_title);
      echo '</a>';
      echo '</div>';
    }

    if ($display_count === 0) {
      echo '<p>公開されている馬はまだありません。</p>';
    }
    ?>

  </div>


  <!-- =========================
    全馬一覧ボタン
  ========================= -->
  <p>
    <a href="<?php echo get_post_type_archive_link('horse'); ?>" class="main-btn">
      馬一覧を見る
    </a>
  </p>

</div>

<?php get_footer(); ?>