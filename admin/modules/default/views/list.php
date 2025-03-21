<?php use Pandao\Common\Utils\DateUtils; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5">
            <form id="form" action="module=<?php echo MODULE; ?>&view=list" method="post" class="ajax-form">
                <div class="row header mb-3 border-bottom">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3">
                        <h1 class="mb0"><i class="fa-solid fa-<?php echo $module->icon; ?>"></i> <?php echo TITLE_ELEMENT; ?></h1>
                        <div class="float-start text-end">
                            &nbsp;&nbsp;
                            <?php
                            if ($adminContext->addAllowed) { ?>
                                <a href="module=<?php echo MODULE; ?>&view=form&id=0" class="btn btn-primary">
                                    <i class="fa-solid fa-fw fa-plus-circle"></i><span class="d-none d-sm-inline-block"> <?php echo $adminContext->texts['NEW']; ?></span>
                                </a>
                                <?php
                            } ?>
                        </div>
                    </div>
                </div>
                
                <div class="alert-container">
                    <div class="alert alert-success alert-dismissible fade show" role="alert"></div>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert"></div>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert"></div>
                </div>

                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
                <div class="card shadow-sm">
                    <div class="card-header pt-3 pb-3 form-inline clearfix">
                        <div class="d-flex justify-content-between">
                            <div class="text-start">
                                <div class="form-inline">
                                    <input type="text" name="q_search" value="<?php echo $_POST['q_search'] ?? ''; ?>" class="form-control form-control-sm" placeholder="<?php echo $adminContext->texts['SEARCH']; ?>...">
                                    <?php echo $filtersHtml; ?>
                                    <button class="btn btn-secondary btn-sm" type="submit" id="search" name="search"><i class="fa-solid fa-fw fa-search"></i></button>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-text"><i class="fa-solid fa-fw fa-th-list"></i><span class="d-none d-sm-inline-block"> <?php echo $adminContext->texts['DISPLAY']; ?></span></div>
                                    <select class="select-url form-select form-select-sm">
                                        <?php
                                        foreach ([50, 100, 200] as $limitOption) {
                                            $selected = ($limit == $limitOption) ? 'selected="selected"' : '';
                                            echo "<option value=\"module=" . MODULE . "&view=list&limit={$limitOption}\" {$selected}>{$limitOption}</option>";
                                        } ?>
                                    </select>
                                </div>
                                <?php
                                if ($total > $limit) { ?>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><?php echo $adminContext->texts['PAGE']; ?></div>
                                        <select class="select-url form-select form-select-sm">
                                            <?php
                                            for ($i = 1; $i <= $num_pages; $i++) {
                                                $offset2 = ($i - 1) * $limit;
                                                $selected = ($offset2 == $offset) ? 'selected="selected"' : '';
                                                echo "<option value=\"module=" . MODULE . "&view=list&offset={$offset2}\" {$selected}>{$i}</option>";
                                            } ?>
                                        </select>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="listing_base">
                                <thead>
                                    <tr class="nodrop nodrag">
                                        <th width="80">
                                            <?php
                                            if (RANKING) { ?>
                                                <a href="module=<?php echo MODULE; ?>&view=list&order=rank&sort=<?php echo ($order == 'rank') ? $rsort : 'asc'; ?>">
                                                    # <i class="fa-solid fa-fw fa-sort<?php if ($order == 'rank') echo '-' . $sort_class; ?>"></i>
                                                </a>
                                                <?php
                                            } ?>
                                        </th>
                                        <th width="60">
                                            <a href="module=<?php echo MODULE; ?>&view=list&order=id&sort=<?php echo ($order == 'id') ? $rsort : 'asc'; ?>">
                                                ID <i class="fa-solid fa-fw fa-sort<?php if ($order == 'id') echo '-' . $sort_class; ?>"></i>
                                            </a>
                                        </th>

                                        <?php
                                        if (NB_FILES > 0) echo '<th width="160">' . $adminContext->texts['IMAGE'] . '</th>';

                                        foreach ($cols as $col) { ?>
                                            <th>
                                                <a href="module=<?php echo MODULE; ?>&view=list&order=<?php echo $col->name; ?>&sort=<?php echo ($order == $col->name) ? $rsort : 'asc'; ?>">
                                                    <?php echo $col->label; ?>
                                                    <i class="fa-solid fa-fw fa-sort<?php if ($order == $col->name) echo '-' . $sort_class; ?>"></i>
                                                </a>
                                            </th>
                                            <?php
                                        }
                                        if (DATES) { ?>
                                            <th width="180"><?php echo $adminContext->texts['ADDED_ON']; ?></th>
                                            <th width="180"><?php echo $adminContext->texts['UPDATED_ON']; ?></th>
                                         <?php
                                        }
                                        if (MAIN) { ?>
                                            <th width="100"><?php echo $adminContext->texts['MAIN']; ?></th>
                                            <?php
                                        }
                                        if (HOME) { ?>
                                            <th width="80"><?php echo $adminContext->texts['HOME']; ?></th>
                                            <?php
                                        }
                                        if (VALIDATION) { ?>
                                            <th width="100"><?php echo $adminContext->texts['STATUS']; ?></th>
                                            <?php
                                        } ?>
                                        <th width="140"><?php echo $adminContext->texts['ACTIONS']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($items)) { ?>
                                        <?php
                                        foreach ($items as $i => $item) { ?>
                                            <tr id="item_<?php echo $item['id']; ?>">
                                                <td class="text-start">
                                                    <input type="checkbox" class="checkitem" name="multiple_item[]" value="<?php echo $item['id']; ?>"/>
                                                    <?php if (RANKING) echo $item['rank']; ?>
                                                </td>
                                                <td class="text-center"><?php echo $item['id']; ?></td>
                                                <?php
                                                if (NB_FILES > 0) { ?>
                                                    <td class="text-center wrap-img">
                                                        <?php
                                                        if (!empty($item['images']['preview'])) { ?>
                                                            <a href="<?php echo $item['images']['zoom']; ?>" class="image-link" rel="<?php echo $item['images']['zoom']; ?>">
                                                                <img src="<?php echo $item['images']['preview']; ?>" alt="">
                                                            </a>
                                                            <?php
                                                        } ?>
                                                    </td>
                                                    <?php
                                                }
                                                foreach ($item['cols'] as $col) { ?>
                                                    <td><?php echo $col->getValue($i); ?></td>
                                                    <?php
                                                }
                                                if (DATES) { ?>
                                                    <td class="text-center"><?php echo DateUtils::strftime(PMS_DATE_FORMAT.' '.PMS_TIME_FORMAT, $item['add_date'], true); ?></td>
                                                    <td class="text-center"><?php echo DateUtils::strftime(PMS_DATE_FORMAT.' '.PMS_TIME_FORMAT, $item['edit_date'], true); ?></td>
                                                    <?php
                                                }
                                                if (MAIN) { ?>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($item['main'] == 1) { ?>
                                                            <i class="fa-solid fa-star text-primary"></i>
                                                            <?php
                                                        } else {
                                                            if ($adminContext->publishAllowed) { ?>
                                                                <a href="module=<?php echo MODULE; ?>&view=list&action=define_main&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>" title="<?php echo $adminContext->texts['DEFINE_MAIN']; ?>">
                                                                    <i class="fa-solid fa-star text-muted"></i>
                                                                </a>
                                                                <?php
                                                            }
                                                        } ?>
                                                    </td>
                                                    <?php
                                                }
                                                if (HOME) { ?>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($item['home'] == 1) { ?>
                                                            <a href="module=<?php echo MODULE; ?>&view=list&action=remove_home&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>" title="<?php echo $adminContext->texts['REMOVE_HOMEPAGE']; ?>">
                                                                <i class="fa-solid fa-fw fa-home text-success"></i>
                                                            </a>
                                                            <?php
                                                        } else { ?>
                                                            <a href="module=<?php echo MODULE; ?>&view=list&action=display_home&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>" title="<?php echo $adminContext->texts['SHOW_HOMEPAGE']; ?>">
                                                                <i class="fa-solid fa-fw fa-eye-slash text-danger"></i>
                                                            </a>
                                                            <?php
                                                        } ?>
                                                    </td>
                                                    <?php
                                                }
                                                if (VALIDATION) { ?>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($item['checked'] == 0) {
                                                            echo '<span class="badge bg-warning">' . $adminContext->texts['AWAITING'] . '</span>';
                                                        } elseif ($item['checked'] == 1) {
                                                            echo '<span class="badge bg-success">' . $adminContext->texts['PUBLISHED'] . '</span>';
                                                        } elseif ($item['checked'] == 2) {
                                                            echo '<span class="badge bg-danger">' . $adminContext->texts['NOT_PUBLISHED'] . '</span>';
                                                        } elseif ($item['checked'] == 3) {
                                                            echo '<span class="badge bg-secondary">' . $adminContext->texts['ARCHIVED'] . '</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                } ?>
                                                <td class="text-center">
                                                    <?php
                                                    if ($adminContext->editAllowed) { ?>

                                                        <a class="tips" href="module=<?php echo MODULE; ?>&view=form&id=<?php echo $item['id']; ?>" title="<?php echo $adminContext->texts['EDIT']; ?>"><i class="fa-solid fa-fw fa-edit"></i></a>
                                                        <?php
                                                    }
                                                    if ($adminContext->deleteAllowed) { ?>
                                                        
                                                        <a class="tips" href="javascript:if(confirm('<?php echo $adminContext->texts['DELETE_CONFIRM2']; ?>')) window.location = 'module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=delete';" title="<?php echo $adminContext->texts['DELETE']; ?>"><i class="fa-solid fa-fw fa-trash-alt text-danger"></i></a>
                                                        <?php
                                                    }
                                                    if (VALIDATION && $adminContext->publishAllowed) {
                                                        if($item['checked'] == 0){ ?>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=check" title="<?php echo $adminContext->texts['PUBLISH']; ?>"><i class="fa-solid fa-fw fa-check text-success"></i></a>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=uncheck" title="<?php echo $adminContext->texts['UNPUBLISH']; ?>"><i class="fa-solid fa-fw fa-ban text-danger"></i></a>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=archive" title="<?php echo $adminContext->texts['ARCHIVE']; ?>"><i class="fa-solid fa-fw fa-archive text-warning"></i></a>
                                                            <?php
                                                        }elseif($item['checked'] == 1){ ?>
                                                            <i class="fa-solid fa-fw fa-check text-muted"></i>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=uncheck" title="<?php echo $adminContext->texts['UNPUBLISH']; ?>"><i class="fa-solid fa-fw fa-ban text-danger"></i></a>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=archive" title="<?php echo $adminContext->texts['ARCHIVE']; ?>"><i class="fa-solid fa-fw fa-archive text-warning"></i></a>
                                                            <?php
                                                        }elseif($item['checked'] == 2){ ?>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=check" title="<?php echo $adminContext->texts['PUBLISH']; ?>"><i class="fa-solid fa-fw fa-check text-success"></i></a>
                                                            <i class="fa-solid fa-fw fa-ban text-muted"></i>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=archive" title="<?php echo $adminContext->texts['ARCHIVE']; ?>"><i class="fa-solid fa-fw fa-archive text-warning"></i></a>
                                                            <?php
                                                        }elseif($item['checked'] == 3){ ?>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=check" title="<?php echo $adminContext->texts['PUBLISH']; ?>"><i class="fa-solid fa-fw fa-check text-success"></i></a>
                                                            <a class="tips" href="module=<?php echo MODULE; ?>&view=list&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $csrf_token; ?>&action=uncheck" title="<?php echo $adminContext->texts['UNPUBLISH']; ?>"><i class="fa-solid fa-fw fa-ban text-danger"></i></a>
                                                            <i class="fa-solid fa-fw fa-archive text-muted"></i>
                                                            <?php
                                                        } ?>
                                                        <?php
                                                    } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        if($total == 0){ ?>
                            <div class="text-center mt-3 mb-3">- <?php echo $adminContext->texts['NO_ELEMENT']; ?> -</div>
                            <?php
                        } ?>
                    </div>
                    <div class="card-footer pt-3 pb-3 form-inline clearfix">
                        <div class="d-flex justify-content-between">
                            <div class="text-start">
                                <?php
                                if($total > 0){ ?>
                                    &nbsp;<input type="checkbox" class="selectall"/>
                                    <?php echo $adminContext->texts['SELECT_ALL']; ?>&nbsp;
                                    <select name="multiple_actions" class="form-select form-select-sm">
                                        <option value="">- <?php echo $adminContext->texts['ACTIONS']; ?> -</option>
                                        <?php
                                        if($adminContext->publishAllowed){
                                            if(VALIDATION){ ?>
                                                <option value="check_multi"><?php echo $adminContext->texts['PUBLISH']; ?></option>
                                                <option value="uncheck_multi"><?php echo $adminContext->texts['UNPUBLISH']; ?></option>
                                                <?php
                                            }
                                            if(HOME){ ?>
                                                <option value="display_home_multi"><?php echo $adminContext->texts['SHOW_HOMEPAGE']; ?></option>
                                                <option value="remove_home_multi"><?php echo $adminContext->texts['REMOVE_HOMEPAGE']; ?></option>
                                                <?php
                                            }
                                        }
                                        if(in_array('delete', $permissions) || in_array('all', $permissions)){ ?>
                                            <option value="delete_multi"><?php echo $adminContext->texts['DELETE']; ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                    <?php
                                } ?>
                            </div>
                            <div class="text-end">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-text"><i class="fa-solid fa-fw fa-th-list"></i> <span class="d-none d-sm-inline-block"><?php echo $adminContext->texts['DISPLAY']; ?></span></div>
                                    <select class="select-url form-select form-select-sm">
                                        <?php
                                        foreach ([50, 100, 200] as $limitOption) {
                                            $selected = ($limit == $limitOption) ? 'selected="selected"' : '';
                                            echo "<option value=\"module=" . MODULE . "&view=list&limit={$limitOption}\" {$selected}>{$limitOption}</option>";
                                        } ?>
                                    </select>
                                </div>
                                <?php
                                if ($total > $limit) { ?>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><?php echo $adminContext->texts['PAGE']; ?></div>
                                        <select class="select-url form-select form-select-sm">
                                            <?php
                                            for ($i = 1; $i <= $num_pages; $i++) {
                                                $offset2 = ($i - 1) * $limit;
                                                $selected = ($offset2 == $offset) ? 'selected="selected"' : '';
                                                echo "<option value=\"module=" . MODULE . "&view=list&offset={$offset2}\" {$selected}>{$i}</option>";
                                            } ?>
                                        </select>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                if (MULTILINGUAL && !empty($languages)) { ?>
                    <div class="card mt-4">
                        <div class="card-body" id="translation">
                            <p><?php echo $adminContext->texts['COMPLETE_LANGUAGE']; ?></p>
                            <?php
                            foreach ($languages as $lang) { ?>
                                <input type="checkbox" name="languages[]" value="<?php echo $lang['id']; ?>">
                                    <img src="<?php echo $lang['image']; ?>" alt="" class="flag">
                                <?php echo $lang['title']; ?><br>
                                <?php
                            } ?>
                            <button type="submit" name="complete_lang" class="btn btn-secondary mt-3" data-toggle="tooltip" data-placement="right" title="<?php echo $adminContext->texts['COMPLETE_LANG_NOTICE']; ?>">
                                <i class="fa-solid fa-fw fa-magic"></i> <?php echo $adminContext->texts['APPLY_LANGUAGE']; ?>
                            </button>
                        </div>
                    </div>
                    <?php
                } ?>

            </form>
        </main>
    </div>
</div>
