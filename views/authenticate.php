<div 
    x-data="{
        formAuthentication:  {
            login_url: '<?= $login_url ?? '' ?>',
            username: '<?= $username ?? '' ?>',
            password: '<?= $password ?? '' ?>',
        },
        sessionData: {
            in_session: '',
            moodle_id: ''
        },
        authenticate() {
            params = {
                login_url: this.formAuthentication.login_url,
                username: this.formAuthentication.username
            }
            postData('/moodle/authenticate', this.formAuthentication)
                .then((data) => {this.sessionData = data; updateQueryParams(params)})
        },
        get isReady() {return !!this.sessionData.in_session.length && !!this.sessionData.moodle_id.length}
    }"
>
    <form class="column">
        <div class="input-group">
            <label for="url">Login Url:</label>
            <input type="text" x-model="formAuthentication.login_url" id="url">
        </div>
        <div class="input-group">
            <label for="username">username:</label>
            <input type="text" x-model="formAuthentication.username" name="username" id="username">
        </div>
        <div class="input-group">
            <label for="password">password:</label>
            <input type="password" x-model="formAuthentication.password"  name="password" id="password">
        </div>
        <button type="button" @click="authenticate">Authenticate</button>
    </form>

    <ul class="overflow-list">
        <li><strong>Logged Session:</strong> <span x-text="sessionData.in_session"></span> </li>
        <li><strong>Moodle ID:</strong> <span x-text="sessionData.moodle_id"></span> </li>
    </ul>
    <a 
        x-bind:href="!isReady ? 'javascript:void(0);' : '/moodle/grab?in_session=' + sessionData.in_session + '&moodle_id=' + sessionData.moodle_id"
        class="button"
        :class="{'disabled': !isReady}"
    >Grab a Moodle Page</a>
    <a 
        x-bind:href="!isReady ? 'javascript:void(0);' : '/moodle/studentsinfo?in_session=' + sessionData.in_session + '&moodle_id=' + sessionData.moodle_id"
        class="button"
        :class="{'disabled': !isReady}"
    >Get Students Information</a>
    <a 
        x-bind:href="!isReady ? 'javascript:void(0);' : '/moodle/forumparticipations?in_session=' + sessionData.in_session + '&moodle_id=' + sessionData.moodle_id"
        class="button"
        :class="{'disabled': !isReady}"
    >Get Students Forum Participation</a>
</div>

  
<?php
use AGustavo87\WebCollector\HTMLView as View;

$body = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $body,
    'title' => 'Authenticate in Moodle'
]);
echo $content;