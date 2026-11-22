<?php require ROOT_PATH . '/resources/views/layouts/header.php'; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <h4>Welcome, <?= htmlspecialchars(\Core\Auth::user()['name']) ?> 👋</h4>
        <p class="text-muted mb-0"><?= htmlspecialchars($message) ?></p>
    </div>
</div>

<?php require ROOT_PATH . '/resources/views/layouts/footer.php'; ?>
