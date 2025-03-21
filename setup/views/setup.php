<body class="bg-gray d-flex justify-content-center vh-100">
    <div id="overlay"><div class="loader"></div></div>
    <div class="container">
        <div class="row justify-content-center vh-100 mt-5 align-items-center">
            <div class="col-sm-8 col-md-8">
                <form id="form" class="form-horizontal" role="form" action="module=setup&action=install" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div id="setup" class="card pt-4 pb-4 ps-5 pe-5 shadow-sm">
                        
                        <div class="mb-4 d-flex">
                            <div>
                                <img src="assets/images/logo-admin.png" alt="Logo" class="me-5 mt-4">
                            </div>
                            <div class="mt-4">
                                <h1>Welcome to Solutions CMS</h1>
                                <p>Fill fields with your information. It will take only a few seconds. You can always modify these parameters later.</p>
                            </div>
                        </div>

                        <div class="alert-container">
                            <div class="alert alert-success alert-dismissible fade show" role="alert"></div>
                            <div class="alert alert-info alert-dismissible fade show" role="alert"></div>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"></div>
                        </div>

                        <?php
                        if(!$installed){ ?>
                            
                            <legend>General</legend>

                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Site title</label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="text" value="<?php echo $config_tmp['pms_site_title']; ?>" name="site_title">
                                    <div class="field-notice text-danger" rel="site_title"></div>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Email</label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="text" value="<?php echo $config_tmp['pms_email']; ?>" name="email">
                                    <div class="field-notice text-danger" rel="email"></div>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Username <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="text" value="<?php echo $user_data['login']; ?>" name="user">
                                    <div class="field-notice text-danger" rel="user"></div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Password</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="password" class="form-control pr-password" value="" name="password" placeholder=">= 8 caracters">
                                        <span class="input-group-text toggle-password"></span>
                                    </div>
                                    <div class="field-notice text-danger" rel="password"></div>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" type="password" value="" name="password2" placeholder="Confirm password">
                                </div>
                            </div>
                            
                            <legend>Database</legend>
                        
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_name']; ?>" name="db_name" id="db_name">
                                    <div class="field-notice text-danger" rel="db_name"></div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Host <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_host']; ?>" name="db_host">
                                    <div class="field-notice text-danger" rel="db_host"></div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Port <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_port']; ?>" name="db_port">
                                    <div class="field-notice text-danger" rel="db_port"></div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">User <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_user']; ?>" name="db_user" id="db_user">
                                    <div class="field-notice text-danger" rel="db_user"></div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label">Password <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-8">
                                    <input class="form-control" type="password" value="<?php echo $config_tmp['pms_db_pass']; ?>" name="db_pass">
                                    <div class="field-notice text-danger" rel="db_pass"></div>
                                </div>
                            </div>

                            <div class="mb-3 mt-4 d-grid">
                                <button class="btn btn-secondary" type="submit" value="" name="install"><i class="fa-solid fa-fw fa-rocket"></i> Install</button>
                            </div>

                            <?php 
                        }else{ ?>
                        
                            <br><a class="btn btn-secondary" href="module=login">Log in</a>

                            <?php
                        } ?>
                    </div>
                    
                    <footer class="text-center pb-3 pt-3 text-muted">
                        &copy; Solutions CMS 2024 - All rights reserved
                    </footer>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(function(){
            $('#db_name').bind('blur keyup', function(){
                $('#db_user').val($(this).val());
            });
            <?php
            foreach($field_notice as $field => $notice){ ?>

                $('.field-notice[rel="<?php echo $field; ?>"]').html('<?php echo addslashes($notice); ?>').fadeIn('slow').parent().addClass('error').find('.form-control, .form-select').addClass('is-invalid');

                <?php
            } ?>
        });
    </script>
</body>