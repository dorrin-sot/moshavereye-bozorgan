<?php
$question = $msg = $en_name = $fa_name = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST["question"];
    $en_name = $_POST["person"];
    $fa_name = json_decode(file_get_contents('people.json'), true)[$en_name];
    if (!str_starts_with($question, 'آیا') || !(str_ends_with($question, '?') || str_ends_with($question, '؟'))) {
        $msg = 'سوال درستی پرسیده نشده!';
    } else {
        $hash = hash("sha256", $question . $fa_name);
        $msgIndex = intval($hash[0]) % 16;

        $msgsFile = fopen('messages.txt', 'r');
        $lineNum = 0;
        while (!feof($msgsFile)) {
            $msg = fgets($msgsFile);
            if ($lineNum == $msgIndex) break;
            $lineNum = $lineNum + 1;
        }
        fclose($msgsFile);
    }
}

if ($en_name == "") {
    $persons = json_decode(file_get_contents('people.json'), true);
    $en_name = array_keys($persons)[array_rand(array_keys($persons))];
    $fa_name = $persons[$en_name];
}
if ($question == '') {
    $msg = 'سوال خود را بپرس!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styles/default.css">
    <title>مشاوره بزرگان</title>
</head>
<body>
<p id="copyright">تهیه شده برای درس کارگاه کامپیوتر،دانشکده کامییوتر، دانشگاه صنعتی شریف</p>
<div id="wrapper">
    <?php
    if ($question != "") {
        echo
            '<div id="title">
                <span id="label">پرسش:</span>
                <span id="question">' . $question . '</span>
            </div>';
    }
    ?>
    <div id="container">
        <div id="message">
            <p><?php echo $msg ?></p>
        </div>
        <div id="person">
            <div id="person">
                <img src="images/people/<?php echo "$en_name.jpg" ?>"/>
                <p id="person-name"><?php echo $fa_name ?></p>
            </div>
        </div>
    </div>
    <div id="new-q">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            سوال
            <input type="text" name="question" value="<?php echo $question ?>" maxlength="150" placeholder="..."/>
            را از
            <select name="person">
                <?php
                $persons = json_decode(file_get_contents('people.json'), true);

                foreach ($persons as $key => $value) {
                    echo '<option value='
                        . $key .
                        ($en_name == $key ? ' selected ' : '') . '>' . $value .
                        '</option>';
                }
                ?>
            </select>
            <input type="submit" value="بپرس"/>
        </form>
    </div>
</div>
</body>
</html>