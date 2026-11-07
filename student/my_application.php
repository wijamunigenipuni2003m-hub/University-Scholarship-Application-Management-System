<?php
require __DIR__.'/../config/database.php';require __DIR__.'/../includes/functions.php';require_role(['student']);
$q=$pdo->prepare('SELECT a.*,s.title FROM applications a JOIN scholarships s ON s.scholarship_id=a.scholarship_id WHERE a.student_id=? ORDER BY a.submitted_at DESC');$q->execute([(int)$_SESSION['user_id']]);$rows=$q->fetchAll();
$q=$pdo->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 10');$q->execute([(int)$_SESSION['user_id']]);$notes=$q->fetchAll();
$pageTitle='My Applications';require __DIR__.'/../includes/header.php';
?>
<h1>My applications</h1><div class="table-wrap"><table><tr><th>Scholarship</th><th>Submitted</th><th>Status</th></tr>
<?php foreach($rows as $r):?><tr><td><?=e($r['title'])?></td><td><?=e($r['submitted_at'])?></td><td><?=e($r['status'])?></td></tr><?php endforeach;?>
</table></div><?php if(!$rows):?><p>You have not submitted any applications.</p><?php endif;?>
<h2>Notifications</h2><?php foreach($notes as $n):?><p class="notice"><?=e($n['message'])?> <small><?=e($n['created_at'])?></small></p><?php endforeach;?>
<?php require __DIR__.'/../includes/footer.php'; ?>
