<?php
require_once('link.php');

require_once('link.php');

if (isset($_POST['addButton']) && !empty($_POST['title']) && !empty($_POST['link'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $links = htmlspecialchars(trim($_POST['link']));
    $filename = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', pathinfo($links, PATHINFO_FILENAME)))) . '.php';

    $filecontent = "<?php\nrequire_once('link.php');\nrequire_once('header.php');\n?>\n<h1>$title</h1>\n<?php\nrequire_once('footer.php');\n?>";

    if (file_put_contents($filename, $filecontent) === false) {
        echo "Ошибка создания файла: " . error_get_last()['message'];
        exit;
    }

    $stmt = $linkBase->prepare("INSERT INTO menu (title, link, sort_order) SELECT ?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM menu)");
    if ($stmt) {
        $stmt->bind_param("ss", $title, $filename);
        if ($stmt->execute()) {
            header("Location: /admin.php");
            exit;
        } else {
            echo 'Ошибка добавления элемента меню: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Ошибка подготовки запроса: " . $linkBase->error;
    }
}

if (isset($_POST['reordered_ids'])) {
    $ids = explode(",", $_POST['reordered_ids']);
    foreach ($ids as $i => $id) {
        $sort_order = $i + 1;
        $stmt = $linkBase->prepare("UPDATE menu SET sort_order = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $sort_order, $id);
            if (!$stmt->execute()) {
                echo "Ошибка обновления сортировки для ID $id: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    header("Location: /admin.php");
    exit;
}

if (isset($_POST['saveButton'])) {
    foreach ($_POST['title_get'] as $id => $title) {
        $link_get = htmlspecialchars(trim($_POST['link_get'][$id]));
        $title = htmlspecialchars(trim($title));

        $stmt = $linkBase->prepare("UPDATE menu SET title = ?, link = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssi", $title, $link_get, $id);
            if (!$stmt->execute()) {
                echo "Ошибка обновления элемента ID $id: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    header("Location: /admin.php");
    exit;
}

if (isset($_POST['deleteButton']) && !empty($_POST['checkboxes'])) {
    $deleteIds = array_filter($_POST['checkboxes'], function ($value) {
        return !empty($value);
    });

    if ($deleteIds) {
        $deleteIdsStr = implode(',', array_map('intval', $deleteIds));
        $query_select = "SELECT title FROM menu WHERE id IN ($deleteIdsStr)";

        if ($result_select = $linkBase->query($query_select)) {
            while ($row = $result_select->fetch_assoc()) {
                $filename = strtolower(str_replace(' ', '_', htmlspecialchars(trim($row["title"])))) . '.php';
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
