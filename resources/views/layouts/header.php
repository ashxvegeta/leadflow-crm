<?php $currentUser = \Core\Auth::user(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'LeadFlow CRM') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/">LeadFlow CRM</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/">Home</a></li>
                <?php if (\Core\Auth::isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/users">Users</a></li>
                <?php endif; ?>
            </ul>

            <?php if ($currentUser): ?>
                <span class="navbar-text text-white me-3">
                    <?= htmlspecialchars($currentUser['name']) ?>
                    <span class="badge bg-light text-primary"><?= htmlspecialchars($currentUser['role']) ?></span>
                </span>
                <form method="POST" action="<?= BASE_URL ?>/logout" class="d-inline">
    <?= \Core\Csrf::field() ?>
    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
</form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container py-4">
<?php foreach (\Core\Flash::pull() as $type => $message): ?>
    <div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endforeach; ?>
