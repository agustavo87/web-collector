<form action="/meta" method="GET" class="column">
    <div class="input-group">
        <label for="url">Url:</label>
        <input type="text" name="url" id="url" value="<?= $url ?>">
    </div>
    <button type="submit">Meta</button>
</form>
<div>
    <pre class="code-display">
Headers
<?= json_encode($data['headers'], JSON_PRETTY_PRINT) ?>

Cookies
<?= json_encode($data['cookies'], JSON_PRETTY_PRINT ) ?>
    </pre>
</div>
<?php
use AGustavo87\WebCollector\HTMLView as View;

$content = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $content,
    'title' => 'Meta Information'
]);
echo $content;