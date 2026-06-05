<?php
if (!is_user_logged_in()) {
  wp_safe_redirect(wp_login_url(get_permalink()));
  exit;
}

get_header();
?>

<div class="container" style="max-width:600px;margin:auto;">

<h1>馬を追加</h1>

<form method="post">

  <p>馬名：</p>
  <input type="text" name="horse_name" required style="width:100%;padding:10px;">

  <p style="margin-top:20px;">公開設定：</p>
  <select name="horse_visibility" style="width:100%;padding:10px;">
    <option value="public">公開</option>
    <option value="private">非公開</option>
  </select>
  <p class="form-help-text">公開：馬一覧に表示され、他の人も閲覧できます。非公開：所有者と管理者だけ閲覧できます。</p>

  <p style="margin-top:20px;">脚質：</p>
  <select name="kyakushitu" style="width:100%;padding:10px;">
    <option value="逃げ">逃げ</option>
    <option value="先行">先行</option>
    <option value="差し">差し</option>
    <option value="追込">追込</option>
    <option value="自在">自在</option>
    <option value="慧眼自在">慧眼自在</option>
    <option value="まくり">まくり</option>
    <option value="ディープまくり">ディープまくり</option>
    <option value="直線一気">直線一気</option>
    <option value="暴走">暴走</option>
    <option value="大逃げ">大逃げ</option>
    <option value="スズカ大逃げ">スズカ大逃げ</option>
    <option value="優馬の逃げ">優馬の逃げ</option>
  </select>

  <p style="margin-top:20px;">性別</p>
  <select name="gender" style="width:100%;padding:10px;">
    <option value="">-- 性別を選択 --</option>
    <option value="牡馬">牡馬</option>
    <option value="牝馬">牝馬</option>
    <option value="セン馬">セン馬</option>
  </select>

  <p style="margin-top:20px;">特性1</p>
  <input type="text" name="tokusei_1" style="width:100%;padding:10px;">

  <p style="margin-top:20px;">特性2</p>
  <input type="text" name="tokusei_2" style="width:100%;padding:10px;">

  <p style="margin-top:20px;">特性3</p>
  <input type="text" name="tokusei_3" style="width:100%;padding:10px;">

  <p style="margin-top:20px;">スタート区分</p>
  <select name="start_type" id="start_type" style="width:100%;padding:10px;">
    <option value="">-- 選択 --</option>
    <option value="3歳スタート">3歳スタート</option>
    <option value="古馬スタート">古馬スタート</option>
  </select>

  <p style="margin-top:20px;">条件戦敗戦数</p>
  <select name="condition_losses" id="condition_losses" style="width:100%;padding:10px;">
    <option value="0">0敗（無敗）</option>
    <option value="1">1敗</option>
    <option value="2">2敗</option>
    <option value="3">3敗</option>
    <option value="4">4敗</option>
  </select>

  <div id="three_year_old_route" style="display:none;">

    <p style="margin-top:20px;">朝日杯 着順</p>
    <select name="asahi_rank" id="asahi_rank" style="width:100%;padding:10px;">
      <option value="">-- 着順を選択 --</option>
      <?php for ($i = 1; $i <= 18; $i++) : ?>
        <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?>着</option>
      <?php endfor; ?>
    </select>

    <div id="hopeful_area" style="display:none;">
      <p style="margin-top:20px;">ホープフル 着順</p>
      <select name="hopeful_rank" id="hopeful_rank" style="width:100%;padding:10px;">
        <option value="">-- 着順を選択 --</option>
        <?php for ($i = 1; $i <= 18; $i++) : ?>
          <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?>着</option>
        <?php endfor; ?>
      </select>
    </div>

  </div>

  <div id="old_horse_route" style="display:none;">

    <p style="margin-top:20px;">フォア賞 着順</p>
    <select name="foy_rank" id="foy_rank" style="width:100%;padding:10px;">
      <option value="">-- 着順を選択 --</option>
      <?php for ($i = 1; $i <= 18; $i++) : ?>
        <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?>着</option>
      <?php endfor; ?>
    </select>

  </div>

  <div id="pre_world_area" style="display:none;">
    <p style="margin-top:20px;">プレワールド 着順</p>
    <select name="pre_world_rank" id="pre_world_rank" style="width:100%;padding:10px;">
      <option value="">-- 着順を選択 --</option>
      <?php for ($i = 1; $i <= 18; $i++) : ?>
        <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?>着</option>
      <?php endfor; ?>
    </select>
  </div>

  <p style="margin-top:20px;">メモ：</p>
  <textarea name="memo" rows="5" style="width:100%;padding:10px;"></textarea>

  <p style="margin-top:20px;">
    <button type="submit" name="add_horse"
      style="padding:10px 20px;background:#333;color:#fff;border:none;border-radius:5px;">
      追加する
    </button>
  </p>

</form>

</div>

<script>
const startType = document.getElementById('start_type');
const losses = document.getElementById('condition_losses');

const threeRoute = document.getElementById('three_year_old_route');
const oldRoute = document.getElementById('old_horse_route');

const asahiRank = document.getElementById('asahi_rank');
const hopefulArea = document.getElementById('hopeful_area');
const hopefulRank = document.getElementById('hopeful_rank');

const foyRank = document.getElementById('foy_rank');
const preWorldArea = document.getElementById('pre_world_area');

function resetHiddenInitialRaceValues() {
  if (threeRoute.style.display === 'none') {
    if (asahiRank) asahiRank.value = '';
    if (hopefulRank) hopefulRank.value = '';
  }

  if (oldRoute.style.display === 'none') {
    if (foyRank) foyRank.value = '';
  }

  if (hopefulArea.style.display === 'none') {
    if (hopefulRank) hopefulRank.value = '';
  }

  if (preWorldArea.style.display === 'none') {
    const preWorldRank = document.getElementById('pre_world_rank');
    if (preWorldRank) preWorldRank.value = '';
  }
}

function updateInitialRoute() {

  threeRoute.style.display = 'none';
  oldRoute.style.display = 'none';
  hopefulArea.style.display = 'none';
  preWorldArea.style.display = 'none';

  if (losses.value !== '0') {
    resetHiddenInitialRaceValues();
    return;
  }

  if (startType.value === '3歳スタート') {
    threeRoute.style.display = 'block';

    if (asahiRank.value === '1') {
      hopefulArea.style.display = 'block';
    }

    if (asahiRank.value === '1' && hopefulRank.value === '1') {
      preWorldArea.style.display = 'block';
    }
  }

  if (startType.value === '古馬スタート') {
    oldRoute.style.display = 'block';

    if (foyRank.value === '1') {
      preWorldArea.style.display = 'block';
    }
  }

  resetHiddenInitialRaceValues();
}

[startType, losses, asahiRank, hopefulRank, foyRank].forEach(el => {
  if (el) {
    el.addEventListener('change', updateInitialRoute);
  }
});
</script>

<?php get_footer(); ?>
