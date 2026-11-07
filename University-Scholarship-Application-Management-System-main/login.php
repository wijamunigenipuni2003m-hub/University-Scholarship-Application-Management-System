<?php
require __DIR__.'/config/database.php'; require __DIR__.'/includes/functions.php';
if (is_logged_in()) redirect('index.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
    $q=$pdo->prepare('SELECT user_id,full_name,email,password_hash,role FROM users WHERE email=?');
    $q->execute([$email]); $u=$q->fetch();
    if ($u && password_verify($password,$u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']=(int)$u['user_id']; $_SESSION['full_name']=$u['full_name']; $_SESSION['role']=$u['role'];
        redirect($u['role']==='student'?'student/dashboard.php':'staff/dashboard.php');
    }
    $error='Invalid email or password.';
}
$pageTitle='Login'; require __DIR__.'/includes/header.php';
?>
<h1>Login</h1>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post" class="form">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<label>Email<input type="email" name="email" required></label>
<label>Password<input type="password" name="password" required></label>
<button>Login</button>
</form>
<?php require __DIR__.'/includes/footer.php'; ?>
