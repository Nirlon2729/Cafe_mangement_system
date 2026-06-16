<?php
// File to store marquee text
$marqueeFile = "marquee.txt";

// Load existing text
$marqueeText = "";
if (file_exists($marqueeFile)) {
    $marqueeText = file_get_contents($marqueeFile);
}

// Save marquee text
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $text = isset($_POST['marquee_text']) ? $_POST['marquee_text'] : "";
    $fp = fopen($marqueeFile, "w");
    fwrite($fp, $text);
    fclose($fp);
    $marqueeText = $text;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Marquee Text</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        header {
            background: #000;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 700px;
            margin: 30px auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            min-height: 120px;
        }
        .btn {
            padding: 10px 16px;
            margin: 5px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .format-btn { background: #007bff; color: white; font-weight: bold; }
        .emoji-btn { background: #f1f1f1; }
        .save-btn { background: #28a745; color: white; font-weight: bold; width: 100%; margin-top: 15px; }
        .preview {
            margin-top: 25px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-align: center;
        }
        marquee { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
<header><h2>⚙️ Manage Marquee Text</h2>
<a href="admin.php">Home</a>
</header>

<div class="container">
    <form method="post">
        <h2>✍️ Edit Message</h2>
        <textarea id="marquee_text" name="marquee_text"><?php echo htmlspecialchars($marqueeText); ?></textarea>

        <div>
            <button type="button" class="btn format-btn" onclick="formatText('b')"><b>B</b></button>
            <button type="button" class="btn format-btn" onclick="formatText('i')"><i>I</i></button>
            <button type="button" class="btn format-btn" onclick="formatText('u')"><u>U</u></button>
        </div>

        <div>
            <button type="button" class="btn emoji-btn" onclick="insertEmoji('🎉')">🎉</button>
            <button type="button" class="btn emoji-btn" onclick="insertEmoji('🔥')">🔥</button>
            <button type="button" class="btn emoji-btn" onclick="insertEmoji('⭐')">⭐</button>
            <button type="button" class="btn emoji-btn" onclick="insertEmoji('🥳')">🥳</button>
            <button type="button" class="btn emoji-btn" onclick="insertEmoji('💯')">💯</button>
        </div>

        <button type="submit" name="save" class="btn save-btn">💾 Save Message</button>
    </form>

    <div class="preview">
        <strong>Preview:</strong><br>
        <marquee><?php echo $marqueeText; ?></marquee>
    </div>
</div>

<script>
function formatText(tag) {
    let textarea = document.getElementById("marquee_text");
    let start = textarea.selectionStart;
    let end = textarea.selectionEnd;
    let selected = textarea.value.substring(start, end);
    if (selected.length > 0) {
        let before = textarea.value.substring(0, start);
        let after = textarea.value.substring(end);
        textarea.value = before + "<" + tag + ">" + selected + "</" + tag + ">" + after;
    }
}

function insertEmoji(emoji) {
    let textarea = document.getElementById("marquee_text");
    let start = textarea.selectionStart;
    let end = textarea.selectionEnd;
    let before = textarea.value.substring(0, start);
    let after = textarea.value.substring(end);
    textarea.value = before + emoji + after;
}
</script>
</body>
</html>
