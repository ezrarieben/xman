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
        Return to <a href="/instances/" title="MODX instances">instance list</a>
      </div>
    <?php endif ?>

    <?php if(empty($error)): ?>
    <form action="/instances/edit/<?= $instance['id'] ?>/" method="POST">

      <fieldset class="field-split">
        <legend>
          Instance info
        </legend>

        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" class="form-control" id="name" name="name" value="<?= $instance['name'] ?>" required>
        </div>
      </fieldset>

      <fieldset class="field-split">
        <legend>
          Instance API settings
        </legend>

        <div class="form-group">
          <label for="url">URL</label>
          <input type="text" class="form-control" id="url" name="url" value="<?= $instance['url'] ?>" required>
        </div>

        <div class="form-group">
          <label for="modx_token">MODX token</label>
          <input type="text" class="form-control" id="modx_token" name="modx_token" value="<?= $instance['modx_token'] ?>" required>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-lg-12">
                <label for="xman_token">XMan token</label>
            </div>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="xman_token" value="<?= $instance['xman_token'] ?>" disabled>
            </div>
            <div class="col-lg-2">
              <a href="#" title="Copy to clipboard" class="btn btn-primary btn-green w-100" copy-to-clipboard-target="#xman_token"><i class="fa fa-copy"></i></a>
            </div>
          </div>
        </div>
      </fieldset>

      <a class="btn btn-light" href="javascript:window.history.go(-1);">Back</a>
      <button type="submit" class="btn btn-success float-right btn-green">Save</button>
    </form>
    <?php endif ?>
    
  </div>
</div>
