<?php
/*
Template Name: 厩舎一覧
*/
/* SH4 generated 20260606-104555 */

get_header();

$selected_user_id = isset($_GET['stable_user']) ? intval($_GET['stable_user']) : 0;

function sh4_stables_get_public_horses($user_id, $limit = -1) {
  return get_posts([
    'post_type' => 'horse',
    'post_status' => 'publish',
    'posts_per_page' => $limit,
    'author' => intval($user_id),
    'meta_key' => 'total_prize',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'meta_query' => [
      'relation' => 'OR',
      [
        'key' => 'horse_visibility',
        'value' => 'private',
        'compare' => '!='
      ],
      [
        'key' => 'horse_visibility',
        'compare' => 'NOT EXISTS'
      ]
    ]
  ]);
}

function sh4_stables_get_top_horse($user_id) {
  $horses = sh4_stables_get_public_horses($user_id, 1);
  return !empty($horses) ? $horses[0] : null;
}

function sh4_stables_get_prize($horse_id) {
  $prize = get_field('total_prize', $horse_id);
  if ($prize === '' || $prize === null) {
    $prize = function_exists('sh4_update_total_prize') ? sh4_update_total_prize($horse_id) : 0;
  }
  return (int)$prize;
}

$users = get_users([
  'fields' => ['ID', 'display_name'],
  'orderby' => 'registered',
  'order' => 'ASC',
]);

$stable_rows = [];

foreach ($users as $user) {
  $public_horses = sh4_stables_get_public_horses($user->ID, -1);
  $horse_count = count($public_horses);

  if ($horse_count <= 0) {
    continue;
  }

  $top_horse = !empty($public_horses) ? $public_horses[0] : null;
  $top_prize = $top_horse ? sh4_stables_get_prize($top_horse->ID) : 0;

  $stable_rows[] = [
    'user' => $user,
    'stable_name' => function_exists('sh4_get_stable_name') ? sh4_get_stable_name($user->ID) : (get_user_meta($user->ID, 'stable_name', true) ?: '名無し厩舎'),
    'x_account' => get_user_meta($user->ID, 'x_account', true),
    'horse_count' => $horse_count,
    'top_horse' => $top_horse,
    'top_prize' => $top_prize,
  ];
}

usort($stable_rows, function($a, $b) {
  if ($a['top_prize'] === $b['top_prize']) {
    return $b['horse_count'] <=> $a['horse_count'];
  }
  return $b['top_prize'] <=> $a['top_prize'];
});

$selected_row = null;
if ($selected_user_id) {
  foreach ($stable_rows as $row) {
    if ((int)$row['user']->ID === $selected_user_id) {
      $selected_row = $row;
      break;
    }
  }
}
?>

<div class="container">
  <div class="sh4-stables-wrap">

    <?php if ($selected_row) : ?>

      <div class="sh4-stables-hero sh4-stable-detail-hero">
        <p class="sh4-stables-kicker">Stable</p>
        <h1><?php echo esc_html($selected_row['stable_name']); ?></h1>
        <p>公開されている所属馬を賞金順に表示しています。</p>

        <div class="sh4-stable-detail-meta">
          <span>登録馬数：<?php echo esc_html(number_format($selected_row['horse_count'])); ?>頭</span>
          <?php if ($selected_row['top_horse']) : ?>
            <span>代表馬：<?php echo esc_html(get_the_title($selected_row['top_horse']->ID)); ?></span>
            <span>最高賞金：<?php echo esc_html(number_format($selected_row['top_prize'])); ?>枚</span>
          <?php endif; ?>
          <?php if (!empty($selected_row['x_account'])) : ?>
            <a href="<?php echo esc_url('https://x.com/' . ltrim($selected_row['x_account'], '@')); ?>" target="_blank" rel="noopener noreferrer">X：@<?php echo esc_html(ltrim($selected_row['x_account'], '@')); ?></a>
          <?php endif; ?>
        </div>
      </div>

      <p class="sh4-stables-back"><a href="<?php echo esc_url(home_url('/stables/')); ?>">← 厩舎一覧へ戻る</a></p>

      <?php $stable_horses = sh4_stables_get_public_horses($selected_row['user']->ID, -1); ?>
      <?php if ($stable_horses) : ?>
        <div class="horse-list sh4-stable-horse-list">
          <?php foreach ($stable_horses as $horse) : ?>
            <?php $horse_prize = sh4_stables_get_prize($horse->ID); ?>
            <div class="horse-card sh4-stable-horse-card">
              <a href="<?php echo esc_url(get_permalink($horse->ID)); ?>">
                <h3><?php echo esc_html(get_the_title($horse->ID)); ?></h3>
              </a>
              <div class="horse-card-prize"><?php echo esc_html(number_format($horse_prize)); ?>枚</div>
              <p class="horse-owner">厩舎：<?php echo esc_html($selected_row['stable_name']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <div class="sh4-empty-card">公開されている馬がまだありません。</div>
      <?php endif; ?>

    <?php else : ?>

      <div class="sh4-stables-hero">
        <p class="sh4-stables-kicker">Stables</p>
        <h1>厩舎一覧</h1>
        <p>登録馬を公開している厩舎を一覧で表示します。代表馬は獲得賞金が最も高い馬です。</p>
      </div>

      <?php if ($stable_rows) : ?>
        <div class="sh4-stables-grid">
          <?php foreach ($stable_rows as $row) : ?>
            <article class="sh4-stable-card">
              <div class="sh4-stable-card-head">
                <div>
                  <p class="sh4-stable-label">厩舎</p>
                  <h2><?php echo esc_html($row['stable_name']); ?></h2>
                </div>
                <div class="sh4-stable-horse-count"><?php echo esc_html(number_format($row['horse_count'])); ?><span>頭</span></div>
              </div>

              <div class="sh4-stable-x">
                <?php if (!empty($row['x_account'])) : ?>
                  <a href="<?php echo esc_url('https://x.com/' . ltrim($row['x_account'], '@')); ?>" target="_blank" rel="noopener noreferrer">X：@<?php echo esc_html(ltrim($row['x_account'], '@')); ?></a>
                <?php else : ?>
                  <span>X：未登録</span>
                <?php endif; ?>
              </div>

              <div class="sh4-stable-representative">
                <span class="sh4-stable-label">代表馬</span>
                <?php if ($row['top_horse']) : ?>
                  <a href="<?php echo esc_url(get_permalink($row['top_horse']->ID)); ?>" class="sh4-stable-top-horse"><?php echo esc_html(get_the_title($row['top_horse']->ID)); ?></a>
                  <strong><?php echo esc_html(number_format($row['top_prize'])); ?>枚</strong>
                <?php else : ?>
                  <p>公開馬なし</p>
                <?php endif; ?>
              </div>

              <a class="sh4-stable-link-btn" href="<?php echo esc_url(add_query_arg('stable_user', $row['user']->ID, home_url('/stables/'))); ?>">所属馬を見る</a>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <div class="sh4-empty-card">公開されている厩舎はまだありません。</div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
