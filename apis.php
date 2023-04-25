<?php
$data = json_decode(file_get_contents('php://input'), true);
$functionName = $data['functionName'];
$args = $data['args'];

if (function_exists($functionName)) {
  $result = call_user_func_array($functionName, $args);
  echo $result;
} else {
  echo 'Function not found.';
}
include './src/bd_config.php';
try {
    $conection = new PDO("mysql:host=".SERVIDOR_BD.":3306;dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch(PDOException $e) {
    exit("Connection error: " . $e->getMessage());
}

#add an api call to google places and catch errors
#separate the api returned data into an array using the json_decode function
#return the array to the calling function
function getPlaces($lat, $lng, $radius, $type, $key) {
    $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lng&radius=$radius&type=$type&key=$key";
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    if ($data['status'] == 'OK') {
        return $data;
    } else {
        return $data['status'];
    }
}

#add an api call to google search and catch errors
#i want to take the json and put the data into an array with clear names
#uses google api to get the website address if there is one
#return the array to the calling function
function getSearch($query, $key) {
    $url = "https://www.googleapis.com/customsearch/v1?key=$key&cx=017576662512468239146:omuauf_lfve&q=$query";
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    if ($data['searchInformation']['totalResults'] > 0) {
        $results = array();
        foreach ($data['items'] as $item) {
            $result = array();
            $result['title'] = $item['title'];
            $result['link'] = $item['link'];
            $result['snippet'] = $item['snippet'];
            $result['displayLink'] = $item['displayLink'];
            $result['htmlSnippet'] = $item['htmlSnippet'];
            $result['htmlTitle'] = $item['htmlTitle'];
            $result['cacheId'] = $item['cacheId'];
            $result['formattedUrl'] = $item['formattedUrl'];
            $result['htmlFormattedUrl'] = $item['htmlFormattedUrl'];
            $result['pagemap'] = $item['pagemap'];
            $result['website'] = getWebsite($item['link']);
            $results[] = $result;
        }
        return $results;
    } else {
        return $data['searchInformation']['totalResults'];
    }
}

#when i call the getSearch function i want to write the results into a pdo mysql database
#if the table doest exist create it (tbl_places)
#use the key as column name and the value as the data
#insert into database if unique
#return the array to the calling function
function getDatabase($results) {
    $db = new PDO('mysql:host=localhost;dbname=database;charset=utf8mb4', 'username', 'password');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8mb4'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER SET 'utf8mb4'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER_SET_CONNECTION = 'utf8mb4'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER_SET_RESULTS = 'utf8mb4'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER_SET_CLIENT = 'utf8mb4'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER_SET_DATABASE = 'utf8mb4'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER_SET_SERVER = 'utf8mb4'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET COLLATION_DATABASE = 'utf8mb4_unicode_ci'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET COLLATION_SERVER = 'utf8mb4_unicode_ci'");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET SQL_MODE = ''");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET GLOBAL sql_mode = ''");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET SESSION sql_mode = ''");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET sql_mode = ''");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET GLOBAL sql_mode = ''");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET SESSION sql_mode = ''");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET sql_mode = ''");
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET GLOBAL sql_mode = ''");


}


#i want to scrape the contents of a url and put it into a variable
#return the variable to the calling function
function getScrape($url) {
    $content=file_get_contents($url);
    $pattern = '/<script\b[^>]*>(.*?)<\/script>/s';
    $newString = preg_replace($pattern, '', $content);
    $pattern = '/<style\b[^>]*>(.*?)<\/style>/s';
    $newString = preg_replace($pattern, '', $newString);

    $html = getClean($newString);
    return $html;
}

#remove all opening and closing html tags from a variable except h,p,span,div,table,ul,ol,li
#return the variable to the calling function
function getClean($html) {
    $clean_text = strip_tags($html,'<h1><h2><h3><h4><h5><h6><p><span><div><table><ul><ol><li>');
    //$clean_text = strip_tags($clean_text, '<h1><h2><h3><h4><h5><h6><p><span><div><table><ul><ol><li>');
    
    return $clean_text;
}



#i want a complete function to take scraped html in a variable and send it to openai api for processing with a custom message prepended to the scraped html
#return the variable to the calling function
function getOpenAI($html, $key) {
    $url = "https://api.openai.com/v1/engines/davinci/completions";
    $data = array(
        'prompt' => "Please give me a summary of this content, it is from scraped html, so may contain garbage. Here is the scraped html: $html",
        'max_tokens' => 100,
        'temperature' => 0.9,
        'top_p' => 1,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
        'stop' => 'Please give me a summary of this content, it is from scraped html, so may contain garbage. Here is the scraped html:'
    );
    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),
        'Authorization: Bearer ' . $key
    ));
    $result = curl_exec($ch);
    $data = json_decode($result, true);
    return $data['choices'][0]['text'];
}



#create a sanitization function to remove anything but letters, numbers, spaces, operators, and punctuation
#remove any foreign characters
#return the variable to the calling function
function getSanitize($text) {
    $text = preg_replace('/[^A-Za-z0-9\-\+\*\/\=\(\)\[\]\{\}\.\,\:\;\!\?\s]/', '', $text);
    $text = preg_replace('/[^\x20-\x7E]/', '', $text);
    return $text;
}

#create a function that accepts pdf and turns it into text
#return the variable to the calling function
function getPDF($url) {
    $pdf = file_get_contents($url);
    $text = '';
    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseContent($pdf);
    $text = $pdf->getText();
    return $text;
}


##create the code for checking if a session exists called user_id
##if it does not exist redirect to index.php
##if it does exist continue and put the session into a variable called $user_id
##return the variable to the calling function
function getCheck() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    } else {
        $user_id = $_SESSION['user_id'];
        return $user_id;
    }
}











