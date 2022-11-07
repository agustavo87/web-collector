<?php
use AGustavo87\WebCollector\HTMLView as View;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/normalize.css">
    <link rel="stylesheet" href="/css/main.css">
    <title>Web Collector | <?= $title ?? 'Welcome' ?></title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="/js/main.js" defer></script>
    <script src="/js/json_tree.js"></script>
    <link href="/css/json_tree.css" rel="stylesheet" />
    <?= $head ?? null ?>
</head>
<body>
    <header class="container fluid">
        <div class="container">
            <div class="topbar">
                <div class="logo">
                    <a href="/">
                        <?= (new View('logo'))->build() ?>
                    </a>
                </div>
                <nav class="main-nav">
                    <a href="/grab" class="item" title="Grab a page for later analysis">Grab</a>
                    <a href="/analize" class="item" title="Analize a previously grabbed page">Analize</a>
                    <a href="/meta" class="item">Meta</a>
                    <a href="/scrap" class="item">Scrap</a>
                    <div class="dropdown">
                        <button class="dropdown-trigger">Moodle</button>
                        <div class="dropdown-content">
                            <a 
                                href="/moodle/authenticate" 
                                class="item"
                                title="Authenticate in Moodle to be able to grab pages"
                            >Authenticate</a>
                            <a 
                                href="/moodle/grab" 
                                class="item"
                                title="Grab a page once you have autentication details"
                            >Grab Page</a>
                            <a 
                                href="/moodle/studentsinfo" 
                                class="item"
                                title="Get data of enrolled students in a course"
                            >Students Info</a>
                            <a 
                                href="/moodle/forumparticipations" 
                                class="item"
                                title="Get data of participations of students in forums."
                            >Forum Participations</a>
                        </div>
                    </div>
                </nav>
            </div>   
        </div>
        <div class="bottom-bar">
            <div class="container main-title">
                <h2><?= $title ?? 'Web Collector' ?></h2>
            </div>
        </div>

    </header>
    <main class="container"> 
        <div class="main">
            <?= $main ?>
        </div>
    </main>
    <?= $foot ?? null ?>
</body>
</html>

