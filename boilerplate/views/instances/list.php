<?php $this->layout('_layouts/default', ['title' => 'Instances']) ?>

<div class="row">
  <div class="col-lg-12 mb-3">
    <div class="row">
      <div class="col-lg-1">
        <a class="btn btn-secondary btn-green" href="/instances/create/" title="Create instance">+ <i class="fab fa-modx"></i></a>
      </div>
      <div class="col-lg-3 offset-lg-8">
        <form action="/instances/" method="POST">
          <div class="row">
            <div class="col-lg-3">
              <div class="d-flex h-100 justify-content-center align-items-center">
                <span class="text-center"><?= count($instances) ?> / <?= $totalInstanceCount ?></span>
              </div>
            </div>
            <div class="col-lg-6">
              <input type="text" name="search" placeholder="Search" class="form-control" value="<?= (!empty($_POST['search'])) ? $this->e($_POST['search'], 'strip_tags') : '' ?>"/>
            </div>
            <div class="col-lg-3">
              <button type="submit" class="btn btn-primary btn-green w-100"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <table class="table -bggrey -cwhite">
      <thead class="-bgdarkgrey">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Name</th>
          <th scope="col">URL</th>
          <th scope="col">MODX version</th>
          <th scope="col">Status</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <?php if(count($instances) < 1): ?>
          <tr>
            <th colspan="6" scope="row" class="text-center">No instances found</th>
          </tr>
        <?php endif ?>

        <?php foreach($instances as $instance): ?>
          <tr <?php if($instance['hasUpdate']): ?> class="bg-warning" <?php endif ?> <?php if($instance['statusCode'] !== 200): ?> class="bg-danger" <?php endif ?>>
            <th scope="row"><a class="-cwhite" href="/instances/<?= $instance['id'] ?>/" title="Instance detail site"><?= $instance['id'] ?></a></th>
            <td><a class="-cwhite" href="/instances/<?= $instance['id'] ?>/" title="Instance detail site"><?= strlen($instance['name']) > 50 ? substr($instance['name'],0,50)."..." : $instance['name']; ?></a></td>
            <td><a class="-cwhite" href="<?= $instance['url'] ?>" target="_blank"><?= strlen($instance['url']) > 50 ? substr($instance['url'],0,50)."..." : $instance['url']; ?></a></td>
            <td<?php if($instance['hasUpdate']): ?> class="-cred" <?php endif ?>><?= $instance['info']['modx']['version_label']?></td>
            <td>
              <?php if($instance['statusCode'] === 200 && $instance['hasUpdate'] !== true): ?>
                <span class="-cgreen">OK</span>
              <?php elseif($instance['hasUpdate']): ?>
                <span>WARNING: MODX update required</span>
              <?php else: ?>
                <span class="-cred">ERROR: <?= $instance['statusCode']?></span>
              <?php endif ?>

            </td>
            <td>
                <a href="/instances/<?= $instance['id'] ?>" title="Show instance info"><i class="fas fa-info-circle"></i></a>
                <a href="/instances/edit/<?= $instance['id'] ?>/" title="Edit instance"><i class="fas fa-edit"></i></a>
                <a href="/instances/delete/<?= $instance['id'] ?>/" title="Delete instance" confirm-instance-delete><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
