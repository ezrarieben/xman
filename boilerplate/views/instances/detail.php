<?php $this->layout('_layouts/default', ['title' => 'Instance: '.$instance['name']]) ?>

<div class="row">

  <?php if ($instance === false): ?>
    <div class="col-lg-12 d-flex justify-content-center">
      <div class="alert alert-danger" role="alert">
        Instance not found
      </div>
    </div>
  <?php endif ?>

  <?php if (is_array($instance)): ?>
  <div class="col-lg-6">

    <fieldset class="field-split">
      <legend>
        Instance
      </legend>
    </fieldset>

    <table class="table -bggrey -cwhite">
      <tbody>
        <tr>
          <th class="-bgdarkgrey" scope="row">Instance ID</th>
          <td><?= $instance['id'] ?></td>
        </tr>

        <tr>
          <th class="-bgdarkgrey" scope="row">Name</th>
          <td><?= $instance['name'] ?></td>
        </tr>

        <tr>
          <th class="-bgdarkgrey" scope="row">URL</th>
          <td><a class="-cwhite" href="<?= $instance['url'] ?>" target="_blank"><?= $instance['url'] ?></a></td>
        </tr>

        <tr>
          <th class="-bgdarkgrey" scope="row">MODX version</th>
          <td<?php if ($instance['hasUpdate']): ?> class="-cred"<?php endif ?>><?= $instance['info']['modx']['version_label'] ?></td>
        </tr>

        <tr>
          <th class="-bgdarkgrey" scope="row">Instance status</th>
          <td>
            <?php if ($instance['statusCode'] === 200): ?>
              <span class="-cgreen">OK</span>
            <?php else: ?>
            <span class="-cred">ERROR: <?= $instance['statusCode'] ?></span>
            <?php endif ?>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="row">
      <div class="col-lg-12">
        <a class="btn btn-secondary btn-green" href="/instances/edit/<?= $instance['id'] ?>/"><i class="fas fa-edit"></i> Edit</a>
        <a class="btn btn-secondary btn-red" href="/instances/delete/<?= $instance['id'] ?>/" confirm-instance-delete><i class="fas fa-trash"></i> Delete</a>
      </div>
    </div>

  </div>

  <div class="col-lg-6">
    <fieldset class="field-split">
      <legend>
        Instance options
      </legend>
    </fieldset>

    <table class="table">
      <tbody>
        <tr>
          <td class="border-white pt-1 pb-1 pl-0 pr-0">
            <div class="row">
              <div class="col-lg-3">
                <a class="btn btn-secondary btn-green w-100" href="<?= $instance['managerUrl'] ?>" target="_blank">Open MODX manager</a>
              </div>
            </div>
          </td>
        </tr>
        <?php if ($instance['hasUpdate']): ?>
        <tr>
          <td class="border-white pt-1 pb-1 pl-0 pr-0">
            <div class="row">
              <div class="col-lg-3">
                <a class="btn btn-secondary btn-green w-100" href="/instances/update/<?= $instance['id'] ?>/">Update MODX</a>
              </div>
            </div>
          </td>
        </tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>

  <div class="col-lg-12 pt-4">
    <fieldset class="field-split">
      <legend>
        Instance log
      </legend>
    </fieldset>

    <code>
      <?php foreach($instance['logEntries'] as $logEntry): ?>
        [<?= $logEntry['createdon'] ?>]: <?= $logEntry['text'] ?><br />
      <?php endforeach ?>
    </code>

    <div class="mt-3">
      <a class="btn btn-secondary btn-green" href="/instances/clearlog/<?= $instance['id'] ?>/"><i class="fas fa-trash"></i> Clear log</a>
    </div>
  </div>
<?php endif ?>

</div>
