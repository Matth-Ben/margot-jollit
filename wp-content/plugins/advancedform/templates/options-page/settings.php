<?php
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;

/**
 * get values of a particular spreadsheet(by Id and range).
 */
function getValues($spreadsheetId = '1K0EUM4ZtQ0dx4CVpfF_L0eM0H_yrwIXOfPKTHCoPmFs', $range = 1)
{   
    /* Load pre-authorized user credentials from the environment.
        TODO(developer) - See https://developers.google.com/identity for
        guides on implementing OAuth2 for your application. */
    global $client;
    echo '<pre>' . var_export($client, true) . '</pre>';

    // $client->useApplicationDefaultCredentials();
    $client->setAuthConfig(__DIR__ . '/../../credentials.json'); // Le fichier JSON des identifiants OAuth
    $client->addScope(Google\Service\Drive::DRIVE);
    $client->setAccessToken($_GET['code']);
    $service = new Google_Service_Sheets($client);
    $result = $service->spreadsheets_values->get($spreadsheetId, $range);
    
    try {
        $numRows = $result->getValues() != null ? count($result->getValues()) : 0;
        printf("%d rows retrieved.", $numRows);
        return $result;
    }
    catch(Exception $e) {
        // TODO(developer) - handle error appropriately
        echo 'Message: ' .$e->getMessage();
    }
}

?>

<form method="POST" action="options.php">
    <?php
        do_settings_sections('af-settings');
        settings_fields('af-settings');
        submit_button();
    ?>
</form>
