<?php
/*
Template Name: ログイン
*/

$redirect_to = isset($_GET['redirect_to'])
  ? esc_url_raw(wp_unslash($_GET['redirect_to']))
  : home_url('/mypage/');

$redirect_to = wp_validate_redirect($redirect_to, home_url('/mypage/'));

if (is_user_logged_in()) {
  wp_safe_redirect($redirect_to);
  exit;
}

$login_error = '';

if (
  $_SERVER['REQUEST_METHOD'] === 'POST' &&
  isset($_POST['sh4_login_submit'])
) {

  if (
    !isset($_POST['sh4_login_nonce']) ||
    !wp_verify_nonce($_POST['sh4_login_nonce'], 'sh4_login')
  ) {
    $login_error = '不正な送信です。もう一度お試しください。';
  } else {

    $posted_redirect_to = isset($_POST['redirect_to'])
      ? esc_url_raw(wp_unslash($_POST['redirect_to']))
      : home_url('/mypage/');

    $posted_redirect_to = wp_validate_redirect($posted_redirect_to, home_url('/mypage/'));

    $credentials = [
      'user_login' => sanitize_text_field(wp_unslash($_POST['log'] ?? '')),
      'user_password' => (string) wp_unslash($_POST['pwd'] ?? ''),
      'remember' => !empty($_POST['rememberme']),
    ];

    $user = wp_signon($credentials, is_ssl());

    if (is_wp_error($user)) {
      $login_error = 'ログイン情報が正しくありません。';
    } else {
      wp_safe_redirect($posted_redirect_to);
      exit;
    }
  }
}

$google_login_url = add_query_arg(
  [
    'loginSocial' => 'google',
    'redirect' => $redirect_to,
    'redirect_to' => $redirect_to,
  ],
  site_url('wp-login.php', 'login')
);

get_header();
?>

<div class="container">

  <div class="sh4-login-wrap">

    <div class="sh4-login-card">

      <h1>ログイン</h1>
      <p class="sh4-login-lead">
        スタホ4競走馬管理にログインします。Googleアカウント、またはユーザー名・メールアドレスでログインできます。
      </p>

      <?php if ($login_error) : ?>
        <div class="sh4-login-error">
          <?php echo esc_html($login_error); ?>
        </div>
      <?php endif; ?>

      <p>
        <a class="sh4-google-login-btn" href="<?php echo esc_url($google_login_url); ?>">
          <span class="sh4-google-icon">G</span>
          Googleでログイン
        </a>
      </p>

      <div class="sh4-login-divider">
        <span>または</span>
      </div>

      <form method="post" class="sh4-login-form">

        <?php wp_nonce_field('sh4_login', 'sh4_login_nonce'); ?>
        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">

        <p>
          <label for="sh4-login-user">ユーザー名またはメールアドレス</label><br>
          <input type="text" id="sh4-login-user" name="log" autocomplete="username" required>
        </p>

        <p>
          <label for="sh4-login-pass">パスワード</label><br>
          <input type="password" id="sh4-login-pass" name="pwd" autocomplete="current-password" required>
        </p>

        <label class="sh4-login-remember">
          <input type="checkbox" name="rememberme" value="forever">
          ログイン状態を保存する
        </label>

        <p>
          <button type="submit" name="sh4_login_submit" class="add-btn sh4-login-submit">
            ログイン
          </button>
        </p>

      </form>

      <div class="sh4-login-links">
        <?php if (get_option('users_can_register')) : ?>
          <a href="<?php echo esc_url(wp_registration_url()); ?>">新規登録</a>
          <span>/</span>
        <?php endif; ?>
        <a href="<?php echo esc_url(wp_lostpassword_url($redirect_to)); ?>">パスワードを忘れた方</a>
      </div>

    </div>

  </div>

</div>

<?php get_footer(); ?>
