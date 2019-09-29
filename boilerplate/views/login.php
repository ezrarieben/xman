<?php $this->layout('_layouts/blank', ['title' => 'Login']) ?>

<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="col-lg-4">
      <h1 class="-cgreen">XMan</h1>
      <?php if(!empty($this->e($error))): ?>
        <div class="alert alert-danger" role="alert">
          <?= $this->e($error) ?>
        </div>
      <?php endif ?>


      <form class="-cwhite" action="/login/" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-success btn-green">Login</button>
      </form>
    </div>
  </div>
</div>
