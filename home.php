<?php
require_once("main.php");
require_once("header.html");
require_once("sidebar.php");


echo '<div class="display-container">
                    <table width="500">';

$r = new Recipe();
foreach ($r->newest() as $nr) {
    $nr->load();
    $user = $nr->User->load();
    $category = $nr->Category->load();
    echo '<tr><td>';
    echo '<img src="pictures/' . $nr->mainPic . '"></img>';
    echo '<ul><li><a href="view.php?id=' . $nr->id . '">' . $nr->name . '</a></li>';
    echo '<li><a href="user.php?id=' . $user->id .'">' . $user->userName . '</a></li>';
    echo '<li><a href="user.php?id=' . $category->id .'">' . $category->name . '</a></li></ul></td></tr>';
}

echo '              </table>
                </div>';

require_once("footer.html");
?>