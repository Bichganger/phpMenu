<?php
require_once('link.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
  <?php 
  if(!empty($_REQUEST['login'])){
    $login = $_REQUEST['login'];
    $pass = $_REQUEST['password'];
    $query = "SELECT * FROM users";
    $result = $link->query($query);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if($login === $row['login'] && $pass === $row['password']){?>
  
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Панель навигации</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Переключатель навигации">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <?php

$query = "SELECT * FROM menu";
$result = $link->query($query);
if($result->num_rows > 0){
    echo '<ul class="navbar-nav">';
    while($row = $result->fetch_assoc()){
        echo '<li class="nav-item">
            <a class="nav-link active" aria-current="page" href="'. $row['link'] .'">' . $row['title'] . '</a>
            </li>';
    }
}
$link->close();

?>
              <li class="nav-link mx-3">
                        <a class="nav-link" id="register" href="admin.php">Админ Панель</a>
                    </li>
                    <li class="nav-link">
                        <a class="nav-link" id="login"  href="index.php">Выйти</a> <!-- Updated this line -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<?php 
        }
      }
    }
    if(empty($_REQUEST)){?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Панель навигации</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Переключатель навигации">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
              <?php 
              $query = "SELECT * FROM menu";
              $result = $link->query($query);
              if($result->num_rows > 0){
                  echo '<ul class="navbar-nav">';
                  while($row = $result->fetch_assoc()){
                      echo '<li class="nav-item">
                          <a class="nav-link active" aria-current="page" href="'. $row['link'] .'">' . $row['title'] . '</a>
                          </li>';
                  }
              }
              $link->close();
              
              ?>
                    <li class="nav-link">
                        <a class="nav-link" id="login" data-bs-toggle="modal" data-bs-target="#modal" href="#">Войти</a> <!-- Updated this line -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>   
  <?php 
    }
  ?>

    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="login" class="form-label">Логин</label>
 <input type="text" class="form-control" id="login" name="login" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <!-- You can add footer buttons or links here if needed -->
                </div>
            </div>
        </div>
    </div>

<?php 


?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>