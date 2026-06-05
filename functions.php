<?php
add_action('wp_enqueue_scripts', function() {

  wp_enqueue_style(
    'parent-style',
    get_template_directory_uri() . '/style.css'
  );

  wp_enqueue_style(
    'child-style',
    get_stylesheet_directory_uri() . '/style.css',
    ['parent-style'],
    filemtime(get_stylesheet_directory() . '/style.css')
  );

});



/* =========================
  厩舎名（ユーザー表示名）
========================= */

if (!function_exists('sh4_get_stable_name')) {
function sh4_get_stable_name($user_id) {

  $user_id = intval($user_id);

  if (!$user_id) {
    return '名無し厩舎';
  }

  $stable_name = get_user_meta($user_id, 'stable_name', true);

  if ($stable_name !== '' && $stable_name !== null) {
    return $stable_name;
  }

  return '名無し厩舎';

}
}

if (!function_exists('sh4_stable_name_profile_field')) {
function sh4_stable_name_profile_field($user) {

  if (!$user || empty($user->ID)) {
    return;
  }
  ?>

  <h2>スタホ4設定</h2>

  <table class="form-table" role="presentation">
    <tr>
      <th><label for="stable_name">厩舎名</label></th>
      <td>
        <input
          type="text"
          name="stable_name"
          id="stable_name"
          value="<?php echo esc_attr(get_user_meta($user->ID, 'stable_name', true)); ?>"
          class="regular-text"
        >
        <p class="description">馬一覧・馬詳細に公開表示される名前です。本名ではなくニックネームや厩舎名を入れてください。</p>
      </td>
    </tr>
  </table>

  <?php
}
}

add_action('show_user_profile', 'sh4_stable_name_profile_field');
add_action('edit_user_profile', 'sh4_stable_name_profile_field');

if (!function_exists('sh4_save_stable_name_profile_field')) {
function sh4_save_stable_name_profile_field($user_id) {

  if (!current_user_can('edit_user', $user_id)) {
    return false;
  }

  update_user_meta(
    $user_id,
    'stable_name',
    sanitize_text_field($_POST['stable_name'] ?? '')
  );

}
}

add_action('personal_options_update', 'sh4_save_stable_name_profile_field');
add_action('edit_user_profile_update', 'sh4_save_stable_name_profile_field');

function sh4_current_user_can_manage_horse($horse_id) {

  $horse_id = intval($horse_id);

  if (!$horse_id || !is_user_logged_in()) {
    return false;
  }

  $owner_id = intval(get_post_field('post_author', $horse_id));
  $user_id = get_current_user_id();

  if ($owner_id && $owner_id === $user_id) {
    return true;
  }

  return current_user_can('manage_options');

}



function sh4_normalize_horse_visibility($value) {

  $value = sanitize_text_field($value);

  if ($value === 'private') {
    return 'private';
  }

  return 'public';

}

function sh4_get_horse_visibility($horse_id) {

  $horse_id = intval($horse_id);

  if (!$horse_id) {
    return 'public';
  }

  $visibility = get_field('horse_visibility', $horse_id);

  return sh4_normalize_horse_visibility($visibility ?: 'public');

}

function sh4_current_user_can_view_horse($horse_id) {

  $horse_id = intval($horse_id);

  if (!$horse_id) {
    return false;
  }

  if (sh4_get_horse_visibility($horse_id) === 'public') {
    return true;
  }

  return sh4_current_user_can_manage_horse($horse_id);

}


if (!function_exists('sh4_get_login_page_url')) {
function sh4_get_login_page_url($redirect_to = '') {

  $redirect_to = $redirect_to ? esc_url_raw($redirect_to) : home_url('/mypage/');

  return add_query_arg(
    'redirect_to',
    rawurlencode($redirect_to),
    home_url('/login/')
  );

}
}

function sh4_redirect_to_login_if_needed() {

  if (is_user_logged_in()) {
    return;
  }

  $redirect_to = wp_get_referer();

  if (!$redirect_to) {
    $redirect_to = home_url('/mypage/');
  }

  wp_safe_redirect(sh4_get_login_page_url($redirect_to));
  exit;

}

function sh4_get_related_horse_id_from_record($record_id) {

  $record_id = intval($record_id);

  if (!$record_id) {
    return 0;
  }

  $horse_id = get_field('horse', $record_id);

  if (is_object($horse_id) && isset($horse_id->ID)) {
    return intval($horse_id->ID);
  }

  return intval($horse_id);

}

