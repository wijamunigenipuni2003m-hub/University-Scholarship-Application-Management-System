<?php
require __DIR__.'/../config/database.php'; require __DIR__.'/../includes/functions.php'; require_role(['student']);
$id=(int)$_SESSION['user_id']; $q=$pdo->prepare('SELECT * FROM student_profiles WHERE user_id=?'); $q->execute([$id]); $p=$q->fetch() ?: [];
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $gpa=filter_var($_POST['current_gpa'],FILTER_VALIDATE_FLOAT);
 if($gpa===false || $gpa<0 || $gpa>4){$error='GPA must be between 0.00 and 4.00.';}
 else{
  $q=$pdo->prepare('UPDATE student_profiles SET student_number=?,faculty=?,degree_program=?,study_year=?,current_gpa=?,achievements=? WHERE user_id=?');
  $q->execute([trim($_POST['student_number']),trim($_POST['faculty']),trim($_POST['degree_program']),($_POST['study_year']===''?null:(int)$_POST['study_year']),$gpa,trim($_POST['achievements']),$id]);
  flash('Profile updated.'); redirect('dashboard.php');
 }
}
$pageTitle='My Profile'; require __DIR__.'/../includes/header.php';
?>
<h1>My student profile</h1><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" class="form"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<label>Student number<input name="student_number" value="<?=e($p['student_number']??'')?>"></label>
<label>Faculty<input name="faculty" value="<?=e($p['faculty']??'')?>"></label>
<label>Degree programme<input name="degree_program" value="<?=e($p['degree_program']??'')?>"></label>
<label>Study year<input type="number" min="1" max="8" name="study_year" value="<?=e($p['study_year']??'')?>"></label>
<label>Current GPA (0–4)<input type="number" step="0.01" min="0" max="4" name="current_gpa" value="<?=e($p['current_gpa']??'0.00')?>" required></label>
<label>Achievements<textarea name="achievements"><?=e($p['achievements']??'')?></textarea></label>
<button>Save profile</button></form>
<?php require __DIR__.'/../includes/footer.php'; ?>
