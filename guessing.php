<?php
    session_start();

    define("RANDOM_NUMBER_MAXIMUM", 100);
    define("RANDOM_NUMBER_MINIMUM", 1);

    $user_submitted_a_guess = isset($_POST['guess']);
    $user_requested_a_reset = isset($_POST['reset']);
    $user_won = false;

    // Initialize the game on the first visit
    if (!isset($_SESSION['random_number']) || $user_requested_a_reset) {
       $_SESSION['random_number'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
        $_SESSION['attempts'] = 0;
    }

    

    if ($user_submitted_a_guess) {
        $user_guess = (int)$_POST['user_guess'];
        $_SESSION['attempts']++;
    
        $answer = $_SESSION['random_number']; // Get the random number from the session
        // echo $answer; echo the answe
        if ($user_guess < $answer) {
            $message = "Lower"; // User's guess is too low
        } elseif ($user_guess > $answer) {
            $message = "Higher"; // User's guess is too high
        } else {
            $message = "Correct! You guessed it in " . $_SESSION['attempts'] . " attempts.";
            $user_won = true;
            $_SESSION['random_number'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
        }
    }

    // Highscore handling
    if ($user_won) {
        if (!isset($_SESSION['highscores'])) {
            $_SESSION['highscores'] = array();
        }

        // Prompt the user for their name
        if (isset($_POST['player'])) {
            $player = $_POST['player'];
            $_SESSION['highscores'][] = array('name' => $player, 'attempts' => $_SESSION['attempts']);
            usort($_SESSION['highscores'], function ($a, $b) {
                return $a['attempts'] - $b['attempts'];
            });
            $_SESSION['highscores'] = array_slice($_SESSION['highscores'], 0, 3);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Number Guessing Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <div class="container">
        <h1>Guessing Game</h1>

        <form method="post">
            <label for="user_guess">Your Guess</label>
            <input id="user_guess" name="user_guess" autofocus>
            <div class="btn">
            <input type="submit"  id="guess" name="guess" id="guess" value="guess">
            <input type="submit" name="reset" id="reset" value="restart">

            </div>
        </form>
        <div class="display"  >
                <?php if (isset($message)) { ?>
                    <p><?php echo $message; ?></p>
                    <p>Number of attempts: <?php echo $_SESSION['attempts']; ?></p>
                <?php } ?>
        </div>

        <?php if ($user_won) { ?>
            <form method="post">
                <label for="player">Enter your name for the highscore board:</label>
                <input type="text" name="player" required>
                <button type="submit" > Submit Name </button>
            </form>
            <div class="display"  >
                <h2>Highscore Board:</h2>
                <ol>
                    <?php foreach ($_SESSION['highscores'] as $score) { ?>
                        <li><?php echo $score['name'] . ' - ' . $score['attempts'] . ' attempts'; ?></li>
                    <?php } ?>
                </ol>
            </div>
        <?php } ?>
    </div>
</body>
</html>
