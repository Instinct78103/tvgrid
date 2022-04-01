<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /');
    exit;
}

require_once('php/define.php');
require_once('header.php');

$tables_ls = [
    'deleteall' => 'Удалить все',
    'deleteallexcept' => 'Удалить все кроме',
    'findreplace' => 'Найти и заменить',
    'realnames' => 'Имена собственные',
    'users' => 'Пользователь'
];

$rendered_list = [];

if ($_SESSION['user']) {
    $conn = new mysqli(SERVER, USER, PWORD);
    if ($conn->connect_error) {
        exit('Ошибка подключения к базе: ' . $conn->connect_error);
    }

    $conn->select_db(DB);

    $result = $conn->query('SHOW TABLES') or die($conn->error);

    $tables = $result->fetch_all();

    $rendered_list = array_map(fn ($item) => $item[0], $tables);
}

?>

<div class="left-bar">
    <ul class="tables">
        <?php
        echo join(array_map(function ($item) use ($tables_ls) {
            $class = isset($_GET['table']) &&  $_GET['table'] === $item ? ' class="active"' : '';
            return '<li' . $class . '><a href="?table=' . $item . '">' . $tables_ls[$item] . '</a></li>';
        }, $rendered_list));
        ?>
    </ul>
</div>

<div class="main">
    <?php

    $select = isset($_GET['table']) ? $_GET['table'] : null;

    if ($select && in_array($select, array_keys($tables_ls))) {

        $userID = $_SESSION['user'][0];

        $result = $conn->query("SELECT * FROM `$select` WHERE `userID`=$userID");

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $rows = array_map(function ($item) {
            unset($item['userID']);
            return $item;
        }, $rows);

        $th = array_keys($rows[0]);

    ?>


        <table>
            <!-- <tr>
                <?php //echo join(array_map(fn ($item) => "<th>$item</th>", $th)); 
                ?>
            </tr> -->
            <?php

            $html = '';

            foreach ($rows as $line) {
                $html .= '<tr>';

                foreach ($line as $key => $item) {
                    if ($key === 'id') {
                        continue;
                    } else {
                        $html .= '<td contenteditable="true" id="' . (isset($line['id']) ? $line['id'] : '')  . '">' . $item . '</td>';
                    }
                }

                $html .= '</tr>';
            }

            echo $html;

            ?>
        </table>


    <?php
    }

    $conn->close();
    ?>
</div>


<?php
require_once('footer.php');
?>