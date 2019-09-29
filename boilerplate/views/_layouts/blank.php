<?php require_once __DIR__ . '/../_sections/head.php' ?>
<?php require_once __DIR__ . '/../_sections/scripts.php' ?>
<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <?= $this->section('head') ?>
</head>
<body class="h-100">

    <main class="h-100 -bggrey">
      <?= $this->section('content') ?>
    </main>

    <?= $this->section('scripts') ?>
</body>
</html>
