<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /');
    exit;
}

require_once('php/define.php');
require_once('header.php');
?>

    <div class="left-bar">
        <?php
        if ($_SESSION['user']) {
            $conn = new mysqli(SERVER, USER, PWORD, DB);
            if ($conn->connect_error) {
                exit('Ошибка подключения к базе: ' . $conn->connect_error);
            }

            $sql = 'SHOW TABLES';

            $result = $conn->query($sql) or die($conn->error);

            $tables = $result->fetch_all();

            $tables_list = array_map(fn($item) => $item[0], $tables);
            

            echo '<ul class="tables">';
            echo join(array_map(fn($item) => '<li id="'. $item . '">' . $item . '</li>', $tables_list));
            echo '</ul>';

            $conn->close();
        }
        ?>
    </div>

    <div class="main">

    </div>


<?php
require_once('footer.php');
?>