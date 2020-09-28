<?php
session_start();
require('dbconnect.php');

if ($_COOKIE['email'] !== '') {
  $email = $_COOKIE['email'];
}
if (!empty($_POST)) {
  $email = $_POST['email'];

  if ($_POST['email'] !== '' && $_POST['password'] !== '') {
    $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
    $login->execute(array(
      $_POST['email'],
      sha1($_POST['password'])
    ));
    $member = $login->fetch();

    if ($member) {
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();

      if ($_POST['save'] === 'on') {
        setcookie('email', $_POST['email'], time() + 60 * 60 * 24 * 14);
      }
      header('Location: https://'.$_SERVER['HTTP_HOST'].'/app/mini_bbs/index.php');
      exit();
    } else {
      $error['login'] = 'failed';
    }
  } else {
    $error['login'] = 'blank';
  }
}
$page_title='ツブヤイター';
require('header.php');
?>

<body>
  <div class="container">
    <div class="row">
      <div class="col-lg-6 pr-lg-0  text-center">
        <img class="img-fluid rounded-circle" src="img/rogo.png" alt="" />
      </div>
      <div class="col-lg-6 text-center mt-lg-5">
        <div class="mt-lg-5 " id="lead">
          <h2>ツブヤイターを始めよう</h2>
        </div>
        <div class="m-5" id="lead">
          <a href="signin.php"><button class="btn btn-primary btn-xl btn-block" type="submit">アカウント作成</button></a>
        </div>
        <div class="m-5" id="lead">
          <button class="btn btn-primary btn-xl btn-block" data-toggle="modal" data-target="#modal1" type="submit">ログインする</button>
        </div>
        <div class="m-5" id="lead">
          <button class="btn btn-primary btn-xl btn-block" data-toggle="modal" data-target="#modal2" type="submit" name="guest">ゲストログイン</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal1" class="modal">
    <div class="modal-dialog">
      <div class="modal-content ">
        <form action="" method="post">
          <div class="modal-header">
            <h5 class="modal-title ">ログインする</h5>
            <button class="btn btn-secondary" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body pt-2 ">
            <div class="form-group">
              <label for="email">メールアドレス</label><br>
              <input id="email" type="text" name="email" size="35" maxlength="255" class="form-control" value="<?php print(htmlspecialchars($email, ENT_QUOTES)); ?>">
              <?php if ($error['login'] === 'blank') : ?>
                <P class="error">*メールアドレスとパスワードをご記入ください</p>
              <?php endif; ?>
              <?php if ($error['login'] === 'failed') : ?>
                <P class="error">*ログインに失敗しました。正しくご記入ください</p>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <label for="password">パスワード</label><br>
              <input id="password" type="password" name="password" size="35" maxlength="255" class="form-control" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>">
            </div>
            <dl>
              <dt>ログイン情報の記録</dt>
              <dd><input id="save" type="checkbox" name="save" value="on">
                <label for="save">次回からは自動的にログインする</label>
              </dd>
            </dl>
            <button class="btn btn-primary btn-block" type="submit">ログインする</button>

          </div>
        </form>
      </div>
    </div>
  </div>


  <div id="modal2" class="modal">
    <div class="modal-dialog">
      <div class="modal-content ">
        <form action="" method="post">
          <div class="modal-header">
            <h5 class="modal-title ">ゲストログインする</h5>
            <button class="btn btn-secondary" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body pt-2 ">
            <div class="form-group">
              <label for="email">メールアドレス</label><br>
              <input id="email" type="password" name="email" size="35" maxlength="255" class="form-control" value="guest@example.com" readonly>
              <?php if ($error['login'] === 'blank') : ?>
                <P class="error">*メールアドレスとパスワードをご記入ください</p>
              <?php endif; ?>
              <?php if ($error['login'] === 'failed') : ?>
                <P class="error">*ログインに失敗しました。正しくご記入ください</p>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <label for="password">パスワード</label><br>
              <input id="password" type="password" name="password" size="35" maxlength="255" class="form-control" value="99999999" readonly>
            </div>
            <button class="btn btn-primary btn-block" type="submit">ログインする</button>

          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS, Popper.js, and jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>

</html>