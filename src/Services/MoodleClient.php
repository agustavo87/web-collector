<?php

namespace AGustavo87\WebCollector\Services;

class MoodleClient
{
    protected DocumentManager $documentManager;

    protected string $login_url;
    protected string $in_session;
    protected string $moodle_id;

    protected string $loginTokenXpath;
    protected string $moodleSessionCookieName;
    protected string $moodleIdCookieName;
    protected string $userAgent;

    public function __construct(
        DocumentManager $documentManager,
        $login_url = null,
        $in_session = null,
        $moodle_id = null,
        array $config,
    ) {
        $this->login_url = $login_url;
        $this->documentManager = $documentManager;
        $this->in_session = $in_session;
        $this->moodle_id = $moodle_id;
        $this->loginTokenXpath = $config['login_token_xpath'];
        $this->moodleSessionCookieName = $config['moodle_session_cookie_name'];
        $this->moodleIdCookieName = $config['moodle_id_cookie_name'];
        $this->userAgent = $config['user_agent'];

        if($this->in_session != null && $this->moodle_id != null) {
            $this->documentManager->setContext(
                stream_context_create($this->getContext())
            );
        }
    }

    public function getLoginAttemptRequirements($login_url = null)
    {
        $login_url = $login_url ?? $this->login_url;
        $out_session = $this->documentManager
                             ->setContext([
                                'http' =>[
                                    'method' => 'GET',
                                    'user_agent' => $this->userAgent,
                                    'follow_location' => 1,
                                    'ignore_errors' => true,
                                    'header'  => [
                                        "Accept: */*",
                                    ]
                                ]
                            ])
                             ->getResponseData($this->login_url)['cookies'][$this->moodleSessionCookieName][0]['value'];
    
        $login_token = $this->documentManager
                                  ->query($this->loginTokenXpath)
                                  ->result()->item(0)->attributes
                                  ->getNamedItem('value')->nodeValue;

        return [
            'login_url' => $login_url,
            'login_token' => $login_token,
            'out_session' => $out_session
        ];
    }

    /**
     * Authenticate in moodle
     *
     * @param string $username
     * @param string $password
     * @return array - ['in_session' => x, 'moodle_id' => x]
     */
    public function authenticate($username, $password)
    {
        [
            'login_token'   => $login_token,
            'out_session'   => $out_session
        ] = $this->getLoginAttemptRequirements();


        $postdata = http_build_query([
                'username' => $username,
                'password' => $password,
                'rememberusername' => 1,
                'logintoken' =>  $login_token
        ]);

         $opts = [
            'http' =>[
                'method' => 'POST',
                'user_agent' => $this->userAgent,
                'follow_location' => 0,
                'ignore_errors' => true,
                'header'  => [
                    "Accept: */*",
                    "Content-Type: application/x-www-form-urlencoded",
                    "Cookie: ". $this->moodleSessionCookieName ."=$out_session"
                ],
                'content' => $postdata
            ]
        ];


        $response = $this->documentManager->setContext($opts)
                                          ->getResponseData($this->login_url);

        $this->in_session = $response['cookies'][$this->moodleSessionCookieName][0]['value'];
        $this->moodle_id = $response['cookies'][$this->moodleIdCookieName][1]['value'];
        
        return [
            'in_session' => $this->in_session,
            'moodle_id' => $this->moodle_id
        ];
    }

    public function fetchAndStore($url)
    {
        return $this->documentManager->fetchAndStorePage($url, $this->getContext());
    }

     /**
     * Returns 'body', 'cookies', and 'headers' of the response of a url.
     *
     * @param string $url
     * @return array
     */
    public function fetch($url): array
    {
        return $this->documentManager->getResponseData($url);
    }

    public function getDocumentManager(): DocumentManager
    {
        return $this->documentManager;
    }

    public function getContext()
    {
        return [
            'http' =>[
                'method' => 'GET',
                'user_agent' => $this->userAgent,
                'follow_location' => 1,
                'ignore_errors' => true,
                'header'  => [
                    "Accept: */*",
                    "Cookie: ".$this->moodleIdCookieName."=$this->moodle_id; ".$this->moodleSessionCookieName."=$this->in_session"
                ]
            ]
        ];
    }
}
