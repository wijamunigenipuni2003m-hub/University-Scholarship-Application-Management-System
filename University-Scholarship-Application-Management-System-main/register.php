<?php
require __DIR__.'/config/database.php'; require __DIR__.'/includes/functions.php';
if (is_logged_in()) redirect('index.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $name=trim($_POST['full_name']??''); $email=strtolower(trim($_POST['email']??''));
    $password=$_POST['password']??''; $studentNo=trim($_POST['student_number']??'');
    if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<8) {
        $error='Enter a name, valid email and password with at least 8 characters.';
    } else {
        try {
            $pdo->beginTransaction();
            $q=$pdo->prepare("INSERT INTO users(full_name,email,password_hash,role) VALUES(?,?,?,'student')");
            $q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
            $id=(int)$pdo->lastInsertId();
            $q=$pdo->prepare('INSERT INTO student_profiles(user_id,student_number) VALUES(?,?)');
            $q->execute([$id,$studentNo]);
            $pdo->commit(); flash('Registration successful. Please log in.'); redirect('login.php');
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error='That email address is already registered.';
        }
    }
}
$pageTitle='Register'; require __DIR__.'/includes/header.php';
?>
<h1>Create student account</h1>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post" class="form">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<label>Full name<input name="full_name" required maxlength="120"></label>
<label>Email<input type="email" name="email" required></label>
<label>Student number<input name="student_number"></label>
<label>Password (minimum 8 characters)<input type="password" name="password" minlength="8" required></label>
<button>Register</button>
</form>
<?php require __DIR__.'/includes/footer.php'; ?>
