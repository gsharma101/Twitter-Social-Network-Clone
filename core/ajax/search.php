<?php
include_once('../init.php');
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = $getFromU->CheckInput($_POST['search']);
    $result = $getFromU->search($search);
    if (!empty($result)) {
        echo '<div class="nav-right-down-wrap">';
        foreach ($result as $user) {
            echo '<ul> 
        <li>
            <div class="nav-right-down-inner">
              <div class="nav-right-down-left">
                  <a href="' . $user->username . '"><img src="' . BASE_URL . $user->profileImage . '"></a>
              </div>
              <div class="nav-right-down-right">
                  <div class="nav-right-down-right-headline">
                      <a href="' . $user->username . '">' . $user->screenName . '</a><span>' . $user->username . '</span>
                  </div>
                  <div class="nav-right-down-right-body">
                   
                  </div>
              </div>
          </div> 
       </li> ';
        }

        echo ' </ul>
      </div> ';
    }
}
