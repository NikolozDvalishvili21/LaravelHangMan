<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PlayHangman extends Command
{
    protected $signature = 'play:hangman';
    protected $description = 'Play a game of Hangman';

    private $hangmanLogFile = 'hangman.log';

    public function handle()
    {
        do {
            $this->info("Welcome to Hangman!");

            // Step 1: ჩაფიქრებული სიტყვა
            $hiddenWord = $this->ask("Enter a word for the second player to guess:");
            $hiddenWord = strtoupper($hiddenWord); // აფერქეისი

            $wordToGuess = str_repeat('_', strlen($hiddenWord)); // ჩაფიქრებული სიტყვის გამოტანა დამალულად
            $guessedLetters = [];
            $attempts = 6; // ცდები

            // Game loop
            while ($attempts > 0 && $wordToGuess !== $hiddenWord) {
                $this->info("Current word: $wordToGuess");
                $this->info("Attempts remaining: $attempts");
                $this->info("Guessed letters: " . implode(', ', $guessedLetters));

                // Step 2: ასოს გამოცნობა
                $guess = $this->ask("Guess a letter: ");
                $guess = strtoupper($guess); // აფერქეისი

                if (in_array($guess, $guessedLetters)) {
                    $this->warn("You've already guessed that letter.");
                    continue;
                }

                // ასოს შენახვა
                $guessedLetters[] = $guess;

                // სწორია თუ არა
                if (strpos($hiddenWord, $guess) !== false) {
                    // ასოს გამოტანა
                    for ($i = 0; $i < strlen($hiddenWord); $i++) {
                        if ($hiddenWord[$i] === $guess) {
                            $wordToGuess[$i] = $guess;
                        }
                    }
                } else {
                    $attempts--; // ცდების მოკლება
                }
            }

            // შედეგი
            if ($wordToGuess === $hiddenWord) {
                $this->info("Congratulations! You've guessed the word: $hiddenWord");
            } else {
                $this->error("Game over! The word was: $hiddenWord");
            }

            // ლოგში შეტანა
            $this->logGameResult($hiddenWord, $guessedLetters);

            // კითხვა
            $restart = $this->ask("Do you want to play again? (yes/no)");
        } while (strtolower($restart) === 'yes');
    }

    private function logGameResult($word, $guessedLetters)
    {
        $logMessage = "Word: $word | Guessed Letters: " . implode(', ', $guessedLetters) . PHP_EOL;
        file_put_contents($this->hangmanLogFile, $logMessage, FILE_APPEND);
    }
}
