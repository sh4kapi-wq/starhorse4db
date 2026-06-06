<?php get_header(); ?>
<?php /* SH4 generated 20260606-111850 - race order insert support */ ?>

<?php
if (!function_exists('sh4_aptitude_label')) {
function sh4_aptitude_label($value) {
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

  if (isset($labels[(int)$value]) && (string)(int)$value === (string)$value) {
    return $labels[(int)$value];
  }

  return $value;
}
}


if (!function_exists('sh4_temper_label')) {
function sh4_temper_label($value) {
  if ($value === null || $value === '') {
    return '-';
  }

  $labels = [
    'calm' => '穏やか',
    'normal' => '普通',
    'rough' => '荒い',
    'intense' => '激しい',
  ];

  return $labels[$value] ?? $value;
}
}


$current_horse_id = get_the_ID();
$can_manage_horse = function_exists('sh4_current_user_can_manage_horse')
  ? sh4_current_user_can_manage_horse($current_horse_id)
  : current_user_can('manage_options');

$horse_visibility = function_exists('sh4_get_horse_visibility')
  ? sh4_get_horse_visibility($current_horse_id)
  : (get_field('horse_visibility') ?: 'public');

$can_view_horse = function_exists('sh4_current_user_can_view_horse')
  ? sh4_current_user_can_view_horse($current_horse_id)
  : true;

if (!$can_view_horse) :
?>

<div class="container">
  <div class="owner-only-notice private-horse-notice">
    <p>この馬は非公開です。</p>
    <p>所有者または管理者のみ閲覧できます。</p>
    <?php if (!is_user_logged_in()) : ?>
      <p><a class="login-btn" href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">ログインする</a></p>
    <?php endif; ?>
  </div>
</div>

<?php
get_footer();
exit;
endif;


$distance_options = [
  '1200_under' => '1200未満',
  '1200' => '1200',
  '1400' => '1400',
  '1600' => '1600',
  '1800' => '1800',
  '2000' => '2000',
  '2200' => '2200',
  '2400' => '2400',
  '2600' => '2600',
  '2800' => '2800',
  '3000' => '3000',
  '3200' => '3200',
  '3200_over' => '3200超',
];

if (!function_exists('sh4_distance_label')) {
function sh4_distance_label($value, $distance_options) {
  if ($value === null || $value === '') {
    return '-';
  }

  return $distance_options[$value] ?? $value;
}
}


if (!function_exists('sh4_distance_range_label')) {
function sh4_distance_range_label($min, $max, $distance_options) {
  if (($min === null || $min === '') && ($max === null || $max === '')) {
    return '-';
  }

  $min_label = sh4_distance_label($min, $distance_options);
  $max_label = sh4_distance_label($max, $distance_options);

  return $min_label . '〜' . $max_label;
}
}


if (!function_exists('sh4_breaking_mark')) {
function sh4_breaking_mark($key) {
  return get_field('breaking_' . $key . '_done') ? '✓' : '';
}
}


if (!function_exists('sh4_rank_value')) {
function sh4_rank_value($value) {
  if ($value === null || $value === '') {
    return '';
  }

  $rank = intval($value);

  if ($rank < 1 || $rank > 18) {
    return '';
  }

  return $rank;
}
}

if (!function_exists('sh4_initial_race_rows')) {
function sh4_initial_race_rows($start_type, $condition_losses, $asahi_rank, $hopeful_rank, $foy_rank, $pre_world_rank, $initial_prizes) {

  $asahi_rank = sh4_rank_value($asahi_rank);
  $hopeful_rank = sh4_rank_value($hopeful_rank);
  $foy_rank = sh4_rank_value($foy_rank);
  $pre_world_rank = sh4_rank_value($pre_world_rank);

  $rows = [];

  if ((int)$condition_losses > 0) {
    return $rows;
  }

  if ($start_type === '3歳スタート') {

    if ($asahi_rank) {
      $rows[] = [
        'key' => 'asahi',
        'race_name' => '朝日杯FS',
        'rank' => $asahi_rank,
        'prize' => isset($initial_prizes['asahi'][$asahi_rank]) ? (int)$initial_prizes['asahi'][$asahi_rank] : 0,
        'memo' => '初期レース',
      ];
    }

    if ($asahi_rank === 1 && $hopeful_rank) {
      $rows[] = [
        'key' => 'hopeful',
        'race_name' => 'ホープフルS',
        'rank' => $hopeful_rank,
        'prize' => isset($initial_prizes['hopeful'][$hopeful_rank]) ? (int)$initial_prizes['hopeful'][$hopeful_rank] : 0,
        'memo' => '初期レース',
      ];
    }

    if ($asahi_rank === 1 && $hopeful_rank === 1 && $pre_world_rank) {
      $rows[] = [
        'key' => 'pre_world',
        'race_name' => 'プレワールド',
        'rank' => $pre_world_rank,
        'prize' => isset($initial_prizes['pre_world'][$pre_world_rank]) ? (int)$initial_prizes['pre_world'][$pre_world_rank] : 0,
        'memo' => '初期レース',
      ];
    }

  } elseif ($start_type === '古馬スタート') {

    if ($foy_rank) {
      $rows[] = [
        'key' => 'foy',
        'race_name' => 'フォア賞',
        'rank' => $foy_rank,
        'prize' => isset($initial_prizes['foy'][$foy_rank]) ? (int)$initial_prizes['foy'][$foy_rank] : 0,
        'memo' => '初期レース',
      ];
    }

    if ($foy_rank === 1 && $pre_world_rank) {
      $rows[] = [
        'key' => 'pre_world',
        'race_name' => 'プレワールド',
        'rank' => $pre_world_rank,
        'prize' => isset($initial_prizes['pre_world'][$pre_world_rank]) ? (int)$initial_prizes['pre_world'][$pre_world_rank] : 0,
        'memo' => '初期レース',
      ];
    }

  }

  return $rows;
}
}


if (!function_exists('sh4_training_result_short_label')) {
function sh4_training_result_short_label($value) {
  if ($value === null || $value === '') {
    return '-';
  }

  $labels = [
    '本格化' => '本',
    '超成功' => '☆',
    '大成功' => '◎',
    '成功' => '〇',
    '普通' => '△',
  ];

  return $labels[$value] ?? $value;
}
}




if (!function_exists('sh4_memo_preview_text')) {
function sh4_memo_preview_text($text, $length = 10) {
  $text = trim((string)$text);

  if ($text === '') {
    return '-';
  }

  if (function_exists('mb_strlen') && function_exists('mb_substr')) {
    if (mb_strlen($text, 'UTF-8') > $length) {
      return mb_substr($text, 0, $length, 'UTF-8') . '…';
    }
  } elseif (strlen($text) > $length) {
    return substr($text, 0, $length) . '...';
  }

  return $text;
}
}

if (!function_exists('sh4_memo_is_long')) {
function sh4_memo_is_long($text, $length = 10) {
  $text = trim((string)$text);

  if ($text === '') {
    return false;
  }

  if (function_exists('mb_strlen')) {
    return mb_strlen($text, 'UTF-8') > $length;
  }

  return strlen($text) > $length;
}
}

$base_weeks = (int)get_field('base_weeks');
$bonus_weeks = (int)get_field('bonus_weeks');
$manual_remaining_weeks = get_field('manual_remaining_weeks');

if (!$base_weeks) {
  $base_weeks = 120;
}

$aptitudes = [
  '芝' => 'turf',
  'ダート' => 'dirt',
  '道悪' => 'bad_track',
  '坂' => 'slope',
  '体質' => 'constitution',
  '根性' => 'guts',
  '気性' => 'temper',
  'スタート' => 'start',
  '右回り' => 'right_turn',
  '左回り' => 'left_turn',
  '小回り' => 'tight_turn',
  '遠征' => 'expedition',
  '瞬発力' => 'acceleration',
];

