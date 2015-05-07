<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\DictionaryWord;

class DictionarySeeder extends Seeder {
    
    /**
        Seed dictionary table with contents
        of dictionary file
    **/
    
    
    public function run()
    {
        
        // load contents of dictionary file
        $dictWords = file(env('DICT_PATH'));

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
        foreach ($dictWords as $word)
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
            
            DictionaryWord::firstOrCreate(array(
                'word' => $word
            ));
            $added ++; 
        }
        
        echo "Finished processing dictionary words.\n";
        echo "Found and ignored " . $tooShort . " words that were too short.\n";
        echo "Found and ignored " . $badChars . " words that contained non-alphabetic characters.\n";
        echo "Added " . $added . " words.\n";
    }
}