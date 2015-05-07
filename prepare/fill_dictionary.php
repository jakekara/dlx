<?php

/**
    Seed dictionary table with contents
    of dictionary file. Running through
    the artisan database seeder was
    phenomenally slow, so I made a
    utility for populating the database myself
**/


/**
    database and dictionary file variables
**/

$username = "user";
$password = "password";
$database = "database";
$servername = "localhost";
$dictPath = "/usr/share/dict/words";
$envPath = "../.env";
/**** start ****/

run($dictPath);

/**
    Get configuartion info
**/
function configure()
{
    global $username, $password, $database, $servername, $dictPath, $envPath;
    
    // if user has supplied a dictionary file, use that
    if(count($argv) == 2)
    {
        $dictPath = $argv[1];
    }
    else if (count($argv) > 2)
    {
        exit("Usage " . $argv[0] . " [/dictionary/file/path]");
    }
    
    // read env file
    if(!is_readable($envPath))
    {
        exit(".env file '" . $envPath . "' not readable\n");
    }
    
    $envFile = fopen($envPath, "r");

    $line = "";
    
    echo "Reading configuration file.\n";
    do
    {
        $line = fgets($envFile, 500);
        
        // if no equals sign, ignore line
        
        $middle = strpos($line, "=");
        if (middle === false)
        {
            continue;
        }
        
        $key = substr($line, 0, $middle);
        $value = substr($line, $middle + 1, strlen($line) - strlen($key) - 1);
        
        //printf ("Key: %s\tValue:%s\n", $key, $value);
        
        if ($key == "DICT_PATH")
        {
            $dictPath = str_replace("\n", "", $value);
        }
        else if ($key == "DB_HOST")
        {
            $servername = str_replace("\n", "", $value);
        }
        else if ($key == "DB_DATABASE")
        {
            $database = str_replace("\n", "", $value);
        }
        else if($key == "DB_USERNAME")
        {
            $username = str_replace("\n", "", $value);
        }
        
        else if ($key == "DB_PASSWORD")
        {
            $password = str_replace("\n", "", $value);
        }
        
    } while ($line !== false);
    
    fclose($envFile);
    
    echo "dictPath: " . $dictPath . "\n";
    echo "hostname: " . $servername . "\n"; 
    echo "database: " . $database . "\n";
    echo "username: " . $username . "\n";
    echo "password: " . "*\n"; 
    
    echo "Read configuration file.\n";
}


/**
    Insert a word
**/
function insertWord($word, $conn)
{
    $sql = "INSERT IGNORE INTO `dictionary` (word) values('" . $word . "')";
    
    if ($conn->query($sql))
    {
        return true;
    }
    
    return false;
}

/**
    Connect to database
**/
function connect()
{
    global $username, $servername, $database, $password;
    
    echo "Connecting to database\n";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected to database\n";
        return $conn;
    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage() . "\n";
        return null;
    }
    return null;
    
}

/**
    Run the main code
**/
function run()
{
    global $dictPath;
    
    // configure
    configure();

    // determine if file exists
    // and is readable
    if (!is_readable($dictPath))
    {
        exit("Cannot open file'" . $dictPath . "'.\n");
    }

    // connect to database
    $conn = connect();
    if ($conn === null)
    {
        echo "Failed to connect to database.";
        exit;
    }
    
    echo "Connected\n";
    
    // load contents of dictionary file
    $dictWords = file($dictPath);

    $wordCount = count($dictWords);
    if ($wordCount < 1)
    {
        echo "Error loading dictionary file. Ensure the DICT_PATH "
            . " specified in .env exists and contains a list"
            . " of dictionary words, each on its own line.\n";
        return;
    }

    echo "Loaded " . count($dictWords) . " dict words\n";

    $tooShort = 0;
    $badChars = 0;
    $added = 0;
    $percentDone = 5;
    $count = 0;

    echo "This could take a while.\n";

    // add each word to database if it is
    // four characters long or greater
    // and contains no punctuation
    foreach (array_reverse($dictWords) as $word)
    {   
        // status report
        $count ++;
        if (100 * $count / $wordCount > $percentDone)
        {
            echo $percentDone . "% done. Processed " . $count . " words.\n";
            $percentDone += 5;

        }
        // remove null terminator
        $wordLength = strlen($word);

        // check word length
        // must be 4 chars, plus newline
        if (strlen($word) < 5)
        {
            $tooShort++;
            continue;
        }

        // strip newline
        $word = substr($word, 0, $wordLength - 1);

        // ensure word is alpha-only
        if (!ctype_alpha($word))
        {
            $badChars++;
            continue;
        }

        // lowercase
        $word = strtolower($word);
        
        // insert the word
        insertWord ($word, $conn);

        $added ++; 
    }

    echo "Finished processing dictionary words.\n";
    echo "Found and ignored " . $tooShort . " words that were too short.\n";
    echo "Found and ignored " . $badChars . " words that contained non-alphabetic characters.\n";
    echo "Added " . $added . " words.\n";
}

