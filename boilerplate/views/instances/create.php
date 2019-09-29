<?php $this->layout('_layouts/default', ['title' => 'New instance']) ?>

<div class="row h-100 justify-content-center align-items-center">
  <div class="col-lg-6">
    <?php if(!empty($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?= $this->e($error) ?>
      </div>
    <?php endif ?>

    <?php if(!empty($success)): ?>
      <div class="alert alert-success" role="alert">
        <?= $this->e($success) ?><br />
        Return to <a href="/instances/" title="MODX instances">instance list</a>
      </div>
    <?php endif ?>

    <?php if(empty($error) && empty($success)): ?>
      <div class="alert alert-info" role="alert">
        <b>NOTE:</b> Please make sure you have installed the 'XMan Client' package in MODX first!
      </div>
    <?php endif ?>

    <form action="/instances/create/" method="POST">

      <fieldset class="field-split">
        <legend>
          Instance info
        </legend>

        <div class="form-group">
          <label for="name">Name*</label>
          <input type="text" class="form-control" id="name" name="name" value="<?= $instance['name'] ?>" required>
        </div>
      </fieldset>

      <fieldset class="field-split">
        <legend>
          Instance API settings
        </legend>

        <div class="form-group">
          <label for="url">URL*</label>
          <input type="text" class="form-control" id="url" name="url" value="<?= $instance['url'] ?>" required>
        </div>

        <div class="form-group">
          <label for="modx_token">MODX token*</label>
          <input type="text" class="form-control" id="modx_token" name="modx_token" value="<?= $instance['modx_token'] ?>" required>
        </div>
      </fieldset>

      <a class="btn btn-light" href="javascript:window.history.go(-1);">Back</a>
      <button type="submit" class="btn btn-success float-right btn-green">Save</button>
    </form>
  </div>
</div>
