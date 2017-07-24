<?php

namespace App\Notifications;

use function array_key_exists;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;


class RequestReview extends Notification
{

    public $notification;
    public $client;

    /**
     * Create a new notification instance.
     * @param array $notification
     */
    public function __construct($notification)
    {
        $this->client = new Client();
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['slack'];
    }

    public function toSlack()
    {
        $notification = $this->notification;

        return (new SlackMessage)
            ->from("Dashi")
            ->image("http://icons.iconarchive.com/icons/thehoth/seo/256/seo-web-code-icon.png")
            ->success()
            ->content(":microscope: *{$notification['username']}* needs you to make a `Code Review` to this changes")
            ->attachment(function ($attachment) use ($notification) {

                $fields = [
                    "Repository" => $notification["repository"],
                    "User" => $notification['username']
                ];

                if (array_key_exists("changed_files", $notification)) {
                    $fields["File(s) changed"] = $notification["changed_files"];
                }

                $attachment->title($notification["title"], $notification["url"])
                    ->content(":sleuth_or_spy: Make sure everything is in order before approve the Pull Request")
                    ->fields($fields);
            });
    }

}