<footer class="text-center">
    <div class="footer-below" style="display:flex!important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="list-inline">
                        <li>
                            <a href="https://fr.linkedin.com/in/thomas-cardoso" class="btn-social btn-outline" target="_blank"><i class="fa fa-fw fa-linkedin"></i></a>
                        </li>
                        <li>
                            <a href="https://github.com/tcardo06" class="btn-social btn-outline" target="_blank"><i class="fa fa-fw fa-github"></i></a>
                        </li>
                        <?php
                        $is_admin = false;
                        if (isset($_SESSION['username']) && $_SESSION['username'] !== "Invité") {
                            $user_id = $_SESSION['user_id'];
                            $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
                            $stmt->bind_param('i', $user_id);
                            $stmt->execute();
                            $stmt->bind_result($is_admin);
                            $stmt->fetch();
                            $stmt->close();
                        }

                        $path_to_dashboard = 'admin/dashboard.php';
                        if (strpos($_SERVER['REQUEST_URI'], '/blog/') !== false) {
                            $path_to_dashboard = '../admin/dashboard.php';
                        } elseif (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
                            $path_to_dashboard = 'dashboard.php';
                        } elseif (strpos($_SERVER['REQUEST_URI'], '') !== false) {
                            $path_to_dashboard = 'admin/dashboard.php';
                        }

                        if ($is_admin) {
                            echo '<li>
                                    <a href="' . $path_to_dashboard . '" class="btn-social btn-outline"><i class="fa fa-fw fa-cogs"></i></a>
                                  </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