function sh4_save_initial_aptitudes($horse_id) {

  $distance_min_initial = sanitize_text_field($_POST['edit_aptitude_distance_min_initial'] ?? '');
  $distance_max_initial = sanitize_text_field($_POST['edit_aptitude_distance_max_initial'] ?? '');
  $distance_min_current = sanitize_text_field($_POST['edit_aptitude_distance_min_current'] ?? '');
  $distance_max_current = sanitize_text_field($_POST['edit_aptitude_distance_max_current'] ?? '');

  if ($distance_min_current === '') {
    $distance_min_current = $distance_min_initial;
  }

  if ($distance_max_current === '') {
    $distance_max_current = $distance_max_initial;
  }

  update_field('aptitude_distance_min_initial', $distance_min_initial, $horse_id);
  update_field('aptitude_distance_max_initial', $distance_max_initial, $horse_id);
  update_field('aptitude_distance_min_current', $distance_min_current, $horse_id);
  update_field('aptitude_distance_max_current', $distance_max_current, $horse_id);

  $temper_initial = sanitize_text_field($_POST['edit_aptitude_temper_initial'] ?? '');
  $temper_current = sanitize_text_field($_POST['edit_aptitude_temper_current'] ?? '');

  if ($temper_current === '') {
    $temper_current = $temper_initial;
  }

  update_field('aptitude_temper_initial', $temper_initial, $horse_id);
  update_field('aptitude_temper_current', $temper_current, $horse_id);

  $aptitude_keys = [
    'turf',
    'dirt',
    'bad_track',
    'slope',
    'constitution',
    'guts',
    'start',
    'right_turn',
    'left_turn',
    'tight_turn',
    'expedition',
    'acceleration',
  ];

  foreach ($aptitude_keys as $key) {

    $initial_value = isset($_POST['edit_aptitude_' . $key . '_initial']) && $_POST['edit_aptitude_' . $key . '_initial'] !== ''
      ? intval($_POST['edit_aptitude_' . $key . '_initial'])
      : '';

    $current_value = isset($_POST['edit_aptitude_' . $key . '_current']) && $_POST['edit_aptitude_' . $key . '_current'] !== ''
      ? intval($_POST['edit_aptitude_' . $key . '_current'])
      : $initial_value;

    update_field('aptitude_' . $key . '_initial', $initial_value, $horse_id);
    update_field('aptitude_' . $key . '_current', $current_value, $horse_id);

    $breaking_done = isset($_POST['edit_breaking_' . $key . '_done']) ? 1 : 0;

    update_field('breaking_' . $key . '_done', $breaking_done, $horse_id);

  }

}


function sh4_aptitude_label_from_value($value) {

  if ($value === null || $value === '') {
    return '-';
  }

  $labels = [
    1 => 'C',
    2 => 'C+',
    3 => 'B',
    4 => 'B+',
    5 => 'A',
    6 => 'A+',
    7 => 'S',
    8 => 'S+',
    9 => 'SS',
  ];

  return $labels[(int)$value] ?? '-';
}

function sh4_training_aptitude_labels() {

  return [
    'turf' => '芝',
    'dirt' => 'ダート',
    'bad_track' => '道悪',
    'slope' => '坂',
    'constitution' => '体質',
    'guts' => '根性',
    'start' => 'スタート',
    'right_turn' => '右回り',
    'left_turn' => '左回り',
    'tight_turn' => '小回り',
    'expedition' => '遠征',
    'acceleration' => '瞬発力',
  ];
}

function sh4_apply_training_aptitude_up($horse_id, $aptitude_keys) {

  $labels = sh4_training_aptitude_labels();
  $memo_parts = [];

  foreach ($aptitude_keys as $key) {

    $key = sanitize_key($key);

    if (!isset($labels[$key])) {
      continue;
    }

    $current_field = 'aptitude_' . $key . '_current';
    $initial_field = 'aptitude_' . $key . '_initial';

    $before = get_field($current_field, $horse_id);

    if ($before === '' || $before === null) {
      $before = get_field($initial_field, $horse_id);
    }

    if ($before === '' || $before === null) {
      continue;
    }

    $before_num = (int)$before;

    if ($before_num < 1 || $before_num > 9) {
      continue;
    }

    $after_num = min(9, $before_num + 1);

    update_field($current_field, $after_num, $horse_id);

    $memo_parts[] = $labels[$key] . ' ' . sh4_aptitude_label_from_value($before_num) . '→' . sh4_aptitude_label_from_value($after_num);
  }

  return implode('、', $memo_parts);
}


