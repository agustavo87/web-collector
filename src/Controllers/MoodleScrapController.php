<?php

namespace AGustavo87\WebCollector\Controllers;

use AGustavo87\WebCollector\{App, Request};
use AGustavo87\WebCollector\Responses\{JSONResponse, RedirectResponse, ViewResponse};
use AGustavo87\WebCollector\Services\{DocumentManager, MoodleClient, Storage};
use AGustavo87\WebCollector\Services\HttpClient\Client as HTTPClient;

class MoodleScrapController extends Controller
{
    protected Storage $forumStore;
    protected Storage $studentStore;
    protected DocumentManager $DocumentManager;
    protected MoodleClient $moodle;
    protected array $defaults;

    public function __construct(Request $request, App $app)
    {
        parent::__construct($request, $app);
        $this->defaults = $app->config('moodle.defaults');
        $this->moodle = new MoodleClient(
            new DocumentManager(
                new Storage('pages'),
                new HTTPClient([
                    'http' => [
                        'method' => 'GET',
                        'user_agent' => 'Mozilla/5.0',
                        'follow_location' => 1,
                        'ignore_errors' => true,
                    ]
                ])
            ),
            $request->getParam('login_url', $this->defaults['login_url']),
            $request->getParam('in_session',  $app->session()->get('in_session')),
            $request->getParam('moodle_id', $app->session()->get('moodle_id')),
            $app->config('moodle.defaults')
        );
        $this->forumStore = new Storage('forum');
        $this->studentStore = new Storage('students');
    }
    
    public function authenticate(): JSONResponse
    {
        [
            'in_session'    => $in_session,
            'moodle_id'     => $moodle_id
        ] = $this->moodle->authenticate(
            $this->request->getParam('username'),
            $this->request->getParam('password')
        );
        $this->app->session()->set('in_session', $in_session);
        $this->app->session()->set('moodle_id', $moodle_id);
        return new JSONResponse([
            'in_session' => $in_session,
            'moodle_id'  => $moodle_id
        ], 200);
    }

    public function showAuthenticate(): ViewResponse
    {
        return new ViewResponse('authenticate', [
            'login_url'     => $this->request->getParam('login_url', $this->defaults['login_url']),
            'username'      => $this->request->getParam('username', $this->defaults['username']),
            'password'      => $this->request->getParam('password', $this->defaults['password']),
        ]);
    }

    public function showGrab()
    {
        $in_session = $this->request->getParam('in_session', $this->app->session()->get('in_session'));
        $moodle_id = $this->request->getParam('moodle_id', $this->app->session()->get('moodle_id'));
        if(!$in_session || !$moodle_id) {
            return new RedirectResponse('/moodle/authenticate');
        }
        return new ViewResponse('moodlegrab', [
            'url'            => $this->request->getParam('url', $this->defaults['home']),
            'in_session'     => $in_session,
            'moodle_id'      => $moodle_id,
        ]);
    }

    public function grab(): JSONResponse
    {
        [$page_uid, $response] =  $this->moodle->fetchAndStore(
            $this->request->getParam('url', '')
        );
        return new JSONResponse([
            'page_uid'   => $page_uid,
            'data' => [
                'cookies' => $response->cookies,
                'headers'=> $response->headers
            ]
        ], 200);
    }

    public function showStudentsInfo()
    {
        $course_id = $this->request->getParam('course_id', $this->defaults['course_id']);
        $in_session = $this->request->getParam('in_session', $this->app->session()->get('in_session'));
        $moodle_id = $this->request->getParam('moodle_id', $this->app->session()->get('moodle_id'));
        if(!$in_session || !$moodle_id) {
            return new RedirectResponse('/moodle/authenticate');
        }

        $students = $this->getStudentsInfo($course_id);

        $this->storeMoodleData(
            $this->studentStore,
            'students',
            $course_id,
            $students,
            ['id', 'role', 'name', 'email', 'profile_link']
        );

        return new ViewResponse('studentsinfo', [
            'course_id' => $course_id,
            'in_session' => $in_session,
            'moodle_id' => $moodle_id,
            'students' => $students
        ]);
    }

