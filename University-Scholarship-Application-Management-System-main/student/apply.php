<?php
require __DIR__.'/../config/database.php'; require __DIR__.'/../includes/functions.php'; require_role(['student']);
$sid=(int)($_GET['id']??0);$q=$pdo->prepare("SELECT * FROM scholarships WHERE scholarship_id=? AND status='published' AND opening_date<=CURDATE() AND closing_date>=CURDATE()");$q->execute([$sid]);$s=$q->fetch();
if(!$s)exit('This scholarship is not currently open for applications.');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$statement=trim($_POST['applicant_statement']??'');$path=null;
 if($statement==='')$error='Please enter your applicant statement.';
 elseif(isset($_FILES['document']) && $_FILES['document']['error']!==UPLOAD_ERR_NO_FILE){
  $f=$_FILES['document'];$allowed=['pdf'=>'application/pdf'];
  $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
  $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
  if($f['error']!==UPLOAD_ERR_OK || $f['size']>2*1024*1024 || $ext!=='pdf' || $mime!=='application/pdf')$error='Upload one PDF file smaller than 2 MB.';
  else{
   $dir=__DIR__.'/../uploads';if(!is_dir($dir))mkdir($dir,0750,true);
   $filename=bin2hex(random_bytes(16)).'.pdf';
   if(!move_uploaded_file($f['tmp_name'],$dir.'/'.$filename))$error='Could not save uploaded document.';
   else $path='uploads/'.$filename;
  }
 }
 if(!$error){
  try{$q=$pdo->prepare('INSERT INTO applications(scholarship_id,student_id,applicant_statement,document_path) VALUES(?,?,?,?)');$q->execute([$sid,(int)$_SESSION['user_id'],$statement,$path]);add_notification($pdo,(int)$_SESSION['user_id'],'Your application has been submitted.');flash('Application submitted successfully.');redirect('my_applications.php');}
  catch(PDOException $ex){$error='You may have already applied, or the application could not be saved.';}
 }
}
$pageTitle='Apply';require __DIR__.'/../includes/header.php';
?>
<h1>Apply: <?=e($s['title'])?></h1><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data" class="form"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<label>Applicant statement<textarea name="applicant_statement" required rows="6"></textarea></label>
<label>Supporting document (PDF, max 2 MB)<input type="file" name="document" accept="application/pdf"></label>
<button>Submit application</button></form>
<?php require __DIR__.'/../includes/footer.php'; ?>
