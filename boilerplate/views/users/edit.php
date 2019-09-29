<?php $this->layout('_layouts/default', ['title' => 'Edit instance: '.$instance['name']]) ?>

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
        Return to <a href="/users/" title="User manager">user manager</a>
      </div>
    <?php endif ?>

    <?php if(empty($error)): ?>
      <form action="/users/edit/<?= $user['id'] ?>/" method="POST">

        <fieldset class="field-split">
          <legend>
            User settings
          </legend>
          <div class="form-group">
            <label for="username">Username*</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" required>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-lg-6">
                <label for="firstname">First name*</label>
                <input type="text" class="form-control" id="firstname" name="firstname" value="<?= $user['firstname'] ?>" required>
              </div>
              <div class="col-lg-6">
                <label for="lastname">Last name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" value="<?= $user['lastname'] ?>" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="email">E-Mail</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
          </div>
        </fieldset>

        <fieldset class="field-split">
          <legend>
            Password settings
          </legend>
          <div class="form-group">
            <label for="password">New password</label>
            <input type="password" class="form-control disabled" id="password" name="password">
          </div>

          <div class="form-group">
            <label for="password_confirm">Confirm password</label>
            <input type="password" class="form-control disabled" id="password_confirm" name="password_confirm">
          </div>
        </fieldset>

        <a class="btn btn-light" href="javascript:window.history.go(-1);">Back</a>
        <button type="submit" class="btn btn-success float-right btn-green">Save</button>
      </form>
    <?php endif ?>
  </div>
</div>
