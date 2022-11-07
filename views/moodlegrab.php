
<div 
    x-data="{
        moodleGrab: {
            url: '<?= $url ?? '' ?>',
            in_session: '<?= $in_session ?? '' ?>',
            moodle_id: '<?= $moodle_id ?? '' ?>',
        },
        grabData: {
            page_uid: '',
            data: {}
        },
        grab() {
            postData('/moodle/grab', this.moodleGrab)
                .then((data) => {
                    this.grabData = data
                    this.tree.loadData(this.grabData.data)
                    updateQueryParams(this.moodleGrab)
                })
        },
        copy() {
            navigator.clipboard.writeText(JSON.stringify(this.grabData.data))
        },
        tree: {}
    }"
    x-init="
        tree = jsonTree.create(grabData.data, $refs.data);
    "
>
    <form class="column">   
        <div class="input-group">
            <label for="url">URL:</label>
            <input type="text" x-model="moodleGrab.url" name="url" id="url">
        </div>
        <div class="input-group">
            <label for="in_session">in_session:</label>
            <input type="text" x-model="moodleGrab.in_session" name="in_session" id="in_session">
        </div>
        <div class="input-group">
            <label for="moodle_id">moodle_id:</label>
            <input type="text" x-model="moodleGrab.moodle_id" name="moodle_id" id="moodle_id">
        </div>
        <button type="button" @click="grab">Grab</button>
        <div class="input-group uid">
            <label for="page_uid">Page UID:</label>
            <input class=" value content" type="text" x-model="grabData.page_uid" name="page_uid" id="page_uid" disabled>
            <a 
                x-bind:href="!grabData.page_uid.length > 0 ? 'javascript:void(0)' : '/analize?page_uid=' + grabData.page_uid" 
                :class="{'disabled': !grabData.page_uid.length > 0}"
                class="button"
            >Analize</a>
        </div>
    </form>
    <h3>Response Data:</h3>
    <div
        class="data-section"
    >
         <button @click="copy" type="button" class="button copy-button">Copy</button>
        <div  class="data-wrapper">
            <div x-ref="data" class="data-data"></div>
        </div>
    </div>
    <div>
        <h3>Page Preview</h3>
        <iframe x-bind:src="'/stored?uid=' + grabData.page_uid" frameborder="0" class="page-presentations"></iframe>
    </div>
</div>
  
<?php
use AGustavo87\WebCollector\HTMLView as View;

$body = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $body,
    'title' => 'Grab a page from Moodle'
]);
echo $content;