function sh4_normalize_rank_value($value) {

  if ($value === '' || $value === null) {
    return '';
  }

  $rank = intval($value);

  if ($rank < 1 || $rank > 18) {
    return '';
  }

  return $rank;

}

function sh4_normalize_initial_races_by_route($start_type, $condition_losses, $asahi_rank, $hopeful_rank, $foy_rank, $pre_world_rank) {

  $asahi_rank = sh4_normalize_rank_value($asahi_rank);
  $hopeful_rank = sh4_normalize_rank_value($hopeful_rank);
  $foy_rank = sh4_normalize_rank_value($foy_rank);
  $pre_world_rank = sh4_normalize_rank_value($pre_world_rank);

  // 条件戦で負けがある場合は、初期追加レースには進めない扱いにします。
  if ((int)$condition_losses > 0) {
    return [
      'asahi' => '',
      'hopeful' => '',
      'foy' => '',
      'pre_world' => '',
    ];
  }

  if ($start_type === '3歳スタート') {

    // 3歳：条件戦3戦 → 朝日杯 → ホープフル → プレワールド
    $foy_rank = '';

    if ($asahi_rank !== 1) {
      $hopeful_rank = '';
      $pre_world_rank = '';
    } elseif ($hopeful_rank !== 1) {
      $pre_world_rank = '';
    }

  } elseif ($start_type === '古馬スタート') {

    // 古馬：条件戦4戦 → フォア賞 → プレワールド
    $asahi_rank = '';
    $hopeful_rank = '';

    if ($foy_rank !== 1) {
      $pre_world_rank = '';
    }

  } else {

    $asahi_rank = '';
    $hopeful_rank = '';
    $foy_rank = '';
    $pre_world_rank = '';

  }

  return [
    'asahi' => $asahi_rank,
    'hopeful' => $hopeful_rank,
    'foy' => $foy_rank,
    'pre_world' => $pre_world_rank,
  ];

}



/* =========================
  獲得賞金キャッシュ
========================= */
if (!function_exists('sh4_calculate_total_prize')) {
function sh4_calculate_total_prize($horse_id) {

  $horse_id = intval($horse_id);

  if (!$horse_id) {
    return 0;
  }

  $total_prize = 0;

  $initial_prizes = [
    'asahi' => [1 => 1500, 2 => 600, 3 => 375, 4 => 225, 5 => 150],
    'hopeful' => [1 => 1500, 2 => 600, 3 => 375, 4 => 225, 5 => 150],
    'foy' => [1 => 500, 2 => 200, 3 => 125, 4 => 75, 5 => 50],
    'pre_world' => [1 => 20000, 2 => 8000, 3 => 5000, 4 => 3000, 5 => 2000],
  ];

  $initial_ranks = sh4_normalize_initial_races_by_route(
    get_field('start_type', $horse_id),
    (int)get_field('condition_losses', $horse_id),
    get_field('asahi_rank', $horse_id),
    get_field('hopeful_rank', $horse_id),
    get_field('foy_rank', $horse_id),
    get_field('pre_world_rank', $horse_id)
  );

  foreach ($initial_ranks as $key => $rank) {
    $rank = intval($rank);

    if ($rank && isset($initial_prizes[$key][$rank])) {
      $total_prize += (int)$initial_prizes[$key][$rank];
    }
  }

  $results = get_posts([
    'post_type' => 'result',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true,
    'meta_query' => [
      [
        'key' => 'horse',
        'value' => $horse_id,
        'compare' => '='
      ]
    ]
  ]);

  foreach ($results as $result_id) {
    $race_id = get_field('race', $result_id);
    $rank = intval(get_field('rank', $result_id));

    if (!$race_id || !$rank) {
      continue;
    }

    $total_prize += (int)get_field('prize_' . $rank, $race_id);
  }

  return max(0, (int)$total_prize);

}
}

if (!function_exists('sh4_update_total_prize')) {
function sh4_update_total_prize($horse_id) {

  $horse_id = intval($horse_id);

  if (!$horse_id) {
    return 0;
  }

  $total_prize = sh4_calculate_total_prize($horse_id);

  update_field('total_prize', $total_prize, $horse_id);
  update_post_meta($horse_id, 'total_prize', $total_prize);

  return $total_prize;

}
}

