</div>
<?php

switch ($_SERVER['DOCUMENT_URI']) {
    case '/index.php':
        echo '<script src="js/script.js"></script>';
        break;
    case '/settings.php':
        echo '<script src="js/settings.js"></script>';
        break;
    default:
        break;
}
?>
</body>

</html>