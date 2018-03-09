<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <style>
            table { 
                border-spacing: 0;
                border-collapse: collapse;
            }

            table td, table th {
                border: 1px solid #ccc;
                padding: 5px;
            }

            table th {
                background: #eee;
            }
            
            form {
                margin-bottom: 10px;
            }
        </style>

        <h1>Список дел на сегодня</h1>
        <div style="float: left">
            <form method="POST">
                <input type="text" name="description" placeholder="Описание задачи" value="" />
                <input type="submit" name="save" value="Добавить" />
            </form>
        </div>
        <div style="float: left; margin-left: 20px;">
            <form method="POST">
                <label for="sort">Сортировать по:</label>
                <select name="sort_by">
                    <option value="date_added">Дате добавления</option>
                    <option value="is_done">Статусу</option>
                    <option value="description">Описанию</option>
                </select>
                <input type="submit" name="sort" value="Отсортировать" />
            </form>
        </div>
        <div style="clear: both"></div>

        <table>
            <tr>
                <th>Описание задачи</th>
                <th>Дата добавления</th>
                <th>Статус</th>
                <th></th>
            </tr>
        <?php
        $pdo = new PDO("mysql:host=localhost;dbname=global", "aabramov", "neto1499");
        $char = "SET names 'utf8'";
        $pdo->query($char);
        $sql_sort = "";
        if (!empty($_POST)){
            if (!empty($_POST["description"])) {
                $descritp = strip_tags($_POST["description"]);
                $sql = "INSERT INTO `tasks`(`description`, `is_done`, `date_added`) VALUES ('$descritp',0,NOW())";
                $pdo->query($sql);
                header("Location: ".$_SERVER['REQUEST_URI']);
            }
            if (!empty($_POST["sort"])){
                $sort = $_POST["sort_by"];
                $sql_sort = "SELECT `id`, `description`, `is_done`, `date_added` FROM `tasks` ORDER BY ". $sort;
                $pdo->query($sql_sort);
            }
        }
        if (!empty($_GET)){
            if ($_GET["action"] == "done"){
                $id = $_GET["id"];
                $sql = "UPDATE `tasks` SET is_done=100 WHERE id='$id'";
                $pdo->query($sql);
                header("Location: /less4.2/index.php");
            }
            if ($_GET["action"] == "delete"){
                $id = $_GET["id"];
                $sql = "DELETE FROM `tasks` WHERE `tasks`.`id` = '$id'";
                $pdo->query($sql);
                header("Location: /less4.2/index.php");
            }
        }
        if (strlen($sql_sort)){
            $sql = $sql_sort;
        } else {
            $sql = "SELECT * FROM `tasks`";
        }
        foreach ($pdo->query($sql) as $row) {
            echo "<tr><td>". $row['description'] . "</td>";
            echo "<td>". $row['date_added'] . "</td>";
            if (!$row['is_done']){
                echo "<td><span style='color: orange;'>В процессе</span></td>";
            } else {
                echo "<td><span style='color: green;'>Выполнено</span></td>";
            }
            echo "<td><a href='?id=". $row['id']. "&action=done'>Выполнить</a>". "  ". "<a href='?id=". $row['id']. "&action=delete'>Удалить</a></td></tr>";
        }
        ?>
    </body>
</html>
