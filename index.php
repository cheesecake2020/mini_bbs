<?php
session_start();
require('dbconnect.php');
  if (isset($_SESSION['id']) && $_SESSION['time']+3600>time()){
    $_SESSION['time'] = time();
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
  } else {
    header('Location: https://'.$_SERVER['HTTP_HOST'].'/app/mini_bbs/login.php');
    exit();
  }

try {
  if (!empty($_POST)) {
    if ($_POST['message'] !== '') {
      $message = $db->prepare('INSERT INTO posts SET member_id=?,message=?,reply_message_id=?,created=NOW()');
      $message->execute(array(
        $member['id'],
        $_POST['message'],
        $_POST['reply_post_id']
      ));

      // header('Location: index.php');
      // exit();
    }
  }
} catch (PDOException $e) {
  print('DB接続エラー：' . $e->getMessage());
}
$page = $_REQUEST['page'];
if ($page == '') {
  $page = 1;
}
$page = max($page, 1);

$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
$posts->bindparam(1, $start, PDO::PARAM_INT);
$posts->execute();

if (isset($_REQUEST['res'])) {
  //返信の処理
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
  $response->execute(array($_REQUEST['res']));

  $table = $response->fetch();
  $message = '@' . $table['name'] . ' ' . $table['message'];
}
$page_title='ツブヤイター';
require('header.php');
?>

<body>
  <div class="container">
    <h1 class="text-center mt-5">ツブヤイター</h1>
    <div>
      <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
      <form action="" method="post">
        <div class="form-group">
          <label><?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>さん、メッセージをどうぞ</label>
          <textarea class="form-control form-control-lg" name="message"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>" />
        </div>
        <button class="btn btn-primary  btn-block my-3" type="submit">投稿する</button>
      </form>

      <?php foreach ($posts as $post) : ?>
        <div class="msg">
          <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" width="48" height="48" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
          <p><span class="font-weight-bold"><?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?></span></p>
          <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?></p>
            <a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">[Re]</a>
          <p class="day"><a href="view.php?id=<?php print(htmlspecialchars($post['id'])); ?>"><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>

            <?php if ($post['reply_message_id'] > 0) : ?>
              <a href="view.php?id=<?php print(htmlspecialchars($post['reply_message_id'])); ?>">
                返信元のメッセージ</a>
            <?php endif; ?>
            <?php if ($_SESSION['id'] == $post['member_id']) : ?>
              <a href="delete.php?id=<?php print(htmlspecialchars($post['id'])); ?>" style="color: #F33;">[削除]</a>
            <?php endif; ?>
          </p>
        </div>
      <?php endforeach; ?>

      <ul class="paging">
        <?php if ($page > 1) : ?>
          <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
        <?php else : ?>
          <li>前のページへ</li>
        <?php endif; ?>

        <?php if ($page > $maxPage) : ?>
          <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
        <?php else : ?>
          <li>次のページへ</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</body>

</html>