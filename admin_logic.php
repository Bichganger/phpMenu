<?php
require_once('link.php');



// Handle menu item updates (drag-and-drop reordering)
if (isset($_POST['reordered_ids'])) {
    $ids = explode(",", $_POST['reordered_ids']);

    for ($i = 0; $i < count($ids); $i++) {
        $id = intval($ids[$i]); // Sanitize the ID to be an integer
        $sort_order = $i + 1;

        // Use prepared statements to prevent SQL injection
        $query = "UPDATE menu SET sort_order = ? WHERE id = ?";
        $stmt = $link->prepare($query);
        $stmt->bind_param("ii", $sort_order, $id);

        if (!$stmt->execute()) {
            echo "Error updating sort order for ID $id: " . $stmt->error . "<br>";
            // Optionally, log the error to a file or database
        }
        $stmt->close(); // Close the statement after execution
    }
    header("Location: /admin.php"); // Redirect back to the admin page after reordering
    exit;
}

// Handle regular form submissions (title, link, delete, save)
if (isset($_POST['saveButton'])) { // Check for the submit button that's enabled
    // Loop through and process title and link updates for each menu item
    foreach ($_POST['title_get'] as $id => $title) {
        $link_get = $_POST['link_get'][$id]; // Get the corresponding link

        // Sanitize input before using
        $title = htmlspecialchars(trim($title)); // Trim whitespace and sanitize
        $link_get = htmlspecialchars(trim($link_get));

        // Update menu item in database using prepared statements
        $stmt = $link->prepare("UPDATE `menu` SET title = ?, link = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $link_get, $id);

        if (!$stmt->execute()) {
            echo "Error updating menu item with ID $id: " . $stmt->error . "<br>";
            // Optionally, log the error
        }
        $stmt->close();
    }

    // Redirect to admin page after processing all updates
    header("Location: admin.php");
    exit;
}
// Handle deletion
if (isset($_POST['delete_btn'])) {
    foreach ($_POST['checkboxes'] as $id => $value) {
        $id = intval($id); // Sanitize the ID
        $stmt = $link->prepare("DELETE FROM `menu` WHERE id = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            echo "Error deleting menu item with ID $id: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
    header("Location: /admin.php");
    exit;
}


// Handle new menu item creation (if you have a separate form for that)
if (!empty($_POST['new_title']) && !empty($_POST['new_link'])) { // Assuming 'new_title' and 'new_link' exist
    $title = $_POST['new_title'];
    $link = $_POST['new_link'];

    // Sanitize input
    $title = htmlspecialchars(trim($title));
    $link = htmlspecialchars(trim($link));

    // Create the PHP file
    $filename = pathinfo($link, PATHINFO_FILENAME);
    $filename = strtolower(str_replace(' ', '_', $filename)) . '.php';
    $filecontent = "<?php\n
require_once('link.php');\n
require_once('header.php');\n
?>
<h1>" . $title . "</h1>
<?php
require_once('footer.php');\n?>";

    if (file_put_contents($filename, $filecontent) !== false) {
        // Insert into the database (using prepared statements)
        $stmt = $link->prepare("INSERT INTO `menu` (title, link, sort_order) VALUES (?, ?, COALESCE((SELECT MAX(sort_order) + 1 FROM `menu`), 1))"); // Automatically set sort_order
        $stmt->bind_param("ss", $title, $filename);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit;
        } else {
            echo 'Ошибка при создании пункта меню: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'Ошибка при создании файла: ' . error_get_last()['message'];
    }
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
?>