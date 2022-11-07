<div 
    x-data="{
        form: {
            page_uid: '<?= $page_uid ?? '' ?>',
            xpath: '<?= $xpath ?? '' ?>'
        },
        elements: {},
        getLoginData() {
            postData('/xpath', this.form)
                .then((data) => {this.elements = data.elements; this.tree.loadData(this.elements); updateQueryParams(this.form)})
        },
        copy() {
                navigator.clipboard.writeText(JSON.stringify(this.elements))
        },
        tree: {}
    }"
    x-init="
        tree = jsonTree.create(elements, $refs.elements_display);
    "
>
    <form @submit.prevent="getLoginData" class="column">
        <div class="input-group">
            <label for="page_uid">Page UID:</label>
            <input x-model.lazy="form.page_uid" type="text" name="page_uid" id="page_uid">
        </div>
        <div class="input-group">
            <label for="xpath">XPath:</label>
            <input x-model="form.xpath" type="text" name="xpath" id="xpath">
        </div>
        <button type="submit">Get Data</button>
    </form>
    <div>
        <h3>Results</h3>
        <div class=" data-section">
            <button @click="copy" type="button" class="button copy-button">Copy</button>
            <div class="data-wrapper">
                <div x-ref="elements_display" class="data-data"></div>
            </div>
        </div>
    </div>
    <div>
        <h3>Page Preview</h3>
        <iframe x-bind:src="'/stored?uid=' + form.page_uid" frameborder="0" class="page-presentations"></iframe>
    </div>
</div>

<?php
use AGustavo87\WebCollector\HTMLView as View;

$body = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $body,
    'title' => 'Analize a page'
]);
echo $content;