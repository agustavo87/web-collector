<form action="/grab" method="GET" class="column">
    <div class="input-group">
        <label for="url">Url:</label>
        <input type="text" name="url" id="url" value="<?= $url ?? '' ?>">
    </div>
    <button type="submit">Grab a Page</button>
</form>
<div>
    <div class="uid">
        <div class="tag">
            Page UID:
        </div>
        <div class="value">
           <span class="content">
            <?= $page_uid ?> 
           </span>  
        </div>
        <a href="/analize?page_uid=<?= $page_uid ?>" class="button">Analize</a>
    </div>
    <script>
        const serverData =  <?= json_encode($data, JSON_PRETTY_PRINT) ?>
    </script>
    <h3>Response Data</h3>
    <div
        x-data="{
            responseData:serverData,
            copy() {
                navigator.clipboard.writeText(JSON.stringify(this.responseData))
            }
        }"
        x-init="
            jsonTree.create(responseData, $refs.data);
        "
        class="data-section"
    >
         <button @click="copy" type="button" class="button copy-button">Copy</button>
        <div  class="data-wrapper">
            <div x-ref="data" class="data-data"></div>
        </div>
    </div>
    <h3>Page Preview</h3>
    <iframe id="pagePresentation" src="/stored?uid=<?= $page_uid ?>" frameborder="0" class="page-presentations"></iframe>
</div>


<?php
use AGustavo87\WebCollector\HTMLView as View;

$body = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $body,
    'title' => 'Grab a page for later analysis'
]);
echo $content;