<?php
require __DIR__.'/config/database.php';
require __DIR__.'/includes/functions.php';
$pageTitle = 'Home';
$stmt = $pdo->query("SELECT s.*, c.category_name FROM scholarships s LEFT JOIN scholarship_categories c ON c.category_id=s.category_id WHERE s.status='published' AND s.closing_date >= CURDATE() ORDER BY s.closing_date LIMIT 6");
$scholarships = $stmt->fetchAll();
require __DIR__.'/includes/header.php';
?>
<section class="hero">
  <h1>Find scholarships. Apply with confidence.</h1>
  <p>Discover opportunities, check basic eligibility and track your applications in one place.</p>
  <a class="button" href="scholarships/index.php">Explore Scholarships</a>
</section>
<h2>Latest scholarships</h2>
<div class="cards">
<?php foreach ($scholarships as $s): ?>
  <article class="card">
    <h3><?= e($s['title']) ?></h3>
    <p><?= e(mb_strimwidth($s['description'],0,130,'...')) ?></p>
    <p><strong>Minimum GPA:</strong> <?= e($s['min_gpa']) ?></p>
    <p><strong>Deadline:</strong> <?= e($s['closing_date']) ?></p>
    <a href="scholarships/view.php?id=<?= (int)$s['scholarship_id'] ?>">View details</a>
  </article>
<?php endforeach; ?>
<?php if (!$scholarships): ?><p>No published scholarships are available right now.</p><?php endif; ?>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>
