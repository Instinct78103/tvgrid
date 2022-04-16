<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /');
    exit;
}

require_once('php/define.php');
require_once('header.php');

$userID = $_SESSION['user']['id'];

$list = [
    [
        'name' => 'deleteall',
        'slug' => 'Удалить все',
        'query' => isset($_GET['table'])
            ? "SELECT `id`, `item` FROM {$_GET['table']}  
            WHERE `UserID` = {$userID} ORDER BY `item` ASC"
            : '',
    ],
    [
        'name' => 'deleteallexcept',
        'slug' => 'Удалить все кроме',
        'query' => isset($_GET['table'])
            ? "SELECT `id`, `item` FROM {$_GET['table']}
            WHERE `UserID` = {$userID} ORDER BY `item` ASC"
            : '',
    ],
    [
        'name' => 'findreplace',
        'slug' => 'Найти и заменить',
        'query' => isset($_GET['table'])
            ? "SELECT `id`, `find_what`, `replace_with` FROM {$_GET['table']}
            WHERE `UserID` = {$userID} ORDER BY `find_what` ASC"
            : '',
    ],
    [
        'name' => 'realnames',
        'slug' => 'Имена собственные',
        'query' => isset($_GET['table'])
            ? "SELECT `id`, `item` FROM {$_GET['table']}
            WHERE `UserID` = {$userID} ORDER BY `item` ASC"
            : '',
    ],
    [
        'name' => 'users',
        'slug' => 'Пользователь',
        'query' => isset($_GET['table'])
            ? "SELECT `id`, `email` FROM {$_GET['table']}
            WHERE `id` = {$userID}"
            : '',
    ],
];

?>

<div class="sidebar">
    <ul class="tables">
        <?php
        echo join(array_map(function ($item) {
            $class = isset($_GET['table']) && $_GET['table'] === $item['name'] ? ' class="active"' : '';
            return '<li' . $class . '><a href="?table=' . $item['name'] . '">' . $item['slug'] . '</a></li>';
        }, $list));
        ?>
    </ul>
</div>

<div class="main">
    <?php

    if (isset($_GET['table'])) {
        $select = array_filter($list, fn ($item) => $item['name'] === $_GET['table']);
        $select = array_shift($select);

        if ($select) {
            $conn = new mysqli(SERVER, USER, PWORD, DB);
            if ($conn->connect_error) {
                exit('Ошибка подключения к базе: ' . $conn->connect_error);
            }

            $result = $conn->query($select['query']) or die($conn->error);
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $conn->close();

            if (count($data)) {
                $th = array_keys($data[0]);

                $content = '<table>';

                foreach ($data as $row) {
                    $content .= '<tr>';

                    foreach ($row as $key => $item) {
                        if ($key !== 'id') {
                            if ($_GET['table'] === 'users') {
                                $content .= '<td><input disabled type="text" value="' . htmlspecialchars($item) . '"></td><td><input type="text" disabled placeholder="********"></td>';
                            } else {
                                $content .= '<td><input id="' . $row['id'] . '" type="text" value="' . htmlspecialchars($item) . '"></td>';
                            }
                        }
                    }

                    $content .= '</tr>';
                }

                $content .= '</table>';

                echo $content;
            }
        }
    }

    ?>
</div>

<?php
require_once('footer.php');
