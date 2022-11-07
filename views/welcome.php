
<p class="head-message">Available Links</p>
<nav class="nav-list">
    <?php foreach ($routes as $path => $routeData): ?>
        <a href="<?= $path ?>" class="item"><?= $routeData['title'] ?></a>
    <?php endforeach; ?>
</nav>

<?php
use AGustavo87\WebCollector\HTMLView as View;

$body = ob_get_clean();
$view = new View('base');
$content = $view->build([
    'main' => $body,
    'title' => 'Welcome to Web Collector'
]);
echo $content;