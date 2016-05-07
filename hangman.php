#!/usr/bin/php
<?php

    function cleanTerminal()
    {
        print chr(27) . "[2J" . chr(27) . "[;H";
        return true;
    }

    function welcome()
    {
        echo 'Hello! Welcome to "PHP Hangman"!' . "\n";
        return true;
    }

    function bingo()
    {
        echo "You Win";
        exit(0);
    }

    function getSecretWord()
    {
        $error = true;
        while ($error)
        {
            echo "Type a word : ";
            $secretWord = readline();
            if (!empty($secretWord) && preg_match("/^[a-zA-Z-]*$/", $secretWord))
            {
                return strtolower($secretWord);
                $error = false;
            }
            else
            {
                echo "Veuillez entrer un mot, composé uniquement de lettres\n";
            }
        }
    }

    function setSecretWord($players)
    {
        $error = true;
        while ($error)
        {
            echo "$players[0] type a word : ";
            $secretWord = readline();
            if (!empty($secretWord) && preg_match("/^[a-zA-Z-]*$/", $secretWord))
            {
                return strtolower($secretWord);
                $error = false;
            }
            else
            {
                echo "Veuillez entrer un mot, composé uniquement de lettres\n";
            }
        }
    }

    function gameStart()
    {
        echo "Game Start !\n";
        return true;
    }

    function compare($secretWord, &$maskedWord, $letter)
    {
        $match = false;
        for ($i = 0 ; $i < strlen($secretWord) ; $i++)
        {
            if ($letter === $secretWord{$i})
            {
                $maskedWord[$i] = $secretWord[$i];
                $match = true;
            }
        }
        if ($match)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function showInfos($maskedWord, $errors, $tried, $turn, $multi)
    {
        echo "Guess the word : $maskedWord                           Already tried : ";

        if ($multi)
        {
            unset($tried[0]);
            $tried = array_reverse($tried);
            // var_dump($tried);

            foreach ($tried[$turn] as  $try)
            {
                echo "$try, ";
            }
        }
        else
        {
            foreach ($tried[0] as $try)
            {
                echo "$try, ";
            }
        }
        echo "\n";
        echo "                                                       Errors : $errors[0]  on $errors[1]\n";
    }

    function checkWin($secretWord, $maskedWord)
    {
        if ($secretWord === $maskedWord)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function getLetter(&$errors, &$tried, $players = null, &$turn, $multi)
    {
            if ($players !== null)
            {
                if ($turn >= count($players) + 1)
                // if ($turn >= count($players))
                {
                    $turn = 1;
                }
                // var_dump($players);
                // var_dump($turn);
                echo "$players[$turn] give a letter : ";
                $turn++;
            }
            else
            {
                echo "Give a letter : ";
            }

            $letter = readline();
            if (preg_match("/^[a-zA-Z-]?$/", $letter))
            {
                if ($multi)
                {
                    if (!in_array($letter, $tried[$turn - 1]))
                    {
                        array_push($tried[$turn - 1], $letter);
                    }
                }
                else
                {
                    if (!in_array($letter, $tried[$turn - 2]))
                    {
                        array_push($tried[$turn - 2], $letter);
                    }
                }

                return strtolower($letter);
            }
            else
            {
                echo "Vous êtes censez entrer UNE lettre. Dommage, ca vous coute 1 essais.\n";
            }
    }

    function youLose($secretWord)
    {
        echo "\033[31mYou lose !\033[0m Sorry bro'...\nThe word was \"$secretWord\"\nThanks for playing, bybye !\n";
        exit();
    }

    function youWin($secretWord, $winner = null)
    {
        echo "Yeaaaaah, the word was indeed \"$secretWord\"\n";
        if ($winner !== null)
        {
            echo "\033[32m$winner Win !!!\033[0m Thank's for playing. See you 'round !\n";
        }
        else
        {
            echo "\033[32mYou Win !!!\033[0m Thank's for playing. See you 'round !\n";
        }
        return true;
    }

    function guess($secretWord, $maskedWord, $errors, $players = null, &$winner = null)
    {
        $loose = true;
        $i = 0;
        $tried = [[]];
        $turn = 2;
        $multi = false;

        for ($i =0 ; $i < count($players) ; $i++)
        {
            array_push($tried, []);
        }

        if ($players !== null)
        {
            $multi = true;
        }

        while ($loose && $errors[0] < $errors[1])
        {
                showInfos($maskedWord, $errors, $tried, $turn - 2, $multi);
                $letter = getLetter($errors, $tried, $players, $turn, $multi);
                if (compare($secretWord, $maskedWord, $letter))
                {
                    $i++;
                }
                else
                {
                    $errors[0]++;
                }

                if (checkWin($secretWord, $maskedWord))
                {
                    $loose = false;
                }

                if ($multi)
                {
                    echoDeco(40);
                }
                else
                {
                    cleanTerminal();
                }
        }

        if ($loose)
        {
            return true;
        }
        else
        {
            $winner = $players[$turn-1];
            return false;
        }
    }

    function playSolo()
    {
        cleanTerminal();
        welcome();

        $secretWord = getSecretWord();
        $maskedWord = str_repeat("_", strlen($secretWord));
        $errors = [0, 10];

        cleanTerminal();
        gameStart();
        $loose = guess($secretWord, $maskedWord, $errors);

        if ($loose)
        {
            youLose($secretWord);
        }
        else
        {
            youWin($secretWord);
        }
    }

    function playMulti($players)
    {
        $winner = "";
        cleanTerminal();
        echoDeco(40);
        welcome();

        $secretWord = setSecretWord($players);
        $maskedWord = str_repeat("_", strlen($secretWord));
        $errors = [0, 10];

        cleanTerminal();
        welcomeAgainMulti($players, $maskedWord);
        echoDeco(40);
        gameStart();

        $loose = guess($secretWord, $maskedWord, $errors, $players, $winner);

        if ($loose)
        {
            youLose($secretWord);
        }
        else
        {
            youWin($secretWord, $winner);
        }
    }

    function echoDeco($nb)
    {
        echo "\n" . str_repeat("=", $nb) . "\n";
    }

    function welcomeAgainMulti(&$players, $maskedWord)
    {
        echoDeco(40);
        welcome();
        echo "$players[0] type a word :  $maskedWord\n";
        unset($players[0]);

    }

    function proceedOptions($options)
    {
        if (preg_match("/^[a-zA-Z, ]*$/", $players = array_shift($options)))
        {
            $players = preg_replace("/[\s]+/", " ", $players);
            $players = explode( ",", $players);
            $players = array_map("trim", $players);

            if (count($players) < 2)
            {
                echo "\033[31mERREUR\033[0m : Vous devez être au moins 2 pour jouer en multijoueur.";
                exit();
            }
            return $players;
        }
        else
        {
            echo "\033[31mERREUR\033[0m : Ca vous embetterais de respecter les consignes ?... \n";
            exit();
        }
    }

    $options = getopt("m:", ["multiplayer:"]);
    if (!empty($options))
    {
        $players =  proceedOptions($options);
        playMulti($players);
    }
    else
    {
        playSolo();
    }
?>