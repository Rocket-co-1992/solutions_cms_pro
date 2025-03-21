<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5">
            <?php
            if (!$adminContext->noAccess) { ?>
                <form id="form" class="form-horizontal" role="form" action="module=<?php echo MODULE; ?>&view=form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <div class="row header mb-3 border-bottom">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3">
                            <h1 class="mb0"><i class="fa-solid fa-<?php echo ICON; ?>"></i> <?php echo TITLE_ELEMENT; ?></h1>
                            <div class="float-start text-end">
                                <?php
                                if ($adminContext->addAllowed && $db_access) { ?>
                                    <a href="javascript:if(confirm('<?php echo $adminContext->texts['LOOSE_DATAS']; ?>')) window.location = 'module=<?php echo MODULE; ?>&view=form&id=0';" class="btn btn-primary">
                                        <i class="fa-solid fa-fw fa-plus-circle"></i> <span class="d-none d-sm-inline-block"><?php echo $adminContext->texts['NEW']; ?></span>
                                    </a>
                                <?php
                                }

                                if (file_exists($directory . '/views/partials/custom_nav_form.php')) include($directory . '/views/partials/custom_nav_form.php'); ?>

                                <a href="module=<?php echo MODULE; ?>&view=list" class="btn btn-secondary">
                                    <i class="fa-solid fa-fw fa-reply"></i> <span class="d-none d-sm-inline-block"><?php echo $adminContext->texts['BACK_TO_LIST']; ?></span>
                                </a>
                                <?php
                                if ($db_access) {
                                    if ($id > 0) {
                                        if ($adminContext->editAllowed) { ?>
                                            <button type="submit" name="edit" class="btn btn-secondary d-none d-sm-inline-block">
                                                <i class="fa-solid fa-fw fa-save"></i> <?php echo $adminContext->texts['SAVE']; ?>
                                            </button>
                                            <button type="submit" name="edit_back" class="btn btn-success">
                                                <i class="fa-solid fa-fw fa-save"></i> <span class="d-none d-sm-inline-block"><?php echo $adminContext->texts['SAVE_EXIT']; ?></span>
                                            </button>
                                        <?php
                                        }
                                        if ($adminContext->addAllowed) { ?>
                                            <button type="submit" name="add" class="btn btn-secondary">
                                                <i class="fa-solid fa-fw fa-copy"></i> <span class="d-none d-sm-inline-block"><?php echo $adminContext->texts['REPLICATE']; ?></span>
                                            </button>
                                        <?php
                                        }
                                    } else {
                                        if ($adminContext->addAllowed) { ?>
                                            <button type="submit" name="add" class="btn btn-secondary d-none d-sm-inline-block">
                                                <i class="fa-solid fa-fw fa-save"></i> <?php echo $adminContext->texts['SAVE']; ?>
                                            </button>
                                            <button type="submit" name="add_back" class="btn btn-success">
                                                <i class="fa-solid fa-fw fa-save"></i> <span class="d-none d-sm-inline-block"><?php echo $adminContext->texts['SAVE_EXIT']; ?></span>
                                            </button>
                                <?php
                                        }
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>

                    <div class="alert-container">
                        <div class="alert alert-success alert-dismissible fade show" role="alert"></div>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert"></div>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert"></div>
                    </div>

                    <?php
                    if ($db_access) { ?>

                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="row">
                            <div class="col-xl-8">
                                <div class="card shadow-sm mb-3">
                                    <?php
                                    if (MULTILINGUAL) { ?>
                                        <div class="card-header">
                                            <ul class="nav nav-tabs card-header-tabs">
                                                <?php
                                                foreach ($languages as $i => $lang) { ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link <?php if (PMS_DEFAULT_LANG == $lang['id']) echo 'active'; ?>" data-bs-toggle="tab" href="#lang_<?php echo $lang['id']; ?>">
                                                            <img src="<?php echo $lang['image']; ?>" alt="" border="0">
                                                            <span class="hidden-xs">
                                                                <?php echo $lang['title'];
                                                                if (PMS_DEFAULT_LANG == $lang['id']) echo ' <em>(default)</em>'; ?>
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php
                                                } ?>
                                            </ul>
                                        </div>
                                    <?php
                                    } ?>
                                    <div class="card-body tab-content">
                                        <?php
                                        foreach ($languages as $i => $lang) {
                                            $id_lang = (MULTILINGUAL) ? $lang['id'] : 0; ?>
                                            <div id="lang_<?php echo $id_lang; ?>" class="tab-pane fade <?php if (!MULTILINGUAL || PMS_DEFAULT_LANG == $id_lang) echo 'show active'; ?>">
                                                <?php
                                                foreach ($fields as $tableName => $fields_table) {
                                                    if ($tableName != MODULE) {
                                                        $id_lang_table = ($fields_table['table']['multi'] == 1) ? $lang['id'] : 0;
                                                        if ($id_lang == PMS_DEFAULT_LANG || $id_lang == 0 || $fields_table['table']['multi'] == 1) { ?>

                                                            <fieldset class="mt-5">
                                                                <legend><?php echo $fields_table['table']['tableLabel']; ?></legend>
                                                                <table class="table table-hover form-table" id="table_<?php echo $tableName; ?>_<?php echo $id_lang; ?>">
                                                                    <thead>
                                                                        <tr>
                                                                            <?php
                                                                            foreach ($fields_table['fields'] as $fieldName => $field) {
                                                                                if ($fieldName == 'id' || $id_lang_table == PMS_DEFAULT_LANG || $field->multilingual || $id_lang_table == 0) { ?>
                                                                                    <th><?php echo $field->label; ?></th>
                                                                            <?php
                                                                                }
                                                                            } ?>
                                                                            <th width="50"><?php echo $adminContext->texts['ACTIONS']; ?></th>
                                                                            </th>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        for ($index = 0; $index < $fields_table['maxRows']; $index++) { ?>
                                                                            <tr>
                                                                                <?php
                                                                                foreach ($fields_table['fields'] as $fieldName => $field) {
                                                                                    if ($fieldName == 'id' || $id_lang_table == PMS_DEFAULT_LANG || $field->multilingual || $id_lang_table == 0) {
                                                                                        $notice = $field->getNotice($index); ?>
                                                                                        <td class="text-center form-inline input-<?php echo $field->type . $model->getClassAttr($field->type, $field->validation, $notice, $id_lang_table); ?>">
                                                                                            <?php
                                                                                            $model->displayField($field, $tableName, $index, $id_lang_table);
                                                                                            if ($notice != '' && ($id_lang_table == PMS_DEFAULT_LANG || $id_lang_table == 0)) { ?>
                                                                                                <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                                                                                            <?php
                                                                                            }
                                                                                            if ($notice != '' && ($id_lang_table == PMS_DEFAULT_LANG || $id_lang_table == 0)) { ?>
                                                                                                <p class="text-danger"><?php echo $notice; ?></p>
                                                                                            <?php
                                                                                            } ?>
                                                                                        </td>
                                                                                <?php
                                                                                    }
                                                                                } ?>
                                                                                <td class="text-center">
                                                                                    <?php
                                                                                    if ($adminContext->deleteAllowed) { ?>
                                                                                        <a class="tips" href="javascript:if(confirm('<?php echo $adminContext->texts['DELETE_CONFIRM2'] . ' ' . $adminContext->texts['LOOSE_DATAS']; ?>')) window.location = 'module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&table=<?php echo $tableName; ?>&row=<?php echo $fields_table['fields']['id']->getValue(false, $index, $id_lang_table); ?>&fieldref=<?php echo $fields_table['table']['fieldRef']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=delete_row';" title="<?php echo $adminContext->texts['DELETE']; ?>"><i class="fa-solid fa-fw fa-trash-alt text-danger"></i></a>
                                                                                    <?php } ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        }
                                                                        if ($index == 0 && ($id_lang_table == PMS_DEFAULT_LANG || $id_lang_table == 0)) { ?>
                                                                            <tr>
                                                                                <?php
                                                                                foreach ($fields_table['fields'] as $fieldName => $field) { ?>
                                                                                    <td class="text-center input-<?php echo $field->type . $model->getClassAttr($field->type, $field->validation, '', $id_lang_table); ?>">
                                                                                        <?php $model->displayField($field, $tableName, 0, $id_lang_table); ?>
                                                                                    </td>
                                                                                <?php
                                                                                } ?>
                                                                                <td></td>
                                                                            </tr>
                                                                        <?php
                                                                        } ?>
                                                                    </tbody>
                                                                </table>
                                                                <?php
                                                                if ($id_lang == PMS_DEFAULT_LANG || $id_lang == 0) {
                                                                    if ($adminContext->editAllowed) { ?>
                                                                        <a href="#table_<?php echo $tableName; ?>_<?php echo $id_lang; ?>" class="new_entry btn btn-link"><i class="fa-solid fa-fw fa-plus"></i> <?php echo $adminContext->texts['NEW_ENTRY']; ?></a>
                                                                <?php
                                                                    }
                                                                } ?>
                                                            </fieldset>
                                                            <?php
                                                        }
                                                    } else {
                                                        foreach ($fields_table['fields'] as $fieldName => $field) {
                                                            if ($field->multilingual || $id_lang == 0) {
                                                                if ($field->type == 'separator') echo '<legend>' . $field->label . '</legend>';
                                                                else {
                                                                    $class = $model->getClassAttr($field->type, $field->validation, $field->getNotice(), $id_lang);
                                                                    $class2 = $field->editor == 1 ? 'row-editor' : ''; ?>

                                                                    <div class="row mb-3 <?php echo $class2; ?>">
                                                                        <label class="col-xl-2 col-form-label">
                                                                            <?php echo $field->label;
                                                                            if (($id_lang == PMS_DEFAULT_LANG || $id_lang == 0) && $field->required) echo '&nbsp;<span class="text-danger">*</span>'; ?>
                                                                        </label>
                                                                        <div class="col-xl-8 col-xxl-7">
                                                                            <div class="<?php echo $class; ?>">
                                                                                <?php
                                                                                $model->displayField($field, $tableName, 0, $id_lang);
                                                                                if ($field->getNotice() != '' && ($id_lang == PMS_DEFAULT_LANG || $id_lang == 0)) echo '<p class="text-danger">' . $field->getNotice() . '</p>'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <?php if ($field->comment != '') { ?>
                                                                            <div class="col-xxl-3">
                                                                                <div class="pt-2 pb-2 text-info"><i class="fa-solid fa-fw fa-info"></i> <?php echo $field->comment; ?></div>
                                                                            </div>
                                                                        <?php
                                                                        } ?>
                                                                    </div>
                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if (NB_FILES > 0) { ?>
                                                    <fieldset class="medias-gallery mt-5">
                                                        <legend class="form-inline">
                                                            <span class="text-uppercase me-3"><?php echo $adminContext->texts['MEDIAS']; ?></span>

                                                            <?php
                                                            if ($id_lang == PMS_DEFAULT_LANG || FILE_MULTI || $id_lang == 0) {
                                                                echo $mediasCounter[$id_lang]['num_uploaded'] . '/' . NB_FILES . ' - ' . $mediasCounter[$id_lang]['max_files'] . ' ' . $adminContext->texts['REMAINING'];

                                                                if ($upload_allowed) {
                                                                    if ($mediasCounter[$id_lang]['num_files'] > 0 && $adminContext->editAllowed) { ?>

                                                                        <select name="multiple_actions_file" class="form-select form-select-sm">
                                                                            <option value="">- <?php echo $adminContext->texts['ACTIONS']; ?> -</option>
                                                                            <option value="check_multi_file"><?php echo $adminContext->texts['PUBLISH']; ?></option>
                                                                            <option value="uncheck_multi_file"><?php echo $adminContext->texts['UNPUBLISH']; ?></option>
                                                                            <option value="display_home_multi_file"><?php echo $adminContext->texts['SHOW_HOMEPAGE']; ?></option>
                                                                            <option value="remove_home_multi_file"><?php echo $adminContext->texts['REMOVE_HOMEPAGE']; ?></option>
                                                                            <option value="delete_multi_file"><?php echo $adminContext->texts['DELETE']; ?></option>
                                                                        </select>
                                                                    <?php
                                                                    }
                                                                    if ($mediasCounter[$id_lang]['max_files'] > 0) { ?>
                                                                        <input type="file" name="file_upload_<?php echo $id_lang; ?>" id="file_upload_<?php echo $id_lang; ?>" class="file_upload" rel="<?php echo $id_lang . ', ' . $mediasCounter[$id_lang]['max_files']; ?>">
                                                            <?php
                                                                    }
                                                                }
                                                            } ?>
                                                        </legend>
                                                        <div class="alert-container">
                                                            <div class="alert alert-info alert-dismissible fade show" role="alert"></div>
                                                        </div>
                                                        <?php
                                                        if ($adminContext->uploadAllowed) { ?>
                                                            <div id="file_upload_<?php echo $id_lang; ?>-queue" class="uploadify-queue"></div>
                                                        <?php
                                                        } ?>
                                                        <div class="uploaded clearfix alert alert-success" id="file_uploaded_<?php echo $id_lang; ?>">
                                                            <p><?php echo $adminContext->texts['FILES_READY_UPLOAD']; ?></p>
                                                            <?php
                                                            if (!empty($tmpFiles[$id_lang])) {
                                                                foreach ($tmpFiles[$id_lang] as $file) { ?>
                                                                    <div class="prev-file card float-start me-3" style="width: 18rem;">
                                                                        <?php
                                                                        if ($file[4] == 0 && $file[5] == 0 && array_key_exists($file[2], $pms_allowable_file_exts)) {
                                                                            $icon = $pms_allowable_file_exts[$file[2]]; ?>
                                                                            <i class="fa-regular fa-<?php echo $icon_file; ?>"></i>
                                                                        <?php echo substr($file[1], 0, 15) . ((mb_strlen($file[1]) >= 15) ? '...' : '.') . $file[2] . '<br>' . $file[3];
                                                                        } else { ?>
                                                                            <img class="card-img-top" src="<?php echo str_replace(SYSBASE, DOCBASE, $file[0]); ?>" alt="">
                                                                            <div class="card-body">
                                                                                <?php echo substr($file[1], 0, 15) . ((mb_strlen($file[1]) >= 15) ? '...' : '.') . $file[2] . '<br>' . $file[3] . ' | ' . $file[4] . ' x ' . $file[5]; ?>
                                                                            </div>
                                                                        <?php
                                                                        } ?>
                                                                    </div>
                                                            <?php
                                                                }
                                                            } ?>
                                                        </div>
                                                        <ul class="files-list<?php if ($id_lang == PMS_DEFAULT_LANG || FILE_MULTI || $id_lang == 0) echo ' sortable'; ?>" id="files_list_<?php echo $id_lang; ?>">
                                                            <?php
                                                            $files = $filesData[$id_lang] ?? [];
                                                            foreach ($files as $i => $file) { ?>

                                                                <li id="file_<?php echo $file['id']; ?>" class="card float-start me-3" style="width: 18rem;">
                                                                    <div class="prev-file">
                                                                        <?php echo $file['preview_html']; ?>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="actions-file">
                                                                            <?php
                                                                            if ($file['type'] == "image") { ?>
                                                                                <a class="image-link" href="<?php echo DOCBASE . $file['paths']['zoom_path']; ?>" target="_blank"><i class="fa-solid fa-fw fa-search-plus"></i></a>
                                                                                <?php
                                                                            }
                                                                            if ($adminContext->editAllowed) {
                                                                                if ($file['checked'] == 0) { ?>
                                                                                    <a class="tips" href="module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&file=<?php echo $file['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=check_file" title="<?php echo $adminContext->texts['PUBLISH']; ?>"><i class="fa-solid fa-fw fa-check text-success"></i></a>
                                                                                    <a class="tips" href="module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&file=<?php echo $file['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=uncheck_file" title="<?php echo $adminContext->texts['UNPUBLISH']; ?>"><i class="fa-solid fa-fw fa-ban text-danger"></i></a>
                                                                                <?php
                                                                                } elseif ($file['checked'] == 1) { ?>
                                                                                    <i class="fa-solid fa-fw fa-check text-muted"></i>
                                                                                    <a class="tips" href="module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&file=<?php echo $file['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=uncheck_file" title="<?php echo $adminContext->texts['UNPUBLISH']; ?>"><i class="fa-solid fa-fw fa-ban text-danger"></i></a>
                                                                                <?php
                                                                                } elseif ($file['checked'] == 2) { ?>
                                                                                    <a class="tips" href="module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&file=<?php echo $file['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=check_file" title="<?php echo $adminContext->texts['PUBLISH']; ?>"><i class="fa-solid fa-fw fa-check text-success"></i></a>
                                                                                    <i class="fa-solid fa-fw fa-ban text-muted"></i>
                                                                                <?php
                                                                                }
                                                                                if ($file['home'] == 0) { ?>
                                                                                    <a class="tips" href="module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&file=<?php echo $file['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=display_home_file" title="<?php echo $adminContext->texts['SHOW_HOMEPAGE']; ?>"><i class="fa-solid fa-fw fa-home text-danger"></i></a>
                                                                                <?php
                                                                                } elseif ($file['home'] == 1) { ?>
                                                                                    <a class="tips" href="module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&file=<?php echo $file['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=remove_home_file" title="<?php echo $adminContext->texts['REMOVE_HOMEPAGE']; ?>"><i class="fa-solid fa-fw fa-home text-success"></i></a>
                                                                                <?php
                                                                                }
                                                                                if ($upload_allowed) { ?>
                                                                                    <a class="tips" href="javascript:if(confirm('<?php echo $adminContext->texts['DELETE_FILE_CONFIRM'] . " " . $adminContext->texts['LOOSE_DATAS']; ?>')) window.location = 'module=<?php echo MODULE; ?>&view=form&id=<?php echo $id; ?>&file=<?php echo $file['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=delete_file';" title="<?php echo $adminContext->texts['DELETE']; ?>"><i class="fa-solid fa-fw fa-trash-alt text-danger"></i></a>
                                                                            <?php
                                                                                }
                                                                            } ?>
                                                                            <a href="module=<?php echo MODULE; ?>&view=form&action=download&file=<?php echo $file['id']; ?>&id=<?php echo $id; ?>&type=<?php echo $file['type']; ?>"><i class="fa-solid fa-fw fa-download"></i></a>
                                                                            <input type="checkbox" name="multiple_file[]" value="<?php echo $file['id']; ?>">
                                                                        </div>
                                                                        <div class="infos-file">
                                                                            <input name="<?php echo $file['fieldname'] . '_label'; ?>" placeholder="Label" class="form-control" type="text" value="<?php echo $file['label']; ?>">
                                                                            <span class="filename"><?php echo $file['filename']; ?></span><br>
                                                                            <span class="filesize"><?php echo $file['filesize']; ?></span>
                                                                        </div>
                                                                    </div>
                                                                </li>

                                                            <?php
                                                            } ?>
                                                        </ul>
                                                        <div style="clear:left;"></div>
                                                    </fieldset>
                                                <?php
                                                } ?>
                                            </div>
                                        <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <?php
                                        if (MULTILINGUAL) { ?>
                                            <legend><?php echo $adminContext->texts['SETTINGS']; ?></legend>
                                            <?php
                                            foreach ($fields[MODULE]['fields'] as $fieldName => $field) {
                                                if (!$field->multilingual) {
                                                    if ($field->type == 'separator') echo '<legend>' . $label . '</legend>';
                                                    else {
                                                        $class = $model->getClassAttr($field->type, $field->validation, $field->getNotice(), PMS_DEFAULT_LANG); ?>
                                                        <div class="row mb-3">
                                                            <label class="col-xl-4 col-form-label">
                                                                <?php echo $field->label;
                                                                if ($field->required) echo '&nbsp;<span class="text-danger">*</span>'; ?>
                                                            </label>
                                                            <div class="col-xl-8 col-xxl-7">
                                                                <div class="<?php echo $class; ?> d-flex justify-content-left align-items-center gap-1">
                                                                    <?php
                                                                    $model->displayField($field, $tableName, 0, PMS_DEFAULT_LANG);
                                                                    if ($field->getNotice() != '') echo '<p class="text-danger">' . $field->getNotice() . '</p>'; ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            if ($field->comment != '') { ?>
                                                                <div class="col-xl-8 offset-xl-4 ">
                                                                    <div class="pt-2 pb-2 text-info"><i class="fa-solid fa-fw fa-info"></i> <?php echo  $field->comment; ?></div>
                                                                </div>
                                                            <?php
                                                            } ?>
                                                        </div>
                                            <?php
                                                    }
                                                }
                                            }
                                        }
                                        if (is_file($directory . '/partials/custom_form.php')) require $directory . '/partials/custom_form.php';

                                        if (RELEASE || VALIDATION || HOME) { ?>
                                            <legend><?php echo $adminContext->texts['RELEASE']; ?></legend>
                                            <?php
                                            if ($adminContext->publishAllowed) {
                                                if (RELEASE) { ?>
                                                    <div class="row mb-3 form-inline">
                                                        <label class="col-xl-4 col-form-label"><?php echo $adminContext->texts['PUBLISH_DATE']; ?></label>
                                                        <div class="col-xl-8">
                                                            <?php
                                                            if (is_numeric($model->publish_date)) {
                                                                $day = date('j', $model->publish_date);
                                                                $month = date('n', $model->publish_date);
                                                                $year = date('Y', $model->publish_date);
                                                                $hour = date('H', $model->publish_date);
                                                                $minute = date('i', $model->publish_date);
                                                            } else {
                                                                $day = '';
                                                                $month = '';
                                                                $year = '';
                                                                $hour = '';
                                                                $minute = '';
                                                            } ?>
                                                            <div class="input-datetime">
                                                                <select name="publish_date_year" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($y = date('Y') + 4; $y >= 2015; $y--) {
                                                                        $s = ($y == $year) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $y . '"' . $s . '>' . $y . '</option>' . "\n";
                                                                    } ?>
                                                                </select>&nbsp;/&nbsp;
                                                                <select name="publish_date_month" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($n = 1; $n <= 12; $n++) {
                                                                        $s = ($n == $month) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $n . '"' . $s . '>' . $n . '</option>' . "\n";
                                                                    } ?>
                                                                </select>&nbsp;/&nbsp;
                                                                <select name="publish_date_day" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($d = 1; $d <= 31; $d++) {
                                                                        $s = ($d == $day) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $d . '"' . $s . '>' . $d . '</option>' . "\n";
                                                                    } ?>
                                                                </select>
                                                                at&nbsp;
                                                                <select name="publish_date_hour" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($h = 0; $h <= 23; $h++) {
                                                                        $s = ($h == $hour) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $h . '"' . $s . '>' . $h . '</option>' . "\n";
                                                                    } ?>
                                                                </select>&nbsp;:&nbsp;
                                                                <select name="publish_date_minute" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($m = 0; $m <= 59; $m++) {
                                                                        $s = ($m == $minute) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $m . '"' . $s . '>' . $m . '</option>' . "\n";
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 form-inline">
                                                        <label class="col-xl-4 col-form-label"><?php echo $adminContext->texts['UNPUBLISH_DATE']; ?></label>
                                                        <div class="col-xl-8">
                                                            <?php
                                                            if (is_numeric($model->unpublish_date)) {
                                                                $day = date('j', $model->unpublish_date);
                                                                $month = date('n', $model->unpublish_date);
                                                                $year = date('Y', $model->unpublish_date);
                                                                $hour = date('H', $model->unpublish_date);
                                                                $minute = date('i', $model->unpublish_date);
                                                            } else {
                                                                $day = '';
                                                                $month = '';
                                                                $year = '';
                                                                $hour = '';
                                                                $minute = '';
                                                            } ?>
                                                            <div class="input-datetime">
                                                                <select name="unpublish_date_year" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($y = date('Y') + 4; $y >= 2015; $y--) {
                                                                        $s = ($y == $year) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $y . '"' . $s . '>' . $y . '</option>' . "\n";
                                                                    } ?>
                                                                </select>&nbsp;/&nbsp;
                                                                <select name="unpublish_date_month" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($n = 1; $n <= 12; $n++) {
                                                                        $s = ($n == $month) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $n . '"' . $s . '>' . $n . '</option>' . "\n";
                                                                    } ?>
                                                                </select>&nbsp;/&nbsp;
                                                                <select name="unpublish_date_day" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($d = 1; $d <= 31; $d++) {
                                                                        $s = ($d == $day) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $d . '"' . $s . '>' . $d . '</option>' . "\n";
                                                                    } ?>
                                                                </select>
                                                                at&nbsp;
                                                                <select name="unpublish_date_hour" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($h = 0; $h <= 23; $h++) {
                                                                        $s = ($h == $hour) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $h . '"' . $s . '>' . $h . '</option>' . "\n";
                                                                    } ?>
                                                                </select>&nbsp;:&nbsp;
                                                                <select name="unpublish_date_minute" class="form-select">
                                                                    <option value="">-</option>
                                                                    <?php
                                                                    for ($m = 0; $m <= 59; $m++) {
                                                                        $s = ($m == $minute) ? ' selected="selected"' : '';
                                                                        echo '<option value="' . $m . '"' . $s . '>' . $m . '</option>' . "\n";
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if ($show_langs) { ?>
                                                   <div class="row mb-3">
                                                        <label class="col-xl-4 col-form-label">Afficher en</label>
                                                        <div class="col-xl-8 col-xxl-7">
                                                            <div class="form-inline d-flex justify-content-left align-items-center gap-1">
                                                                <select name="show_langs_tmp[]" multiple="multiple" id="show_langs_1_0_tmp" size="4" class="form-select">
                                                                    <?php
                                                                    foreach ($languages as $i => $lang) {
                                                                        if (!in_array($lang['id'], $model->show_langs)) {
                                                                            echo '<option value="' . $lang['id'] . '">' . $lang['title'] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <div class="d-flex flex-column">
                                                                    <a href="#" class="btn btn-circle btn-secondary remove_option mb-2" rel="show_langs_1_0">
                                                                        <i class="fa fa-fw fa-arrow-left"></i>
                                                                    </a>
                                                                    <a href="#" class="btn btn-circle btn-secondary add_option" rel="show_langs_1_0">
                                                                        <i class="fa fa-fw fa-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                                <select name="show_langs[]" multiple="multiple" id="show_langs_1_0" size="4" class="form-select">
                                                                    <?php
                                                                    foreach ($languages as $i => $lang) {
                                                                        if (in_array($lang['id'], $model->show_langs)) {
                                                                            echo '<option value="' . $lang['id'] . '" selected="selected">' . $lang['title'] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if ($hide_langs) { ?>
                                                    <div class="row mb-3">
                                                        <label class="col-xl-4 col-form-label">Masquer en</label>
                                                        <div class="col-xl-8 col-xxl-7">
                                                            <div class="form-inline d-flex justify-content-left align-items-center gap-1">
                                                                <select name="hide_langs_tmp[]" multiple="multiple" id="hide_langs_1_0_tmp" size="4" class="form-select">
                                                                    <?php
                                                                    foreach ($languages as $i => $lang) {
                                                                        if (!in_array($lang['id'], $model->hide_langs)) {
                                                                            echo '<option value="' . $lang['id'] . '">' . $lang['title'] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <div class="d-flex flex-column">
                                                                    <a href="#" class="btn btn-circle btn-secondary remove_option mb-2" rel="hide_langs_1_0">
                                                                        <i class="fa fa-fw fa-arrow-left"></i>
                                                                    </a>
                                                                    <a href="#" class="btn btn-circle btn-secondary add_option" rel="hide_langs_1_0">
                                                                        <i class="fa fa-fw fa-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                                <select name="hide_langs[]" multiple="multiple" id="hide_langs_1_0" size="4" class="form-select">
                                                                    <?php
                                                                    foreach ($languages as $i => $lang) {
                                                                        if (in_array($lang['id'], $model->hide_langs)) {
                                                                            echo '<option value="' . $lang['id'] . '" selected="selected">' . $lang['title'] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if (VALIDATION) { ?>
                                                    <div class="row mb-3">
                                                        <label class="col-xl-4 col-form-label"><?php echo $adminContext->texts['RELEASE']; ?></label>
                                                        <div class="col-xl-8">
                                                            <div class="form-check">
                                                                <label class="form-check-label"><input name="checked" type="radio" class="form-check-input" value="1" <?php if ($model->checked == 1) echo ' checked="checked"'; ?>><?php echo $adminContext->texts['PUBLISHED']; ?><br>
                                                            </div>
                                                            <div class="form-check">
                                                                <label class="form-check-label"><input name="checked" type="radio" class="form-check-input" value="2" <?php if ($model->checked == 2) echo ' checked="checked"'; ?>><?php echo $adminContext->texts['NOT_PUBLISHED']; ?><br>
                                                            </div>
                                                            <div class="form-check">
                                                                <label class="form-check-label"><input name="checked" type="radio" class="form-check-input" value="0" <?php if ($model->checked == 0) echo ' checked="checked"'; ?>><?php echo $adminContext->texts['AWAITING']; ?>
                                                            </div>
                                                            <div class="form-check">
                                                                <label class="form-check-label"><input name="checked" type="radio" class="form-check-input" value="3" <?php if ($model->checked == 3) echo ' checked="checked"'; ?>><?php echo $adminContext->texts['ARCHIVED']; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if (HOME) { ?>
                                                    <div class="row mb-3">
                                                        <label class="col-xl-4 col-form-label"><?php echo $adminContext->texts['HOMEPAGE']; ?></label>
                                                        <div class="col-xl-8">
                                                            <div class="form-check form-switch form-check-inline">
                                                                <label class="form-check-label"><input name="home" type="checkbox" class="form-check-input" value="1" <?php if ($model->home == 1) echo ' checked="checked"'; ?>><br>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                            }
                                        }
                                        if ($is_users_field) { ?>
                                            <div class="row mb-3 form-inline">
                                                <label class="col-xl-4 col-form-label"><?php echo $adminContext->texts['USER']; ?></label>
                                                <div class="col-xl-8">
                                                    <div class="d-flex">
                                                        <select name="users_tmp[]" multiple="multiple" id="users_tmp" size="4" class="form-select me-2">
                                                            <?php
                                                            foreach ($usersIn as $user) { ?>
                                                                <option value="<?php echo $user['id']; ?>"><?php echo $user['login']; ?></option>
                                                            <?php
                                                            } ?>
                                                        </select>
                                                        <div class="d-flex flex-column">
                                                            <a href="#" class="btn btn-circle btn-secondary mb-2 remove_option" rel="users"><i class="fa-solid fa-fw fa-arrow-left"></i></a>
                                                            <a href="#" class="btn btn-circle btn-secondary add_option" rel="users"><i class="fa-solid fa-fw fa-arrow-right"></i></a>
                                                        </div>
                                                        <select name="users[]" multiple="multiple" id="users" size="4" class="form-select ms-2">
                                                            <?php
                                                            foreach ($usersNotIn as $user) { ?>
                                                                <option value="<?php echo $user['id']; ?>" selected="selected"><?php echo $user['login']; ?></option>
                                                            <?php
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } ?>
                </form>
            <?php
            } else {
                echo '<p>' . $adminContext->texts['ACCESS_DENIED'] . '</p>';
            } ?>
        </main>
    </div>
</div>