add_action('init', function(){

  register_post_type('horse', [
    'labels' => [
      'name' => '馬',
      'singular_name' => '馬'
    ],
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'horse'],
    'menu_position' => 4,
    'menu_icon' => 'dashicons-carrot',
    'supports' => ['title', 'author'],
  ]);

});

add_action('init', function(){

  register_post_type('race', [
    'labels' => [
      'name' => 'レース',
      'singular_name' => 'レース'
    ],
    'public' => true,
    'has_archive' => true,
    'menu_position' => 5,
    'menu_icon' => 'dashicons-flag',
    'supports' => ['title', 'author'],
  ]);

});

add_action('init', function(){

  register_post_type('result', [
    'labels' => [
      'name' => '出走履歴',
      'singular_name' => '出走履歴'
    ],
    'public' => true,
    'has_archive' => false,
    'menu_position' => 6,
    'menu_icon' => 'dashicons-chart-line',
    'supports' => ['title', 'author'],
  ]);

});


add_action('init', function(){

  register_post_type('training', [
    'labels' => [
      'name' => '調教履歴',
      'singular_name' => '調教履歴'
    ],
    'public' => true,
    'has_archive' => false,
    'menu_position' => 7,
    'menu_icon' => 'dashicons-clipboard',
    'supports' => ['title', 'author'],
  ]);

});

add_action('template_redirect', function(){

  if (
    !isset($_POST['add_result']) ||
    $_SERVER['REQUEST_METHOD'] !== 'POST'
  ) {
    return;
  }

  $unique_key = md5(json_encode($_POST));

  if (get_transient('add_result_' . $unique_key)) {
    return;
  }

  set_transient('add_result_' . $unique_key, true, 5);

  $horse_id   = intval($_POST['horse_id']);
  $race_id    = intval($_POST['race_id']);
  $rank       = intval($_POST['rank']);
  $popularity = intval($_POST['popularity']);
  $odds       = floatval($_POST['odds']);

  $limit_odds = isset($_POST['limit_odds'])
    ? floatval($_POST['limit_odds'])
    : '';

  $race_remaining_weeks = isset($_POST['race_remaining_weeks']) && $_POST['race_remaining_weeks'] !== ''
    ? intval($_POST['race_remaining_weeks'])
    : '';

  $race_memo = sanitize_textarea_field($_POST['race_memo'] ?? '');

  if (!$horse_id || !$race_id) {
    return;
  }

  sh4_redirect_to_login_if_needed();

  if (!sh4_current_user_can_manage_horse($horse_id)) {
    wp_die('この馬の出走履歴を追加する権限がありません。');
  }

  $post_id = wp_insert_post([
    'post_type'   => 'result',
    'post_status' => 'publish',
    'post_title'  => 'result-' . $horse_id . '-' . $race_id . '-' . microtime(true),
    'post_author' => get_current_user_id()
  ]);

  if ($post_id) {

    update_field('horse', $horse_id, $post_id);
    update_field('race', $race_id, $post_id);
    update_field('rank', $rank, $post_id);
    update_field('popularity', $popularity, $post_id);
    update_field('odds', $odds, $post_id);
    update_field('limit_odds', $limit_odds, $post_id);
    update_field('race_remaining_weeks', $race_remaining_weeks, $post_id);
    update_field('race_memo', $race_memo, $post_id);

    sh4_update_total_prize($horse_id);

  }

  clean_post_cache($horse_id);
  delete_transient('race_list_cache');

  if (function_exists('do_action')) {
    do_action('litespeed_purge_url', home_url('/horse/'));
  }

  wp_safe_redirect(get_permalink($horse_id));
  exit;

});


