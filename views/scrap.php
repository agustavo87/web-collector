<form action="/scrap" method="GET" class="column">
    <div class="input-group">
        <label for="url">Url:</label>
        <input type="text" name="url" id="url" value="<?= $url ?>">
    </div>
    <div class="input-group">
        <label for="tag">Tag:</label>
        <input type="text" name="tag" id="tag" value="<?= $tag ?>">
    </div>
    <button type="submit">Scrap</button>
</form>
<div>
    <pre class="code-display">
        <?php print_r( $tags ) ?>
    </pre>
</div>

<?php
use AGustavo87\WebCollector\HTMLView as View;

$content = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $content,
    'title' => 'Scrap Tags'
]);
echo $content;