    public function showForumParticipations()
    {
        $course_id = $this->request->getParam('course_id', $this->defaults['course_id']);
        $in_session = $this->request->getParam('in_session', $this->app->session()->get('in_session'));
        $moodle_id = $this->request->getParam('moodle_id', $this->app->session()->get('moodle_id'));
        if(!$in_session || !$moodle_id) {
            return new RedirectResponse('/moodle/authenticate');
        }

        $results = [];

        $studentsInfo = $this->getStudentsInfo($course_id);
        foreach($studentsInfo as $student) {
            $user_id = $student['id'];
            $studentsInfoUrl = str_replace(['{USER_ID}', '{COURSE_ID}'], [$user_id, $course_id], $this->defaults['students_info_url']);
            $this->moodle->fetch($studentsInfoUrl);
            $studentPosts = $this->getUserForumPostsInfo(
                $this->moodle->getDocumentManager(),
                $user_id,
                $course_id
            );
            $results = array_merge($results, $studentPosts);
        }

        $this->storeMoodleData(
            $this->forumStore,
            'participations', 
            $course_id, 
            $results, 
            ['course_id', 'user_id', 'name', 'time', 'content', 'length_chars', 'length_words']
        );

        return new ViewResponse('forumparticipations', [
            'course_id' => $course_id,
            'in_session' => $in_session,
            'moodle_id' => $moodle_id,
            'participations' => $results
        ]);
    }

    protected function storeMoodleData(Storage $store, $tag, $course_id, $data, $columns)
    {
        $now = date('d_m_Y');
        $store->storeCSV(
            "{$tag}_course_{$course_id}_{$now}.csv",
            $data,
            $columns
        );
    }

    protected function getUserForumPostsInfo(DocumentManager $document, $user_id, $course_id)
    {
        $document->query("//article[contains(@class, 'forum-post-container')]");
        $results = [];
        $participation = [];
        foreach($document->result() as $node) {
            $participation['course_id'] = $course_id;
            $participation['user_id'] = $user_id;
            $participation['name'] = trim($document->query(".//address/a", $node )->result()[0]->nodeValue);
            $participation['time'] = trim($document->query(".//time", $node )->result()[0]->nodeValue);
            $participation['content'] = trim($document->query(".//div[@class='post-content-container']", $node )->result()[0]->nodeValue);
            $participation['length_chars'] = strlen($participation['content']);
            $participation['length_words'] = str_word_count($participation['content']);
            $results[] = $participation;
        }
        return $results;
    }

    protected function getStudentsInfo($course_id)
    {
        $url = $this->getCourseURL($course_id);
        $studentsData =  [];
        do {
            $this->moodle->fetch($url);
            [
                'students'  => $students,
                'link_next' => $url
            ] = $this->parseStudentsData($this->moodle->getDocumentManager());
            $studentsData = array_merge($studentsData, $students);
        } while ($url);
        return $studentsData;
    }

    public function parseStudentsData(DocumentManager $document)
    {
        $result_a = $document->query("//table[@id='participants']/tbody/tr[@class != 'emptyrow']/td[@class='cell c1']/a")
                           ->getElementsArray();

        $info_a = array_map(function($element) {
            return [
                'name' => $element['value'],
                'profile_link' => $element['attributes']['href']
            ];
        }, $result_a);

        $result_b = $document->query("//table[@id='participants']/tbody/tr[@class != 'emptyrow']/td[@class='cell c2']")
                    ->getElementsArray();
        $info_b = array_map(function($element) {
            return [
                'email' => $element['value'],
            ];
        }, $result_b);

        $result_c = $document->query("//table[@id='participants']/tbody/tr[@class != 'emptyrow']/td[@class='cell c3']")
                            ->getElementsArray();
        $info_c = array_map(function($element) {
            return [
                'role' => $element['value'],
            ];
        }, $result_c);

        $students = array_map(function($el_a, $el_b, $el_c) {
            $matches = [];
            $profile_link = $el_a['profile_link'];
            preg_match('/id=([0-9]+)&/', $profile_link, $matches);
            return [
                'name' => $el_a['name'],
                'profile_link' => $profile_link,
                'email' => $el_b['email'],
                'role'  => $el_c['role'],
                'id' => $matches[1],
            ];
        }, $info_a, $info_b, $info_c);

        $link_next = $document->query("//ul[@class='pagination mt-3']/li[last()]/a[@class='page-link']")
                    ->getElementsArray();

        $link_next = count($link_next) ? $link_next[0]['attributes']['href']: null;

        $link_next = $link_next != null
        ? ($link_next == '#' ? null : $link_next)
        : null;

        return [
            'students'  => $students,
            'link_next' => $link_next
        ];
    }

    public function getCourseURL($courseID)
    {
        return str_replace('{COURSE_ID}', $courseID, $this->defaults['course_url']);
    }
}