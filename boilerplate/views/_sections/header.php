<?php $this->start('header') ?>

<header class="header position-fixed w-100 -bggrey">
  <div class="container-fluid h-100">
    <div class="row h-100 align-items-center">
      <div class="col-lg-3">
        <div class="logo -cwhite">
          <a href="/instances/">
          XMan
          </a>
          <span class="sub">by <a class="link" target="_blank" href="https://github.com/ezrarieben">@ezrarieben</a></span>
        </div>
      </div>

      <div class="col-lg-7">
        <nav>
          <ul class="nav">
            <li>
              <a href="/instances/"><i class="fab fa-modx"></i> MODX instances</a>
            </li>
            <li>
              <a href="/users/"><i class="fas fa-users"></i> User manager</a>
            </li>
          </ul>
        </nav>
      </div>

      <div class="col-lg-2">
        <div class="btn-group dropleft float-right dropdown-green">
          <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
            Account
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/users/edit/<?= $_SESSION['xman']['user']['id'] ?>/">Edit</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/logout/">Log out</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>


<?php $this->end() ?>
