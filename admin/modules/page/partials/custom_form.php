<?php
debug_backtrace() || die ('Direct access not permitted');

// Add the corresponding menu item in solutionsCMS_menu

if (!$isNav) { ?>
    
    <div class="row mb-3">
        <label class="col-xl-4 col-form-label">Ajouter au menu principal</label>
        <div class="col-xl-8">
            <div class="form-check form-switch form-check-inline">
                <label class="form-check-label">
                    <input name="add_to_menu" type="checkbox" class="form-check-input" value="1"<?php if(isset($_POST['add_to_menu']) && $_POST['add_to_menu'] == 1) echo ' checked="checked"'; ?>>
                </label>
            </div>
        </div>
    </div>
    <?php
}