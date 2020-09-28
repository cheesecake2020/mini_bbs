<?php
session_start();
require('dbconnect.php');

if (!empty($_POST)) {
	if ($_POST['name'] === '') {
		$error['name'] = 'blank';
	}
	if ($_POST['email'] === '') {
		$error['email'] = 'blank';
	}
	if (strlen($_POST['password']) < 4) {
		$error['password'] = 'length';
	}
	if ($_POST['password'] === '') {
		$error['password'] = 'blank';
	}
	$fileName = $_FILES['image']['name'];
	if (!empty($fileName)) {
		$ext = substr($fileName, -3);
		if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
			$error['image'] = 'type';
		}
	}

	//アカウントの重複をチェック
	if (empty($error)) {
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if ($record['cnt'] > 0) {
			$error['email'] = 'duplicate';
		}
	}

	if (empty($error)) {
		$image = date('YmdHis') . $_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'],'./member_picture/'.$image);
		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;
		header('Location: https://'.$_SERVER['HTTP_HOST'].'/app/mini_bbs/check.php');
		exit();
	}
}

if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {
	$_POST = $_SESSION['join'];
}
$page_title = '会員登録';
require('header.php');
?>

<body>
	<div class="container ">
		<div class="d-flex mt-5" id="lead">
			<img class="img-fluid " src="img/minirogo.png" alt="" />
			<h1>会員登録</h1>
			<img class="img-fluid " src="img/minirogo.png" alt="" />
		</div>

		<form action="" method="post" enctype="multipart/form-data">
			<p>次のフォームに必要事項をご記入ください。</p>
			<div class="form-group">
				<label for="nickname">ニックネーム <span class="text-light bg-danger px-2">必須</span></label>
				<input class="form-control" type="text" id="nickname" name="name" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>" />
				<?php if ($error['name'] === 'blank') : ?>
					<p class="error">*ニックネームを入力してください</p>
				<?php endif; ?>

			</div>
			<div class="form-group">
				<label for="email">メールアドレス<span class="text-light bg-danger px-2">必須</span></label>
				<input class="form-control" type="text" name="email" id="email" value="<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>" />
				<?php if ($error['email'] === 'blank') : ?>
					<p class="error">*メールアドレスを入力してください</p>
				<?php endif; ?>
				<?php if ($error['email'] === 'duplicate') : ?>
					<p class="error">*指定されたメールアドレスは、既に登録されています</p>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<label for="password">パスワード<span class="text-light bg-danger px-2">必須</span></label>
				<input class="form-control" type="password" name="password" id="password" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
				<?php if ($error['password'] === 'length') : ?>
					<p class="error">*パスワードは4文字以上で入力してください</p>
				<?php endif; ?>
				<?php if ($error['password'] === 'blank') : ?>
					<p class="error">*パスワードを入力してください</p>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<label for="file">写真など</label>
				<input class="form-control-file" type="file" name="image" id="file" />
				<?php if ($error['image'] === 'type') : ?>
					<p class="error">*写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
				<?php endif; ?>
				<?php if (!empty($error)) : ?>
					<p class="error">*恐れ入りますが、画像を改めて指定してください</p>
				<?php endif; ?>
			</div>
			<button class="btn btn-primary  btn-block" type="submit">入力内容を確認する</button>

		</form>

	</div>
</body>

</html>