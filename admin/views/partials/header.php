<?php debug_backtrace() || die ("Direct access not permitted"); ?>

<body class="d-flex flex-column">
<div id="overlay"><div class="loader"></div></div>
<main class="flex-fill">
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#"><?php echo PMS_SITE_TITLE; ?></a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav">
        <div class="nav-item text-nowrap text-end">
            <div class="hidden-xs" id="info-header">
                <span class="d-none d-sm-inline-block"><?php echo $adminContext->texts['CONNECTED_AS']; ?> </span><a href="module=settings#profile"><i class="fas fa-fw fa-user"></i> <?php echo "<b>".$_SESSION['user']['login']."</b> (".$_SESSION['user']['type'].")"; ?></a>&nbsp;
                <a href="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/module=login&action=logout"><i class="fa-solid fa-fw fa-power-off"></i> <?php echo $adminContext->texts['LOG_OUT']; ?></a>
            </div>
        </div>
    </div>
</header>
<!-- Sidebar -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php if($view == 'dashboard') echo "active"; ?>" aria-current="page" href="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/">
                    <i class="fa-solid fa-fw fa-tachometer-alt"></i> <?php echo $adminContext->texts['DASHBOARD']; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseModules" aria-expanded="true" aria-controls="collapseModules">
                    <i class="fa-solid fa-fw fa-th"></i> <?php echo $adminContext->texts['MODULES']; ?>
                    <i class="fa-solid fa-fw fa-chevron-up float-end"></i>
                </a>
                <div id="collapseModules" class="collapse show">
                    <ul class="btn-toggle-nav list-unstyled fw-normal">
                        <?php
                        foreach($adminContext->modules as $module){
                            echo '<li><a class="nav-link ' . $module->classname . '" href="module=' . $module->name . '&view=list"><i class="fa-solid fa-fw fa-' . $module->icon . '"></i> ' . $module->title . '</a></li>';
                        } ?>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo DOCBASE; ?>" target="_blank">
                    <i class="fa-solid fa-fw fa-eye"></i> <?php echo $adminContext->texts['PREVIEW']; ?>
                </a>
            </li>
            <?php
            if($_SESSION['user']['type'] == "administrator"){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php if($view == 'settings') echo "active"; ?>" href="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/module=settings">
                        <i class="fa-solid fa-fw fa-cog"></i> <?php echo $adminContext->texts['SETTINGS']; ?>
                    </a>
                </li>
                <?php
            } ?>
        </ul>
    </div>
</nav>
<script>
    $(document).ready(function () {
        var $collapseModules = $('#collapseModules');
        var $expandNav = $('[data-bs-target="#collapseModules"]');

        $collapseModules.on('shown.bs.collapse', function () {
            $expandNav.find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });

        $collapseModules.on('hidden.bs.collapse', function () {
            $expandNav.find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        });
    });
</script>