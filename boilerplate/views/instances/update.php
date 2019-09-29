<?php $this->layout('_layouts/default', ['title' => 'Update MODX instance: '.$instance['name']]) ?>

<div class="row h-100 justify-content-center align-items-center">
  <div class="col-lg-6">

    <?php if(!empty($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?= $this->e($error) ?><br />
        <a href="javascript:window.history.go(-1);"> &lt; Go Back</a>
      </div>
    <?php endif ?>

    <?php if(!empty($success)): ?>
      <div class="alert alert-success" role="alert">
        <?= $this->e($success) ?><br />
        Return to <a href="/instances/<?= $instance['id'] ?>/" title="Instance details">instance details</a>
      </div>
    <?php endif ?>

  </div>
</div>
