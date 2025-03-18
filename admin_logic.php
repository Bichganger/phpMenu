<?php 
require_once('link.php');

if (isset($_POST['reordered_ids'])) {
    $ids = explode(",", $_POST['reordered_ids']);
    foreach ($ids as $i => $id) {
        $sort_order = $i + 1;
        $stmt = $link->prepare("UPDATE menu SET sort_order = ? WHERE id = ?");

        // $stmt->bind_param("ii", $sort_order, $id);

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

        $stmt = $link->prepare("UPDATE menu SET title = ?, link = ? WHERE id = ?");
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
    $deleteIds = $_POST['checkboxes'] ?? [];
    $deleteIds = array_filter($deleteIds);
    
    if ($deleteIds) {
        $deleteIdsStr = implode(',', array_map('intval', $deleteIds));
        $query_select = "SELECT title FROM menu WHERE id IN ($deleteIdsStr)";
        $result_select = $link->query($query_select);
        
        while ($row = $result_select->fetch_assoc()) {
            $title = $row["title"];
            $filename = strtolower(str_replace(' ', '_', $title)) . '.php';
            if (file_exists($filename)) {
                unlink($filename);
            }
        }

        $link->query("DELETE FROM menu WHERE id IN ($deleteIdsStr)");
        header("Location: /admin.php");
        exit;
    }
}
?>