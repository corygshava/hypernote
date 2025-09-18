<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <link rel="shortcut icon" href="lrvl icon.png" type="image/png">
    <link rel="stylesheet" href="_assets/BS4/css/bootstrap.min.css">
    <link rel="stylesheet" href="_assets/css/fa-all.css">
    <link rel="stylesheet" href="_assets/css/styles.css">
    <link rel="stylesheet" href="_assets/css/w3.css">
    <link rel="stylesheet" href="_assets/css/coryG_base.css">
</head>
<body>
    <div class="formguy spacy-md">
        <h2>register</h2>
        <form action="./register" method="post">
            @csrf
            <input type="text" name="name" id="name" placeholder="enter name 1">
            <input type="text" name="email" id="email" placeholder="enter email 2">
            <input type="text" name="password" id="password" placeholder="enter password 3">
            <button class="btn primary"><i class="fa fa-paper-plane"></i></button>
        </form>
    </div>
</body>
</html>