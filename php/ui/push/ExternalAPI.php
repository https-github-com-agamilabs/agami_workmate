<?php

class TelegramBotAPI {
    private $apiUrl;
    private $botToken = "8225078226:AAElZpXjq8StxkiIolI2p85FVM966GGUwV4";
    private $chatId = "1293704299";

    public function __construct($botToken = "8225078226:AAElZpXjq8StxkiIolI2p85FVM966GGUwV4", $chatId = "1293704299") {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->apiUrl = "https://api.telegram.org/bot" . $this->botToken . "/";
    }

    public function sendMessage($message) {
        $url = $this->apiUrl . "sendMessage";
        $data = [
            'chat_id' => $this->chatId,
            'text' => $message
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
            throw new Exception("Error sending message to Telegram API");
        }

        return json_decode($result, true);
    }

    public function sendSimpleMessage($message) {
        $botToken = $this->botToken;
        // Build the query string
        $data = [
            'chat_id' => $this->chatId,
            'text' => $message
        ];
        // Construct the URL
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage?" . http_build_query($data);
        // Send the request
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public function loginLog($response){
        $firstname = $_SESSION['wm_firstname'];
        $lastname = $_SESSION['wm_lastname'];
        $fullname = $firstname." ".$lastname;
        $photo_url = $_SESSION['wm_photo_url'];
        $email = $_SESSION['wm_email'];

        $this->sendSimpleMessage("Login Alert
Name:$fullname
Status: ".($response['error']?"Failed":"Success")."
Message: ".$response['message']."
Email: $email
        ");
    }

    public function timerLog($response){
        $firstname = $_SESSION['wm_firstname'];
        $lastname = $_SESSION['wm_lastname'];
        $fullname = $firstname." ".$lastname;
        $photo_url = $_SESSION['wm_photo_url'];
        $email = $_SESSION['wm_email'];

        $this->sendSimpleMessage("Working Time Alert
Name: $fullname
Status: ".($response['error']?"Failed":"Success")."
Message: ".$response['message']."
Email: $email
        ");
    }
}