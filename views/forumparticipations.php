<form action="/moodle/forumparticipations" method="GET" class="column">
    <div class="input-group">
        <label for="course_id">Course ID:</label>
        <input type="text" name="course_id" id="course_id" value="<?= $course_id ?>">
    </div>
    <div class="input-group">
        <label for="in_session">Session ID:</label>
        <input type="text" name="in_session" id="in_session" value="<?= $in_session ?>">
    </div>
    <div class="input-group">
        <label for="moodle_id">Moodle ID:</label>
        <input type="text" name="moodle_id" id="moodle_id" value="<?= $moodle_id ?>">
    </div>
    <button type="submit">Get</button>
</form>
<div>
    <pre class="code-display">
<?php print_r($participations) ?>
    </pre>
</div>
<?php
use AGustavo87\WebCollector\HTMLView as View;

$content = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $content,
    'title' => 'Forum Participations'
]);
echo $content;