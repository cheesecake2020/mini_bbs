<?php
session_start();
require('dbconnect.php');

if (!isset($_SESSION['join'])) {
	header('Location: https://'.$_SERVER['HTTP_HOST'].'/app/mini_bbs/index.php');
	exit();
}
if (!empty($_POST)) {
	$statment = $db->prepare('INSERT INTO members SET name=?, email=?,password=?,picture=?,created=NOW()');
	$statment->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['image']
	));
	unset($_SESSION['join']);
	header('Location: https://'.$_SERVER['HTTP_HOST'].'/app/mini_bbs/thanks.php');
	exit();
}
$page_title = '会員登録';
require('header.php');
?>


<body>
	<div class="container">
		<div class="d-flex mt-5" id="lead">
			<img class="img-fluid " src="img/minirogo.png" alt="" />
			<h1>会員登録</h1>
			<img class="img-fluid " src="img/minirogo.png" alt="" />
		</div>


		<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
		<form action="" method="post">
			<input type="hidden" name="action" value="submit" />
			<div class="form-group">
				<label for="nickname">ニックネーム </label><br>
				<p><?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES)); ?></p>

			
			<div class="form-group">
				<label for="email">メールアドレス </label><br>
				<p><?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?></p>
				</div>
				<div class="form-group">
					<label for="password">パスワード</label>
					<p>【表示されません】</p>
				</div>
				<div class="form-group">
					<label>写真など</label>
					<?php if ($_SESSION['join']['image'] !== '') : ?>
						<img src="./member_picture/<?php print(htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES)); ?>">
					<?php endif; ?>
				</div>
				<a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>
				<button class="btn btn-primary mt-2" type="submit">登録する</button>
				</form>
		
	</div>

	</div>
</body>

</html>