<?php require_once __DIR__ . '/../_sections/head.php' ?>
<?php require_once __DIR__ . '/../_sections/scripts.php' ?>
<?php require_once __DIR__ . '/../_sections/header.php' ?>
<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <?= $this->section('head') ?>
</head>
<body class="h-100">
    <?= $this->section('header') ?>

    <main>
      <div class="container-fluid">
        <?= $this->section('content') ?>
      </div>
    </main>

    <?= $this->section('scripts') ?>
</body>
</html>
