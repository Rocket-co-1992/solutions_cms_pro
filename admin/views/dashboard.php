<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-5 border-bottom">
                <h1><i class="fa-solid fa-gauge-high"></i><?php echo $adminContext->texts['DASHBOARD']; ?></h1>
            </div>
            
            <div class="alert-container">
                <div class="alert alert-success alert-dismissible fade show" role="alert"></div>
                <div class="alert alert-warning alert-dismissible fade show" role="alert"></div>
                <div class="alert alert-danger alert-dismissible fade show" role="alert"></div>
            </div>
            <div class="row" id="dashboard">
                <?php foreach ($modulesData as $module): ?>
                    <div class="dashboard-entry col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-white">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="huge">
                                    <i class="fa-solid fa-fw fa-4x fa-fw fa-<?php echo $module->icon; ?>"></i>
                                </div>
                                <div class="text-end">
                                    <div class="display-4"><?php echo $module->count; ?></div>
                                    <h5 class="mt-0"><?php echo $module->title; ?></h5>
                                    <?php if (!empty($module->last_date)): ?>
                                        <i class="fa-solid fa-fw fa-clock"></i> <small><?php echo $module->last_date; ?></small>
                                    <?php else: ?>
                                        <small>&nbsp;</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="module=<?php echo $module->name; ?>&view=list" class="card-footer text-white d-flex justify-content-between align-items-center">
                                <span><?php echo $adminContext->texts['SHOW']; ?></span>
                                <i class="fa-solid fa-fw fa-chevron-circle-right"></i>
                            </a>
                            <a href="module=<?php echo $module->name; ?>&view=form" class="card-footer text-white d-flex justify-content-between align-items-center">
                                <span><?php echo $adminContext->texts['ADD']; ?></span>
                                <i class="fa-solid fa-fw fa-plus-circle"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="dashboard-entry dashboard-entry-sm col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card bg-yellow">
                        <a href="https://www.google.com/analytics/web/" target="_blank">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="huge">
                                    <i class="fa-solid fa-fw fa-4x fa-fw fa-chart-simple"></i>
                                </div>
                                <div class="text-end">
                                    <h5>Google Analytics <i class="fa-solid fa-fw fa-arrow-right-from-bracket"></i></h5>
                                </div>
                            </div>
                            <div class="card-footer text-white d-flex justify-content-between align-items-center"></div>
                        </a>
                    </div>
                </div>
                <div class="dashboard-entry dashboard-entry-sm col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card bg-blue">
                        <a href="https://www.google.com/analytics/web/" target="_blank">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="huge">
                                    <i class="fa-solid fa-fw fa-4x fa-fw fa-store"></i>
                                </div>
                                <div class="text-end">
                                    <h5>Google MyBuisness <i class="fa-solid fa-fw fa-arrow-right-from-bracket"></i></h5>
                                </div>
                            </div>
                            <div class="card-footer text-white d-flex justify-content-between align-items-center"></div>
                        </a>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>