add_action('template_redirect', function(){

  if (
    !isset($_POST['update_result']) ||
    $_SERVER['REQUEST_METHOD'] !== 'POST'
  ) {
    return;
  }

  $result_id = intval($_POST['result_id'] ?? 0);

  if (!$result_id || get_post_type($result_id) !== 'result') {
    return;
  }

  sh4_redirect_to_login_if_needed();

  $horse_id = sh4_get_related_horse_id_from_record($result_id);

  if (!$horse_id || !sh4_current_user_can_manage_horse($horse_id)) {
    wp_die('この出走履歴を編集する権限がありません。');
  }

  $race_id = intval($_POST['edit_result_race_id'] ?? 0);
  $rank = intval($_POST['edit_result_rank'] ?? 0);
  $popularity = intval($_POST['edit_result_popularity'] ?? 0);

  $odds = isset($_POST['edit_result_odds']) && $_POST['edit_result_odds'] !== ''
    ? floatval($_POST['edit_result_odds'])
    : '';

  $limit_odds = isset($_POST['edit_result_limit_odds']) && $_POST['edit_result_limit_odds'] !== ''
    ? floatval($_POST['edit_result_limit_odds'])
    : '';

  $race_remaining_weeks = isset($_POST['edit_result_remaining_weeks']) && $_POST['edit_result_remaining_weeks'] !== ''
    ? intval($_POST['edit_result_remaining_weeks'])
    : '';

  $race_memo = sanitize_textarea_field($_POST['edit_result_memo'] ?? '');

  if (!$race_id) {
    wp_die('レースが選択されていません。');
  }

  update_field('race', $race_id, $result_id);
  update_field('rank', $rank, $result_id);
  update_field('popularity', $popularity, $result_id);
  update_field('odds', $odds, $result_id);
  update_field('limit_odds', $limit_odds, $result_id);
  update_field('race_remaining_weeks', $race_remaining_weeks, $result_id);
  update_field('race_memo', $race_memo, $result_id);

  sh4_update_total_prize($horse_id);
  clean_post_cache($horse_id);

  if (function_exists('do_action')) {
    do_action('litespeed_purge_url', get_permalink($horse_id));
    do_action('litespeed_purge_url', home_url('/horse/'));
  }

  wp_safe_redirect(get_permalink($horse_id));
  exit;

});


add_action('template_redirect', function(){

  if (
    !isset($_POST['add_training']) ||
    $_SERVER['REQUEST_METHOD'] !== 'POST'
  ) {
    return;
  }

  $unique_key = md5(json_encode($_POST));

  if (get_transient('add_training_' . $unique_key)) {
    return;
  }

  set_transient('add_training_' . $unique_key, true, 5);

  $horse_id = intval($_POST['horse_id'] ?? 0);

  if (!$horse_id) {
    return;
  }

  sh4_redirect_to_login_if_needed();

  if (!sh4_current_user_can_manage_horse($horse_id)) {
    wp_die('この馬の調教履歴を追加する権限がありません。');
  }

  $training_type = sanitize_text_field($_POST['training_type'] ?? '');
  $training_type_other = sanitize_text_field($_POST['training_type_other'] ?? '');

  $training_name = $training_type;

  if ($training_type === 'その他' && $training_type_other !== '') {
    $training_name = $training_type_other;
  }

  $training_result = sanitize_text_field($_POST['training_result'] ?? '');
  $feed_name = sanitize_text_field($_POST['feed_name'] ?? '');
  $feed_result = sanitize_text_field($_POST['feed_result'] ?? '');

  $training_partner_count = isset($_POST['training_partner_count']) && $_POST['training_partner_count'] !== ''
    ? intval($_POST['training_partner_count'])
    : '';

  $training_remaining_weeks = isset($_POST['training_remaining_weeks']) && $_POST['training_remaining_weeks'] !== ''
    ? intval($_POST['training_remaining_weeks'])
    : '';

  $training_memo = sanitize_textarea_field($_POST['training_memo'] ?? '');

  $training_aptitude_up = isset($_POST['training_aptitude_up']) ? '1' : '';
  $training_aptitude_up_items = [];

  if ($training_aptitude_up && !empty($_POST['training_aptitude_up_items']) && is_array($_POST['training_aptitude_up_items'])) {
    foreach ($_POST['training_aptitude_up_items'] as $item) {
      $item = sanitize_key($item);
      if ($item !== '') {
        $training_aptitude_up_items[] = $item;
      }
    }
  }

  $training_aptitude_up_items_text = implode(',', $training_aptitude_up_items);
  $training_aptitude_up_memo = '';

  if ($training_aptitude_up && !empty($training_aptitude_up_items)) {
    $training_aptitude_up_memo = sh4_apply_training_aptitude_up($horse_id, $training_aptitude_up_items);
  }

  $post_id = wp_insert_post([
    'post_type'   => 'training',
    'post_status' => 'publish',
    'post_title'  => 'training-' . $horse_id . '-' . microtime(true),
    'post_author' => get_current_user_id()
  ]);

  if ($post_id) {

    update_field('horse', $horse_id, $post_id);
    update_field('training_type', $training_type, $post_id);
    update_field('training_type_other', $training_type_other, $post_id);
    update_field('training_name', $training_name, $post_id);
    update_field('training_result', $training_result, $post_id);
    update_field('feed_name', $feed_name, $post_id);
    update_field('feed_result', $feed_result, $post_id);
    update_field('training_partner_count', $training_partner_count, $post_id);
    update_field('training_remaining_weeks', $training_remaining_weeks, $post_id);
    update_field('training_memo', $training_memo, $post_id);
    update_field('training_aptitude_up', $training_aptitude_up, $post_id);
    update_field('training_aptitude_up_items', $training_aptitude_up_items_text, $post_id);
    update_field('training_aptitude_up_memo', $training_aptitude_up_memo, $post_id);

  }

  clean_post_cache($horse_id);

  if (function_exists('do_action')) {
    do_action('litespeed_purge_url', get_permalink($horse_id));
    do_action('litespeed_purge_url', home_url('/horse/'));
  }

  wp_safe_redirect(get_permalink($horse_id));
  exit;

});

