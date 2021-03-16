<?
session_start();
require_once('header.php');

if (!isset($_POST['afterDot'])) {
    $_POST['afterDot'] = null;
}
if (!isset($_POST['changeTime'])) {
    $_POST['changeTime'] = 0;
}
if (!isset($_POST['deleteReps'])) {
    $_POST['deleteReps'] = 1;
}
if (!isset($_POST['deleteShortPros'])) {
    $_POST['deleteShortPros'] = 1;
}
if (!isset($_POST['lowerCase'])) {
    $_POST['lowerCase'] = null;
}
?>
    <div class="left-bar">
        <input type="time" name="startTime" id="startTime" value="08:00">
        <input type="time" name="endTime" id="endTime" value="02:00"><br><br>

        <input type="checkbox" name="deleteReps"
               id="deleteReps" <?php echo (isset($_POST['deleteReps'])) ? 'checked' : ''; ?>>
        <label for="deleteReps">Удалять повторы</label><br>

        <input type="checkbox" name="deleteShortPros"
               id="deleteShortPros" <?php echo (isset($_POST['deleteShortPros'])) ? 'checked' : ''; ?>>
        <label for="deleteShortPros">Удалять программы менее 10 минут</label><br>

        <input type="checkbox" name="lowerCase"
               id="lowerCase" <?php echo (isset($_POST['lowerCase'])) ? 'checked' : ''; ?>>
        <label for="lowerCase">Сменить регистр</label><br>

        <input type="checkbox" name="afterDot"
               id="afterDot" <?php echo (isset($_POST['afterDot'])) ? 'checked' : ''; ?>>
        <label for="afterDot">Удалить часть передачи ПОСЛЕ точки или двоеточия</label><br><br>

        <input type="number" name="changeTime" id="changeTime" value="0">
        <label for="changeTime">Перевод времени</label><br><br>

        <div class="files"></div>
    </div>
    <div class="textarea-input">
        <textarea name="in" class="in padding-5" placeholder="Paste text, drop files in here!" autofocus></textarea>
    </div>
    <div class="textarea-output">
        <textarea name="out" class="out padding-5"></textarea>
    </div>

<? require_once('footer.php'); ?>