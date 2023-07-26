<?php
require __DIR__ . '/vendor/autoload.php';
//echo __DIR__ .'\credentials.json';exit;

/*if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}*/
ini_set('display_errors', true);
use Google\Client;
use Google\Service\Calendar;

/**
 * Returns an authorized API client.
 * @return Client the authorized client object
 */
function getClient()
{
    $client = new Client();
    $client->setApplicationName('Google Calendar API PHP Quickstart');
    $client->setScopes('https://www.googleapis.com/auth/calendar');//change calendar.events.readonly to calendar for insert
    $client->setAuthConfig(__DIR__ .'/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Calendar($client);

// Print the next 10 events on the user's calendar.
try{

    $calendarId = 'primary';
    $optParams = array(
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => "0000-01-01T09:50:42+00:00",//date('c'),display past event
    );
    $results = $service->events->listEvents($calendarId, $optParams);
    $events = $results->getItems();
//start insert event
// Refer to the PHP quickstart on how to setup the environment:
// https://developers.google.com/calendar/quickstart/php
// Change the scope to Google_Service_Calendar::CALENDAR and delete any stored
// credentials.

$event = new Google_Service_Calendar_Event(array(
    'summary' => 'Google I/O 2015',
    'location' => '800 Howard St., San Francisco, CA 94103',
    'description' => 'A chance to hear more about Google\'s developer products.',
    'start' => array(
      'dateTime' => '2022-07-06T17:00:00',
      'timeZone' => 'UTC',
    ),
    'end' => array(
      'dateTime' => '2022-07-06T18:00:00',
      'timeZone' => 'UTC',
    ),
    'recurrence' => array(
      'RRULE:FREQ=DAILY;COUNT=1'//daily repeat count
    ),
    'attendees' => array(
      array('email' => 'jayathanam1998@gmail.com'),
      array('email' => 'yeskayselva@gmail.com'),
    ),
    'reminders' => array(
      'useDefault' => FALSE,
      'overrides' => array(
        array('method' => 'email', 'minutes' => 24 * 60),
        array('method' => 'popup', 'minutes' => 10),
      ),
    ),
    'conferenceData' => [
        'createRequest' => [
            'requestId' => 'randomString'.time()
        ]
    ]
  ));
  //var_dump($event);
  $calendarId = 'primary';
$event = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));
  //var_dump($event);
  printf('Event created: %s\n',$event->htmlLink);//event created html link
  
//$eventId=$event->getId();
 //echo $eventId;
  //$service->events->delete('primary', 'ouovujus3g8ulpm9rauat2hboc');
  //end insert event
    if (empty($events)) {
        print "No upcoming events found.\n";
    } else {
        print "Upcoming events:\n";
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            printf("%s %s (%s)\n", $event->getSummary(),$event->getDescription(), $start);

           // $service->events->delete('primary', $event->getId());
           // var_dump($event);
        }
    }
}
catch(Exception $e) {
    // TODO(developer) - handle error appropriately
    echo 'Message: ' .$e->getMessage();
}
//echo date('c');display only future 
?>