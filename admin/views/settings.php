<?php

use Pandao\Common\Utils\DateUtils;
use Pandao\Common\Utils\StrUtils; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5">
            <form id="form" class="form-horizontal" role="form" action="module=settings" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="row header mb-3 border-bottom">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3">
                        <h1 class="mb0"><i class="fa-solid fa-cog"></i> <?php echo TITLE_ELEMENT; ?></h1>
                        <button type="submit" name="edit_settings" class="btn btn-success">
                            <i class="fa-solid fa-save"></i> <?php echo $adminContext->texts['SAVE']; ?>
                        </button>
                    </div>
                </div>

                <div class="alert-container">
                    <div class="alert alert-success alert-dismissible fade show" role="alert"></div>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert"></div>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert"></div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <?php if($_SESSION['user']['type'] == 'administrator'){ ?>
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#general"><i class="fa-solid fa-cog"></i> <?php echo $adminContext->texts['GENERAL']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contact"><i class="fa-solid fa-phone-square"></i> <?php echo $adminContext->texts['CONTACT']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#database"><i class="fa-solid fa-database"></i> <?php echo $adminContext->texts['DATABASE']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#email_settings"><i class="fa-solid fa-envelope"></i> <?php echo $adminContext->texts['EMAIL_SETTINGS']; ?></a></li>
                            <?php } ?>
                            <li class="nav-item"><a class="nav-link <?php if($_SESSION['user']['type'] != 'administrator') echo 'active'; ?>" data-bs-toggle="tab" href="#profile"><i class="fa-solid fa-user"></i> <?php echo $adminContext->texts['PROFILE']; ?></a></li>
                        </ul>
                    </div>

                    <div class="card-body tab-content">
                        <?php if($_SESSION['user']['type'] == 'administrator'){ ?>
                            <div id="general" class="tab-pane fade show active">
                                <!-- Site Title -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SITE_TITLE']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_site_title']; ?>" name="site_title">
                                    </div>
                                </div>
                                <!-- Maintenance Mode -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['MAINTENANCE_MODE']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="maintenance_mode" value="0" <?php if($config_tmp['pms_maintenance_mode'] == 0) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['NO_OPTION']; ?></label>
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="maintenance_mode" value="1" <?php if($config_tmp['pms_maintenance_mode'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['YES_OPTION']; ?></label>
                                        </div>
                                        <div class="field-notice text-danger" rel="maintenance_mode"></div>
                                    </div>
                                </div>
                                <!-- Maintenance Message -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['MAINTENANCE_MSG']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <textarea class="form-control" name="maintenance_msg"><?php echo stripslashes($config_tmp['pms_maintenance_msg']); ?></textarea>
                                    </div>
                                </div>
                                <!-- Enable Languages -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['ENABLE_LANGUAGES']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="lang_enabled" value="0" <?php if($config_tmp['pms_lang_enabled'] == 0) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['NO_OPTION']; ?></label>
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="lang_enabled" value="1" <?php if($config_tmp['pms_lang_enabled'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['YES_OPTION']; ?></label>
                                        </div>
                                        <div class="field-notice text-danger" rel="lang_enabled"></div>
                                    </div>
                                </div>
                                <!-- Enable Currencies -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['ENABLE_CURRENCIES']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="currency_enabled" value="0" <?php if($config_tmp['pms_currency_enabled'] == 0) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['NO_OPTION']; ?></label>
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="currency_enabled" value="1" <?php if($config_tmp['pms_currency_enabled'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['YES_OPTION']; ?></label>
                                        </div>
                                        <div class="field-notice text-danger" rel="currency_enabled"></div>
                                    </div>
                                </div>
                                <!-- Currency Position -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['CURRENCY_POS']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="currency_pos" value="before" <?php if($config_tmp['pms_currency_pos'] == 'before') echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo PMS_DEFAULT_CURRENCY_SIGN.' 123'; ?></label>
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="currency_pos" value="after" <?php if($config_tmp['pms_currency_pos'] == 'after') echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo '123 '.PMS_DEFAULT_CURRENCY_SIGN; ?></label>
                                        </div>
                                        <div class="field-notice text-danger" rel="currency_pos"></div>
                                    </div>
                                </div>
                                <!-- Template -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['TEMPLATE']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <select name="template" class="form-select">
                                            <?php
                                            $dir = SYSBASE . 'templates/';
                                            $rep = opendir($dir) or die('Error directory opening : '.$dir);
                                            while($entry = @readdir($rep)){
                                                if(is_dir($dir . '/' . $entry) && $entry != '.' && $entry != '..'){
                                                    $selected = ($config_tmp['pms_template'] == $entry) ? ' selected="selected"' : ''; ?>
                                                    <option value="<?php echo $entry; ?>"<?php echo $selected; ?>><?php echo $entry; ?></option>
                                                <?php
                                                }
                                            } ?>
                                        </select>
                                        <div class="field-notice text-danger" rel="template"></div>
                                    </div>
                                </div>
                                <!-- Admin Panel Language -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['ADMIN_PANEL_LANGUAGE']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <select name="admin_lang_file" class="form-select">
                                            <?php
                                            $dir = 'includes/langs/';
                                            $rep = opendir($dir) or die('Error directory opening : '.$dir);
                                            while($entry = @readdir($rep)){
                                                if(is_file($dir . '/' . $entry) && $entry != '.' && $entry != '..'){
                                                    $selected = ($config_tmp['pms_admin_lang_file'] == $entry) ? ' selected="selected"' : ''; ?>
                                                    <option value="<?php echo $entry; ?>"<?php echo $selected; ?>><?php echo $entry; ?></option>
                                                <?php
                                                }
                                            } ?>
                                        </select>
                                        <div class="field-notice text-danger" rel="admin_lang_file"></div>
                                    </div>
                                </div>
                                <!-- Time Zone -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['TIME_ZONE']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <select name="time_zone" class="form-select">
                                            <?php
                                            foreach(timezone_identifiers_list() as $key => $zone){
                                                date_default_timezone_set($zone);
                                                $selected = ($config_tmp['pms_time_zone'] == $zone) ? ' selected="selected"' : ''; ?>
                                                <option value="<?php echo $zone; ?>"<?php echo $selected; ?>><?php echo 'UTC/GMT '.date('P').' - '.$zone; ?></option>
                                                <?php
                                            }
                                            date_default_timezone_set(PMS_TIME_ZONE); ?>
                                        </select>
                                        <div class="field-notice text-danger" rel="time_zone"></div>
                                    </div>
                                </div>
                                <!-- Date Format -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['DATE_FORMAT']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <select name="date_format" class="form-select">
                                            <option value='%e %B %Y'<?php if($config_tmp['pms_date_format'] == '%e %B %Y') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%e %B %Y'); ?></option>
                                            <option value="%B %e, %Y"<?php if($config_tmp['pms_date_format'] == '%B %e, %Y') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%B %e, %Y'); ?></option>
                                            <option value="%b %e, %Y"<?php if($config_tmp['pms_date_format'] == '%b %e, %Y') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%b %e, %Y'); ?></option>
                                            <option value="%A %e %B %Y"<?php if($config_tmp['pms_date_format'] == '%A %e %B %Y') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%A %e %B %Y'); ?></option>
                                            <option value="%a %e %b %Y"<?php if($config_tmp['pms_date_format'] == '%a %e %b %Y') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%a %e %b %Y'); ?></option>
                                            <option value="%Y-%m-%d"<?php if($config_tmp['pms_date_format'] == '%Y-%m-%d') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%Y-%m-%d'); ?></option>
                                            <option value="%m/%d/%Y"<?php if($config_tmp['pms_date_format'] == '%m/%d/%Y') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%m/%d/%Y'); ?></option>
                                            <option value="%d/%m/%Y"<?php if($config_tmp['pms_date_format'] == '%d/%m/%Y') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%d/%m/%Y'); ?></option>
                                        </select>
                                        <div class="field-notice text-danger" rel="date_format"></div>
                                    </div>
                                </div>
                                <!-- Time Format -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['TIME_FORMAT']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <select name="time_format" class="form-select">
                                            <option value="%I:%M%P"<?php if($config_tmp['pms_time_format'] == '%I:%M%P') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%I:%M%P'); ?></option>
                                            <option value="%R"<?php if($config_tmp['pms_time_format'] == '%R') echo ' selected="selected"'; ?>><?php echo DateUtils::strftime('%R'); ?></option>
                                        </select>
                                        <div class="field-notice text-danger" rel="time_format"></div>
                                    </div>
                                </div>
                                <!-- Enable Cookies Notice -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['ENABLE_COOKIES_NOTICE']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="enable_cookies_notice" value="0" <?php if($config_tmp['pms_enable_cookies_notice'] == 0) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['NO_OPTION']; ?></label>
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="enable_cookies_notice" value="1" <?php if($config_tmp['pms_enable_cookies_notice'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['YES_OPTION']; ?></label>
                                        </div>
                                        <div class="field-notice text-danger" rel="enable_cookies_notice"></div>
                                    </div>
                                </div>
                                <!-- Admin Folder -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['ADMIN_FOLDER']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="" name="admin_folder">
                                        <div class="field-notice text-danger" rel="admin_folder"></div>
                                        <div class="form-text text-muted"><?php echo $adminContext->texts['ADMIN_FOLDER_NOTICE'].' '.$config_tmp['pms_admin_folder']; ?></div>
                                    </div>
                                </div>
                                <!-- Analytics Code -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['ANALYTICS_CODE']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <textarea class="form-control" name="analytics_code"><?php echo stripslashes($config_tmp['pms_analytics_code']); ?></textarea>
                                        <div class="form-text text-muted"><?php echo $adminContext->texts['ANALYTICS_CODE_NOTICE']; ?></div>
                                    </div>
                                </div>
                                <!-- Google Maps API Key -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['GMAPS_API_KEY']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_gmaps_api_key']; ?>" name="gmaps_api_key">
                                        <div class="form-text text-muted"><?php echo $adminContext->texts['GMAPS_API_KEY_NOTICE']; ?></div>
                                    </div>
                                </div>
                                <!-- CAPTCHA Public Key -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['CAPTCHA_PKEY']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_captcha_pkey']; ?>" name="captcha_pkey">
                                        <div class="form-text text-muted"><?php echo $adminContext->texts['CAPTCHA_PKEY_NOTICE']; ?></div>
                                    </div>
                                </div>
                                <!-- CAPTCHA Secret Key -->
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['CAPTCHA_SKEY']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_captcha_skey']; ?>" name="captcha_skey">
                                        <div class="form-text text-muted"><?php echo $adminContext->texts['CAPTCHA_SKEY_NOTICE']; ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Section -->
                            <div id="contact" class="tab-pane fade">
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['NAME']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_owner']; ?>" name="owner">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['ADDRESS']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <textarea class="form-control" name="address" rows="4"><?php echo stripslashes(StrUtils::br2nl($config_tmp['pms_address'])); ?></textarea>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['PHONE']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_phone']; ?>" name="phone">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['MOBILE']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_mobile']; ?>" name="mobile">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['EMAIL']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_email']; ?>" name="email2">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Profile Section -->
                        <div id="profile" class="tab-pane fade <?php if($_SESSION['user']['type'] != 'administrator') echo 'show active'; ?>">
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['USER']; ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-6">
                                    <input class="form-control" type="text" value="<?php echo $user_data['login']; ?>" name="user">
                                    <div class="field-notice text-danger" rel="user"></div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['PASSWORD']; ?></label>
                                <div class="col-lg-9 col-xxl-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="password" class="form-control pr-password" value="" name="password" placeholder="<?php echo $adminContext->texts['PASSWORD_NOTICE']; ?>">
                                                <span class="input-group-text toggle-password"></span>
                                            </div>
                                            <div class="field-notice text-danger" rel="password"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="password" class="form-control" value="" name="password2" placeholder="<?php echo $adminContext->texts['PASSWORD_CONFIRM']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['EMAIL']; ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9 col-xxl-6">
                                    <input class="form-control" type="text" value="<?php echo $user_data['email']; ?>" name="email">
                                    <div class="field-notice text-danger" rel="email"></div>
                                </div>
                            </div>
                        </div>

                        <?php if($_SESSION['user']['type'] == 'administrator'){ ?>
                            <!-- Database Settings -->
                            <div id="database" class="tab-pane fade">
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['NAME']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_name']; ?>" name="db_name">
                                        <div class="field-notice text-danger" rel="db_name"></div>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['HOST']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_host']; ?>" name="db_host">
                                        <div class="field-notice text-danger" rel="db_host"></div>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['PORT']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_port']; ?>" name="db_port">
                                        <div class="field-notice text-danger" rel="db_port"></div>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['USER']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_db_user']; ?>" name="db_user">
                                        <div class="field-notice text-danger" rel="db_user"></div>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['PASSWORD']; ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="password" value="<?php echo $config_tmp['pms_db_pass']; ?>" name="db_pass">
                                        <div class="field-notice text-danger" rel="db_pass"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Settings -->
                            <div id="email_settings" class="tab-pane fade">
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SENDER_EMAIL']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_sender_email']; ?>" name="sender_email">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SENDER_NAME']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_sender_name']; ?>" name="sender_name">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['USE_SMTP']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="use_smtp" value="0" <?php if($config_tmp['pms_use_smtp'] == 0) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['NO_OPTION']; ?></label>
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="use_smtp" value="1" <?php if($config_tmp['pms_use_smtp'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['YES_OPTION']; ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SMTP_SECURITY']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <select class="form-select" name="smtp_security">
                                            <option value="" <?php if($config_tmp['pms_smtp_security'] == '') echo 'selected'; ?>>None</option>
                                            <option value="ssl" <?php if($config_tmp['pms_smtp_security'] == 'ssl') echo 'selected'; ?>>SSL</option>
                                            <option value="tls" <?php if($config_tmp['pms_smtp_security'] == 'tls') echo 'selected'; ?>>TLS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SMTP_AUTH']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="smtp_auth" value="0" <?php if($config_tmp['pms_smtp_auth'] == 0) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['NO_OPTION']; ?></label>
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="smtp_auth" value="1" <?php if($config_tmp['pms_smtp_auth'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label"><?php echo $adminContext->texts['YES_OPTION']; ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SMTP_HOST']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_smtp_host']; ?>" name="smtp_host">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SMTP_PORT']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_smtp_port']; ?>" name="smtp_port">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SMTP_USER']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="text" value="<?php echo $config_tmp['pms_smtp_user']; ?>" name="smtp_user">
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label class="col-lg-3 col-form-label"><?php echo $adminContext->texts['SMTP_PASS']; ?></label>
                                    <div class="col-lg-9 col-xxl-6">
                                        <input class="form-control" type="password" value="<?php echo $config_tmp['pms_smtp_pass']; ?>" name="smtp_pass">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>


<script>
    $(document).ready(function () {
        let hash = window.location.hash;

        if (hash) {
            let $activeTab = $(`.nav-tabs .nav-link[href="${hash}"]`);
            if ($activeTab.length) {

                $('.nav-tabs .nav-link.active').removeClass('active');
                $('.tab-pane.active').removeClass('active show');

                $activeTab.addClass('active');
                $(hash).addClass('active show');
            }
        }

        $(window).on('hashchange', function () {
            let newHash = window.location.hash;
            let $newTab = $(`.nav-tabs .nav-link[href="${newHash}"]`);
            if ($newTab.length) {
                $('.nav-tabs .nav-link.active').removeClass('active');
                $('.tab-pane.active').removeClass('active show');

                $newTab.addClass('active');
                $(newHash).addClass('active show');
            }
        });
    });
</script>