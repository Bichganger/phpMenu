<?php 
require_once('link.php');

if(!empty($_POST['title'])&&!empty($_POST['link'])){
    $title=$_POST['title'];
    $link=$_POST['link'];

    $filename=pathinfo($link,PATHINFO_FILENAME);
    $filename=strtolower(str_replace(' ','_',$filename)).'.php';
    $filecontent="<?php\n
    require_once('link.php');\n
    require_once('header.php');\n
?>\n
<h1>".$title."</h1>\n
<?php\n
require_once('footer.php');
?>";
file_put_contents($filename,$filecontent);

    $stmt = $linkBase->prepare("INSERT INTO menu (title, link, sort_order) SELECT ?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM menu)");
    $stmt->bind_param("ss",$title,$filename);
    if($stmt->execute()){
        header("Location: /admin.php");
        exit;
    } else {
        echo "Ошибка при создании пункта меню: ".$stmt->error;
    }
    $stmt->close();
}

if(isset($_POST['deleteButton'])){
    $deleteIds=array_filter($_POST['checkboxes'],function($value){
        return $value;
    });
    if(!empty($deleteIds)){
        $deleteIdsStr=implode(',',array_keys($deleteIds));
        $query_select="SELECT title FROM `menu` WHERE id In ($deleteIdsStr)";
        $result_select=$linkBase->query($query_select);
        while($row=$result_select->fetch_assoc()){
            $title=$row['title'];
            $filename=strtolower(str_replace(' ','_',$title)).'.php';
            if(file_exists($filename)){
                unlink($filename);
            }
        }
        $delere_query="DELETE FROM `menu` Where id in ($deleteIdsStr)";
        if(mysqli_query($linkBase,$delere_query)){
            header("Location: admin.php");
        }
        else{
            echo "Ошибка удления записей: ".mysqli_error($linkBase);
        }
    }
}

if(isset($_POST['saveButton'])){
    $query_update="SELECT * FROM menu";
    $resulr_update=$linkBase->query($query_update);

    while($row=$resulr_update->fetch_assoc()){
        $id=$row['id'];
        $title_get=$row['title'];
        $link_get=$row['link'];
        if (isset($_POST['title_get'][$id])&&isset($_POST['link_get'][$id])){
            $title_ins=$_POST['title_get'][$id];
            $link_ins=$_POST['link_get'][$id];

            if($title_get!=$title_ins || $link_get!=$link_ins){
                $new_filename=strtolower(str_replace(' ','_',$title_ins)).'.php';
                $old_filename=strtolower(str_replace(' ','_',$title_get)).'.php';
                if(file_exists($old_filename)){
                    rename($old_filename,$new_filename);
                }
                $filecontent="<?php\n// Файл:$new_filename\nheader('Location:$link_ins');\n?>";
                file_put_contents($new_filename,$filecontent);

                $stmt=$linkBase->prepare("UPDATE `menu` SET `title` = ?, `link`=? WHERE id=?");
                $stmt->bind_param("ssi", $title_ins,$link_ins,$id);
                if($stmt->execute()){
                header('Location: admin.php');
                }
                else{
                    echo 'Ошибка при обновлении данных: '.$stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

if (isset($_POST['reordered_ids'])) {
    $ids = explode(",", $_POST['reordered_ids']);
    foreach ($ids as $i => $id) {
        $sort_order = $i + 1;
        $stmt = $linkBase->prepare("UPDATE menu SET sort_order = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $sort_order, $id);
            if ($stmt->execute()) {
                header('Location: admin.php');
                
            }
            else{
                echo "Ошибка обновления сортировки для ID $id: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    exit;
}
?>













