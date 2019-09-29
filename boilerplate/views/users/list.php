<?php $this->layout('_layouts/default', ['title' => 'Users']) ?>

<div class="row">
  <div class="col-lg-12 mb-3">
    <a class="btn btn-secondary btn-green" href="/users/create/" title="Create user"><i class="fas fa-user-plus"></i></a>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <table class="table -bggrey -cwhite">
      <thead class="-bgdarkgrey">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Username</th>
          <th scope="col">First name</th>
          <th scope="col">Last name</th>
          <th scope="col">E-Mail</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <?php if($totalUserCount < 1): ?>
          <tr>
            <th colspan="6" scope="row" class="text-center">No users found</th>
          </tr>
        <?php endif ?>
        <?php foreach($users as $user): ?>
          <tr>
            <th scope="row"><?= $user['id'] ?> <?php if($user['active']): ?> (You) <?php endif ?></th>
            <td><?= $user['username'] ?></td>
            <td><?= $user['firstname'] ?></td>
            <td><?= $user['lastname'] ?></td>
            <td><?= $user['email'] ?></td>
            <td>
                <a href="/users/edit/<?= $user['id'] ?>/" title="Edit user"><i class="fas fa-edit"></i></a>
              <?php if($user['active'] !== true): ?>
                <a href="/users/delete/<?= $user['id'] ?>/" title="Delete user" confirm-user-delete><i class="fas fa-trash"></i></a>
              <?php endif ?>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
