<?php
/*
Template Name: マイページ
*/
/* SH4 generated 20260606-104555 */

if (!is_user_logged_in()) {
  wp_safe_redirect(wp_login_url(get_permalink()));
  exit;
}

$current_user = wp_get_current_user();
$user_id = get_current_user_id();

if (
  $_SERVER['REQUEST_METHOD'] === 'POST' &&
  isset($_POST['sh4_update_stable_name'])
) {
  if (
    !isset($_POST['sh4_stable_name_nonce']) ||
    !wp_verify_nonce($_POST['sh4_stable_name_nonce'], 'sh4_update_stable_name')
  ) {
    wp_die('不正な送信です。');
  }

  $stable_name = sanitize_text_field(wp_unslash($_POST['stable_name'] ?? ''));
  $x_account = sanitize_text_field(wp_unslash($_POST['x_account'] ?? ''));
  $x_account = ltrim($x_account, '@');
  $x_account = preg_replace('/[^A-Za-z0-9_]/', '', $x_account);

  update_user_meta($user_id, 'stable_name', $stable_name);
  update_user_meta($user_id, 'x_account', $x_account);

  wp_safe_redirect(add_query_arg('stable_updated', '1', get_permalink()));
  exit;
}

$stable_name = get_user_meta($user_id, 'stable_name', true);
$x_account = get_user_meta($user_id, 'x_account', true);

$display_stable_name = function_exists('sh4_get_stable_name')
  ? sh4_get_stable_name($user_id)
  : ($stable_name ?: '名無し厩舎');

$my_horses = get_posts([
  'post_type' => 'horse',
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'author' => $user_id,
  'meta_key' => 'total_prize',
  'orderby' => 'meta_value_num',
  'order' => 'DESC',
]);

get_header();
?>

<div class="container">
  <div class="sh4-mypage-wrap">

    <h1>マイページ</h1>

    <?php if (isset($_GET['stable_updated']) && $_GET['stable_updated'] === '1') : ?>
      <div class="sh4-success-message">
        厩舎設定を更新しました。
      </div>
    <?php endif; ?>

    <div class="sh4-mypage-card">
      <h2>厩舎設定</h2>
      <p class="sh4-mypage-help">
        馬一覧や馬詳細に表示される名前です。Googleの登録名ではなく、この厩舎名が公開表示されます。
      </p>

      <form method="post" class="sh4-stable-form">
        <?php wp_nonce_field('sh4_update_stable_name', 'sh4_stable_name_nonce'); ?>

        <p>
          <label for="stable_name">厩舎名</label><br>
          <input
            type="text"
            id="stable_name"
            name="stable_name"
            value="<?php echo esc_attr($stable_name); ?>"
            maxlength="30"
            placeholder="例：かぴ厩舎"
          >
        </p>

        <p>
          <label for="x_account">Xアカウント</label><br>
          <input
            type="text"
            id="x_account"
            name="x_account"
            value="<?php echo esc_attr($x_account); ?>"
            maxlength="15"
            placeholder="例：sh4_kapi（@なしでもOK）"
          >
          <span class="form-help-text">入力すると厩舎一覧にXリンクが表示されます。</span>
        </p>

        <div class="sh4-current-stable-name">
          <div>現在の表示：<strong><?php echo esc_html($display_stable_name); ?></strong></div>
          <div>
            X：
            <?php if ($x_account) : ?>
              <a href="<?php echo esc_url('https://x.com/' . $x_account); ?>" target="_blank" rel="noopener noreferrer">@<?php echo esc_html($x_account); ?></a>
            <?php else : ?>
              未登録
            <?php endif; ?>
          </div>
        </div>

        <p>
          <button type="submit" name="sh4_update_stable_name" class="add-btn">
            厩舎設定を保存
          </button>
        </p>
      </form>
    </div>

    <div class="sh4-mypage-card">
      <h2>自分の馬一覧</h2>

      <?php if ($my_horses) : ?>
        <div class="horse-list sh4-my-horse-list">
          <?php foreach ($my_horses as $horse) : ?>
            <div class="horse-card">
              <a href="<?php echo esc_url(get_permalink($horse->ID)); ?>">
                <h3><?php echo esc_html(get_the_title($horse->ID)); ?></h3>
              </a>

              <?php
              $horse_total_prize = get_field('total_prize', $horse->ID);
              if ($horse_total_prize === '' || $horse_total_prize === null) {
                $horse_total_prize = function_exists('sh4_update_total_prize') ? sh4_update_total_prize($horse->ID) : 0;
              }
              ?>

              <div class="horse-card-prize"><?php echo esc_html(number_format((int)$horse_total_prize)); ?>枚</div>
              <p class="horse-owner">厩舎：<?php echo esc_html($display_stable_name); ?></p>

              <?php
              $horse_visibility = function_exists('sh4_get_horse_visibility')
                ? sh4_get_horse_visibility($horse->ID)
                : (get_field('horse_visibility', $horse->ID) ?: 'public');
              ?>

              <p class="horse-visibility <?php echo $horse_visibility === 'private' ? 'horse-visibility-private' : 'horse-visibility-public'; ?>">
                <?php echo esc_html($horse_visibility === 'private' ? '非公開' : '公開'); ?>
              </p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <p>まだ馬が登録されていません。</p>
      <?php endif; ?>

      <p class="sh4-mypage-add-horse">
        <a href="<?php echo esc_url(home_url('/add-horse/')); ?>" class="add-btn">＋ 馬を追加</a>
      </p>
    </div>

    <div class="sh4-mypage-card">
      <h2>メニュー</h2>
      <div class="sh4-mypage-links">
        <a href="<?php echo esc_url(home_url('/horse/')); ?>">馬一覧へ</a>
        <a href="<?php echo esc_url(home_url('/stables/')); ?>">厩舎一覧へ</a>
        <a href="<?php echo esc_url(home_url('/add-horse/')); ?>">馬を追加</a>
        <a href="<?php echo esc_url(wp_logout_url(home_url('/horse/'))); ?>">ログアウト</a>
      </div>
    </div>

  </div>
</div>

<?php get_footer(); ?>
