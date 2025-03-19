<?php 
require_once('link.php');
if (!empty($_POST['title']) && !empty($_POST['link'])) {
    $title = $_POST['title'];
    $links = $_POST['link'];

    // Sanitize the inputs
    $title = htmlspecialchars(trim($title));
    $links = htmlspecialchars(trim($links));

    // Create a valid filename (and sanitize further)
    $filename = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', pathinfo($links, PATHINFO_FILENAME)))) . '.php';

    $filecontent = "<?php\n
                        require_once('link.php');\n
                        require_once('header.php');\n
                    ?>
                    <h1>" . $title . "</h1>
                    <?php
                    require_once('footer.php');\n?>";

    // Create the file
    if (file_put_contents($filename, $filecontent) !== false) {
        // Prepare the SQL statement
        $stmt = $linkBase->prepare("INSERT INTO menu (title, link, sort_order) 
SELECT ?, ?, (SELECT MAX(sort_order) + 1 FROM menu)");



        // Check if prepare was successful
        if ($stmt === false) {
            echo "Error preparing statement: " . $link->error(); // Use link_get->error
        } else {
            // Bind the parameters
            $stmt->bind_param("ss", $title, $filename); // Use $filename and bind the third parameter

            // Execute the statement
            if ($stmt->execute()) {
                header("Location: admin.php");
                exit;
            } else {
                echo 'Error adding menu item: ' . $stmt->error; // Use $stmt->error
            }

            // Close the statement
            $stmt->close();
        }
    } else {
        echo "Error creating file: " . error_get_last()['message']; // Print file creation error
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



    if (isset($_POST['delete_btn'])) {
        $deleteIds = array_filter($_POST['checkboxes'], function ($value) {
            return $value;
        });
    
        if (!empty($deleteIds)) {
            $deleteIdsStr = implode(',', array_keys($deleteIds));
    
            $query_select = "SELECT `title` FROM `menu` WHERE `id` IN ($deleteIdsStr)";
            $result_select = $linkBase->query($query_select);
    
            while ($row = $result_select->fetch_assoc()) {
                $title = $row['title'];
                $filename = strtolower(str_replace(' ', '_', $title)) . '.php';
    
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }
            $delete_query = "DELETE FROM `menu` WHERE `id` IN ($deleteIdsStr)";
            if (mysqli_query($linkBase, $delete_query)) {
                header("Location: /admin.php");
            } else {
                echo "ТИ СНОВА ОСИБСЯ КОГДА УДЯЛЯЛЬ ЗЯПИСЬ: " . mysqli_error($linkBase);
            }
        }
    }

?>