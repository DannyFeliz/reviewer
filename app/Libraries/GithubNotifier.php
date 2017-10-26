<?php

namespace App\Libraries;

use App\Libraries\Parser\GithubParser;
use App\Libraries\Slack\SlackAttachment;
use App\Libraries\Slack\SlackNotifier;
use App\SlackToken;

class GithubNotifier
{
    private $parser;

    /**
     * GithubNotifier constructor.
     *
     * @param $notification
     * @param mixed $request
     */
    public function __construct($request)
    {
        $this->parser = new GithubParser(json_decode($request->toArray()['payload'], true));
        $this->run();
    }

    public function run()
    {
        // We need to check if the action key exists because Github send us a request to verify
        // if the given endpoint exists, otherwise is an event that has been triggered
        if (!$this->parser->isAnActionRequest()) {
            return;
        }

        if (!$this->parser->isASupportedActionRequest()) {
            $actionsList = implode("', '", $this->parser->getSupportedActionRequest());

            echo "Only '{$actionsList}' actions are supported at this moment.\n";

            return;
        }

        if (!$this->parser->parse()) {
            echo 'We werent able to recognize this event :(';

            return;
        }

        $this->notify($this->parser->getSuscribers(), $this->parser->getAttachment());
    }

    /**
     * Dispatch the corresponding notification
     *
     * @param array $suscribers
     * @param mixed $attachment
     */
    public function notify(array $suscribers, SlackAttachment $attachment)
    {
        foreach ($suscribers as $suscriber) {
            $slackToken = SlackToken::where('github_username', $suscriber)->first();
            if ($slackToken) {
                $notifier = new SlackNotifier($slackToken);
                $notifier->send($attachment);
            }
        }
    }
}