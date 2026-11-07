<?php
require __DIR__.'/../config/database.php'; require __DIR__.'/../includes/functions.php';
$id=(int)($_GET['id']??0);$q=$pdo->prepare("SELECT s.*,c.category_name FROM scholarships s LEFT JOIN scholarship_categories c ON c.category_id=s.category_id WHERE s.scholarship_id=? AND s.status='published'");$q->execute([$id]);$s=$q->fetch();
if(!$s){http_response_code(404);exit('Scholarship not found.');}
$pageTitle=$s['title'];require __DIR__.'/../includes/header.php';
?>
<h1><?=e($s['title'])?></h1><p><?=nl2br(e($s['description']))?></p>
<p><strong>Provider:</strong> <?=e($s['provider_name'])?></p><p><strong>Benefits:</strong> <?=nl2br(e($s['benefits']))?></p>
<p><strong>Category:</strong> <?=e($s['category_name']??'Other')?></p><p><strong>Minimum GPA:</strong> <?=e($s['min_gpa'])?></p>
<p><strong>Required documents:</strong> <?=nl2br(e($s['required_documents']))?></p>
<p><strong>Apply between:</strong> <?=e($s['opening_date'])?> and <?=e($s['closing_date'])?></p>
<?php if(is_logged_in() && $_SESSION['role']==='student' && $s['closing_date']>=date('Y-m-d')):?>
<a class="button" href="../student/apply.php?id=<?=$id?>">Apply now</a>
<?php elseif(!is_logged_in()):?><a class="button" href="../login.php">Login to apply</a><?php endif;?>
<?php require __DIR__.'/../includes/footer.php'; ?>
