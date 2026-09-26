<?php
    $msg = isset($msg) ? $msg : "an error occured";
?>

<div class="spacy-lg w3-center">
    <div class="flow center">
        <span class="h3">Error</span>
        <p><?=$msg?></p>
        <button class="mybtn primary" onclick="window.location.reload()">reload page</button>
    </div>
</div>
