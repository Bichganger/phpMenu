<?php
require_once('link.php');

if (!empty($_POST['title']) && !empty($_POST['link'])) {
    $title = $_POST['title'];
    $links = $_POST['link'];

    $filename = pathinfo($links, PATHINFO_FILENAME);
    $filename = strtolower(str_replace(' ', '_', $filename)) . '.php';
    $filecontent = "<?php\n
require_once('link.php');\n
require_once('header.php');\n
?>
<h1>" . $title . "</h1>
<?php
require_once('footer.php');\n?>";

    file_put_contents($filename, $filecontent);
    $stmt = $link->prepare("INSERT INTO `menu` (title, link) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $filename);

    if ($stmt->execute()) {
        header("Location: /admin.php");
        exit;
    } else {
        echo 'Ошибка при создании пункта меню: ' . $stmt->error;
    }
    $stmt->close();
}

if (isset($_POST['delete_btn'])) {
    print_r ($_POST);
    echo "123";
    $deleteIds = array_filter($_POST['checkboxes'], function ($value) {
        return $value;
    });

    if (!empty($deleteIds)) {
        $deleteIdsStr = implode(',', array_keys($deleteIds));
        $query_select = "SELECT title FROM `menu` WHERE id IN ($deleteIdsStr)";
        $result_select = $link->query($query_select);

        while ($row = $result_select->fetch_assoc()) {
            $title = $row["title"];
            $filename = strtolower(str_replace(' ', '_', $title)) . '.php';

            if (file_exists($filename)) {
                unlink($filename);
            }
        }

        $delete_query = "DELETE FROM `menu` WHERE id IN ($deleteIdsStr)";
        if (mysqli_query($link, $delete_query)) {
            header("Location: /admin.php");
            exit;
        } else {
            echo "Ошибка удаления записи: " . mysqli_error($link);
        }
    }
}

if (isset($_POST['saveButton'])) {
    $query_update = "SELECT * FROM `menu`";
    $result_update = $link->query($query_update);

    while ($row = $result_update->fetch_assoc()) {
        $id = $row["id"];
        $title_get = $row["title"];
        $link_get = $row["link"];

        if (isset($_POST["title_get"][$id]) && isset($_POST["link_get"][$id])) {
            $title_ins = $_POST["title_get"][$id];
            $link_ins = $_POST["link_get"][$id];

            if ($title_get != $title_ins || $link_get != $link_ins) {
                $new_filename = strtolower(str_replace(' ', '_', $title_ins)) . '.php';
                $old_filename = strtolower(str_replace(' ', '_', $title_get)) . '.php';

                if (file_exists($old_filename)) {
                    rename($old_filename, $new_filename);
                }

                $filecontent = "<?php\n//Файл:\n$new_filename\nheader('Location: $link_ins');\n?>";
                file_put_contents($new_filename, $filecontent);

                $stmt = $link->prepare("UPDATE `menu` SET `title` = ?, `link` = ? WHERE id = ?");
                $stmt->bind_param("ssi", $title_ins, $link_ins, $id);

                if ($stmt->execute()) {
                    header("Location: /admin.php");
                    exit;
                } else {
                    echo "Ошибка при обновлении данных: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

$link->close();
