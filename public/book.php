<?php

require '../connec.php';
$pdo = new \PDO(DSN, USER, PASS);
$res = 0;
$errors = [];
$arrays = [];
$arrays =  range('A', 'Z');
$payments = [];

if (empty($_SERVER['QUERY_STRING'])){
    $querys = "SELECT * FROM bride";
    $statement = $pdo->query($querys);
    $payments = $statement->fetchAll(PDO::FETCH_ASSOC);
} else {
    $querys = "SELECT * FROM bride WHERE `name` LIKE :caract " ;
    $statement = $pdo->prepare($querys);
    $statement->bindValue(':caract', $_SERVER['QUERY_STRING'][1] . '%', PDO::PARAM_STR_CHAR);
    $statement->execute();
    $payments = $statement->fetchAll(PDO::FETCH_ASSOC);
}


if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $debt = array_map('trim', $_POST);

    // Validate data
    if (empty($debt['name'])) {
        $errors[] = 'The name is required';
    }
    $maxdebtname = 255;
    if ((strlen($debt['name'])) > $maxdebtname) {
        $errors[] = 'The name must be shorter than ' . $maxdebtname;
    }
    if (empty($debt['payment'])) {
        $errors[] = 'The payment is required';
    }
    if (!filter_var($debt['payment'], FILTER_VALIDATE_INT)) {
        $errors[] = 'The payment must be a number';
    }

    if ($debt['name'] === 'Eliott Ness') {
        $errors[] = 'This man is untouchable';
    }

    if ($debt['payment'] < 0) {
        $errors[] = 'Don\'t try to trick me';
    }

    // Save the recipe
    if (empty($errors)) {
        $query = 'INSERT INTO bride (`name`, payment) VALUES (:name, :payment)';
        $statement = $pdo->prepare($query);
        $statement->bindValue(':name', $debt['name'], PDO::PARAM_STR);
        $statement->bindValue(':payment', $debt['payment'], PDO::PARAM_INT);
        $statement->execute();
        header('Location: book.php');

    }


}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/book.css">
    <title>Checkpoint PHP 1</title>
</head>

<body>

    <?php include 'header.php'; ?>

    <main class="container">
        <div class="array">
            <?php foreach ($arrays as $array) { ?>

                <div><a href="book.php?=<?=$array?>"><?= $array;?></a></div>
            <?php } ?>
        </div>
        <section class="desktop">
            <img src="image/whisky.png" alt="a whisky glass" class="whisky" />
            <img src="image/empty_whisky.png" alt="an empty whisky glass" class="empty-whisky" />

            <div class="pages">
                <div class="page leftpage">
                    <div class= "letter"><?= $_SERVER['QUERY_STRING'][1];?></div>
                    Add a bribe
                    <ul>
                        <?php foreach ($errors as $error) { ?>
                            <li><?= $error ?></li>
                        <?php } ?>
                    </ul>
                    <form action="" method="POST">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name">
                        <label for="payment">Payment</label>
                        <input type="number" name="payment" id="payment">
                        <button>Pay !</button>
                    </form>
                </div>

                <div class="page rightpage">
                    <table>
                        <?php foreach ($payments as $payment) { ?>
                            <tr class="tablePayment">
                                <td><?= $payment['name'] ?></td>
                                <td><?= $payment['payment'] ?></td>
                                <?php $res += $payment['payment'] ?>
                            </tr>
                        <?php } ?>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td><?= $res ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <img src="image/inkpen.png" alt="an ink pen" class="inkpen" />
        </section>
    </main>
</body>

</html>