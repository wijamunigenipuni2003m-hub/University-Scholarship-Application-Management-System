<?php
require __DIR__.'/../config/database.php'; require __DIR__.'/../includes/functions.php';
$term=trim($_GET['q']??'');
$sql="SELECT s.*,c.category_name FROM scholarships s LEFT JOIN scholarship_categories c ON c.category_id=s.category_id WHERE s.status='published' AND s.closing_date>=CURDATE()";
$params=[];
if($term!==''){$sql.=" AND (s.title LIKE ? OR s.description LIKE ? OR c.category_name LIKE ?)";$like="%$term%";$params=[$like,$like,$like];}
$sql.=" ORDER BY s.closing_date ASC";
$q=$pdo->prepare($sql);$q->execute($params);$items=$q->fetchAll();
$pageTitle='Scholarship Directory'; require __DIR__.'/../includes/header.php';
?>
<h1>Scholarship directory</h1>
<form method="get" class="search"><input name="q" placeholder="Search by title or category" value="<?=e($term)?>"><button>Search</button></form>
<div class="cards"><?php foreach($items as $s):?><article class="card"><h3><?=e($s['title'])?></h3><p><?=e(mb_strimwidth($s['description'],0,160,'...'))?></p><p>Category: <?=e($s['category_name']??'Other')?> | Minimum GPA: <?=e($s['min_gpa'])?></p><p>Deadline: <?=e($s['closing_date'])?></p><a href="view.php?id=<?=(int)$s['scholarship_id']?>">View details</a></article><?php endforeach;?></div>
<?php if(!$items):?><p>No matching open scholarships found.</p><?php endif;?>
<?php require __DIR__.'/../includes/footer.php'; ?>