add_action('init', function(){

  if (!isset($_POST['delete_result_id'])) {
    return;
  }

  $result_id = intval($_POST['delete_result_id']);

  if ($result_id) {
    sh4_redirect_to_login_if_needed();

    $horse_id = sh4_get_related_horse_id_from_record($result_id);

    if (!$horse_id || !sh4_current_user_can_manage_horse($horse_id)) {
      wp_die('この出走履歴を削除する権限がありません。');
    }

    wp_delete_post($result_id, true);

    if ($horse_id) {
      sh4_update_total_prize($horse_id);
      clean_post_cache($horse_id);
    }
  }

});

add_action('init', function(){

  if (!isset($_POST['delete_horse_id'])) {
    return;
  }

  $horse_id = intval($_POST['delete_horse_id']);

  if (!$horse_id) {
    return;
  }

  sh4_redirect_to_login_if_needed();

  if (!sh4_current_user_can_manage_horse($horse_id)) {
    wp_die('この馬を削除する権限がありません。');
  }

  $results = get_posts([
    'post_type' => 'result',
    'posts_per_page' => -1,
    'meta_query' => [
      [
        'key' => 'horse',
        'value' => $horse_id,
        'compare' => '='
      ]
    ]
  ]);

  foreach ($results as $result) {
    wp_delete_post($result->ID, true);
  }

  $trainings = get_posts([
    'post_type' => 'training',
    'posts_per_page' => -1,
    'meta_query' => [
      [
        'key' => 'horse',
        'value' => $horse_id,
        'compare' => '='
      ]
    ]
  ]);

  foreach ($trainings as $training) {
    wp_delete_post($training->ID, true);
  }

  wp_delete_post($horse_id, true);

  wp_redirect(home_url('/horse/'));
  exit;

});