$breaking_keys = [
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

$aptitude_options = [
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

$temper_options = [
  'calm' => '穏やか',
  'normal' => '普通',
  'rough' => '荒い',
  'intense' => '激しい',
];
?>

<div class="container">
<div class="horse-wrap">

  <h1 class="horse-title"><?php the_title(); ?></h1>

  <div class="horse-info-tabs">
    <button type="button" class="tab-btn active" data-target="horse-basic-panel">基本情報</button>
    <button type="button" class="tab-btn" data-target="horse-aptitude-panel">適性</button>
  </div>

  <div id="horse-basic-panel" class="tab-panel">

    <div class="horse-profile-card">

      <div class="horse-profile-row horse-profile-row-3">

        <div class="profile-item">
          <span class="profile-label">性別</span>
          <span class="profile-value"><?php echo esc_html(get_field('gender') ?: '-'); ?></span>
        </div>

        <div class="profile-item">
          <span class="profile-label">脚質</span>
          <span class="profile-value"><?php echo esc_html(get_field('kyakushitu') ?: '-'); ?></span>
        </div>

        <div class="profile-item">
          <span class="profile-label">デビュー</span>
          <span class="profile-value">
            <?php
            $start_type = get_field('start_type');

            if ($start_type === '3歳スタート') {
              echo '3歳';
            } elseif ($start_type === '古馬スタート') {
              echo '古馬';
            } else {
              echo '-';
            }
            ?>
          </span>
        </div>

      </div>

      <?php
      $horse_owner_id = intval(get_post_field('post_author', get_the_ID()));
      $horse_stable_name = function_exists('sh4_get_stable_name') ? sh4_get_stable_name($horse_owner_id) : '名無し厩舎';
      ?>

      <div class="profile-item horse-owner-profile">
        <span class="profile-label">厩舎</span>
        <span class="profile-value"><?php echo esc_html($horse_stable_name); ?></span>
      </div>

      <div class="profile-item horse-visibility-profile">
        <span class="profile-label">公開設定</span>
        <span class="profile-value"><?php echo esc_html($horse_visibility === 'private' ? '非公開' : '公開'); ?></span>
      </div>

      <div class="horse-traits">

        <div class="trait-box">
          <span class="profile-label">特性1</span>
          <span class="trait-value"><?php echo esc_html(get_field('tokusei_1') ?: '-'); ?></span>
        </div>

        <div class="trait-box">
          <span class="profile-label">特性2</span>
          <span class="trait-value"><?php echo esc_html(get_field('tokusei_2') ?: '-'); ?></span>
        </div>

        <div class="trait-box">
          <span class="profile-label">特性3</span>
          <span class="trait-value"><?php echo esc_html(get_field('tokusei_3') ?: '-'); ?></span>
        </div>

      </div>

      <div class="profile-item">
        <span class="profile-label">条件戦</span>
        <span class="profile-value">
          <?php echo esc_html(get_field('condition_wins') ?: 0); ?>勝
          <?php echo esc_html(get_field('condition_losses') ?: 0); ?>敗
        </span>
      </div>

      <div class="horse-memo-box">
        <span class="profile-label">メモ</span>
        <div class="memo-content">
          <?php echo nl2br(esc_html(get_field('memo') ?: '')); ?>
        </div>
      </div>

    </div>

  </div>

  <div id="horse-aptitude-panel" class="tab-panel" style="display:none;">

    <div class="horse-profile-card">

      <div class="aptitude-title-row">
        <h2 class="aptitude-title">適性</h2>
        <?php if ($can_manage_horse) : ?>
          <button type="button" class="aptitude-edit-jump-btn" aria-label="適性を編集">✏️</button>
        <?php endif; ?>
      </div>

      <div class="aptitude-table-wrap">
        <table class="aptitude-table">
          <thead>
            <tr>
              <th>項目</th>
              <th>生産時</th>
              <th>馴致</th>
              <th>現在</th>
            </tr>
          </thead>
          <tbody>

            <tr>
              <th>距離</th>
              <td>
                <?php
                echo esc_html(sh4_distance_range_label(
                  get_field('aptitude_distance_min_initial'),
                  get_field('aptitude_distance_max_initial'),
                  $distance_options
                ));
                ?>
              </td>
              <td>-</td>
              <td>
                <?php
                echo esc_html(sh4_distance_range_label(
                  get_field('aptitude_distance_min_current'),
                  get_field('aptitude_distance_max_current'),
                  $distance_options
                ));
                ?>
              </td>
            </tr>

            <?php foreach ($aptitudes as $label => $key) : ?>
              <?php
              $initial = get_field('aptitude_' . $key . '_initial');
              $current = get_field('aptitude_' . $key . '_current');
              ?>
              <tr>
                <th><?php echo esc_html($label); ?></th>

                <?php if ($key === 'temper') : ?>
                  <td><?php echo esc_html(sh4_temper_label($initial)); ?></td>
                  <td>-</td>
                  <td><?php echo esc_html(sh4_temper_label($current)); ?></td>
                <?php else : ?>
                  <td><?php echo esc_html(sh4_aptitude_label($initial)); ?></td>
                  <td class="breaking-mark"><?php echo esc_html(sh4_breaking_mark($key)); ?></td>
                  <td><?php echo esc_html(sh4_aptitude_label($current)); ?></td>
                <?php endif; ?>

              </tr>
            <?php endforeach; ?>

          </tbody>
        </table>
      </div>

    </div>

  </div>

  <?php

  $results = get_posts([
    'post_type' => 'result',
    'posts_per_page' => -1,
    'meta_query' => [
      [
        'key' => 'horse',
        'value' => get_the_ID(),
        'compare' => '='
      ]
    ]
  ]);

  $trainings_for_count = get_posts([
    'post_type' => 'training',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'meta_query' => [
      [
        'key' => 'horse',
        'value' => get_the_ID(),
        'compare' => '='
      ]
    ]
  ]);

  $total_trainings = count($trainings_for_count);

  $total_prize = 0;

  $initial_prizes = [
    'asahi' => [1 => 1500, 2 => 600, 3 => 375, 4 => 225, 5 => 150],
    'hopeful' => [1 => 1500, 2 => 600, 3 => 375, 4 => 225, 5 => 150],
    'foy' => [1 => 500, 2 => 200, 3 => 125, 4 => 75, 5 => 50],
    'pre_world' => [1 => 20000, 2 => 8000, 3 => 5000, 4 => 3000, 5 => 2000],
  ];


  $start_type_for_initial = get_field('start_type');
  $condition_losses_for_initial = (int)get_field('condition_losses');

  $asahi_rank = sh4_rank_value(get_field('asahi_rank'));
  $hopeful_rank = sh4_rank_value(get_field('hopeful_rank'));
  $foy_rank = sh4_rank_value(get_field('foy_rank'));
  $pre_world_rank = sh4_rank_value(get_field('pre_world_rank'));

  $initial_history_rows = sh4_initial_race_rows(
    $start_type_for_initial,
    $condition_losses_for_initial,
    $asahi_rank,
    $hopeful_rank,
    $foy_rank,
    $pre_world_rank,
    $initial_prizes
  );

  $record_grade_labels = [
    'SWBC' => 'SWBC',
    'WBC' => 'WBC',
    '海外G1' => '海外G1',
    '国内G1' => '国内G1',
    'G2' => '国内G2',
    'G3' => '国内G3',
  ];

  $record = [];

  foreach ($record_grade_labels as $grade_key => $grade_label) {
    $record[$grade_key] = [
      'label' => $grade_label,
      'first' => 0,
      'second' => 0,
      'third' => 0,
      'outside' => 0,
    ];
  }

  $total_races = 0;
  $total_wins = 0;

  // 残り週の消化対象は、通常の出走履歴 + 調教履歴のみです。
  // 条件戦・朝日杯・ホープフル・フォア賞・プレワールドは
  // デビュー時の初期成績として扱うため、残り週の消化には含めません。
  $week_count_races = 0;

  $total_races += (int)get_field('condition_races');
  $total_wins += (int)get_field('condition_wins');

  foreach ($initial_history_rows as $initial_row_for_total) {
    $total_races++;

    $initial_record_grade = '';

    if ($initial_row_for_total['key'] === 'asahi' || $initial_row_for_total['key'] === 'hopeful') {
      $initial_record_grade = '国内G1';
    } elseif ($initial_row_for_total['key'] === 'foy') {
      $initial_record_grade = 'G2';
    } elseif ($initial_row_for_total['key'] === 'pre_world') {
      $initial_record_grade = 'WBC';
    }

    $initial_rank = (int)$initial_row_for_total['rank'];

    if ($initial_rank === 1) {
      $total_wins++;
    }

    if ($initial_record_grade && isset($record[$initial_record_grade])) {
      if ($initial_rank === 1) {
        $record[$initial_record_grade]['first']++;
      } elseif ($initial_rank === 2) {
        $record[$initial_record_grade]['second']++;
      } elseif ($initial_rank === 3) {
        $record[$initial_record_grade]['third']++;
      } elseif ($initial_rank >= 4) {
        $record[$initial_record_grade]['outside']++;
      }
    }

    $total_prize += (int)$initial_row_for_total['prize'];
  }

  foreach ($results as $result) {

    $race_id = get_field('race', $result->ID);
    $rank = get_field('rank', $result->ID);

    if (!$race_id) continue;

    $total_races++;
    $week_count_races++;

    $prize = $rank ? (int)get_field('prize_' . $rank, $race_id) : 0;
    $total_prize += $prize;

    $rank_num = (int)$rank;
    $grade = get_field('grade', $race_id);

    if ($rank_num === 1) {
      $total_wins++;
    }

    if (isset($record[$grade])) {
      if ($rank_num === 1) {
        $record[$grade]['first']++;
      } elseif ($rank_num === 2) {
        $record[$grade]['second']++;
      } elseif ($rank_num === 3) {
        $record[$grade]['third']++;
      } elseif ($rank_num >= 4) {
        $record[$grade]['outside']++;
      }
    }
  }

  update_field('total_prize', (int)$total_prize, get_the_ID());
  update_post_meta(get_the_ID(), 'total_prize', (int)$total_prize);

  $used_weeks = $week_count_races + $total_trainings;
  $auto_remaining_weeks = $base_weeks + $bonus_weeks - $used_weeks;

  $current_horse_id = get_the_ID();
  $latest_manual_remaining_weeks = '';
  $latest_manual_remaining_time = 0;

  $latest_remaining_results = get_posts([
    'post_type' => 'result',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' => 'horse',
        'value' => $current_horse_id,
        'compare' => '='
      ],
      [
        'key' => 'race_remaining_weeks',
        'compare' => 'EXISTS'
      ],
      [
        'key' => 'race_remaining_weeks',
        'value' => '',
        'compare' => '!='
      ]
    ]
  ]);

  if (!empty($latest_remaining_results)) {
    $latest_manual_remaining_weeks = get_post_meta($latest_remaining_results[0]->ID, 'race_remaining_weeks', true);
    $latest_manual_remaining_time = strtotime($latest_remaining_results[0]->post_date);
  }

  $latest_remaining_trainings = get_posts([
    'post_type' => 'training',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' => 'horse',
        'value' => $current_horse_id,
        'compare' => '='
      ],
      [
        'key' => 'training_remaining_weeks',
        'compare' => 'EXISTS'
      ],
      [
        'key' => 'training_remaining_weeks',
        'value' => '',
        'compare' => '!='
      ]
    ]
  ]);

  if (!empty($latest_remaining_trainings)) {
    $training_remaining_time = strtotime($latest_remaining_trainings[0]->post_date);

    if (
      $latest_manual_remaining_weeks === '' ||
      $training_remaining_time >= $latest_manual_remaining_time
    ) {
      $latest_manual_remaining_weeks = get_post_meta($latest_remaining_trainings[0]->ID, 'training_remaining_weeks', true);
      $latest_manual_remaining_time = $training_remaining_time;
    }
  }

  if ($manual_remaining_weeks !== '' && $manual_remaining_weeks !== null) {
    $remaining_weeks = (int)$manual_remaining_weeks;
    $remaining_mode = 'manual';
  } elseif ($latest_manual_remaining_weeks !== '' && $latest_manual_remaining_weeks !== null) {
    $remaining_weeks = (int)$latest_manual_remaining_weeks;
    $remaining_mode = 'history';
  } else {
    $remaining_weeks = $auto_remaining_weeks;
    $remaining_mode = 'auto';
  }

  ?>

  <div class="horse-summary">

    <div class="remaining-weeks-card">

      <div class="remaining-weeks-main">
        <span class="remaining-weeks-label">残り週</span>
        <span class="remaining-weeks-value">
          <?php echo esc_html($remaining_weeks); ?>週
        </span>
        <?php if ($can_manage_horse) : ?>
          <button type="button" class="week-edit-btn" data-target="week-edit-panel" aria-label="残り週を編集">✏️</button>
        <?php endif; ?>
        <span class="remaining-weeks-separator">/</span>
        <span class="remaining-weeks-label">加算</span>
        <span class="remaining-weeks-value">
          +<?php echo esc_html($bonus_weeks); ?>週
        </span>
        <?php if ($can_manage_horse) : ?>
          <button type="button" class="week-edit-btn" data-target="week-edit-panel" aria-label="加算週を編集">✏️</button>
        <?php endif; ?>
      </div>

      <?php if ($can_manage_horse) : ?>
        <div id="week-edit-panel" class="week-edit-panel" style="display:none;">
          <form method="post" class="week-edit-form">
            <?php wp_nonce_field('sh4_update_weeks_' . get_the_ID(), 'sh4_weeks_nonce'); ?>
            <input type="hidden" name="edit_horse_id" value="<?php echo esc_attr(get_the_ID()); ?>">

            <label>
              残り週
              <input type="number" name="manual_remaining_weeks" min="0" max="999" value="<?php echo esc_attr($remaining_weeks); ?>">
            </label>

            <label>
              加算
              <input type="number" name="bonus_weeks" min="-999" max="999" value="<?php echo esc_attr($bonus_weeks); ?>">
            </label>

            <button type="submit" name="sh4_update_weeks" class="week-save-btn">保存</button>
            <button type="button" class="week-cancel-btn" data-target="week-edit-panel">閉じる</button>
          </form>
        </div>
      <?php endif; ?>

    </div>

    <p class="horse-prize">
      獲得賞金：<?php echo number_format($total_prize); ?>枚
    </p>

    <p class="horse-total-record">
      <?php echo $total_races; ?>戦<?php echo $total_wins; ?>勝
    </p>

    <div class="horse-record-table-wrap">

      <table class="horse-record-table">
        <thead>
          <tr>
            <th>グレード</th>
            <th>1着</th>
            <th>2着</th>
            <th>3着</th>
            <th>着外</th>
            <th>勝率</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($record as $record_row) : ?>
            <?php
            $record_grade_total =
              (int)$record_row['first'] +
              (int)$record_row['second'] +
              (int)$record_row['third'] +
              (int)$record_row['outside'];

            $record_win_rate = $record_grade_total > 0
              ? round(((int)$record_row['first'] / $record_grade_total) * 100, 1)
              : 0;
            ?>
            <tr>
              <th><?php echo esc_html($record_row['label']); ?></th>
              <td><?php echo esc_html($record_row['first']); ?></td>
              <td><?php echo esc_html($record_row['second']); ?></td>
              <td><?php echo esc_html($record_row['third']); ?></td>
              <td><?php echo esc_html($record_row['outside']); ?></td>
              <td><?php echo esc_html(number_format($record_win_rate, 1)); ?>%</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>

  </div>

  <div class="history-main-tabs">
    <button type="button" class="tab-btn active" data-target="race-history-panel">出走履歴(<?php echo count($results) + count($initial_history_rows); ?>)</button>
    <?php if ($can_manage_horse) : ?>
      <button type="button" class="history-shortcut-btn" data-shortcut-target="race-add-panel" aria-label="出走履歴を追加">✏️</button>
    <?php endif; ?>
    <button type="button" class="tab-btn" data-target="training-history-panel">調教履歴(<?php echo esc_html($total_trainings); ?>)</button>
    <?php if ($can_manage_horse) : ?>
      <button type="button" class="history-shortcut-btn" data-shortcut-target="training-add-panel" aria-label="調教履歴を追加">✏️</button>
    <?php endif; ?>
  </div>

  <div id="race-history-panel" class="tab-panel">

    <?php

    $display_result_ids = function_exists('sh4_get_ordered_result_ids')
      ? sh4_get_ordered_result_ids(get_the_ID())
      : [];

    if (!empty($display_result_ids)) {
      $display_results = get_posts([
        'post_type' => 'result',
        'posts_per_page' => -1,
        'post__in' => $display_result_ids,
        'orderby' => 'post__in',
      ]);
    } else {
      $display_results = get_posts([
        'post_type' => 'result',
        'posts_per_page' => -1,
        'orderby' => 'ID',
        'order' => 'DESC',
        'meta_query' => [
          [
            'key' => 'horse',
            'value' => get_the_ID(),
            'compare' => '='
          ]
        ]
      ]);
    }

    $all_races_for_edit = [];

    if ($can_manage_horse) {
      $all_races_for_edit = get_transient('race_list_cache');

      if ($all_races_for_edit === false) {
        $all_races_for_edit = get_posts([
          'post_type' => 'race',
          'posts_per_page' => -1,
          'orderby' => 'title',
          'order' => 'ASC'
        ]);

        set_transient('race_list_cache', $all_races_for_edit, HOUR_IN_SECONDS);
      }
    }

    $race_order_display_labels = [];
    if (function_exists('sh4_get_ordered_result_ids')) {
      $race_order_display_ids = sh4_get_ordered_result_ids(get_the_ID());
    } else {
      $race_order_display_ids = !empty($display_results) ? wp_list_pluck($display_results, 'ID') : [];
    }

    foreach ($race_order_display_ids as $index => $order_result_id) {
      $order_result_id = intval($order_result_id);
      $order_race_id = get_field('race', $order_result_id);
      $order_race_name = $order_race_id ? get_the_title($order_race_id) : get_the_title($order_result_id);
      $race_order_display_labels[$index + 1] = $order_race_name ?: '出走履歴';
    }

    if ($display_results || $initial_history_rows) {

      echo '<div class="history-title-row"><h2>出走履歴</h2>';
      if ($can_manage_horse) {
        echo '<button type="button" class="history-shortcut-btn" data-shortcut-target="race-add-panel" aria-label="出走履歴を追加">✏️</button>';
      }
      echo '</div>';
      echo '<div class="race-filter-tabs" aria-label="出走履歴フィルター">';
      echo '<div class="race-filter-primary">';
      echo '<button type="button" class="race-filter-btn active" data-race-filter="all">全レース</button>';
      echo '</div>';
      echo '<div class="race-filter-grade-grid">';
      echo '<button type="button" class="race-filter-btn" data-race-filter="SWBC">SWBC</button>';
      echo '<button type="button" class="race-filter-btn" data-race-filter="WBC">WBC</button>';
      echo '<button type="button" class="race-filter-btn" data-race-filter="海外G1">海外G1</button>';
      echo '<button type="button" class="race-filter-btn" data-race-filter="国内G1">国内G1</button>';
      echo '<button type="button" class="race-filter-btn" data-race-filter="G2">国内G2</button>';
      echo '<button type="button" class="race-filter-btn" data-race-filter="G3">国内G3</button>';
      echo '</div>';
      echo '</div>';
      echo '<div class="table-wrap race-table-wrap">';
      echo '<table class="sh4-race-table race-history-table">';

      echo '
      <thead>
        <tr>
          <th class="race-col-name">レース</th>
          <th class="race-col-rank">着</th>
          <th class="race-col-popularity">人</th>
          <th class="race-col-odds">ｵｯｽﾞ</th>
          <th class="race-col-limit">単ラ</th>
          <th class="race-col-prize">賞金</th>
          <th class="race-col-week">残週</th>
          <th class="race-col-memo">メモ</th>
          <th class="race-col-delete">操作</th>
        </tr>
      </thead>
      <tbody>
      ';

      foreach ($display_results as $result) {

        $race_id = get_field('race', $result->ID);
        $rank = get_field('rank', $result->ID);
        $popularity = get_field('popularity', $result->ID);
        $odds = get_field('odds', $result->ID);
        $limit_odds = get_field('limit_odds', $result->ID);
        $race_remaining_weeks = get_field('race_remaining_weeks', $result->ID);
        $race_memo = get_field('race_memo', $result->ID);
        $race_order = get_post_meta($result->ID, 'race_order', true);

        if (!$race_id) continue;

        $prize = $rank ? (int)get_field('prize_' . $rank, $race_id) : 0;
        $race_grade = get_field('grade', $race_id);

        echo '<tr class="race-history-data-row" data-race-grade="'.esc_attr($race_grade).'">';

        echo '<td data-label="レース" class="race-col-name">'.esc_html(get_the_title($race_id)).'</td>';
        echo '<td data-label="着" class="race-col-rank">'.esc_html($rank).'着</td>';
        echo '<td data-label="人" class="race-col-popularity">'.esc_html($popularity).'</td>';
        echo '<td data-label="ｵｯｽﾞ" class="race-col-odds">'.esc_html(number_format((float)$odds, 1)).'</td>';
        echo '<td data-label="単ラ" class="race-col-limit">'.esc_html(number_format((float)$limit_odds, 3)).'</td>';
        echo '<td data-label="賞金" class="race-col-prize">'.esc_html(number_format($prize)).'</td>';
        echo '<td data-label="残週" class="race-col-week">'.esc_html($race_remaining_weeks !== '' && $race_remaining_weeks !== null ? $race_remaining_weeks : '-').'</td>';
        $race_memo_text = trim((string)($race_memo ?: ''));
        $race_memo_long = sh4_memo_is_long($race_memo_text);
        $race_memo_detail_id = 'race-memo-detail-' . intval($result->ID);

        echo '<td data-label="メモ" class="race-col-memo" title="'.esc_attr($race_memo_text).'">';
        echo '<div class="sh4-memo-inline">';
        echo '<span class="sh4-memo-preview">'.esc_html(sh4_memo_preview_text($race_memo_text)).'</span>';
        if ($race_memo_long) {
          echo '<button type="button" class="sh4-memo-toggle" data-target="'.esc_attr($race_memo_detail_id).'" aria-label="メモを表示">＋</button>';
        }
        echo '</div>';
        echo '</td>';

        $result_edit_id = 'result-edit-' . intval($result->ID);

        if ($can_manage_horse) {
          echo '<td data-label="操作" class="race-col-delete race-col-actions">';
          echo '<button type="button" class="edit-btn result-edit-toggle" data-target="'.esc_attr($result_edit_id).'">編集</button>';
          echo '<form method="post" onsubmit="return confirm(\'この出走履歴を削除しますか？\');">';
          echo '<input type="hidden" name="delete_result_id" value="'.esc_attr($result->ID).'">';
          echo '<button type="submit" class="delete-btn">削除</button>';
          echo '</form>';
          echo '</td>';
        } else {
          echo '<td data-label="操作" class="race-col-delete">-</td>';
        }

        echo '</tr>';

        if ($can_manage_horse) {
          echo '<tr id="'.esc_attr($result_edit_id).'" class="result-edit-row race-history-related-row" data-race-grade="'.esc_attr($race_grade).'">';
          echo '<td colspan="9" class="result-edit-cell">';
          echo '<form method="post" class="result-edit-form">';
          echo '<input type="hidden" name="result_id" value="'.esc_attr($result->ID).'">';

          echo '<div class="result-edit-grid">';

          $result_order_count = count($display_results);
          $current_result_order = intval($race_order ?: 0);
          if ($current_result_order < 1) {
            $current_result_order = 1;
          }
          echo '<p><label>出走順<br><select name="edit_result_order_position">';
          for ($i = 1; $i <= max(1, $result_order_count); $i++) {
            $edit_order_label = $i . '番目';
            if (!empty($race_order_display_labels[$i])) {
              $edit_order_label .= '：' . $race_order_display_labels[$i];
            }
            echo '<option value="'.esc_attr($i).'" '.selected($current_result_order, $i, false).'>'.esc_html($edit_order_label).'</option>';
          }
          echo '</select></label><span class="form-help-text">変更すると前後の出走順も自動で並び替わります。</span></p>';

          echo '<p><label>レース<br><select name="edit_result_race_id">';
          foreach ($all_races_for_edit as $edit_race) {
            echo '<option value="'.esc_attr($edit_race->ID).'" '.selected((int)$race_id, (int)$edit_race->ID, false).'>'.esc_html(get_the_title($edit_race->ID)).'</option>';
          }
          echo '</select></label></p>';

          echo '<p><label>着順<br><select name="edit_result_rank">';
          echo '<option value="">--</option>';
          for ($i = 1; $i <= 18; $i++) {
            echo '<option value="'.esc_attr($i).'" '.selected((int)$rank, $i, false).'>'.esc_html($i).'着</option>';
          }
          echo '</select></label></p>';

          echo '<p><label>人気<br><select name="edit_result_popularity">';
          echo '<option value="">--</option>';
          for ($i = 1; $i <= 18; $i++) {
            echo '<option value="'.esc_attr($i).'" '.selected((int)$popularity, $i, false).'>'.esc_html($i).'人気</option>';
          }
          echo '</select></label></p>';

          echo '<p><label>オッズ<br><input type="number" step="0.1" name="edit_result_odds" value="'.esc_attr($odds).'"></label></p>';
          echo '<p><label>単ラ<br><input type="number" step="0.001" name="edit_result_limit_odds" value="'.esc_attr($limit_odds).'"></label></p>';
          echo '<p><label>残週<br><input type="number" min="0" max="999" name="edit_result_remaining_weeks" value="'.esc_attr($race_remaining_weeks).'"></label></p>';

          echo '</div>';

          echo '<p><label>メモ<br><textarea name="edit_result_memo" rows="3">'.esc_textarea($race_memo_text).'</textarea></label></p>';

          echo '<div class="result-edit-actions">';
          echo '<button type="submit" name="update_result" class="add-btn">保存</button>';
          echo '<button type="button" class="back-btn result-edit-close" data-target="'.esc_attr($result_edit_id).'">閉じる</button>';
          echo '</div>';

          echo '</form>';
          echo '</td>';
          echo '</tr>';
        }

        if ($race_memo_long) {
          echo '<tr id="'.esc_attr($race_memo_detail_id).'" class="sh4-memo-detail-row race-history-related-row" data-race-grade="'.esc_attr($race_grade).'">';
          echo '<td colspan="9" class="sh4-memo-detail-cell">';
          echo '<div class="sh4-memo-detail-box">';
          echo '<div class="sh4-memo-detail-title">出走メモ全文</div>';
          echo '<div class="sh4-memo-detail-text">'.nl2br(esc_html($race_memo_text)).'</div>';
          echo '<button type="button" class="sh4-memo-close" data-target="'.esc_attr($race_memo_detail_id).'">閉じる</button>';
          echo '</div>';
          echo '</td>';
          echo '</tr>';
        }
      }

      foreach ($initial_history_rows as $initial_row) {

        $initial_filter_grade = '';
        if ($initial_row['key'] === 'asahi' || $initial_row['key'] === 'hopeful') {
          $initial_filter_grade = '国内G1';
        } elseif ($initial_row['key'] === 'foy') {
          $initial_filter_grade = 'G2';
        } elseif ($initial_row['key'] === 'pre_world') {
          $initial_filter_grade = 'WBC';
        }

        echo '<tr class="initial-race-row race-history-data-row" data-race-grade="'.esc_attr($initial_filter_grade).'">';

        echo '<td data-label="レース" class="race-col-name">'.esc_html($initial_row['race_name']).'</td>';
        echo '<td data-label="着" class="race-col-rank">'.esc_html($initial_row['rank']).'着</td>';
        echo '<td data-label="人" class="race-col-popularity">-</td>';
        echo '<td data-label="ｵｯｽﾞ" class="race-col-odds">-</td>';
        echo '<td data-label="単ラ" class="race-col-limit">-</td>';
        echo '<td data-label="賞金" class="race-col-prize">'.esc_html(number_format($initial_row['prize'])).'</td>';
        echo '<td data-label="残週" class="race-col-week">-</td>';
        echo '<td data-label="メモ" class="race-col-memo" title="'.esc_attr($initial_row['memo']).'">'.esc_html($initial_row['memo']).'</td>';
        echo '<td data-label="操作" class="race-col-delete">-</td>';

        echo '</tr>';
      }

      echo '</tbody>';
      echo '</table>';
      echo '</div>';

    } else {
      echo '<p>出走履歴はまだありません。</p>';
    }

    ?>

  </div>

  <div id="training-history-panel" class="tab-panel" style="display:none;">

    <div class="history-title-row">
      <h2>調教履歴</h2>
      <?php if ($can_manage_horse) : ?>
        <button type="button" class="history-shortcut-btn" data-shortcut-target="training-add-panel" aria-label="調教履歴を追加">✏️</button>
      <?php endif; ?>
    </div>

    <?php

    $trainings = get_posts([
      'post_type' => 'training',
      'posts_per_page' => -1,
      'orderby' => 'ID',
      'order' => 'DESC',
      'meta_query' => [
        [
          'key' => 'horse',
          'value' => get_the_ID(),
          'compare' => '='
        ]
      ]
    ]);

    if ($trainings) {

      echo '<div class="table-wrap training-table-wrap">';
      echo '<table class="sh4-training-table training-history-table">';

      echo '
      <thead>
        <tr>
          <th>調教</th>
          <th>ﾊﾟｰﾄﾅｰ</th>
          <th>結果</th>
          <th>飼葉</th>
          <th>結果</th>
          <th>適性</th>
          <th>残週</th>
          <th>メモ</th>
        </tr>
      </thead>
      <tbody>
      ';

      foreach ($trainings as $training) {

        $training_type = get_field('training_type', $training->ID);
        $training_type_other = get_field('training_type_other', $training->ID);
        $training_name = get_field('training_name', $training->ID);
        $training_result = get_field('training_result', $training->ID);
        $feed_name = get_field('feed_name', $training->ID);
        $feed_result = get_field('feed_result', $training->ID);
        $training_partner_count = get_field('training_partner_count', $training->ID);
        $training_remaining_weeks = get_field('training_remaining_weeks', $training->ID);
        $training_memo = get_field('training_memo', $training->ID);
        $training_aptitude_up = get_field('training_aptitude_up', $training->ID);
        $training_aptitude_up_memo = get_field('training_aptitude_up_memo', $training->ID);

        if ($training_type === 'その他' && $training_type_other) {
          $display_training_name = $training_type_other;
        } elseif ($training_name) {
          $display_training_name = $training_name;
        } elseif ($training_type) {
          $display_training_name = $training_type;
        } else {
          $display_training_name = get_the_title($training->ID);
        }

        echo '<tr>';
        echo '<td data-label="調教" class="training-col-name">'.esc_html($display_training_name ?: '-').'</td>';
        echo '<td data-label="ﾊﾟｰﾄﾅｰ" class="training-col-partner">'.esc_html($training_partner_count !== '' && $training_partner_count !== null ? $training_partner_count . '枚' : '-').'</td>';
        echo '<td data-label="結果" class="training-col-result">'.esc_html(sh4_training_result_short_label($training_result)).'</td>';
        echo '<td data-label="飼葉" class="training-col-feed">'.esc_html($feed_name ?: '-').'</td>';
        echo '<td data-label="結果" class="training-col-result">'.esc_html(sh4_training_result_short_label($feed_result)).'</td>';
        echo '<td data-label="適性" class="training-col-aptitude">'.esc_html($training_aptitude_up_memo ?: ($training_aptitude_up ? 'あり' : '-')).'</td>';
        echo '<td data-label="残週" class="training-col-week">'.esc_html($training_remaining_weeks !== '' && $training_remaining_weeks !== null ? $training_remaining_weeks : '-').'</td>';
        $training_memo_text = trim((string)($training_memo ?: ''));
        $training_memo_long = sh4_memo_is_long($training_memo_text);
        $training_memo_detail_id = 'training-memo-detail-' . intval($training->ID);

        echo '<td data-label="メモ" class="training-col-memo" title="'.esc_attr($training_memo_text).'">';
        echo '<div class="sh4-memo-inline">';
        echo '<span class="sh4-memo-preview">'.esc_html(sh4_memo_preview_text($training_memo_text)).'</span>';
        if ($training_memo_long) {
          echo '<button type="button" class="sh4-memo-toggle" data-target="'.esc_attr($training_memo_detail_id).'" aria-label="メモを表示">＋</button>';
        }
        echo '</div>';
        echo '</td>';
        echo '</tr>';

        if ($training_memo_long) {
          echo '<tr id="'.esc_attr($training_memo_detail_id).'" class="sh4-memo-detail-row">';
          echo '<td colspan="8" class="sh4-memo-detail-cell">';
          echo '<div class="sh4-memo-detail-box">';
          echo '<div class="sh4-memo-detail-title">調教メモ全文</div>';
          echo '<div class="sh4-memo-detail-text">'.nl2br(esc_html($training_memo_text)).'</div>';
          echo '<button type="button" class="sh4-memo-close" data-target="'.esc_attr($training_memo_detail_id).'">閉じる</button>';
          echo '</div>';
          echo '</td>';
          echo '</tr>';
        }

      }

      echo '</tbody>';
      echo '</table>';
      echo '</div>';

    } else {
      echo '<p>調教履歴はまだありません。</p>';
    }

    ?>

  </div>

  <?php if ($can_manage_horse) : ?>

  <div class="history-action-tabs">
    <button type="button" class="tab-btn" data-target="race-add-panel">出走履歴を追加</button>
    <button type="button" class="tab-btn" data-target="training-add-panel">調教履歴を追加</button>
    <button type="button" class="tab-btn" data-target="horse-edit-panel">馬情報を編集</button>
  </div>

  <div id="race-add-panel" class="tab-panel" style="display:none;">

    <h2>出走履歴を追加</h2>

    <form method="post" class="result-form">

      <?php
      $race_order_insert_ids = [];
      if (function_exists('sh4_normalize_result_order') && function_exists('sh4_get_ordered_result_ids')) {
        sh4_normalize_result_order(get_the_ID());
        $race_order_insert_ids = sh4_get_ordered_result_ids(get_the_ID());
      } elseif (!empty($display_results)) {
        $race_order_insert_ids = wp_list_pluck($display_results, 'ID');
      }

      $race_order_insert_count = count($race_order_insert_ids);
      $race_order_insert_labels = [];

      foreach ($race_order_insert_ids as $index => $order_result_id) {
        $order_result_id = intval($order_result_id);
        $order_race_id = get_field('race', $order_result_id);
        $order_race_name = $order_race_id ? get_the_title($order_race_id) : get_the_title($order_result_id);
        $race_order_insert_labels[$index + 1] = $order_race_name ?: '出走履歴';
      }
      ?>

      <p>
        出走順：
        <select name="race_order_position">
          <option value="">最後に追加<?php echo $race_order_insert_count > 0 ? '（' . esc_html($race_order_insert_count + 1) . '番目）' : ''; ?></option>
          <?php for ($i = 1; $i <= $race_order_insert_count + 1; $i++) : ?>
            <?php
            if ($i === 1) {
              $insert_label = '1番目：先頭に挿入';
            } elseif ($i <= $race_order_insert_count) {
              $insert_label = $i . '番目：' . ($race_order_insert_labels[$i] ?? '現在の' . $i . '番目') . 'の前に挿入';
            } else {
              $insert_label = $i . '番目：最後に追加';
            }
            ?>
            <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($insert_label); ?></option>
          <?php endfor; ?>
        </select>
        <span class="form-help-text">途中に入れると、その位置以降の出走順は自動で後ろにずれます。</span>
      </p>

      <div id="race-tabs">
        <button type="button" data-grade="SWBC">SWBC</button>
        <button type="button" data-grade="WBC">WBC</button>
        <button type="button" data-grade="海外G1">海外G1</button>
        <button type="button" data-grade="国内G1">G1</button>
        <button type="button" data-grade="G2">G2</button>
        <button type="button" data-grade="G3">G3</button>
      </div>

      <select name="race_id" id="race_select">
        <option value="">-- レースを選択 --</option>

        <?php

        $races = get_transient('race_list_cache');

        if ($races === false) {
          $races = get_posts([
            'post_type' => 'race',
            'posts_per_page' => -1
          ]);

          set_transient('race_list_cache', $races, HOUR_IN_SECONDS);
        }

        foreach ($races as $race) {
          $grade = get_field('grade', $race->ID);

          echo '<option value="'.$race->ID.'" data-grade="'.$grade.'">';
          echo esc_html($race->post_title);
          echo '</option>';
        }

        ?>

      </select>

      <p>
        着順：
        <select name="rank">
          <option value="">-- 着順を選択 --</option>
          <?php for ($i = 1; $i <= 18; $i++) : ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?>着</option>
          <?php endfor; ?>
        </select>
      </p>

      <p>
        人気：
        <select name="popularity">
          <option value="">-- 人気を選択 --</option>
          <?php for ($i = 1; $i <= 18; $i++) : ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?>人気</option>
          <?php endfor; ?>
        </select>
      </p>

      <p>
        オッズ：
        <input type="number" step="0.1" name="odds">
      </p>

      <p>
        限界オッズ：
        <input type="number" step="0.001" name="limit_odds">
      </p>

      <p>
        残週：
        <input type="number" name="race_remaining_weeks" min="0" max="999">
      </p>

      <p>
        メモ：<br>
        <textarea name="race_memo" rows="3"></textarea>
      </p>

      <input type="hidden" name="horse_id" value="<?php echo get_the_ID(); ?>">

      <p>
        <button type="submit" name="add_result" class="add-btn">追加</button>
      </p>

    </form>

  </div>

  <div id="training-add-panel" class="tab-panel" style="display:none;">

    <h2>調教履歴を追加</h2>

    <form method="post" class="training-form">

      <p>
        調教内容<br>
        <select name="training_type" id="training_type_select">
          <option value="">-- 調教内容を選択 --</option>
          <option value="曳き運動">曳き運動</option>
          <option value="角馬場">角馬場</option>
          <option value="ウッドチップ">ウッドチップ</option>
          <option value="ポリトラック">ポリトラック</option>
          <option value="芝">芝</option>
          <option value="ダート">ダート</option>
          <option value="プール">プール</option>
          <option value="坂路">坂路</option>
          <option value="ゲート練習">ゲート練習</option>
          <option value="森林馬道">森林馬道</option>
          <option value="その他">その他</option>
        </select>
      </p>

      <p id="training_type_other_wrap" style="display:none;">
        その他の調教内容<br>
        <input type="text" name="training_type_other" id="training_type_other_input">
      </p>

      <p>
        パートナー枚数<br>
        <select name="training_partner_count">
          <option value="">-- 枚数を選択 --</option>
          <?php for ($i = 1; $i <= 5; $i++) : ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?>枚</option>
          <?php endfor; ?>
        </select>
      </p>

      <p>
        調教結果<br>
        <select name="training_result">
          <option value="">-- 調教結果を選択 --</option>
          <option value="普通">普通</option>
          <option value="成功">成功</option>
          <option value="大成功">大成功</option>
          <option value="超成功">超成功</option>
          <option value="本格化">本格化</option>
        </select>
      </p>

      <p>
        飼葉内容<br>
        <input type="text" name="feed_name">
      </p>

      <p>
        飼葉結果<br>
        <select name="feed_result">
          <option value="">-- 飼葉結果を選択 --</option>
          <option value="普通">普通</option>
          <option value="成功">成功</option>
          <option value="大成功">大成功</option>
          <option value="超成功">超成功</option>
          <option value="本格化">本格化</option>
        </select>
      </p>

      <div class="training-aptitude-up-box">
        <label class="training-aptitude-up-toggle">
          <input type="checkbox" name="training_aptitude_up" value="1" id="training_aptitude_up_check">
          適性上昇あり
        </label>

        <div id="training_aptitude_up_items_wrap" class="training-aptitude-up-items" style="display:none;">
          <p class="form-help-text">上がった適性を選んでください。登録すると現在適性が自動で1段階上がります。</p>

          <div class="training-aptitude-checkbox-grid">
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="turf"> 芝</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="dirt"> ダート</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="bad_track"> 道悪</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="slope"> 坂</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="constitution"> 体質</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="guts"> 根性</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="start"> スタート</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="right_turn"> 右回り</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="left_turn"> 左回り</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="tight_turn"> 小回り</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="expedition"> 遠征</label>
            <label><input type="checkbox" name="training_aptitude_up_items[]" value="acceleration"> 瞬発力</label>
          </div>
        </div>
      </div>

      <p>
        調教時点の残り週<br>
        <input type="number" name="training_remaining_weeks" min="0" max="999">
      </p>

      <p>
        メモ<br>
        <textarea name="training_memo" rows="3"></textarea>
      </p>

      <input type="hidden" name="horse_id" value="<?php echo get_the_ID(); ?>">

      <p>
        <button type="submit" name="add_training" class="add-btn">調教履歴を追加</button>
      </p>

    </form>

  </div>

  <div id="horse-edit-panel" class="tab-panel" style="display:none;">

    <h2>馬情報を編集</h2>

    <form method="post" class="horse-edit-form">

      <div class="horse-edit-inner-tabs">
        <button type="button" class="edit-tab-btn active" data-target="horse-edit-basic-panel">基本情報</button>
        <button type="button" class="edit-tab-btn" data-target="horse-edit-aptitude-panel">適性</button>
      </div>

      <div id="horse-edit-basic-panel" class="horse-edit-inner-panel">

        <h3>基本情報</h3>

        <p>
          馬名<br>
          <input type="text" name="edit_horse_name" value="<?php echo esc_attr(get_the_title()); ?>">
        </p>

        <p>
          公開設定<br>
          <?php $edit_horse_visibility = function_exists('sh4_get_horse_visibility') ? sh4_get_horse_visibility(get_the_ID()) : (get_field('horse_visibility') ?: 'public'); ?>
          <select name="edit_horse_visibility">
            <option value="public" <?php selected($edit_horse_visibility, 'public'); ?>>公開</option>
            <option value="private" <?php selected($edit_horse_visibility, 'private'); ?>>非公開</option>
          </select>
          <span class="form-help-text">公開：馬一覧に表示され、他の人も閲覧できます。非公開：所有者と管理者だけ閲覧できます。</span>
        </p>

        <p>
          性別<br>
          <?php $gender = get_field('gender'); ?>
          <select name="edit_gender">
            <option value="">-- 性別を選択 --</option>
            <option value="牡馬" <?php selected($gender, '牡馬'); ?>>牡馬</option>
            <option value="牝馬" <?php selected($gender, '牝馬'); ?>>牝馬</option>
            <option value="セン馬" <?php selected($gender, 'セン馬'); ?>>セン馬</option>
          </select>
        </p>

        <p>
          脚質<br>
          <input type="text" name="edit_kyakushitu" value="<?php echo esc_attr(get_field('kyakushitu')); ?>">
        </p>

        <p>
          特性1<br>
          <input type="text" name="edit_tokusei_1" value="<?php echo esc_attr(get_field('tokusei_1')); ?>">
        </p>

        <p>
          特性2<br>
          <input type="text" name="edit_tokusei_2" value="<?php echo esc_attr(get_field('tokusei_2')); ?>">
        </p>

        <p>
          特性3<br>
          <input type="text" name="edit_tokusei_3" value="<?php echo esc_attr(get_field('tokusei_3')); ?>">
        </p>

        <hr>

        <h3>条件戦・初期レース</h3>

        <p class="form-help-text">3歳：条件戦3戦→朝日杯→ホープフル→プレワールド / 古馬：条件戦4戦→フォア賞→プレワールド。条件を満たさない初期レースは保存時に空欄になります。</p>

        <p>
          デビュー<br>
          <?php $edit_start_type = get_field('start_type'); ?>
          <select name="edit_start_type">
            <option value="">-- デビューを選択 --</option>
            <option value="3歳スタート" <?php selected($edit_start_type, '3歳スタート'); ?>>3歳スタート</option>
            <option value="古馬スタート" <?php selected($edit_start_type, '古馬スタート'); ?>>古馬スタート</option>
          </select>
        </p>

        <p>
          条件戦敗数<br>
          <input type="number" name="edit_condition_losses" min="0" max="4" value="<?php echo esc_attr((int)get_field('condition_losses')); ?>">
          <span class="form-help-text">3歳は3戦、古馬は4戦として勝数を自動計算します。</span>
        </p>

        <div class="initial-race-edit-grid">
          <p>
            朝日杯FS 着順<br>
            <?php $asahi_rank = get_field('asahi_rank'); ?>
            <select name="edit_asahi_rank">
              <option value="">未出走</option>
              <?php for ($i = 1; $i <= 18; $i++) : ?>
                <option value="<?php echo $i; ?>" <?php selected($asahi_rank, $i); ?>><?php echo $i; ?>着</option>
              <?php endfor; ?>
            </select>
          </p>

          <p>
            ホープフルS 着順<br>
            <?php $hopeful_rank = get_field('hopeful_rank'); ?>
            <select name="edit_hopeful_rank">
              <option value="">未出走</option>
              <?php for ($i = 1; $i <= 18; $i++) : ?>
                <option value="<?php echo $i; ?>" <?php selected($hopeful_rank, $i); ?>><?php echo $i; ?>着</option>
              <?php endfor; ?>
            </select>
          </p>

          <p>
            フォア賞 着順<br>
            <?php $foy_rank = get_field('foy_rank'); ?>
            <select name="edit_foy_rank">
              <option value="">未出走</option>
              <?php for ($i = 1; $i <= 18; $i++) : ?>
                <option value="<?php echo $i; ?>" <?php selected($foy_rank, $i); ?>><?php echo $i; ?>着</option>
              <?php endfor; ?>
            </select>
          </p>

          <p>
            プレワールド 着順<br>
            <?php $pre_world_rank = get_field('pre_world_rank'); ?>
            <select name="edit_pre_world_rank">
              <option value="">未出走</option>
              <?php for ($i = 1; $i <= 18; $i++) : ?>
                <option value="<?php echo $i; ?>" <?php selected($pre_world_rank, $i); ?>><?php echo $i; ?>着</option>
              <?php endfor; ?>
            </select>
          </p>
        </div>

        <p>
          メモ<br>
          <textarea name="edit_memo" rows="5"><?php echo esc_textarea(get_field('memo')); ?></textarea>
        </p>

      </div>

      <div id="horse-edit-aptitude-panel" class="horse-edit-inner-panel" style="display:none;">

        <h3>適性</h3>
        <p class="form-help-text">生産時と現在の適性を別々に編集できます。現在が空の場合は、生産時と同じ値で保存されます。</p>

        <div class="aptitude-edit-current-grid">

          <div class="aptitude-edit-current-item aptitude-edit-distance-item">
            <div class="aptitude-edit-current-label">距離 下限</div>
            <?php
            $distance_min_initial = get_field('aptitude_distance_min_initial');
            $distance_min_current = get_field('aptitude_distance_min_current');
            if ($distance_min_current === '' || $distance_min_current === null) {
              $distance_min_current = $distance_min_initial;
            }
            ?>
            <label>
              生産時
              <select name="edit_aptitude_distance_min_initial">
                <option value="">-- 選択 --</option>
                <?php foreach ($distance_options as $value => $label_text) : ?>
                  <option value="<?php echo esc_attr($value); ?>" <?php selected($distance_min_initial, $value); ?>>
                    <?php echo esc_html($label_text); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              現在
              <select name="edit_aptitude_distance_min_current">
                <option value="">-- 選択 --</option>
                <?php foreach ($distance_options as $value => $label_text) : ?>
                  <option value="<?php echo esc_attr($value); ?>" <?php selected($distance_min_current, $value); ?>>
                    <?php echo esc_html($label_text); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>

          <div class="aptitude-edit-current-item aptitude-edit-distance-item">
            <div class="aptitude-edit-current-label">距離 上限</div>
            <?php
            $distance_max_initial = get_field('aptitude_distance_max_initial');
            $distance_max_current = get_field('aptitude_distance_max_current');
            if ($distance_max_current === '' || $distance_max_current === null) {
              $distance_max_current = $distance_max_initial;
            }
            ?>
            <label>
              生産時
              <select name="edit_aptitude_distance_max_initial">
                <option value="">-- 選択 --</option>
                <?php foreach ($distance_options as $value => $label_text) : ?>
                  <option value="<?php echo esc_attr($value); ?>" <?php selected($distance_max_initial, $value); ?>>
                    <?php echo esc_html($label_text); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              現在
              <select name="edit_aptitude_distance_max_current">
                <option value="">-- 選択 --</option>
                <?php foreach ($distance_options as $value => $label_text) : ?>
                  <option value="<?php echo esc_attr($value); ?>" <?php selected($distance_max_current, $value); ?>>
                    <?php echo esc_html($label_text); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>

          <?php foreach ($aptitudes as $label => $key) : ?>
            <?php
            $initial_value = get_field('aptitude_' . $key . '_initial');
            $current_value = get_field('aptitude_' . $key . '_current');
            if ($current_value === '' || $current_value === null) {
              $current_value = $initial_value;
            }
            ?>
            <div class="aptitude-edit-current-item">
              <div class="aptitude-edit-current-label"><?php echo esc_html($label); ?></div>

              <?php if ($key === 'temper') : ?>
                <label>
                  生産時
                  <select name="edit_aptitude_temper_initial">
                    <option value="">-- 選択 --</option>
                    <?php foreach ($temper_options as $value => $text) : ?>
                      <option value="<?php echo esc_attr($value); ?>" <?php selected((string)$initial_value, (string)$value); ?>>
                        <?php echo esc_html($text); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>
                  現在
                  <select name="edit_aptitude_temper_current">
                    <option value="">-- 選択 --</option>
                    <?php foreach ($temper_options as $value => $text) : ?>
                      <option value="<?php echo esc_attr($value); ?>" <?php selected((string)$current_value, (string)$value); ?>>
                        <?php echo esc_html($text); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </label>
              <?php else : ?>
                <label>
                  生産時
                  <select name="edit_aptitude_<?php echo esc_attr($key); ?>_initial">
                    <option value="">-- 選択 --</option>
                    <?php foreach ($aptitude_options as $value => $text) : ?>
                      <option value="<?php echo esc_attr($value); ?>" <?php selected((string)$initial_value, (string)$value); ?>>
                        <?php echo esc_html($text); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>
                  現在
                  <select name="edit_aptitude_<?php echo esc_attr($key); ?>_current">
                    <option value="">-- 選択 --</option>
                    <?php foreach ($aptitude_options as $value => $text) : ?>
                      <option value="<?php echo esc_attr($value); ?>" <?php selected((string)$current_value, (string)$value); ?>>
                        <?php echo esc_html($text); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </label>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

        </div>

        <hr>

        <h3>馴致済み</h3>

        <p class="form-help-text">馴致で上げた適性にチェックを入れてください。</p>

        <div class="breaking-edit-grid">

          <?php foreach ($aptitudes as $label => $key) : ?>

            <?php if ($key === 'temper') continue; ?>

            <label class="breaking-check-item">
              <input
                type="checkbox"
                name="edit_breaking_<?php echo esc_attr($key); ?>_done"
                value="1"
                <?php checked(get_field('breaking_' . $key . '_done'), 1); ?>
              >
              <?php echo esc_html($label); ?>
            </label>

          <?php endforeach; ?>

        </div>

      </div>

      <input type="hidden" name="edit_horse_id" value="<?php echo get_the_ID(); ?>">

      <p>
        <button type="submit" name="update_horse" class="add-btn">馬情報を更新</button>
      </p>

    </form>

    <form method="post" onsubmit="return confirm('この馬を削除しますか？');">

      <input type="hidden" name="delete_horse_id" value="<?php echo get_the_ID(); ?>">

      <button type="submit" class="delete-horse-btn">この馬を削除</button>

    </form>

  </div>

  <?php else : ?>

    <div class="owner-only-notice">
      <?php if (!is_user_logged_in()) : ?>
        <p>履歴追加・馬情報編集はログイン後に利用できます。</p>
        <p><a class="login-btn" href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">ログインする</a></p>
      <?php else : ?>
        <p>この馬は閲覧専用です。編集や履歴追加は所有者のみできます。</p>
      <?php endif; ?>
    </div>

  <?php endif; ?>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

  const tabButtons = document.querySelectorAll('.tab-btn');

  tabButtons.forEach(function(button) {

    button.addEventListener('click', function() {

      const targetId = button.getAttribute('data-target');
      const parentTabs = button.parentElement;
      const siblingButtons = parentTabs.querySelectorAll('.tab-btn');

      if (parentTabs.classList.contains('history-action-tabs')) {
        const targetPanel = document.getElementById(targetId);
        const isAlreadyOpen = button.classList.contains('active') && targetPanel.style.display === 'block';

        siblingButtons.forEach(function(btn) {
          btn.classList.remove('active');
        });

        document.getElementById('race-add-panel').style.display = 'none';
        document.getElementById('training-add-panel').style.display = 'none';
        document.getElementById('horse-edit-panel').style.display = 'none';

        if (!isAlreadyOpen) {
          button.classList.add('active');
          targetPanel.style.display = 'block';
        }

        return;
      }

      siblingButtons.forEach(function(btn) {
        btn.classList.remove('active');
      });

      button.classList.add('active');

      if (parentTabs.classList.contains('horse-info-tabs')) {
        document.getElementById('horse-basic-panel').style.display = 'none';
        document.getElementById('horse-aptitude-panel').style.display = 'none';
      }

      if (parentTabs.classList.contains('history-main-tabs')) {
        document.getElementById('race-history-panel').style.display = 'none';
        document.getElementById('training-history-panel').style.display = 'none';
      }

      document.getElementById(targetId).style.display = 'block';

    });

  });


  const editTabButtons = document.querySelectorAll('.edit-tab-btn');

  editTabButtons.forEach(function(button) {

    button.addEventListener('click', function() {

      const targetId = button.getAttribute('data-target');
      const parentTabs = button.parentElement;
      const siblingButtons = parentTabs.querySelectorAll('.edit-tab-btn');

      siblingButtons.forEach(function(btn) {
        btn.classList.remove('active');
      });

      button.classList.add('active');

      document.getElementById('horse-edit-basic-panel').style.display = 'none';
      document.getElementById('horse-edit-aptitude-panel').style.display = 'none';
      document.getElementById(targetId).style.display = 'block';

    });

  });


  function sh4OpenActionPanel(targetId) {
    const targetPanel = document.getElementById(targetId);
    const actionTabs = document.querySelector('.history-action-tabs');

    if (!targetPanel || !actionTabs) return;

    actionTabs.querySelectorAll('.tab-btn').forEach(function(btn) {
      btn.classList.toggle('active', btn.getAttribute('data-target') === targetId);
    });

    ['race-add-panel', 'training-add-panel', 'horse-edit-panel'].forEach(function(panelId) {
      const panel = document.getElementById(panelId);
      if (panel) {
        panel.style.display = panelId === targetId ? 'block' : 'none';
      }
    });

    targetPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function sh4OpenHorseEditAptitude() {
    sh4OpenActionPanel('horse-edit-panel');

    const aptitudeTab = document.querySelector('.edit-tab-btn[data-target="horse-edit-aptitude-panel"]');
    if (aptitudeTab) {
      aptitudeTab.click();
    }
  }

  document.querySelectorAll('.history-shortcut-btn').forEach(function(button) {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      event.stopPropagation();
      sh4OpenActionPanel(button.getAttribute('data-shortcut-target'));
    });
  });

  document.querySelectorAll('.aptitude-edit-jump-btn').forEach(function(button) {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      sh4OpenHorseEditAptitude();
    });
  });

  const raceFilterButtons = document.querySelectorAll('.race-filter-btn');

  raceFilterButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      const filter = button.getAttribute('data-race-filter');

      raceFilterButtons.forEach(function(btn) {
        btn.classList.toggle('active', btn === button);
      });

      document.querySelectorAll('.race-history-data-row, .race-history-related-row').forEach(function(row) {
        const grade = row.getAttribute('data-race-grade') || '';
        const isVisible = filter === 'all' || grade === filter;

        row.classList.toggle('is-filter-hidden', !isVisible);

        if (!isVisible) {
          row.classList.remove('is-open');
        }
      });

      document.querySelectorAll('.result-edit-toggle').forEach(function(btn) {
        btn.textContent = '編集';
      });

      document.querySelectorAll('.sh4-memo-toggle').forEach(function(btn) {
        btn.textContent = '＋';
        btn.setAttribute('aria-label', 'メモを表示');
      });
    });
  });

  const raceGradeTabs = document.querySelectorAll('#race-tabs button');
  const raceSelect = document.getElementById('race_select');

  if (raceSelect) {

    const allOptions = Array.from(raceSelect.querySelectorAll('option'));

    raceGradeTabs.forEach(function(tab) {

      tab.addEventListener('click', function() {

        const grade = tab.dataset.grade;

        raceGradeTabs.forEach(function(btn) {
          btn.classList.remove('active');
        });

        tab.classList.add('active');

        raceSelect.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- レースを選択 --';
        raceSelect.appendChild(defaultOption);

        allOptions.forEach(function(option) {

          if (!option.value) return;

          if (option.dataset.grade === grade) {
            raceSelect.appendChild(option.cloneNode(true));
          }

        });

      });

    });

  }

  const trainingTypeSelect = document.getElementById('training_type_select');
  const trainingTypeOtherWrap = document.getElementById('training_type_other_wrap');
  const trainingTypeOtherInput = document.getElementById('training_type_other_input');

  if (trainingTypeSelect && trainingTypeOtherWrap) {

    const toggleTrainingOther = function() {
      if (trainingTypeSelect.value === 'その他') {
        trainingTypeOtherWrap.style.display = 'block';
      } else {
        trainingTypeOtherWrap.style.display = 'none';

        if (trainingTypeOtherInput) {
          trainingTypeOtherInput.value = '';
        }
      }
    };

    trainingTypeSelect.addEventListener('change', toggleTrainingOther);
    toggleTrainingOther();

  }

  const trainingAptitudeUpCheck = document.getElementById('training_aptitude_up_check');
  const trainingAptitudeUpItemsWrap = document.getElementById('training_aptitude_up_items_wrap');

  if (trainingAptitudeUpCheck && trainingAptitudeUpItemsWrap) {

    const toggleTrainingAptitudeUpItems = function() {
      if (trainingAptitudeUpCheck.checked) {
        trainingAptitudeUpItemsWrap.style.display = 'block';
      } else {
        trainingAptitudeUpItemsWrap.style.display = 'none';

        const checks = trainingAptitudeUpItemsWrap.querySelectorAll('input[type="checkbox"]');
        checks.forEach(function(check) {
          check.checked = false;
        });
      }
    };

    trainingAptitudeUpCheck.addEventListener('change', toggleTrainingAptitudeUpItems);
    toggleTrainingAptitudeUpItems();

  }


  const weekEditButtons = document.querySelectorAll('.week-edit-btn');
  const weekCancelButtons = document.querySelectorAll('.week-cancel-btn');

  weekEditButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      const targetId = button.getAttribute('data-target');
      const panel = document.getElementById(targetId);
      if (panel) {
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        const firstInput = panel.querySelector('input[type="number"]');
        if (firstInput && panel.style.display === 'block') {
          firstInput.focus();
        }
      }
    });
  });

  weekCancelButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      const targetId = button.getAttribute('data-target');
      const panel = document.getElementById(targetId);
      if (panel) {
        panel.style.display = 'none';
      }
    });
  });



  const resultEditButtons = document.querySelectorAll('.result-edit-toggle');
  const resultEditCloseButtons = document.querySelectorAll('.result-edit-close');

  resultEditButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      const targetId = button.getAttribute('data-target');
      const row = document.getElementById(targetId);

      if (!row) return;

      const isOpen = row.classList.contains('is-open');
      row.classList.toggle('is-open', !isOpen);
      button.textContent = isOpen ? '編集' : '閉じる';
    });
  });

  resultEditCloseButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      const targetId = button.getAttribute('data-target');
      const row = document.getElementById(targetId);

      if (!row) return;

      row.classList.remove('is-open');

      const opener = document.querySelector('.result-edit-toggle[data-target="' + targetId + '"]');
      if (opener) {
        opener.textContent = '編集';
      }
    });
  });


  const memoToggleButtons = document.querySelectorAll('.sh4-memo-toggle');
  const memoCloseButtons = document.querySelectorAll('.sh4-memo-close');

  memoToggleButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      const targetId = button.getAttribute('data-target');
      const row = document.getElementById(targetId);

      if (!row) return;

      const isOpen = row.classList.contains('is-open');

      if (isOpen) {
        row.classList.remove('is-open');
        button.textContent = '＋';
        button.setAttribute('aria-label', 'メモを表示');
      } else {
        row.classList.add('is-open');
        button.textContent = '－';
        button.setAttribute('aria-label', 'メモを閉じる');
      }
    });
  });

  memoCloseButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      const targetId = button.getAttribute('data-target');
      const row = document.getElementById(targetId);

      if (!row) return;

      row.classList.remove('is-open');

      const opener = document.querySelector('.sh4-memo-toggle[data-target="' + targetId + '"]');
      if (opener) {
        opener.textContent = '＋';
        opener.setAttribute('aria-label', 'メモを表示');
      }
    });
  });


});
</script>

<?php get_footer(); ?>