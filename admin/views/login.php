<body class="bg-gray justify-content-center vh-100">
    <div id="overlay"><div class="loader"></div></div>
    <div class="container">
        <div class="row justify-content-center vh-90 align-items-center">
            <div class="col-sm-6 col-md-4">
                <form id="form" class="form-horizontal" role="form" action="module=login&action=<?php echo $display; ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div id="login" class="card pt-4 pb-4 ps-5 pe-5 shadow-sm">
                        <div id="logo" class="text-center mb-4">
                            <img src="assets/images/logo-admin.png" alt="Logo">
                        </div>
                        <div class="alert-container">
                            <div class="alert alert-success alert-dismissible fade show" role="alert"></div>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert"></div>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"></div>
                        </div>
                        <?php if($display == 'reset'){ ?>
                            <p><?php echo $adminContext->texts['PASSWORD_ENTER_EMAIL']; ?></p>
                            <div class="mb-3">
                                <label class="form-label"><?php echo $adminContext->texts['EMAIL']; ?></label>
                                <input class="form-control" type="text" value="" name="email">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="module=login"><i class="fa-solid fa-fw fa-arrow-left"></i> <?php echo $adminContext->texts['LOGIN']; ?></a>
                                <button class="btn btn-secondary" type="submit" value="" name="reset"><i class="fa-solid fa-fw fa-sync"></i> <?php echo $adminContext->texts['NEW_PASSWORD']; ?></button>
                            </div>
                            <?php
                        } else {
                            if(defined('PMS_DEMO') && PMS_DEMO == 1) echo '<div class="alert alert-info text-center">DEMO &nbsp;&nbsp; <i class="fa fa-fw fa-user"></i> <i>admin</i>&nbsp; | &nbsp;<i class="fa fa-fw fa-lock"></i> <i>admin123</i></div>'; ?>
                            
                            <div class="mb-3">
                                <label class="form-label"><?php echo $adminContext->texts['USERNAME']; ?></label>
                                <input class="form-control" type="text" value="" name="username">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo $adminContext->texts['PASSWORD']; ?></label>

                                <div class="input-group">
                                    <input type="password" class="form-control" value="" name="password">
                                    <span class="input-group-text toggle-password"></span>
                                </div>
                            </div>
                            <div class="mb-3 d-grid">
                                <button class="btn btn-secondary" type="submit" value="" name="login"><i class="fa-solid fa-fw fa-power-off"></i> <?php echo $adminContext->texts['LOGIN']; ?></button>
                            </div>
                            <div class="mb-3">
                                <i class="fa-solid fa-lock fa-fw"></i> <a href="module=login&display=reset"><?php echo $adminContext->texts['FORGOTTEN_PASSWORD']; ?></a>
                            </div>
                            <?php
                        } ?>
                    </div>
                    <footer class="text-center pb-3 pt-3 text-muted">
                        &copy; Solutions CMS 2024 - All rights reserved
                    </footer>
                </form>
            </div>
        </div>
    </div>
</body>
</html>