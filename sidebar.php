<?php
require_once("main.php");

$c = new Category();

echo '<div class="body-outer">
            <div class="body-container">
                <div class="sidebar-container">
                    <ul>';

foreach($c->name() as $n) {
    echo '<li><a href="list.php?id=' . $n->id . '">' . $n->name . '</a></li>';
}                
              
echo '</ul></div>';                

?>