add_action('init', function(){

  if (!isset($_POST['add_horse'])) {
    return;
  }

  $name       = sanitize_text_field($_POST['horse_name'] ?? '');
  $kyakushitu = sanitize_text_field($_POST['kyakushitu'] ?? '');
  $memo       = sanitize_textarea_field($_POST['memo'] ?? '');
  $horse_visibility = sh4_normalize_horse_visibility($_POST['horse_visibility'] ?? 'public');

  $gender    = sanitize_text_field($_POST['gender'] ?? '');
  $tokusei_1 = sanitize_text_field($_POST['tokusei_1'] ?? '');
  $tokusei_2 = sanitize_text_field($_POST['tokusei_2'] ?? '');
  $tokusei_3 = sanitize_text_field($_POST['tokusei_3'] ?? '');

  $start_type       = sanitize_text_field($_POST['start_type'] ?? '');
  $condition_losses = isset($_POST['condition_losses']) && $_POST['condition_losses'] !== ''
    ? intval($_POST['condition_losses'])
    : 0;

  $initial_ranks = sh4_normalize_initial_races_by_route(
    $start_type,
    $condition_losses,
    $_POST['asahi_rank'] ?? '',
    $_POST['hopeful_rank'] ?? '',
    $_POST['foy_rank'] ?? '',
    $_POST['pre_world_rank'] ?? ''
  );

  $asahi_rank = $initial_ranks['asahi'];
  $hopeful_rank = $initial_ranks['hopeful'];
  $foy_rank = $initial_ranks['foy'];
  $pre_world_rank = $initial_ranks['pre_world'];

  $condition_races = 0;

  if ($start_type === '3歳スタート') {
    $condition_races = 3;
  } elseif ($start_type === '古馬スタート') {
    $condition_races = 4;
  }

  $condition_wins = max(0, $condition_races - $condition_losses);

  if (!$name) {
    return;
  }

  sh4_redirect_to_login_if_needed();

  $horse_id = wp_insert_post([
    'post_type'   => 'horse',
    'post_status' => 'publish',
    'post_title'  => $name,
    'post_author' => get_current_user_id()
  ]);

  if ($horse_id) {

    update_field('kyakushitu', $kyakushitu, $horse_id);
    update_field('memo', $memo, $horse_id);
    update_field('horse_visibility', $horse_visibility, $horse_id);
    update_field('gender', $gender, $horse_id);

    update_field('tokusei_1', $tokusei_1, $horse_id);
    update_field('tokusei_2', $tokusei_2, $horse_id);
    update_field('tokusei_3', $tokusei_3, $horse_id);

    update_field('start_type', $start_type, $horse_id);
    update_field('condition_races', $condition_races, $horse_id);
    update_field('condition_wins', $condition_wins, $horse_id);
    update_field('condition_losses', $condition_losses, $horse_id);

    update_field('asahi_rank', $asahi_rank, $horse_id);
    update_field('hopeful_rank', $hopeful_rank, $horse_id);
    update_field('foy_rank', $foy_rank, $horse_id);
    update_field('pre_world_rank', $pre_world_rank, $horse_id);

    update_field('base_weeks', 120, $horse_id);
    update_field('bonus_weeks', 0, $horse_id);

    sh4_update_total_prize($horse_id);

    wp_safe_redirect(get_permalink($horse_id));
    exit;
  }

});

add_action('save_post_horse', function($post_id){

  static $is_updating = false;

  if ($is_updating) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (wp_is_post_revision($post_id)) {
    return;
  }

  if (get_post_type($post_id) !== 'horse') {
    return;
  }

  $number = str_pad($post_id, 5, '0', STR_PAD_LEFT);

  update_post_meta($post_id, 'horse_number', $number);

  $current_slug = get_post_field('post_name', $post_id);

  if ($current_slug !== $number) {

    $is_updating = true;

    wp_update_post([
      'ID' => $post_id,
      'post_name' => $number
    ]);

    $is_updating = false;
  }

}, 20);

