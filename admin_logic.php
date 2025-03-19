<?php
require_once('link.php');
if (!empty($_POST['title']) && !empty($_POST['link'])) {
    $title = $_POST['title'];
    $links = $_POST['link'];
    $title = htmlspecialchars(trim($title));
    $links = htmlspecialchars(trim($links));
    $filename = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', pathinfo($links, PATHINFO_FILENAME)))) . '.php';
    $filecontent = "<?php\n
                        require_once('link.php');\n
                        require_once('header.php');\n
                    ?>
                    <h1>" . $title . "</h1>
                    <?php
                    require_once('footer.php');\n?>";
    if (file_put_contents($filename, $filecontent) !== false) {
        $stmt = $linkBase->prepare("INSERT INTO menu (title, link, sort_order) SELECT ?, ?, (SELECT MAX(sort_order) + 1 FROM menu)");
        if ($stmt === false) {
            echo "Error preparing statement: " . $link->error();
        } else {

            $stmt->bind_param("ss", $title, $filename);


            if ($stmt->execute()) {
                header("Location: admin.php");
                exit;
            } else {
                echo 'Error adding menu item: ' . $stmt->error;
            }


            $stmt->close();
        }
    } else {
        echo "Error creating file: " . error_get_last()['message'];
    }
}
if (isset($_POST['reordered_ids'])) {
    $ids = explode(",", $_POST['reordered_ids']);
    foreach ($ids as $i => $id) {
        $sort_order = $i + 1;
        $stmt = $linkBase->prepare("UPDATE menu SET sort_order = ? WHERE id = ?");
        $stmt->bind_param("ii", $sort_order, $id);

        if (!$stmt->execute()) {
            echo "Error updating sort order for ID $id: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
    header("Location: /admin.php");
    exit;
}
if (isset($_POST['saveButton'])) {
    foreach ($_POST['title_get'] as $id => $title) {
        $link_get = $_POST['link_get'][$id];
        $title = htmlspecialchars(trim($title));
        $link_get = htmlspecialchars(trim($link_get));

        $stmt = $linkBase->prepare("UPDATE menu SET title = ?, link = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $link_get, $id);
        if (!$stmt->execute()) {
            echo "Error updating item ID $id: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
    header("Location: /admin.php");
    exit;
}
if (isset($_POST['delete']) && !empty($_POST['checkboxes'])) {
    $deleteIds = array_filter($_POST['checkboxes'], function ($value) {
        return !empty($value);
    });

    if (!empty($deleteIds)) {
        $deleteIdsStr = implode(',', array_map('intval', array_keys($deleteIds))); // Санитизация ID
        $query_select = "SELECT title FROM menu WHERE id IN ($deleteIdsStr)";

        if ($result_select = $linkBase->query($query_select)) {
            while ($row = $result_select->fetch_assoc()) {
                $title = htmlspecialchars(trim($row["title"])); // Санитизация
                $filename = strtolower(str_replace(' ', '_', $title)) . '.php';

                if (file_exists($filename) && !unlink($filename)) {
                    echo "Ошибка при удалении файла: $filename";
                }
            }

            $delete_query = "DELETE FROM menu WHERE id IN ($deleteIdsStr)";
            if ($linkBase->query($delete_query)) {
                header("Location: /admin.php");
                exit;
            } else {
                echo "Ошибка удаления записи: " . $linkBase->error;
            }
        } else {
            echo "Ошибка выполнения запроса: " . $linkBase->error;
        }
    }
}