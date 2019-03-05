<?php


session_start();
// バリデーション
// 入力値が正しく設定されているかを確認し、不正な場合はユーザーに再入力を促す機能

$errors = [];

//check.phpから戻るできた場合
if (isset($_GET['action']) && $_GET['action'] == 'rewrite') {
    $_POST['input_name'] = $_SESSION['51_LearnSNS']['name'];
    $_POST['input_email'] = $_SESSION['51_LearnSNS']['email'];
    $_POST['input_password'] = $_SESSION['51_LearnSNS']['password'];
    $errors['rewrite'] = true;
}

// POST送信時
$name = '';
$email = '';

if (!empty($_POST)) {
    $name = $_POST['input_name'];
    $email = $_POST['input_email'];
    $password = $_POST['input_password'];

    if ($name == '') {
        // echo '空です';
        $errors['name'] = 'blank';
    }
    if ($email == '') {
        $errors['email'] = 'blank';
    }
    $count = strlen($password);
    if ($password == '') {
        $errors['password'] = $_FILES;
    } elseif ($count < 4 || 16 < $count) {
        $errors['password'] = 'blank';
    }
    // 画像のチェック
    // input type="file"で送られるもの
    // ファイルに関しては$_FILESというスーパーグローバル変数を使用
    // $_FILESを利用するための決まりごと

    $file_name = '';
    if (!isset($_GET['action'])) {
        $file_name = $_FILES['input_img_name']['name'];
    }

    if ($file_name != '') {
        //拡張子のチェック
        $file_type = substr($file_name, -3);
        // 後ろから三文字を取り出した
        $file_type = strtolower($file_type);
        // 小文字にして再代入
        if ($file_type != 'jpg' && $file_type != 'png' && $file_type != 'gif') {
            $errors['img_name'] = 'type';
        }
    } else {
        $errors['img_name'] = 'blank';
    }
    if (empty($errors)) {
        // 画像のアップロード
        // 画像自体はフォルダに保存
        // 画像のパスはDBに保存
        // 2019030514052529.png
        // 現在日時分秒を取得
        $date_str = date('YmdHis');

        // タイムスタンプとファイル名を結合して一意のファイル名を生成する
        $submit_file_name = $date_str.$file_name;

        move_uploaded_file($_FILES['input_img_name']['tmp_name'], '../user_profile_img/'.$submit_file_name);
        // ../は一個上のフォルダという意味

        // セッションに送信されたデータを保存
        // PHPでは$_SESSIONを利用する
        // セッションに値を格納
        // 連想配列形式
        $_SESSION['51_LearnSNS']['name'] = $name;
        $_SESSION['51_LearnSNS']['email'] = $email;
        $_SESSION['51_LearnSNS']['password'] = $password;
        $_SESSION['51_LearnSNS']['img_name'] = $submit_file_name;

        // 画面遷移
        header('Location: check.php');
        exit();
    }
}

// フォーム送信された値の取得

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Learn SNS</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>
<body style="margin-top: 60px">
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2 thumbnail">
                <h2 class="text-center content_header">アカウント作成</h2>
                <!-- 
                    method:送信方法
                    action:送信先
                    どこにどんな方法で値を送るのかをみる 
                -->
                <!-- 
                    signup.phpで値に不備がないかを確認したのちに、OKだったらcheck.phpに遷移する
                    値の送信先はsign.php
                 -->
                <form method="POST" action="signup.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">ユーザー名</label>
                        <input type="text" name="input_name" class="form-control" id="name" placeholder="山田 太郎"
                            value="<?php echo $name; ?>">
                        <?php if (isset($errors['name']) && $errors['name'] == 'blank'): ?>
                        <p class="text-danger">ユーザー名を入力してください</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="email">メールアドレス</label>
                        <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com"
                            value="<?php echo $email; ?>">
                        <?php if (isset($errors['email']) && $errors['email'] == 'blank'): ?>
                        <p class="text-danger">emailを入力してください</p>
                        <?php endif; ?>
                        
                    </div>
                    <div class="form-group">
                        <label for="password">パスワード</label>
                        <input type="password" name="input_password" class="form-control" id="password" placeholder="4 ~ 16文字のパスワード">
                        <?php if (isset($errors['password']) && $errors['password'] == 'blank'): ?>
                        <p class="text-danger">パスワードを入力してください</p>
                        <?php endif; ?>
                        <?php if (isset($errors['password']) && $errors['password'] == 'length'): ?>
                        <p class="text-danger">パスワードは4-16で入力してください</p>
                        <?php endif; ?>
                        <?php if(!empty($errors)): ?>
                        <p class = "text-danger">パスワードを再度入力してください</p>
                        <?php endif；； ?>
                        
                    </div>
                    <div class="form-group">
                        <label for="img_name">プロフィール画像</label>
                        <input type="file" name="input_img_name" id="img_name" accept="image/*">
                        <?php if (isset($errors['img_name']) && $errors['img_name'] == 'blank'): ?>
                        <p class="text-danger">画像をアップロードしてください</p>
                        <?php endif; ?>
                        <?php if (isset($errors['img_name']) && $errors['img_name'] == 'type'): ?>
                        <p class = "text-danger">拡張子がjpg,png,gifの画像を選択してください</p>
                        <?php endif; ?>
                    </div>
                    <input type="submit" class="btn btn-default" value="確認">
                    <span style="float: right; padding-top: 6px;">ログインは
                        <a href="../signin.php">こちら</a>
                    </span>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="../assets/js/jquery-3.1.1.js"></script>
<script src="../assets/js/jquery-migrate-1.4.1.js"></script>
<script src="../assets/js/bootstrap.js"></script>
</html>