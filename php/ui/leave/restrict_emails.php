<?php

function is_email_allowed($to)
{
    try {
        $allowed_emails = array('shmazumder23@gmail.com', 'khanam.toslima@gmail.com', 'hanif@cu.ac.bd', 'info@agamilabs.com');
        $allowed_domains = array('@agamilabs.com');

        if (!isset($to) || strlen($to) === 0) {
            throw new \Exception("Recipient not found", 1);
        }

        if (in_array($to, $allowed_emails)) {
            return true;
        }

        $match = 0;

        for ($y = 0; $y < count($allowed_domains); $y++) {
            // code...

            if ((strripos($to, $allowed_domains[$y]) + strlen($allowed_domains[$y])) === strlen($to)) {
                $match = 1;
                break;
            }
        }

        if ($match != 1) {
            throw new \Exception("Email [$to] is not allowed.", 1);
        }
        return true;
    } catch (\Exception $e) {
        // $restrict_response['error'] = true;
        // $restrict_response['message'] = $e->getMessage();
        //echo json_encode($restrict_response);
        return false;
    }
}