add_action('template_redirect', function(){

  if (!isset($_POST['update_horse'])) {
    return;
  }

  $horse_id = intval($_POST['edit_horse_id'] ?? 0);

  if (!$horse_id) {
    return;
  }

  sh4_redirect_to_login_if_needed();

  if (!sh4_current_user_can_manage_horse($horse_id)) {
    wp_die('この馬を編集する権限がありません。');
  }

  $name       = sanitize_text_field($_POST['edit_horse_name'] ?? '');
  $gender     = sanitize_text_field($_POST['edit_gender'] ?? '');
  $kyakushitu = sanitize_text_field($_POST['edit_kyakushitu'] ?? '');
  $tokusei_1  = sanitize_text_field($_POST['edit_tokusei_1'] ?? '');
  $tokusei_2  = sanitize_text_field($_POST['edit_tokusei_2'] ?? '');
  $tokusei_3  = sanitize_text_field($_POST['edit_tokusei_3'] ?? '');
  $memo       = sanitize_textarea_field($_POST['edit_memo'] ?? '');
  $horse_visibility = sh4_normalize_horse_visibility($_POST['edit_horse_visibility'] ?? 'public');

  $start_type       = sanitize_text_field($_POST['edit_start_type'] ?? '');
  $condition_losses = isset($_POST['edit_condition_losses']) && $_POST['edit_condition_losses'] !== ''
    ? intval($_POST['edit_condition_losses'])
    : 0;

  $initial_ranks = sh4_normalize_initial_races_by_route(
    $start_type,
    $condition_losses,
    $_POST['edit_asahi_rank'] ?? '',
    $_POST['edit_hopeful_rank'] ?? '',
    $_POST['edit_foy_rank'] ?? '',
    $_POST['edit_pre_world_rank'] ?? ''
  );

  $asahi_rank = $initial_ranks['asahi'];
  $hopeful_rank = $initial_ranks['hopeful'];
  $foy_rank = $initial_ranks['foy'];
  $pre_world_rank = $initial_ranks['pre_world'];

  $condition_races = 0;

  if ($start_type === '3歳スタート') {
    $condition_races = 3;
  } elseif ($start_type === '古馬スタート') {
    $condition_races = 4;
  }

  $condition_wins = max(0, $condition_races - $condition_losses);

  if ($name) {
    wp_update_post([
      'ID' => $horse_id,
      'post_title' => $name
    ]);
  }

  update_field('gender', $gender, $horse_id);
  update_field('kyakushitu', $kyakushitu, $horse_id);
  update_field('memo', $memo, $horse_id);
  update_field('horse_visibility', $horse_visibility, $horse_id);

  update_field('tokusei_1', $tokusei_1, $horse_id);
  update_field('tokusei_2', $tokusei_2, $horse_id);
  update_field('tokusei_3', $tokusei_3, $horse_id);

  update_field('start_type', $start_type, $horse_id);
  update_field('condition_races', $condition_races, $horse_id);
  update_field('condition_wins', $condition_wins, $horse_id);
  update_field('condition_losses', $condition_losses, $horse_id);

  update_field('asahi_rank', $asahi_rank, $horse_id);
  update_field('hopeful_rank', $hopeful_rank, $horse_id);
  update_field('foy_rank', $foy_rank, $horse_id);
  update_field('pre_world_rank', $pre_world_rank, $horse_id);

  sh4_save_initial_aptitudes($horse_id);

  sh4_update_total_prize($horse_id);

  wp_safe_redirect(get_permalink($horse_id));
  exit;

});


/* =========================
  残り週・加算週の直接編集
========================= */
add_action('template_redirect', function(){

  if (
    !isset($_POST['sh4_update_weeks']) ||
    $_SERVER['REQUEST_METHOD'] !== 'POST'
  ) {
    return;
  }

  $horse_id = intval($_POST['edit_horse_id'] ?? 0);

  if (!$horse_id) {
    return;
  }

  sh4_redirect_to_login_if_needed();

  if (!sh4_current_user_can_manage_horse($horse_id)) {
    wp_die('この馬の残り週を編集する権限がありません。');
  }

  if (
    !isset($_POST['sh4_weeks_nonce']) ||
    !wp_verify_nonce($_POST['sh4_weeks_nonce'], 'sh4_update_weeks_' . $horse_id)
  ) {
    wp_die('不正な送信です。');
  }

  $manual_remaining_weeks = isset($_POST['manual_remaining_weeks']) && $_POST['manual_remaining_weeks'] !== ''
    ? max(0, intval($_POST['manual_remaining_weeks']))
    : '';

  $bonus_weeks = isset($_POST['bonus_weeks']) && $_POST['bonus_weeks'] !== ''
    ? intval($_POST['bonus_weeks'])
    : 0;

  update_field('manual_remaining_weeks', $manual_remaining_weeks, $horse_id);
  update_post_meta($horse_id, 'manual_remaining_weeks', $manual_remaining_weeks);

  update_field('bonus_weeks', $bonus_weeks, $horse_id);
  update_post_meta($horse_id, 'bonus_weeks', $bonus_weeks);

  clean_post_cache($horse_id);

  if (function_exists('do_action')) {
    do_action('litespeed_purge_url', get_permalink($horse_id));
    do_action('litespeed_purge_url', home_url('/horse/'));
  }

  wp_safe_redirect(get_permalink($horse_id));
  exit;

});

add_action('send_headers', function(){

  if (
    is_post_type_archive('horse') ||
    is_singular('horse')
  ) {

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');

  }

});

add_action('wp_head', function(){
?>
<style>

@media screen and (max-width:768px){

  .result-table thead{
    display:none !important;
  }

  .result-table tr{
    display:block !important;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:8px;
    padding:10px;
    background:#fff;
  }

  .result-table td{
    display:flex !important;
    justify-content:space-between;
    padding:5px 0;
    border:none;
  }

  .result-table td::before{
    content: attr(data-label);
    font-weight:bold;
    color:#555;
  }

}

</style>
<?php
});

