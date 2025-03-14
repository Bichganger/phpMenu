<?php 
require_once('link.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Панель навигации</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключатель навигации">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav"></div>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" id="register" href="index.php">Выход</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="register" href="admin.php">Админ панель</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="container">
        <div class="row">
            <div class="col12">
                <h3>Создать пункт меню</h3>
            </div>
        </div>
        <form method="POST" action="admin_logic.php">
            <div class="row mb-3">
                <div class="col-1">
                    <label for="title" class="form-label">Заголовок:</label>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>
                <div class="col-1">
                    <label for="link" class="form-label">Ссылка:</label>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" name="link" id="link" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" value="Создать пункт меню">Создать пункт меню</button>
            </div>
        </form>
    </div>
    <div class="container">
        <div class="row">
            <div class="col12">
                <h3 class="text-danger">Редактировать пункт меню</h3>
            </div>
        </div>
    </div>
    <div class="container">
        <form action="admin_logic.php" method="POST">
            <?php
                require_once('link.php');
                $query_get = "SELECT * FROM menu";
                $result_get = $link->query($query_get);

                while($row = $result_get->fetch_assoc()){
                    $title_get = $row['title'];
                    $link_get = $row['link'];
                    $id = $row["id"];
                ?>
            <div class="row mt-2">
                <div class="row mt-2 main">
    <div class="col-1">
        <label for="title_get<?= $id ?>">Заголовок:</label>
    </div>
    <div class="col-4">
        <input type="text" class="form-control" name="title_get[<?= $id ?>]" id="title_get<?= $id ?>" value="<?= $title_get ?>" onchange="activateButton()">
    </div>

    <div class="col-1">
        <label for="link_get<?= $id ?>">Cсылка:</label>
    </div>
    <div class="col-4">
        <input type="text" class="form-control" name="link_get[<?= $id ?>]" id="link_get<?= $id ?>" value="<?= $link_get ?>" onchange="activateButton()">
    </div>
    <div class="col-1">
        <input type="checkbox" name="checkboxes[<?= $id ?>]" id="checkboxes[<?= $id ?>]" class="del">
    </div>
</div>
</div>              
  <?php
                }
                $link->close();
                ?>
                <div class="row mt-2 justify-content-between">
                    <div class="col-2">
                        <input type="submit" name="saveButton" value="Сохранить" disabled="disabled" id="saveButton">
                    </div>
                    <div class="col-2">
                    <input type="submit" name="delete_btn" value="Удалить">
                    </div>
                </div>
            </form>
        </div>
        <script>
            function activateButton(){
                document.getElementById('saveButton').disabled = false;
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/script.js"></script>
<?php
require_once('footer.php');
?>
</body>

</html>